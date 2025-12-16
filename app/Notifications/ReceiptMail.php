<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReceiptMail extends Notification
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
            ->subject('Your Order Receipt - Order #' . $this->order->order_id)
            ->greeting('Hello ' . $this->order->user->username . ',')
            ->line('Thank you for your order! Here is your receipt details.')
            ->line('')
            ->line('**Order Details:**')
            ->line('Order ID: ' . $this->order->order_id)
            ->line('Order Date: ' . $this->order->date)
            ->line('Pickup Time: ' . $this->order->pickup_time)
            ->line('Total Amount: RM ' . number_format($this->order->total, 2))
            ->line('')
            ->line('**Items:**')
            ->line($this->formatOrderItems())
            ->line('')
            ->action('View Your Order', url('/'))
            ->line('If you have any questions, please reply to this email.')
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
