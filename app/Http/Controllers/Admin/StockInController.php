<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Barang Masuk Gudang — Admin mencatat penerimaan barang dari Supplier.
 */
class StockInController extends Controller
{
    public function index(): View
    {
        $stockIns = StockIn::with(['item', 'supplier', 'user'])
            ->where('location', StockIn::LOCATION_GUDANG)
            ->latest()
            ->paginate(15);

        return view('admin.stock_in.index', compact('stockIns'));
    }

    public function create(): View
    {
        $items     = Item::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.stock_in.create', compact('items', 'suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'item_id'     => 'required|exists:items,id',
            'quantity'    => 'required|integer|min:1',
            'keterangan'  => 'nullable|string|max:255',
            'tanggal'     => 'required|date',
        ]);

        StockIn::create([
            'item_id'     => $request->item_id,
            'supplier_id' => $request->supplier_id,
            'user_id'     => Auth::id(),
            'quantity'    => $request->quantity,
            'location'    => StockIn::LOCATION_GUDANG,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
        ]);

        return redirect()->route('admin.stock_in.index')
            ->with('success', 'Barang masuk gudang berhasil dicatat. Stok Gudang bertambah.');
    }
}
