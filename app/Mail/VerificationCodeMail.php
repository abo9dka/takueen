<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pinCode;

    /**
     * Create a new message instance.
     *
     * @param string $pinCode
     * @return void
     */
    public function __construct($pinCode)
    {
        $this->pinCode = $pinCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $code = $this->pinCode;

        return $this->subject('Verification Code')
            ->html("
                <h2>Verification Code</h2>
                <p>Your code is: <b>{$code}</b></p>
            ");
    }
}
