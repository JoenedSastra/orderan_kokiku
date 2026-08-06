<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        $stockIns  = StockIn::with(['item', 'user'])->latest()->paginate(10, ['*'], 'masuk');
        $stockOuts = StockOut::with(['item', 'user'])->latest()->paginate(10, ['*'], 'keluar');
        return view('admin.stock.index', compact('stockIns', 'stockOuts'));
    }
}
