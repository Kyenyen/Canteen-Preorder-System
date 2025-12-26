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

    /** Cancelled order details */
    protected Order $order;

    /** Create New Customer Cancellation Notification */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /** Get Notification Delivery Channels */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /** Build Customer Cancellation Email */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Cancellation Confirmation - Order #' . $this->order->order_id)
            ->view('canteen.emails.cancellation', ['order' => $this->order]);
    }
}
