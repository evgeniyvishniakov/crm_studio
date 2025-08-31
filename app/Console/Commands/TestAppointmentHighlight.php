<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clients\Appointment;
use App\Models\Notification;

class TestAppointmentHighlight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:appointment-highlight {appointment_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирует подсветку конкретной записи';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appointmentId = $this->argument('appointment_id');
        
        $this->info("🧪 Тестируем подсветку записи ID: {$appointmentId}");
        
        // Проверяем, существует ли запись
        $appointment = Appointment::find($appointmentId);
        
        if (!$appointment) {
            $this->error("❌ Запись с ID {$appointmentId} не найдена");
            return;
        }
        
        $this->info("✅ Запись найдена:");
        $this->table(['Поле', 'Значение'], [
            ['ID', $appointment->id],
            ['Клиент', $appointment->client->name ?? 'N/A'],
            ['Услуга', $appointment->service->name ?? 'N/A'],
            ['Дата', $appointment->date],
            ['Время', $appointment->time],
            ['Статус', $appointment->status],
            ['Проект ID', $appointment->project_id],
            ['Создано', $appointment->created_at->format('d.m.Y H:i:s')]
        ]);
        
        // Проверяем, есть ли уведомления для этой записи
        $notifications = Notification::where('appointment_id', $appointmentId)->get();
        
        $this->info("🔔 Уведомления для записи {$appointmentId}: " . $notifications->count());
        
        if ($notifications->count() > 0) {
            foreach ($notifications as $notification) {
                $this->line("  - Уведомление ID: {$notification->id}, Тип: {$notification->type}");
            }
        }
        
        // Проверяем, есть ли уведомления о веб-записях для этой записи
        $webBookingNotifications = Notification::where('appointment_id', $appointmentId)
            ->where('type', 'web_booking')
            ->get();
            
        $this->info("🌐 Уведомления о веб-записях для записи {$appointmentId}: " . $webBookingNotifications->count());
        
        if ($webBookingNotifications->count() > 0) {
            foreach ($webBookingNotifications as $notification) {
                $this->line("  - Уведомление ID: {$notification->id}, URL: {$notification->url}");
                
                // Формируем URL для тестирования
                $url = $notification->url;
                $separator = strpos($url, '?') !== false ? '&' : '?';
                $testUrl = $url . $separator . 'highlight_appointment=' . $appointmentId;
                
                $this->line("  - Тестовый URL: {$testUrl}");
            }
        }
        
        $this->info('🏁 Тест завершен');
    }
}
