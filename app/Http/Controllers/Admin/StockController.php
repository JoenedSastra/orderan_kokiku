<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
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
            ->whereDate('created_at', today())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock.index', compact('stockIns', 'title'));
    }
}
