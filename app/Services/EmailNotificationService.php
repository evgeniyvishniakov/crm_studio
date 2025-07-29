<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Admin\Project;
use App\Models\Clients\Appointment;
use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentReminder;
use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentRescheduled;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Отправить подтверждение записи клиенту
     */
    public function sendConfirmation(Appointment $appointment): bool
    {
        try {
            $project = $appointment->project;
            
            // Проверяем, включены ли email уведомления
            if (!$project->email_notifications_enabled) {
                Log::info('Email notifications disabled for project', ['project_id' => $project->id]);
                return false;
            }
            
            // Проверяем, есть ли email у клиента
            if (!$appointment->client->email) {
                Log::info('Client has no email', [
                    'client_id' => $appointment->client->id,
                    'appointment_id' => $appointment->id
                ]);
                return false;
            }
            
            // Настраиваем SMTP для проекта
            $this->configureProjectMail($project);
            
            // Отправляем письмо
            Mail::to($appointment->client->email)->send(new AppointmentConfirmation($appointment));
            
            Log::info('Appointment confirmation email sent', [
                'appointment_id' => $appointment->id,
                'client_email' => $appointment->client->email,
                'project_id' => $project->id
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send appointment confirmation email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Отправить напоминание о записи клиенту
     */
    public function sendReminder(Appointment $appointment): bool
    {
        try {
            $project = $appointment->project;
            
            // Проверяем, включены ли email уведомления
            if (!$project->email_notifications_enabled) {
                Log::info('Email notifications disabled for project', ['project_id' => $project->id]);
                return false;
            }
            
            // Проверяем, есть ли email у клиента
            if (!$appointment->client->email) {
                Log::info('Client has no email', [
                    'client_id' => $appointment->client->id,
                    'appointment_id' => $appointment->id
                ]);
                return false;
            }
            
            // Настраиваем SMTP для проекта
            $this->configureProjectMail($project);
            
            // Отправляем письмо
            Mail::to($appointment->client->email)->send(new AppointmentReminder($appointment));
            
            Log::info('Appointment reminder email sent', [
                'appointment_id' => $appointment->id,
                'client_email' => $appointment->client->email,
                'project_id' => $project->id
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send appointment reminder email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Отправить уведомление об отмене записи клиенту
     */
    public function sendCancellation(Appointment $appointment, string $reason = null): bool
    {
        try {
            $project = $appointment->project;
            
            // Проверяем, включены ли email уведомления
            if (!$project->email_notifications_enabled) {
                Log::info('Email notifications disabled for project', ['project_id' => $project->id]);
                return false;
            }
            
            // Проверяем, есть ли email у клиента
            if (!$appointment->client->email) {
                Log::info('Client has no email', [
                    'client_id' => $appointment->client->id,
                    'appointment_id' => $appointment->id
                ]);
                return false;
            }
            
            // Настраиваем SMTP для проекта
            $this->configureProjectMail($project);
            
            // Отправляем письмо
            Mail::to($appointment->client->email)->send(new AppointmentCancelled($appointment, $reason));
            
            Log::info('Appointment cancellation email sent', [
                'appointment_id' => $appointment->id,
                'client_email' => $appointment->client->email,
                'project_id' => $project->id,
                'reason' => $reason
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send appointment cancellation email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Отправить уведомление о переносе записи клиенту
     */
    public function sendReschedule(Appointment $appointment, string $oldDate = null, string $oldTime = null, string $reason = null): bool
    {
        try {
            $project = $appointment->project;
            
            // Проверяем, включены ли email уведомления
            if (!$project->email_notifications_enabled) {
                Log::info('Email notifications disabled for project', ['project_id' => $project->id]);
                return false;
            }
            
            // Проверяем, есть ли email у клиента
            if (!$appointment->client->email) {
                Log::info('Client has no email', [
                    'client_id' => $appointment->client->id,
                    'appointment_id' => $appointment->id
                ]);
                return false;
            }
            
            // Настраиваем SMTP для проекта
            $this->configureProjectMail($project);
            
            // Отправляем письмо
            Mail::to($appointment->client->email)->send(new AppointmentRescheduled($appointment, $oldDate, $oldTime, $reason));
            
            Log::info('Appointment reschedule email sent', [
                'appointment_id' => $appointment->id,
                'client_email' => $appointment->client->email,
                'project_id' => $project->id,
                'old_date' => $oldDate,
                'old_time' => $oldTime,
                'reason' => $reason
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send appointment reschedule email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Настроить SMTP для конкретного проекта
     */
    private function configureProjectMail(Project $project): void
    {
        config([
            'mail.mailers.smtp.host' => $project->email_host,
            'mail.mailers.smtp.port' => $project->email_port,
            'mail.mailers.smtp.username' => $project->email_username,
            'mail.mailers.smtp.password' => $project->email_password,
            'mail.mailers.smtp.encryption' => $project->email_encryption,
            'mail.from.address' => $project->email_username,
            'mail.from.name' => $project->email_from_name,
        ]);
    }
}