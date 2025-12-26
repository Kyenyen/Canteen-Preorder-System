<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /** Password reset token */
    public $token;

    /** Create New Password Reset Notification */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /** Get Notification Delivery Channels */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /** Build Password Reset Email */
    public function toMail($notifiable)
    {
        // Construct the reset URL
        // CHANGE THIS URL to match your Vue frontend URL
        // Example: http://localhost:5173/reset-password?token=...&email=...
        $url = url('/reset-password?token=' . $this->token . '&email=' . $notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Reset Your UniCanteen Password')
            ->view('canteen.emails.reset_password', ['url' => $url, 'username' => $notifiable->username]);
    }
}