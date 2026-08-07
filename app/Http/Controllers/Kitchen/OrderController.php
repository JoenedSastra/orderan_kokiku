<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['item', 'approvedBy'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
        return view('kitchen.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $items = Item::orderBy('name')->get();
        return view('kitchen.orders.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'item_id'    => 'required|exists:items,id',
            'quantity'   => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $order = Order::create([
            'item_id'    => $request->item_id,
            'user_id'    => Auth::id(),
            'quantity'   => $request->quantity,
            'keterangan' => $request->keterangan,
            'status'     => Order::STATUS_MENUNGGU,
        ]);

        $admins = User::whereHas('role', fn ($q) => $q->where('slug', Role::ADMIN))->get();
        Notification::send($admins, new NewOrderNotification($order));

        return redirect()->route('kitchen.orders.index')->with('success', 'Permintaan barang berhasil dikirim ke Admin.');
    }
}
