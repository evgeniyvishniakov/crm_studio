<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Carbon\Carbon;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=30 : Количество дней для хранения уведомлений}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка старых уведомлений';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $deletedCount = Notification::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("Удалено {$deletedCount} старых уведомлений (старше {$days} дней)");
        
        // Логируем очистку
        \Log::info('Notifications cleanup completed', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate,
            'days' => $days
        ]);
        
        return 0;
    }
} 