<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancellationMail extends Notification
{
    use Queueable;

    protected Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Cancellation Confirmation - Order #' . $this->order->order_id)
            ->greeting('Hello ' . $this->order->user->username . ',')
            ->line('Your order has been successfully cancelled.')
            ->line('')
            ->line('**Cancelled Order Details:**')
            ->line('Order ID: ' . $this->order->order_id)
            ->line('Order Date: ' . $this->order->date)
            ->line('Total Amount: RM ' . number_format($this->order->total, 2))
            ->line('')
            ->line('**Refund Information:**')
            ->line('Your refund will be processed within **3 working days** to your original payment method.')
            ->line('')
            ->line('**Cancelled Items:**')
            ->line($this->formatOrderItems())
            ->line('')
            ->line('If you have any questions about your cancellation or refund, please contact us.')
            ->salutation('Best regards, UniCanteen Team');
    }

    /**
     * Format order items for email
     */
    private function formatOrderItems(): string
    {
        $items = [];
        foreach ($this->order->products as $product) {
            $quantity = $product->pivot->quantity;
            $subtotal = $product->pivot->subtotal;
            $items[] = "- {$product->name} x{$quantity}: RM " . number_format($subtotal, 2);
        }
        return implode("\n", $items);
    }
}
