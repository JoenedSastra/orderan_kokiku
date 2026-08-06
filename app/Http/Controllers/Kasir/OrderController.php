<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['item', 'approvedBy'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
        return view('kasir.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $items = Item::orderBy('name')->get();
        return view('kasir.orders.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'item_id'    => 'required|exists:items,id',
            'quantity'   => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Order::create([
            'item_id'    => $request->item_id,
            'user_id'    => Auth::id(),
            'quantity'   => $request->quantity,
            'keterangan' => $request->keterangan,
            'status'     => Order::STATUS_MENUNGGU,
        ]);

        return redirect()->route('kasir.orders.index')->with('success', 'Permintaan barang berhasil dikirim ke Admin.');
    }
}
