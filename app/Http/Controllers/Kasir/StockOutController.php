<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockOutController extends Controller
{
    public function index(): View
    {
        $stockOuts = StockOut::with('item')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
        return view('kasir.stock_out.index', compact('stockOuts'));
    }

    public function create(): View
    {
        $items = Item::orderBy('name')->get();
        return view('kasir.stock_out.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'item_id'    => 'required|exists:items,id',
            'quantity'   => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
            'tanggal'    => 'required|date',
        ]);

        // Cek stok restoran tersedia
        $item = Item::findOrFail($request->item_id);
        $stok = $item->stokRestoran();

        if ($request->quantity > $stok) {
            return back()
                ->withErrors(['quantity' => 'Stok tidak mencukupi. Stok tersedia: ' . $stok . ' ' . $item->unit])
                ->withInput();
        }

        StockOut::create([
            'item_id'    => $request->item_id,
            'user_id'    => Auth::id(),
            'quantity'   => $request->quantity,
            'location'   => StockOut::LOCATION_RESTORAN,
            'keterangan' => $request->keterangan,
            'tanggal'    => $request->tanggal,
        ]);

        return redirect()->route('kasir.stock_out.index')->with('success', 'Stock keluar berhasil dicatat.');
    }
}
