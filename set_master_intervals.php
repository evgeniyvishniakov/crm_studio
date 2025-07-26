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
    echo "Пример: php set_master_intervals.php 45\n";
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

echo "\n🎯 Установка индивидуальных интервалов:\n";

foreach ($users as $user) {
    echo "\n👤 {$user->name}:\n";
    
    // Получаем расписание мастера
    $schedules = UserSchedule::where('user_id', $user->id)->get();
    
    foreach ($schedules as $schedule) {
        if ($schedule->is_working) {
            echo "  • {$schedule->day_name}: ";
            
            // Запрашиваем интервал для каждого рабочего дня
            echo "Текущий интервал: " . ($schedule->booking_interval ?: "общий") . " мин\n";
            echo "    Введите новый интервал (15-120 мин, Enter для пропуска): ";
            
            $handle = fopen("php://stdin", "r");
            $interval = trim(fgets($handle));
            fclose($handle);
            
            if ($interval !== '') {
                $interval = (int) $interval;
                if ($interval >= 15 && $interval <= 120) {
                    $schedule->update(['booking_interval' => $interval]);
                    echo "    ✅ Установлен интервал: {$interval} мин\n";
                } else {
                    echo "    ❌ Неверный интервал (должен быть 15-120 мин)\n";
                }
            } else {
                echo "    ⏭️ Пропущено\n";
            }
        }
    }
}

echo "\n✅ Настройка интервалов завершена!\n";
echo "\n💡 Теперь каждый мастер может иметь свой интервал записи.\n";
echo "   Если интервал не задан, будет использоваться общий интервал из настроек проекта.\n"; 