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
    public $token;
    public $language;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $salon, $phone = null, $token = null, $language = 'ru')
    {
        $this->email = $email;
        $this->salon = $salon;
        $this->phone = $phone;
        $this->token = $token;
        $this->language = $language;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Устанавливаем язык для переводов
        app()->setLocale($this->language);
        
        return $this->subject(__('emails.registration.welcome.title'))
            ->markdown('emails.registration.welcome')
            ->with([
                'email' => $this->email,
                'salon' => $this->salon,
                'phone' => $this->phone,
                'token' => $this->token,
                // Используем маршрут для клиентской части с языком
                'resetUrl' => route('password.reset', ['token' => $this->token, 'email' => $this->email, 'lang' => $this->language]),
            ]);
    }
}
