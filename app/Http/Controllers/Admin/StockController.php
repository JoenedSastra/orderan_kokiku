<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Stock Gudang Utama / Stock Gudang Resto — dipilih lewat query string
     * ?filter=gudang_utama atau ?filter=gudang_resto dari sidebar.
     */
    public function index(Request $request): View
    {
        return $this->render(
            [Item::MASTER_GUDANG_UTAMA, Item::MASTER_GUDANG_RESTO],
            'Gudang & Resto',
            $request->get('filter'),
            [
                Item::MASTER_GUDANG_UTAMA => 'Gudang Utama',
                Item::MASTER_GUDANG_RESTO => 'Gudang Resto',
            ]
        );
    }

    /**
     * Stock Kasir / Stock Kitchen — dipilih lewat query string
     * ?filter=kasir atau ?filter=kitchen dari sidebar.
     */
    public function kasirKitchen(Request $request): View
    {
        return $this->render(
            [Item::MASTER_KASIR, Item::MASTER_KITCHEN],
            'Kasir & Kitchen',
            $request->get('filter'),
            [
                Item::MASTER_KASIR   => 'Kasir',
                Item::MASTER_KITCHEN => 'Kitchen',
            ]
        );
    }

    private function render(array $masterLocations, string $title, ?string $filter, array $filterOptions): View
    {
        // Kalau ada filter dan valid (salah satu opsi di halaman ini),
        // persempit ke 1 Master Barang saja. Kalau tidak, tampilkan gabungan
        // keduanya seperti biasa.
        $activeLocations = ($filter && in_array($filter, $masterLocations, true))
            ? [$filter]
            : $masterLocations;

        // Judul halaman mengikuti divisi yang sedang aktif dari sidebar,
        // bukan judul gabungan, supaya jelas sedang melihat stok divisi apa.
        $title = ($filter && isset($filterOptions[$filter])) ? $filterOptions[$filter] : $title;

        // Tampilkan STOK TERKINI per barang (masuk dikurangi keluar lewat
        // Item::totalStock()) — bukan daftar transaksi Barang Masuk mentah.
        // Dengan begini, saat barang dikirim lewat "Kirim Barang", jumlah yang
        // tampil di sini langsung ikut berkurang karena dihitung ulang tiap
        // kali halaman ini dibuka, bukan angka statis dari satu transaksi.
        //
        // Cuma tampilkan barang yang MASIH punya riwayat masuk (StockIn).
        // Barang yang riwayat masuknya sudah dihapus semua (lewat "Hapus
        // Terpilih" di Riwayat) otomatis hilang dari sini juga, walau dulu
        // pernah punya riwayat "Kirim Barang" — riwayat kirimnya sendiri
        // tetap bisa dilihat lengkap di halaman "Riwayat Terkirim".
        $items = Item::with(['stockIns.user.role', 'stockOuts.user.role'])
            ->whereIn('master_location', $activeLocations)
            ->whereHas('stockIns')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Tombol "Kirim Barang" cuma tampil di halaman Stock Gudang Utama.
        // Daftar barangnya cuma dihitung kalau memang lagi di halaman ini,
        // supaya halaman Stock lain tidak ikut menghitung stok tanpa perlu.
        $itemsGudangUtama = collect();
        if ($activeLocations === [Item::MASTER_GUDANG_UTAMA]) {
            $itemsGudangUtama = Item::where('master_location', Item::MASTER_GUDANG_UTAMA)
                ->orderBy('name')
                ->get()
                ->filter(fn (Item $item) => $item->stokGudangUtama() > 0)
                ->values();
        }

        // Fetch all items for the selected filter to be used in the Adjust Stock modal dropdown
        $allItemsForAdjust = Item::whereIn('master_location', $activeLocations)
            ->orderBy('name')
            ->get();

        return view('admin.stock.index', compact('items', 'title', 'itemsGudangUtama', 'allItemsForAdjust'));
    }

    /**
     * Tampilkan data stock dari 4 devisi dalam 1 halaman.
     */
    public function dataStock(): View
    {
        $groupItems = function($masterLocation) {
            return Item::where('master_location', $masterLocation)
                ->whereHas('stockIns')
                ->orderBy('name')
                ->get()
                ->groupBy(fn($item) => strtolower(trim($item->name)))
                ->map(function($items) use ($masterLocation) {
                    $first = $items->first();
                    $totalStock = $items->sum(fn($item) => $item->stokByLocation($item->master_location));
                    
                    $unit = $first->unit;
                    if ($masterLocation === \App\Models\Item::MASTER_KASIR) {
                        $unit = $first->kasir_unit ?? $first->unit;
                    } elseif ($masterLocation === \App\Models\Item::MASTER_KITCHEN) {
                        $unit = $first->kitchen_unit ?? $first->unit;
                    }
                    
                    return (object)[
                        'name' => $first->name,
                        'stock' => $totalStock,
                        'unit' => $unit
                    ];
                })
                ->values();
        };

        $gudangUtama = $groupItems(Item::MASTER_GUDANG_UTAMA);
        $gudangResto = $groupItems(Item::MASTER_GUDANG_RESTO);
        $kasir       = $groupItems(Item::MASTER_KASIR);
        $kitchen     = $groupItems(Item::MASTER_KITCHEN);

        return view('admin.stock.data_stock', compact('gudangUtama', 'gudangResto', 'kasir', 'kitchen'));
    }

    /**
     * Kirim barang dari Gudang Utama ke Gudang Resto / Kasir / Kitchen.
     * Stok Gudang Utama berkurang (StockOut ledger "gudang"), dan otomatis
     * ditambahkan ke Master Barang tujuan (StockIn ledger "restoran") dengan
     * nama & satuan yang sama — barang baru dibuat otomatis kalau belum ada.
     */
    public function kirimBarang(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'item_id'     => 'required|exists:items,id',
            'destination' => 'required|in:gudang_resto,kasir,kitchen',
            'quantity'    => 'required|integer|min:1',
            'keterangan'  => 'required|string|max:255',
        ]);

        $sourceItem = Item::findOrFail($request->item_id);

        if ($sourceItem->master_location !== Item::MASTER_GUDANG_UTAMA) {
            return back()->withErrors(['item_id' => 'Barang ini bukan barang Gudang Utama.'])->withInput();
        }

        $stokTersedia = $sourceItem->stokGudangUtama();

        if ($request->quantity > $stokTersedia) {
            return back()->withErrors([
                'quantity' => 'Stok Gudang Utama tidak mencukupi. Stok tersedia: ' . $stokTersedia . ' ' . $sourceItem->unit,
            ])->withInput();
        }

        $labelTujuan = [
            'gudang_resto' => 'Gudang Resto',
            'kasir'        => 'Kasir',
            'kitchen'      => 'Kitchen',
        ][$request->destination];

        // Teks Keterangan yang disimpan adalah input manual dari user
        $keteranganTersimpan = $request->keterangan;

        $targetLocation = match ($request->destination) {
            'kasir'        => StockIn::LOCATION_KASIR,
            'kitchen'      => StockIn::LOCATION_KITCHEN,
            default        => StockIn::LOCATION_GUDANG_UTAMA, // Gudang Resto uses gudang_utama location for stock pooling
        };

        $stockInModel = null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $sourceItem, $labelTujuan, $keteranganTersimpan, $targetLocation, &$stockInModel) {
            $userId = \Illuminate\Support\Facades\Auth::id();

            // Kurangi stok Gudang Utama pada barang sumber.
            StockOut::create([
                'item_id'    => $sourceItem->id,
                'user_id'    => $userId,
                'quantity'   => $request->quantity,
                'location'   => StockOut::LOCATION_GUDANG_UTAMA,
                'keterangan' => $keteranganTersimpan,
                'tanggal'    => today(),
            ]);

            // Cari/buat barang tujuan dengan nama & satuan yang sama.
            $targetItem = Item::firstOrCreate(
                ['name' => $sourceItem->name, 'master_location' => $request->destination],
                ['unit' => $sourceItem->unit, 'min_stock' => 0]
            );

            // Stok divisi tujuan bertambah otomatis, dengan keterangan yang
            // sama persis ("Kirim di <Divisi>").
            $stockInModel = StockIn::create([
                'item_id'      => $targetItem->id,
                'user_id'      => $userId,
                'quantity'     => $request->quantity,
                'location'     => $targetLocation,
                'keterangan'   => $keteranganTersimpan,
                'tanggal'      => today(),
                'is_completed' => true,
            ]);
        });

        if ($stockInModel) {
            $roleSlug = null;
            if ($stockInModel->location === \App\Models\Item::MASTER_KASIR) {
                $roleSlug = \App\Models\Role::KASIR;
            } elseif ($stockInModel->location === \App\Models\Item::MASTER_KITCHEN) {
                $roleSlug = \App\Models\Role::KITCHEN;
            }

            if ($roleSlug) {
                $recipients = \App\Models\User::whereHas('role', fn ($q) => $q->where('slug', $roleSlug))->get();
                if ($recipients->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\StockInNotification($stockInModel));
                }
            }
        }

        return back()->with('success', $sourceItem->name . ' berhasil dikirim ke ' . $labelTujuan . '.');
    }

    /**
     * Penyesuaian jumlah stok manual oleh Admin (Masuk dan Keluar).
     */
    public function adjustStock(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'new_masuk' => 'required|array',
            'new_masuk.*' => 'nullable|numeric|min:0',
            'new_keluar' => 'required|array',
            'new_keluar.*' => 'nullable|numeric|min:0',
        ]);

        $userId = \Illuminate\Support\Facades\Auth::id();
        $changedCount = 0;

        foreach ($request->new_masuk as $itemId => $newMasuk) {
            $item = Item::find($itemId);
            if (!$item) continue;
            
            $newMasukInt = (float) $newMasuk;
            $newKeluarInt = (float) ($request->new_keluar[$itemId] ?? 0);
            
            $currentMasuk = $item->masukByLocation($item->master_location);
            $currentKeluar = $item->keluarByLocation($item->master_location);

            $diffMasuk = $newMasukInt - $currentMasuk;
            $diffKeluar = $newKeluarInt - $currentKeluar;

            if ($diffMasuk === 0 && $diffKeluar === 0) {
                continue;
            }

            $locationIn = match ($item->master_location) {
                Item::MASTER_KASIR   => StockIn::LOCATION_KASIR,
                Item::MASTER_KITCHEN => StockIn::LOCATION_KITCHEN,
                default              => StockIn::LOCATION_GUDANG_UTAMA,
            };

            $locationOut = match ($item->master_location) {
                Item::MASTER_KASIR   => StockOut::LOCATION_KASIR,
                Item::MASTER_KITCHEN => StockOut::LOCATION_KITCHEN,
                default              => StockOut::LOCATION_GUDANG_UTAMA,
            };

            $keterangan = 'Penyesuaian stok manual oleh Admin';

            \Illuminate\Support\Facades\DB::transaction(function () use ($item, $userId, $locationIn, $locationOut, $keterangan, $diffMasuk, $diffKeluar) {
                
                // --- Handle Masuk diff ---
                if ($diffMasuk > 0) {
                    StockIn::create([
                        'item_id'      => $item->id,
                        'user_id'      => $userId,
                        'quantity'     => $diffMasuk,
                        'location'     => $locationIn,
                        'keterangan'   => $keterangan,
                        'tanggal'      => today(),
                        'is_completed' => true,
                    ]);
                } elseif ($diffMasuk < 0) {
                    $toReduce = abs($diffMasuk);
                    $stockIns = StockIn::where('item_id', $item->id)->where('location', $locationIn)->orderBy('id', 'desc')->get();
                    foreach ($stockIns as $si) {
                        if ($toReduce <= 0) break;
                        if ($si->quantity <= $toReduce) {
                            $toReduce -= $si->quantity;
                            $si->delete();
                        } else {
                            $si->quantity -= $toReduce;
                            $si->save();
                            $toReduce = 0;
                        }
                    }
                }

                // --- Handle Keluar diff ---
                if ($diffKeluar > 0) {
                    StockOut::create([
                        'item_id'    => $item->id,
                        'user_id'    => $userId,
                        'quantity'   => $diffKeluar,
                        'location'   => $locationOut,
                        'keterangan' => $keterangan,
                        'tanggal'    => today(),
                    ]);
                } elseif ($diffKeluar < 0) {
                    $toReduce = abs($diffKeluar);
                    $stockOuts = StockOut::where('item_id', $item->id)->where('location', $locationOut)->orderBy('id', 'desc')->get();
                    foreach ($stockOuts as $so) {
                        if ($toReduce <= 0) break;
                        if ($so->quantity <= $toReduce) {
                            $toReduce -= $so->quantity;
                            $so->delete();
                        } else {
                            $so->quantity -= $toReduce;
                            $so->save();
                            $toReduce = 0;
                        }
                    }
                }
            });

            $changedCount++;
        }

        if ($changedCount > 0) {
            return back()->with('success', $changedCount . ' barang berhasil disesuaikan sinkronisasi Masuk/Keluarnya.');
        }

        return back()->with('info', 'Tidak ada perubahan stok yang dilakukan.');
    }

    /**
     * Hapus barang secara massal (beserta riwayatnya secara kaskade).
     */
    public function deleteItems(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'item_ids'   => 'required|array',
            'item_ids.*' => 'exists:items,id',
        ]);

        $itemIds = $request->input('item_ids');
        $deletedCount = 0;

        foreach ($itemIds as $id) {
            $item = Item::find($id);
            if ($item) {
                // Sesuai permintaan: hapus dari halaman Data Stock HANYA akan mengosongkan riwayat
                // (sehingga hilang dari Data Stock karena stok/riwayat = 0),
                // tetapi TIDAK menghapus nama barang dari master list (Catatan Barang Masuk).
                $item->stockIns()->delete();
                $item->stockOuts()->delete();
                $deletedCount++;
            }
        }

        return back()->with('success', $deletedCount . ' riwayat stok barang berhasil di-reset. Barang tidak tampil lagi di Data Stock, namun namanya tetap aman di form pencatatan.');
    }

    /**
     * Riwayat barang yang sudah dikirim KELUAR dari Gudang Utama ke divisi
     * lain (Gudang Resto/Kasir/Kitchen) — ledger StockOut lokasi "gudang".
     */
    public function riwayatTerkirim(Request $request): View
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $riwayat = StockOut::with(['item', 'user.role'])
            ->select('stock_outs.*')
            ->join('items', 'stock_outs.item_id', '=', 'items.id')
            ->where('stock_outs.location', StockOut::LOCATION_GUDANG_UTAMA)
            ->where('items.master_location', Item::MASTER_GUDANG_UTAMA)
            ->where(function ($q) {
                $q->whereNull('stock_outs.keterangan')
                  ->orWhere('stock_outs.keterangan', 'not like', '%Penyesuaian stok manual%');
            })
            ->when($tanggal, fn ($q) => $q->whereDate('stock_outs.created_at', $tanggal))
            ->orderBy('items.name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock.riwayat_terkirim', compact('riwayat', 'tanggal'));
    }
}
