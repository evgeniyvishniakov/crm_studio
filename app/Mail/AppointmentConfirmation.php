<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Clients\Appointment;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $project;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->project = $appointment->project;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Подтверждение записи - ' . $this->project->project_name)
            ->markdown('emails.appointments.confirmation')
            ->with([
                'appointment' => $this->appointment,
                'project' => $this->project,
                'client' => $this->appointment->client,
                'service' => $this->appointment->service,
                'master' => $this->appointment->user,
            ]);
    }
}