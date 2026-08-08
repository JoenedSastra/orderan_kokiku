<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        // Sisa Barang, Barang Masuk, dan Barang Keluar semuanya terhubung ke
        // Master Barang lewat relasi item (Master Barang menentukan
        // master_location-nya, sisa stok dihitung dari ledger stock_ins/stock_outs).
        $items = Item::orderBy('name')->get();

        $stockIns  = StockIn::with(['item', 'user.role'])->latest()->paginate(10, ['*'], 'masuk');
        $stockOuts = StockOut::with(['item', 'user.role'])->latest()->paginate(10, ['*'], 'keluar');

        return view('admin.stock.index', compact('items', 'stockIns', 'stockOuts'));
    }
}
