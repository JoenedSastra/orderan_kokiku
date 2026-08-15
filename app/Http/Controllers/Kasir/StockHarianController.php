<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockHarianController extends Controller
{
    public function index(Request $request): View
    {
        $query = \App\Models\Item::with(['stockIns.user.role', 'stockOuts.user.role'])
            ->where('master_location', \App\Models\Item::MASTER_KASIR)
            ->whereHas('stockIns');

        if ($request->filled('kk_search')) {
            $query->where('name', 'like', '%' . $request->kk_search . '%');
        }

        $items = $query->orderBy('name')->paginate(15)->withQueryString();

        $allItemsForAdjust = \App\Models\Item::where('master_location', \App\Models\Item::MASTER_KASIR)
            ->whereHas('stockIns', function($q) {
                $q->where('location', \App\Models\StockIn::LOCATION_KASIR);
            })
            ->orderBy('name')
            ->get();

        return view('kasir.stock-harian.index', compact('items', 'allItemsForAdjust'));
    }
}
