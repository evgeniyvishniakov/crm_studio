<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\Admin\User;
use App\Models\Admin\Project;

class TestNotificationHighlight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification-highlight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирует систему подсветки уведомлений';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Тестируем систему подсветки уведомлений...');
        
        // Проверяем структуру таблицы уведомлений
        $this->info('📋 Проверяем структуру таблицы notifications...');
        
        try {
            $notifications = Notification::all();
            $this->info("✅ Найдено уведомлений: " . $notifications->count());
            
            if ($notifications->count() > 0) {
                $this->info('📊 Последние уведомления:');
                $table = [];
                
                foreach ($notifications->take(5) as $notification) {
                    $table[] = [
                        'ID' => $notification->id,
                        'Тип' => $notification->type,
                        'Заголовок' => $notification->title,
                        'URL' => $notification->url,
                        'Appointment ID' => $notification->appointment_id ?? 'NULL',
                        'Проект ID' => $notification->project_id,
                        'Пользователь ID' => $notification->user_id,
                        'Прочитано' => $notification->is_read ? 'Да' : 'Нет',
                        'Создано' => $notification->created_at->format('d.m.Y H:i:s')
                    ];
                }
                
                $this->table([
                    'ID', 'Тип', 'Заголовок', 'URL', 'Appointment ID', 
                    'Проект ID', 'Пользователь ID', 'Прочитано', 'Создано'
                ], $table);
            }
            
            // Проверяем уведомления о веб-записях
            $webBookingNotifications = Notification::where('type', 'web_booking')->get();
            $this->info("🔍 Уведомлений о веб-записях: " . $webBookingNotifications->count());
            
            if ($webBookingNotifications->count() > 0) {
                $this->info('📋 Уведомления о веб-записях:');
                foreach ($webBookingNotifications as $notification) {
                    $this->line("  - ID: {$notification->id}, Appointment ID: " . 
                               ($notification->appointment_id ?? 'NULL') . 
                               ", URL: {$notification->url}");
                }
            }
            
            // Проверяем, есть ли поле appointment_id в таблице
            $this->info('🔍 Проверяем наличие поля appointment_id...');
            $columns = \Schema::getColumnListing('notifications');
            if (in_array('appointment_id', $columns)) {
                $this->info('✅ Поле appointment_id существует в таблице');
            } else {
                $this->error('❌ Поле appointment_id НЕ существует в таблице!');
                $this->warn('⚠️ Возможно, миграция не была выполнена');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Ошибка при проверке: ' . $e->getMessage());
        }
        
        $this->info('🏁 Тест завершен');
    }
}
