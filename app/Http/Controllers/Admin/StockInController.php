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
 * Nama barang diketik bebas: kalau belum ada di Master Barang, otomatis
 * dibuat baru dan langsung digolongkan ke salah satu dari 4 Master Barang
 * (Gudang Utama, Gudang Resto, Kasir, Kitchen) sesuai pilihan Admin.
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
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.stock_in.create', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'item_name'       => 'required|string|max:150',
            'unit'            => 'required|string|max:30',
            'master_location' => 'required|in:gudang_utama,gudang_resto,kasir,kitchen',
            'quantity'        => 'required|integer|min:1',
            'tanggal'         => 'required|date',
        ]);

        // Kalau nama barang sudah ada, pakai data yang sama (tidak dobel).
        // Kalau belum ada, buat baru langsung dengan satuan & master barang pilihan Admin.
        $item = Item::firstOrCreate(
            ['name' => trim($request->item_name)],
            [
                'unit'            => $request->unit,
                'master_location' => $request->master_location,
                'min_stock'       => 0,
            ]
        );

        StockIn::create([
            'item_id'      => $item->id,
            'supplier_id'  => $request->supplier_id,
            'user_id'      => Auth::id(),
            'quantity'     => $request->quantity,
            'location'     => StockIn::LOCATION_GUDANG,
            'tanggal'      => $request->tanggal,
            'is_completed' => $request->boolean('is_completed'),
        ]);

        return redirect()->route('admin.stock_in.index')
            ->with('success', 'Barang masuk gudang berhasil dicatat & otomatis masuk ke Master Barang ' . $item->masterLocationLabel() . '.');
    }
}
