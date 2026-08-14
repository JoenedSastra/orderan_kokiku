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

        // Satu-satunya tabel yang ditampilkan sekarang adalah ledger Barang
        // Masuk — otomatis sinkron dengan hasil input di "Barang Masuk Harian".
        $stockIns = StockIn::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->whereIn('master_location', $activeLocations))
            ->latest()
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
                ->filter(fn (Item $item) => $item->stokGudang() > 0)
                ->values();
        }

        return view('admin.stock.index', compact('stockIns', 'title', 'itemsGudangUtama'));
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
            'keterangan'  => 'required|in:Gudang Resto,Kasir,Kitchen',
        ]);

        $sourceItem = Item::findOrFail($request->item_id);

        if ($sourceItem->master_location !== Item::MASTER_GUDANG_UTAMA) {
            return back()->withErrors(['item_id' => 'Barang ini bukan barang Gudang Utama.'])->withInput();
        }

        $stokTersedia = $sourceItem->stokGudang();

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

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $sourceItem, $labelTujuan) {
            $userId = \Illuminate\Support\Facades\Auth::id();

            // Kurangi stok Gudang Utama pada barang sumber. Keterangan yang
            // tersimpan PERSIS sama dengan yang dipilih admin di dropdown.
            StockOut::create([
                'item_id'    => $sourceItem->id,
                'user_id'    => $userId,
                'quantity'   => $request->quantity,
                'location'   => StockOut::LOCATION_GUDANG,
                'keterangan' => $request->keterangan,
                'tanggal'    => today(),
            ]);

            // Cari/buat barang tujuan dengan nama & satuan yang sama.
            $targetItem = Item::firstOrCreate(
                ['name' => $sourceItem->name, 'master_location' => $request->destination],
                ['unit' => $sourceItem->unit, 'min_stock' => 0]
            );

            StockIn::create([
                'item_id'      => $targetItem->id,
                'user_id'      => $userId,
                'quantity'     => $request->quantity,
                'location'     => StockIn::LOCATION_RESTORAN,
                'keterangan'   => 'Diterima',
                'tanggal'      => today(),
                'is_completed' => true,
            ]);
        });

        return back()->with('success', $sourceItem->name . ' berhasil dikirim ke ' . $labelTujuan . '.');
    }

    /**
     * Riwayat barang yang sudah dikirim KELUAR dari Gudang Utama ke divisi
     * lain (Gudang Resto/Kasir/Kitchen) — ledger StockOut lokasi "gudang".
     */
    public function riwayatTerkirim(): View
    {
        $riwayat = StockOut::with(['item', 'user.role'])
            ->where('location', StockOut::LOCATION_GUDANG)
            ->whereHas('item', fn ($q) => $q->where('master_location', Item::MASTER_GUDANG_UTAMA))
            ->latest()
            ->paginate(15);

        return view('admin.stock.riwayat_terkirim', compact('riwayat'));
    }
}
