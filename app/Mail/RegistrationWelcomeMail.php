<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegistrationWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $salon;
    public $phone;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $salon, $phone = null)
    {
        $this->email = $email;
        $this->salon = $salon;
        $this->phone = $phone;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Добро пожаловать в CRM BeautyFlow!')
            ->markdown('emails.registration.welcome')
            ->with([
                'email' => $this->email,
                'salon' => $this->salon,
                'phone' => $this->phone,
            ]);
    }
}
