<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StockIn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['item', 'user', 'approvedBy'])
            ->latest()
            ->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Setujui permintaan → otomatis buat stock_in untuk requester.
     */
    public function approve(Order $order): RedirectResponse
    {
        if ($order->status !== Order::STATUS_MENUNGGU) {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $order->update([
            'status'      => Order::STATUS_DISETUJUI,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Otomatis tambah stock masuk untuk role requester
        StockIn::create([
            'item_id'     => $order->item_id,
            'user_id'     => $order->user_id,
            'quantity'    => $order->quantity,
            'keterangan'  => 'Dari permintaan #' . $order->id . ' (disetujui Admin)',
            'tanggal'     => now()->toDateString(),
        ]);

        return back()->with('success', 'Permintaan #' . $order->id . ' disetujui. Stock barang bertambah.');
    }

    /**
     * Tolak permintaan.
     */
    public function reject(Order $order): RedirectResponse
    {
        if ($order->status !== Order::STATUS_MENUNGGU) {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $order->update([
            'status'      => Order::STATUS_DITOLAK,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Permintaan #' . $order->id . ' ditolak.');
    }
}
