<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Jobs\SendAdminTelegramNotification;

class CheckExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiring {--days=3 : Количество дней до истечения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет подписки, которые истекают в ближайшие дни, и отправляет уведомления';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $expirationDate = now()->addDays($days);
        
        $this->info("Проверяем подписки, истекающие в течение {$days} дней...");
        
        // Находим подписки, которые истекают в указанный период
        $expiringSubscriptions = Subscription::with(['project', 'adminUser'])
            ->where('status', 'active')
            ->where('expires_at', '<=', $expirationDate)
            ->where('expires_at', '>', now())
            ->get();
        
        $count = 0;
        
        foreach ($expiringSubscriptions as $subscription) {
            // Отправляем уведомление в Telegram
            SendAdminTelegramNotification::dispatch('subscription_expires', [
                'project_name' => $subscription->project->project_name,
                'plan_name' => $subscription->plan_type,
                'expires_at' => $subscription->expires_at->format('d.m.Y H:i:s'),
                'user_name' => $subscription->adminUser->name,
                'user_email' => $subscription->adminUser->email,
            ]);
            
            $count++;
            $this->line("Уведомление отправлено для проекта: {$subscription->project->project_name}");
        }
        
        $this->info("Отправлено уведомлений: {$count}");
        
        return 0;
    }
}
