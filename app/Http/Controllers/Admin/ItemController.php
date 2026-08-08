<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Master Barang — sekarang read-only + hapus saja. Barang baru masuk
 * otomatis lewat form "Barang Masuk Gudang" (Admin\StockInController),
 * dikelompokkan ke salah satu dari 4 Master Barang berdasarkan pilihan
 * Admin saat itu: Gudang Utama, Gudang Resto, Kasir, atau Kitchen.
 */
class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::orderBy('name')->get();

        $grouped = [
            'gudang_utama' => $items->where('master_location', Item::MASTER_GUDANG_UTAMA)->values(),
            'gudang_resto' => $items->where('master_location', Item::MASTER_GUDANG_RESTO)->values(),
            'kasir'        => $items->where('master_location', Item::MASTER_KASIR)->values(),
            'kitchen'      => $items->where('master_location', Item::MASTER_KITCHEN)->values(),
        ];

        return view('admin.items.index', compact('grouped'));
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
