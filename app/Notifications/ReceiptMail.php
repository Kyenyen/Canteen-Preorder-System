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

    /** Order details for receipt */
    protected Order $order;

    /** Create New Receipt Notification */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /** Get Notification Delivery Channels */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /** Build Order Receipt Email */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Order Receipt - Order #' . $this->order->order_id)
            ->view('canteen.emails.receipt', ['order' => $this->order]);
    }
}
