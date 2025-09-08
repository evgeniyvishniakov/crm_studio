<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AdminTelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAdminTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $type;
    private array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(AdminTelegramService $adminTelegramService): void
    {
        try {
            $sent = match($this->type) {
                'new_project' => $adminTelegramService->sendNewProjectNotification($this->data),
                'new_subscription' => $adminTelegramService->sendNewSubscriptionNotification($this->data),
                'new_message' => $adminTelegramService->sendNewMessageNotification($this->data),
                'subscription_expires' => $adminTelegramService->sendSubscriptionExpiresNotification($this->data),
                'payment_issue' => $adminTelegramService->sendPaymentIssueNotification($this->data),
                default => false
            };

            if ($sent) {
                Log::info('Admin Telegram notification sent successfully', [
                    'type' => $this->type,
                    'data' => $this->data
                ]);
            } else {
                Log::warning('Admin Telegram notification not sent', [
                    'type' => $this->type,
                    'data' => $this->data
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in SendAdminTelegramNotification job', [
                'type' => $this->type,
                'data' => $this->data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
