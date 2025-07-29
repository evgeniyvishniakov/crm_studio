<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Clients\Appointment;

class AppointmentCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $project;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, string $reason = null)
    {
        $this->appointment = $appointment;
        $this->project = $appointment->project;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Запись отменена - ' . $this->project->project_name)
            ->markdown('emails.appointments.cancelled')
            ->with([
                'appointment' => $this->appointment,
                'project' => $this->project,
                'client' => $this->appointment->client,
                'service' => $this->appointment->service,
                'master' => $this->appointment->user,
                'reason' => $this->reason,
            ]);
    }
}