<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\Clients\Appointment;

class FixNotificationAppointmentIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:notification-appointment-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Исправляет существующие уведомления, добавляя им appointment_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Исправляем существующие уведомления...');
        
        // Находим уведомления о веб-записях без appointment_id
        $notifications = Notification::where('type', 'web_booking')
            ->whereNull('appointment_id')
            ->get();
            
        $this->info("📋 Найдено уведомлений для исправления: " . $notifications->count());
        
        if ($notifications->count() == 0) {
            $this->info('✅ Все уведомления уже исправлены');
            return;
        }
        
        $updatedCount = 0;
        
        foreach ($notifications as $notification) {
            $this->line("🔍 Обрабатываем уведомление ID: {$notification->id}");
            
            // Пытаемся найти запись по содержимому уведомления
            // Обычно в body есть информация о клиенте, услуге, дате и времени
            $body = $notification->body;
            
            // Ищем запись по дате создания уведомления (обычно запись создается в то же время)
            $appointment = Appointment::where('created_at', '>=', $notification->created_at->subMinutes(5))
                ->where('created_at', '<=', $notification->created_at->addMinutes(5))
                ->where('project_id', $notification->project_id)
                ->first();
                
            if ($appointment) {
                $notification->appointment_id = $appointment->id;
                $notification->save();
                
                $this->line("  ✅ Найдена запись ID: {$appointment->id}, обновлено");
                $updatedCount++;
            } else {
                $this->line("  ❌ Запись не найдена для уведомления ID: {$notification->id}");
                
                // Попробуем найти по содержимому body
                if (preg_match('/\[ID:([a-f0-9]{32})\]/', $body, $matches)) {
                    $bookingKey = $matches[1];
                    $this->line("  🔑 Найден ключ записи: {$bookingKey}");
                    
                    // Ищем запись по этому ключу или по другим параметрам
                    // Это сложно, поэтому пока просто логируем
                }
            }
        }
        
        $this->info("✅ Обновлено уведомлений: {$updatedCount}");
        $this->info("🏁 Исправление завершено");
    }
}
