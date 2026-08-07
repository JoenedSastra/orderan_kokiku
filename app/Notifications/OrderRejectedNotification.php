<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class OrderRejectedNotification extends Notification
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
            'title'   => 'Permintaan Ditolak',
            'message' => 'Permintaan #' . $this->order->id . ' (' . $this->order->item->name . ' '
                . $this->order->quantity . ' ' . $this->order->item->unit . ') ditolak oleh Admin.',
            'url'     => route($routeName),
        ];
    }
}
