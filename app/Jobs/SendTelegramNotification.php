<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\TelegramService;
use App\Models\Admin\Project;
use Illuminate\Support\Facades\Log;

class SendTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $appointmentData;
    private int $projectId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $appointmentData, int $projectId)
    {
        $this->appointmentData = $appointmentData;
        $this->projectId = $projectId;
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramService $telegramService): void
    {
        try {
            // Получаем проект и его настройки Telegram
            $project = Project::find($this->projectId);
            
            if (!$project || !$project->telegram_notifications_enabled) {
                Log::info('Telegram notifications disabled for project', ['project_id' => $this->projectId]);
                return;
            }

            if (!$project->telegram_bot_token || !$project->telegram_chat_id) {
                Log::warning('Telegram settings not configured for project', ['project_id' => $this->projectId]);
                return;
            }

            // Устанавливаем настройки проекта
            $telegramService->setProjectSettings($project->telegram_bot_token ?? '', $project->telegram_chat_id ?? '');

            // Добавляем название салона в данные
            $this->appointmentData['salon_name'] = $project->project_name;

            // Отправляем уведомление
            $sent = $telegramService->sendAppointmentNotification($this->appointmentData);

            if ($sent) {
                Log::info('Telegram notification sent successfully', [
                    'project_id' => $this->projectId,
                    'appointment_data' => $this->appointmentData
                ]);
            } else {
                Log::error('Failed to send Telegram notification', [
                    'project_id' => $this->projectId,
                    'appointment_data' => $this->appointmentData
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in SendTelegramNotification job', [
                'project_id' => $this->projectId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
