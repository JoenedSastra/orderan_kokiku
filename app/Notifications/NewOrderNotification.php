<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
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
        return [
            'title'   => 'Permintaan Barang Baru',
            'message' => $this->order->user->name . ' (' . $this->order->user->role?->name . ') meminta '
                . $this->order->quantity . ' ' . $this->order->item->unit . ' ' . $this->order->item->name,
            'url'     => route('admin.orders.index'),
        ];
    }
}
