<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        // Dikelompokkan per kategori secara dinamis — kategori baru otomatis
        // muncul sebagai tab baru tanpa perlu ubah kode.
        $categories = Category::with(['items' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        $stockIns  = StockIn::with(['item', 'user.role'])->latest()->paginate(10, ['*'], 'masuk');
        $stockOuts = StockOut::with(['item', 'user.role'])->latest()->paginate(10, ['*'], 'keluar');

        return view('admin.stock.index', compact('categories', 'stockIns', 'stockOuts'));
    }
}
