<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class OrderApprovedNotification extends Notification
{
    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $routeName = $notifiable->isKasir() ? 'kasir.orders.index' : 'kitchen.orders.index';

        return [
            'title'   => 'Permintaan Disetujui',
            'message' => 'Permintaan #' . $this->order->id . ' (' . $this->order->item->name . ' '
                . $this->order->quantity . ' ' . $this->order->item->unit . ') telah disetujui Admin. Barang sudah tersedia di Resto.',
            'url'     => route($routeName),
        ];
    }
}
