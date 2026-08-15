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
            ->whereHas('stockIns')
            ->orderBy('name')
            ->get();

        return view('admin.stock.index', compact('items', 'title', 'itemsGudangUtama', 'allItemsForAdjust'));
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
            'keterangan'  => 'required|in:Kirim di Gudang Resto,Kirim di Kasir,Kirim di Kitchen',
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

        // Teks Keterangan yang benar-benar tersimpan — selalu "Kirim di
        // <Divisi>", diturunkan dari "Kirim ke" (bukan dari nilai mentah
        // dropdown Keterangan), supaya selalu konsisten dengan divisi tujuan
        // yang sesungguhnya menerima barang ini.
        $keteranganTersimpan = 'Kirim di ' . $labelTujuan;

        $targetLocation = match ($request->destination) {
            'kasir'        => StockIn::LOCATION_KASIR,
            'kitchen'      => StockIn::LOCATION_KITCHEN,
            default        => StockIn::LOCATION_GUDANG_UTAMA, // Gudang Resto uses gudang_utama location for stock pooling
        };

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $sourceItem, $labelTujuan, $keteranganTersimpan, $targetLocation) {
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
            StockIn::create([
                'item_id'      => $targetItem->id,
                'user_id'      => $userId,
                'quantity'     => $request->quantity,
                'location'     => $targetLocation,
                'keterangan'   => $keteranganTersimpan,
                'tanggal'      => today(),
                'is_completed' => true,
            ]);
        });

        return back()->with('success', $sourceItem->name . ' berhasil dikirim ke ' . $labelTujuan . '.');
    }

    /**
     * Penyesuaian jumlah stok manual oleh Admin.
     */
    public function adjustStock(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'new_stock' => 'required|array',
            'new_stock.*' => 'required|integer|min:0',
        ]);

        $userId = \Illuminate\Support\Facades\Auth::id();
        $changedCount = 0;

        foreach ($request->new_stock as $itemId => $newStock) {
            $item = Item::find($itemId);
            if (!$item) continue;
            
            $currentStock = $item->stokByLocation($item->master_location);
            $newStockInt = (int) $newStock;
            
            if ($newStockInt === $currentStock) {
                continue;
            }

            $difference = $newStockInt - $currentStock;

            $location = match ($item->master_location) {
                Item::MASTER_KASIR   => StockIn::LOCATION_KASIR,
                Item::MASTER_KITCHEN => StockIn::LOCATION_KITCHEN,
                default              => StockIn::LOCATION_GUDANG_UTAMA,
            };

            $keterangan = 'Penyesuaian stok manual oleh Admin';

            if ($difference > 0) {
                // Stok bertambah
                StockIn::create([
                    'item_id'      => $item->id,
                    'user_id'      => $userId,
                    'quantity'     => $difference,
                    'location'     => $location,
                    'keterangan'   => $keterangan,
                    'tanggal'      => today(),
                    'is_completed' => true,
                ]);
            } else {
                // Stok berkurang
                StockOut::create([
                    'item_id'    => $item->id,
                    'user_id'    => $userId,
                    'quantity'   => abs($difference),
                    'location'   => $location,
                    'keterangan' => $keterangan,
                    'tanggal'    => today(),
                ]);
            }
            
            $changedCount++;
        }

        if ($changedCount > 0) {
            return back()->with('success', $changedCount . ' barang berhasil disesuaikan stoknya.');
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
                $item->delete(); // Automatically cascades to stock_ins, stock_outs, and orders
                $deletedCount++;
            }
        }

        return back()->with('success', $deletedCount . ' barang berhasil dihapus permanen beserta seluruh riwayatnya.');
    }

    /**
     * Riwayat barang yang sudah dikirim KELUAR dari Gudang Utama ke divisi
     * lain (Gudang Resto/Kasir/Kitchen) — ledger StockOut lokasi "gudang".
     */
    public function riwayatTerkirim(Request $request): View
    {
        $tanggal = $request->input('tanggal');

        $riwayat = StockOut::with(['item', 'user.role'])
            ->where('location', StockOut::LOCATION_GUDANG_UTAMA)
            ->whereHas('item', fn ($q) => $q->where('master_location', Item::MASTER_GUDANG_UTAMA))
            ->when($tanggal, fn ($q) => $q->whereDate('created_at', $tanggal))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock.riwayat_terkirim', compact('riwayat', 'tanggal'));
    }
}
