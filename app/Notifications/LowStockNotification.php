<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public Inventory $inv;

    public function __construct(Inventory $inv)
    {
        $this->inv = $inv;
    }

    public function via($notifiable)
    {
        return ['database']; // ممكن تضيف 'mail' أو 'broadcast' لو عايز
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => "المخزون قليل: {$this->inv->product->name} ({$this->inv->color}/{$this->inv->size}) = {$this->inv->quantity}",
            'inventory_id' => $this->inv->id,
            'product_id'   => $this->inv->product_id,
        ];
    }
}
