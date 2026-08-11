<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(): View
    {
        // Tampilkan SEMUA barang masuk untuk Master Barang Kasir — otomatis
        // sinkron dengan yang diinput Admin lewat "Barang Masuk Harian" dan
        // diklasifikasikan ke Kasir. Kasir tidak lagi input manual di sini.
        $stockIns = StockIn::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->where('master_location', Item::MASTER_KASIR))
            ->latest()
            ->paginate(15);
        return view('kasir.stock_in.index', compact('stockIns'));
    }
}
