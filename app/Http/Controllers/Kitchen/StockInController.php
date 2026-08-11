<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(): View
    {
        // Tampilkan SEMUA barang masuk untuk Master Barang Kitchen — otomatis
        // sinkron dengan yang diinput Admin lewat "Barang Masuk Harian" dan
        // diklasifikasikan ke Kitchen. Kitchen tidak lagi input manual di sini.
        $stockIns = StockIn::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->where('master_location', Item::MASTER_KITCHEN))
            ->latest()
            ->paginate(15);
        return view('kitchen.stock_in.index', compact('stockIns'));
    }
}
