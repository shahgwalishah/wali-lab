<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LabOrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->order->status === 'completed' ? 'report_ready' : 'status_update',
            'title' => $this->order->status === 'completed' ? 'Report ready' : 'Order status updated',
            'message' => "{$this->order->invoice_no} is now ".str_replace('_', ' ', $this->order->status),
            'order_id' => $this->order->id,
            'status' => $this->order->status,
        ];
    }
}
