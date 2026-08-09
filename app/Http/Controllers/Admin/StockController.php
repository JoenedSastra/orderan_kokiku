<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Stock Gudang & Resto — khusus Master Barang Gudang Utama + Gudang Resto.
     */
    public function index(): View
    {
        return $this->render(
            [Item::MASTER_GUDANG_UTAMA, Item::MASTER_GUDANG_RESTO],
            'Gudang & Resto'
        );
    }

    /**
     * Stock Kasir & Kitchen — khusus Master Barang Kasir + Kitchen.
     */
    public function kasirKitchen(): View
    {
        return $this->render(
            [Item::MASTER_KASIR, Item::MASTER_KITCHEN],
            'Kasir & Kitchen'
        );
    }

    private function render(array $masterLocations, string $title): View
    {
        // Sisa Barang, Barang Masuk, dan Barang Keluar semuanya disaring dari
        // relasi item->master_location yang sama, supaya kedua halaman ini
        // otomatis tetap sinkron dengan Master Barang tanpa data terduplikasi.
        $items = Item::whereIn('master_location', $masterLocations)
            ->orderBy('name')
            ->get();

        $stockIns = StockIn::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->whereIn('master_location', $masterLocations))
            ->latest()
            ->paginate(10, ['*'], 'masuk');

        $stockOuts = StockOut::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->whereIn('master_location', $masterLocations))
            ->latest()
            ->paginate(10, ['*'], 'keluar');

        return view('admin.stock.index', compact('items', 'stockIns', 'stockOuts', 'title'));
    }
}
