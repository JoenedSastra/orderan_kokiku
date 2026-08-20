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
        $items = Item::whereHas('stockIns')->orderBy('name')->get();
        return view('kitchen.orders.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.item_name'  => 'required|string|max:255',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        $admins = User::whereHas('role', fn ($q) => $q->where('slug', Role::ADMIN))->get();

        foreach ($request->items as $itemData) {
            if (empty($itemData['item_name'])) continue;
            
            $item = Item::firstOrCreate(
                ['name' => $itemData['item_name']],
                [
                    'master_location' => Item::MASTER_KITCHEN,
                    'unit' => 'Pcs',
                    'min_stock' => 0
                ]
            );
            
            $order = Order::create([
                'item_id'    => $item->id,
                'user_id'    => Auth::id(),
                'quantity'   => $itemData['quantity'],
                'keterangan' => $itemData['keterangan'] ?? null,
                'status'     => Order::STATUS_MENUNGGU,
            ]);

            Notification::send($admins, new NewOrderNotification($order));
        }

        return redirect()->route('kitchen.orders.create')->with('success', 'Permintaan barang berhasil dikirim ke Admin.');
    }
}
