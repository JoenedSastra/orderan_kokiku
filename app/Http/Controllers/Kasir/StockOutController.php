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
    public function index(Request $request): View
    {
        $query = StockOut::with(['item', 'user.role'])
            ->select('stock_outs.*')
            ->join('items', 'stock_outs.item_id', '=', 'items.id')
            ->where('stock_outs.location', StockOut::LOCATION_KASIR)
            ->orderBy('items.name', 'asc');

        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        $stockOuts = $query->paginate(15);
        return view('kasir.stock_out.index', compact('stockOuts'));
    }

    public function create(): View
    {
        $items = Item::where('master_location', Item::MASTER_KASIR)
            ->orderBy('name')
            ->get()
            ->filter(fn (Item $item) => $item->stokKasir() > 0)
            ->values();
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

        // Cek stok kasir tersedia
        $item = Item::findOrFail($request->item_id);
        $stok = $item->stokKasir();

        if ($request->quantity > $stok) {
            return back()
                ->withErrors(['quantity' => 'Stok tidak mencukupi. Stok Kasir tersedia: ' . $stok . ' ' . $item->unit])
                ->withInput();
        }

        StockOut::create([
            'item_id'    => $request->item_id,
            'user_id'    => Auth::id(),
            'quantity'   => $request->quantity,
            'location'   => StockOut::LOCATION_KASIR,
            'keterangan' => $request->keterangan,
            'tanggal'    => $request->tanggal,
        ]);

        return redirect()->route('kasir.stock_out.index')->with('success', 'Stock keluar berhasil dicatat.');
    }

    public function destroy($id): RedirectResponse
    {
        $stockOut = StockOut::findOrFail($id);
        
        // Pastikan hanya bisa menghapus catatan milik Kasir
        if ($stockOut->location !== StockOut::LOCATION_KASIR) {
            abort(403);
        }

        $stockOut->delete();
        return back()->with('success', 'Riwayat barang keluar berhasil dihapus (masuk ke tempat sampah).');
    }

    public function restore($id): RedirectResponse
    {
        $stockOut = StockOut::withTrashed()->findOrFail($id);
        
        if ($stockOut->location !== StockOut::LOCATION_KASIR) {
            abort(403);
        }

        $stockOut->restore();
        return back()->with('success', 'Riwayat barang keluar berhasil dipulihkan.');
    }
}
