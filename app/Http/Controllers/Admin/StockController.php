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
        // Dikelompokkan per master_location (sama seperti Master Barang), supaya
        // barang yang baru diklasifikasikan lewat "Barang Masuk Gudang" otomatis
        // muncul & nambah stoknya di sini tanpa perlu pengaturan tambahan.
        $items = Item::orderBy('name')->get();

        $grouped = [
            'gudang_utama' => $items->where('master_location', Item::MASTER_GUDANG_UTAMA)->values(),
            'gudang_resto' => $items->where('master_location', Item::MASTER_GUDANG_RESTO)->values(),
            'kasir'        => $items->where('master_location', Item::MASTER_KASIR)->values(),
            'kitchen'      => $items->where('master_location', Item::MASTER_KITCHEN)->values(),
        ];

        $stockIns  = StockIn::with(['item', 'user.role'])->latest()->paginate(10, ['*'], 'masuk');
        $stockOuts = StockOut::with(['item', 'user.role'])->latest()->paginate(10, ['*'], 'keluar');

        return view('admin.stock.index', compact('grouped', 'stockIns', 'stockOuts'));
    }
}
