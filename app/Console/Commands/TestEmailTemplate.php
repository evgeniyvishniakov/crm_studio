<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailTemplateService;
use App\Models\Clients\Appointment;

class TestEmailTemplate extends Command
{
    protected $signature = 'email:test-template {type} {email}';
    protected $description = 'Test email template with placeholders';

    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->argument('email');
        
        $templateService = new EmailTemplateService();
        
        // Тестовые данные
        $testData = [
            'date' => '15.09.2025',
            'time' => '14:30',
            'client_name' => 'Анна Иванова',
            'service_name' => 'Стрижка и укладка',
            'master_name' => 'Мария Петрова',
            'project_name' => 'Салон красоты "Элегант"',
            'price' => '2500 ₽',
            'notes' => 'Принести фото желаемой стрижки',
            'phone' => '+7 (495) 123-45-67',
            'address' => 'ул. Тверская, 15',
            'working_hours' => '9:00 - 21:00',
        ];
        
        $this->info("Отправляем тестовое письмо типа '{$type}' на {$email}...");
        
        $result = $templateService->sendEmailWithTemplate($type, $testData, $email);
        
        if ($result) {
            $this->info("✅ Письмо успешно отправлено!");
        } else {
            $this->error("❌ Ошибка отправки письма. Проверьте настройки почты и существование шаблона.");
        }
    }
}
