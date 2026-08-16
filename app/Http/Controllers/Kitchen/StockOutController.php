<?php

namespace App\Http\Controllers\Kitchen;

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
            ->whereIn('stock_outs.location', [StockOut::LOCATION_KITCHEN, 'restoran'])
            ->orderBy('items.name', 'asc');

        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        $stockOuts = $query->paginate(15);
        return view('kitchen.stock_out.index', compact('stockOuts'));
    }

    public function create(): View
    {
        $items = Item::where('master_location', Item::MASTER_KITCHEN)
            ->orderBy('name')
            ->get()
            ->filter(fn (Item $item) => $item->stokKitchen() > 0)
            ->values();
        return view('kitchen.stock_out.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'item_id'    => 'required|exists:items,id',
            'quantity'   => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
            'tanggal'    => 'required|date',
        ]);

        $userId = Auth::id();
        $item   = Item::findOrFail($request->item_id);
        $stok   = $item->stokKitchen();

        if ($request->quantity > $stok) {
            return back()->withErrors(['quantity' => 'Stok Kitchen tidak mencukupi. Stok tersedia: ' . $stok . ' ' . $item->unit])->withInput();
        }

        StockOut::create([
            'item_id'    => $request->item_id,
            'user_id'    => $userId,
            'quantity'   => $request->quantity,
            'location'   => StockOut::LOCATION_KITCHEN,
            'keterangan' => $request->keterangan,
            'tanggal'    => $request->tanggal,
        ]);

        return redirect()->route('kitchen.stock_out.index')->with('success', 'Stock keluar berhasil dicatat.');
    }

    public function destroy($id): RedirectResponse
    {
        $stockOut = StockOut::findOrFail($id);
        
        // Pastikan hanya bisa menghapus catatan milik Kitchen (termasuk data lama dengan location 'restoran')
        if (!in_array($stockOut->location, [StockOut::LOCATION_KITCHEN, 'restoran'])) {
            abort(403);
        }

        $stockOut->delete();
        return back()->with('success', 'Riwayat barang keluar berhasil dihapus (masuk ke tempat sampah).');
    }

    public function restore($id): RedirectResponse
    {
        $stockOut = StockOut::withTrashed()->findOrFail($id);
        
        if (!in_array($stockOut->location, [StockOut::LOCATION_KITCHEN, 'restoran'])) {
            abort(403);
        }

        $stockOut->restore();
        return back()->with('success', 'Riwayat barang keluar berhasil dipulihkan.');
    }
}
