<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockSisaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::with(['category', 'stockIns', 'stockOuts'])
            ->where('master_location', Item::MASTER_KASIR)
            ->whereHas('stockIns', function ($q) {
                $q->where('location', \App\Models\StockIn::LOCATION_KASIR);
            });

        if ($request->filled('kk_search')) {
            $query->where('name', 'like', '%' . $request->kk_search . '%');
        }

        $items = $query->orderBy('name')->paginate(15);

        $items->getCollection()->transform(function ($item) {
            $masuk = $item->stockIns->where('location', \App\Models\StockIn::LOCATION_KASIR)->sum('quantity');
            $keluar = $item->stockOuts->where('location', \App\Models\StockOut::LOCATION_KASIR)->sum('quantity');
            $sisa = max(0, $masuk - $keluar);
            
            $item->total_masuk = $masuk;
            $item->total_keluar = $keluar;
            $item->sisa_stok = $sisa;
            
            return $item;
        });

        return view('kasir.stock_sisa.index', compact('items'));
    }
}
