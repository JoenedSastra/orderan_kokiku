<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Notifications\OrderApprovedNotification;
use App\Notifications\OrderRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Setujui permintaan.
     * Barang dipindahkan dari Stok Gudang ke Stok Restoran secara otomatis:
     *  - StockOut (gudang)   -> mengurangi stok gudang
     *  - StockIn  (restoran) -> menambah stok restoran, dimiliki oleh requester
     */
    public function approve(Order $order): RedirectResponse
    {
        if ($order->status !== Order::STATUS_MENUNGGU) {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $stokGudang = $order->item->stokGudang();

        if ($order->quantity > $stokGudang) {
            return back()->with('error',
                'Stok Gudang tidak mencukupi untuk menyetujui permintaan #' . $order->id .
                '. Stok Gudang saat ini: ' . $stokGudang . ' ' . $order->item->unit .
                '. Silakan tolak permintaan ini atau tambah stok gudang dulu.'
            );
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status'      => Order::STATUS_DISETUJUI,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Keluar dari Gudang
            StockOut::create([
                'item_id'    => $order->item_id,
                'user_id'    => Auth::id(),
                'quantity'   => $order->quantity,
                'location'   => StockOut::LOCATION_GUDANG,
                'keterangan' => 'Transfer ke Restoran — Permintaan #' . $order->id,
                'tanggal'    => now()->toDateString(),
            ]);

            // Masuk ke Restoran, atas nama requester (kasir/kitchen)
            StockIn::create([
                'item_id'    => $order->item_id,
                'user_id'    => $order->user_id,
                'quantity'   => $order->quantity,
                'location'   => StockIn::LOCATION_RESTORAN,
                'keterangan' => 'Dari Gudang — Permintaan #' . $order->id . ' (disetujui Admin)',
                'tanggal'    => now()->toDateString(),
            ]);
        });

        $order->user->notify(new OrderApprovedNotification($order));

        $order->user->notify(new OrderApprovedNotification($order));

        return back()->with('success', 'Permintaan #' . $order->id . ' disetujui. Barang dipindah dari Gudang ke Restoran.');
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

        $order->user->notify(new OrderRejectedNotification($order));

        return back()->with('success', 'Permintaan #' . $order->id . ' ditolak.');
    }
}
