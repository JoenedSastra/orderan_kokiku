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
     * Stock Gudang & Resto — khusus Master Barang Gudang Utama + Gudang Resto.
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
     * Stock Kasir & Kitchen — khusus Master Barang Kasir + Kitchen.
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

        // Sisa Barang, Barang Masuk, dan Barang Keluar semuanya disaring dari
        // relasi item->master_location yang sama, supaya kedua halaman ini
        // otomatis tetap sinkron dengan Master Barang tanpa data terduplikasi.
        $items = Item::whereIn('master_location', $activeLocations)
            ->orderBy('name')
            ->get();

        $stockIns = StockIn::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->whereIn('master_location', $activeLocations))
            ->latest()
            ->paginate(10, ['*'], 'masuk')
            ->withQueryString();

        $stockOuts = StockOut::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->whereIn('master_location', $activeLocations))
            ->latest()
            ->paginate(10, ['*'], 'keluar')
            ->withQueryString();

        return view('admin.stock.index', compact('items', 'stockIns', 'stockOuts', 'title', 'filter', 'filterOptions'));
    }
}
