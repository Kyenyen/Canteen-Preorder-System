<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /** One-time password for email verification */
    public $otp;

    /** Create New OTP Email Instance */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /** Build OTP Email Message */
    public function build()
    {
        return $this->subject('Verify Your Email - UniCanteen')
                    ->view('canteen.emails.otp'); 
    }
}