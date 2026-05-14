<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pinCode;

    public function __construct($code)
    {
        $this->pinCode = $code;
    }

    public function build()
    {
        $code = $this->pinCode;

        return $this->subject('Your Password Reset Code')
            ->html("
                <h2>Password Reset Code</h2>
                <p>Your code is: <b>{$code}</b></p>
            ");
    }
}
