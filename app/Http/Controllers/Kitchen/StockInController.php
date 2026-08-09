<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(): View
    {
        // Tampilkan SEMUA barang masuk untuk Master Barang Kitchen — baik yang
        // dicatat sendiri oleh Kitchen, maupun yang diinput Admin lewat
        // "Barang Masuk Gudang" dan diklasifikasikan ke Kitchen.
        $stockIns = StockIn::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->where('master_location', Item::MASTER_KITCHEN))
            ->latest()
            ->paginate(15);
        return view('kitchen.stock_in.index', compact('stockIns'));
    }

    public function create(): View
    {
        $items = Item::orderBy('name')->get();
        return view('kitchen.stock_in.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'item_id'    => 'required|exists:items,id',
            'quantity'   => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
            'tanggal'    => 'required|date',
        ]);

        StockIn::create([
            'item_id'    => $request->item_id,
            'user_id'    => Auth::id(),
            'quantity'   => $request->quantity,
            'location'   => StockIn::LOCATION_RESTORAN,
            'keterangan' => $request->keterangan,
            'tanggal'    => $request->tanggal,
        ]);

        return redirect()->route('kitchen.stock_in.index')->with('success', 'Stock masuk berhasil dicatat.');
    }
}
