<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Clients\Appointment;

class AppointmentRescheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $project;
    public $oldDate;
    public $oldTime;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, string $oldDate = null, string $oldTime = null, string $reason = null)
    {
        $this->appointment = $appointment;
        $this->project = $appointment->project;
        $this->oldDate = $oldDate;
        $this->oldTime = $oldTime;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Запись перенесена - ' . $this->project->project_name)
            ->markdown('emails.appointments.rescheduled')
            ->with([
                'appointment' => $this->appointment,
                'project' => $this->project,
                'client' => $this->appointment->client,
                'service' => $this->appointment->service,
                'master' => $this->appointment->user,
                'oldDate' => $this->oldDate,
                'oldTime' => $this->oldTime,
                'reason' => $this->reason,
            ]);
    }
}