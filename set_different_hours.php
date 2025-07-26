<?php

require_once 'vendor/autoload.php';

use App\Models\Clients\UserSchedule;
use App\Models\Admin\Project;

// Инициализируем Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Получаем project_id из аргументов командной строки
$projectId = $argv[1] ?? null;

if (!$projectId) {
    echo "❌ Укажите project_id как аргумент\n";
    echo "Пример: php set_different_hours.php 45\n";
    exit(1);
}

$project = Project::find($projectId);

if (!$project) {
    echo "❌ Проект с ID {$projectId} не найден\n";
    exit(1);
}

echo "📋 Проект: {$project->name}\n\n";

// Получаем всех мастеров проекта
$users = \App\Models\Admin\User::where('project_id', $projectId)->get();

if ($users->isEmpty()) {
    echo "❌ В проекте нет мастеров\n";
    exit(1);
}

echo "👥 Мастера в проекте:\n";
foreach ($users as $user) {
    echo "  • ID: {$user->id} - {$user->name}\n";
}

echo "\n🎯 Установка разных часов работы:\n";
echo "💡 Примеры расписаний:\n";
echo "  • Утренний мастер: 08:00 - 16:00\n";
echo "  • Дневной мастер: 10:00 - 18:00\n";
echo "  • Вечерний мастер: 14:00 - 22:00\n";
echo "  • Полный день: 09:00 - 19:00\n\n";

foreach ($users as $user) {
    echo "\n👤 {$user->name}:\n";
    
    // Получаем расписание мастера
    $schedules = UserSchedule::where('user_id', $user->id)->get();
    
    foreach ($schedules as $schedule) {
        if ($schedule->is_working) {
            echo "  • {$schedule->day_name}: ";
            echo "Текущее время: {$schedule->start_time_formatted} - {$schedule->end_time_formatted}\n";
            echo "    Введите новое время (формат: HH:MM-HH:MM, Enter для пропуска): ";
            
            $handle = fopen("php://stdin", "r");
            $timeInput = trim(fgets($handle));
            fclose($handle);
            
            if ($timeInput !== '') {
                $times = explode('-', $timeInput);
                if (count($times) === 2) {
                    $startTime = trim($times[0]);
                    $endTime = trim($times[1]);
                    
                    // Простая валидация формата времени
                    if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $startTime) && 
                        preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $endTime)) {
                        
                        $schedule->update([
                            'start_time' => $startTime,
                            'end_time' => $endTime
                        ]);
                        echo "    ✅ Установлено время: {$startTime} - {$endTime}\n";
                    } else {
                        echo "    ❌ Неверный формат времени (используйте HH:MM-HH:MM)\n";
                    }
                } else {
                    echo "    ❌ Неверный формат (используйте HH:MM-HH:MM)\n";
                }
            } else {
                echo "    ⏭️ Пропущено\n";
            }
        }
    }
}

echo "\n✅ Настройка часов работы завершена!\n";
echo "\n💡 Теперь каждый мастер может работать в любое удобное время.\n";
echo "   Общие часы салона используются только для информации.\n"; 