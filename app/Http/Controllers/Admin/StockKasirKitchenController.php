<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\View\View;

class StockKasirKitchenController extends Controller
{
    public function index(): View
    {
        $masterLocations = [Item::MASTER_KASIR, Item::MASTER_KITCHEN];

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

        return view('admin.stock-kasir-kitchen.index', compact('items', 'stockIns', 'stockOuts'));
    }
}
