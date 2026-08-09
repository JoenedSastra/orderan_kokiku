<?php

namespace App\Notifications;

use App\Models\StockIn;
use Illuminate\Notifications\Notification;

class StockInNotification extends Notification
{
    public function __construct(public StockIn $stockIn)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $item = $this->stockIn->item;

        return [
            'title'   => 'Barang Masuk — ' . $item->masterLocationLabel(),
            'message' => $item->name . ' ' . $this->stockIn->quantity . ' ' . $item->unit
                . ' baru saja masuk ke Master Barang ' . $item->masterLocationLabel() . '.',
            'url'     => $this->notificationUrl($notifiable),
        ];
    }

    private function notificationUrl(object $notifiable): string
    {
        if (method_exists($notifiable, 'isKasir') && $notifiable->isKasir()) {
            return route('kasir.stock_out.index');
        }

        if (method_exists($notifiable, 'isKitchen') && $notifiable->isKitchen()) {
            return route('kitchen.stock_out.index');
        }

        return route('admin.stock.index');
    }
}
