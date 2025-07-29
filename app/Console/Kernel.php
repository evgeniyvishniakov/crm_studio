<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Очистка старых уведомлений каждый день в 2:00
        $schedule->command('notifications:cleanup')
            ->dailyAt('02:00')
            ->appendOutputTo(storage_path('logs/notifications-cleanup.log'));
            
        // Отправка напоминаний о записях каждый день в 9:00
        $schedule->command('appointments:send-reminders')
            ->dailyAt('09:00')
            ->appendOutputTo(storage_path('logs/appointment-reminders.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
