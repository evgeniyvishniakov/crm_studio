<?php

require_once 'vendor/autoload.php';

// Загружаем Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Clients\UserSchedule;
use App\Models\Admin\User;
use Illuminate\Support\Facades\DB;

// Получаем ID проекта из аргументов командной строки
$projectId = $argv[1] ?? null;

if (!$projectId) {
    echo "❌ ОШИБКА: Не указан ID проекта!\n";
    echo "Использование: php fix_schedule_by_project.php [ID_ПРОЕКТА]\n";
    echo "Пример: php fix_schedule_by_project.php 45\n";
    exit;
}

echo "=== ИСПРАВЛЕНИЕ РАСПИСАНИЯ МАСТЕРОВ ПРОЕКТА {$projectId} ===\n\n";

// Проверяем существование проекта
$project = \App\Models\Admin\Project::find($projectId);
if (!$project) {
    echo "❌ ОШИБКА: Проект с ID {$projectId} не найден!\n";
    exit;
}

echo "Проект: {$project->project_name} (ID: {$project->id})\n\n";

// Находим всех мастеров указанного проекта
$users = User::where('project_id', $projectId)->get();
echo "Мастеров в проекте {$projectId}: " . $users->count() . "\n";

if ($users->count() == 0) {
    echo "❌ В проекте нет мастеров!\n";
    exit;
}

foreach($users as $user) {
    echo "- {$user->name} (ID: {$user->id})\n";
}

echo "\n=== ПРОВЕРЯЕМ ТЕКУЩЕЕ РАСПИСАНИЕ ===\n";

foreach($users as $user) {
    echo "\n--- Мастер: {$user->name} (ID: {$user->id}) ---\n";
    
    $schedules = UserSchedule::where('user_id', $user->id)->get();
    echo "Записей расписания: " . $schedules->count() . "\n";
    
    if ($schedules->count() == 0) {
        echo "❌ НЕТ РАСПИСАНИЯ!\n";
    } else {
        foreach ($schedules as $schedule) {
            $dayNames = [0=>'Вс',1=>'Пн',2=>'Вт',3=>'Ср',4=>'Чт',5=>'Пт',6=>'Сб'];
            $dayName = $dayNames[$schedule->day_of_week] ?? '??';
            $status = $schedule->is_working ? 'Работает' : 'Выходной';
            echo "  {$dayName} (день {$schedule->day_of_week}): {$status} ({$schedule->start_time} - {$schedule->end_time})\n";
        }
    }
}

echo "\n=== ИСПРАВЛЯЕМ РАСПИСАНИЕ ===\n";

// Определяем рабочие дни (понедельник - пятница)
$workingDays = [1, 2, 3, 4, 5]; // Пн, Вт, Ср, Чт, Пт

$totalUpdated = 0;

foreach($users as $user) {
    echo "\n--- Исправляем расписание мастера: {$user->name} ---\n";
    
    $schedules = UserSchedule::where('user_id', $user->id)->get();
    
    if ($schedules->count() == 0) {
        echo "❌ Нет расписания для исправления!\n";
        continue;
    }
    
    $updatedCount = 0;
    foreach ($schedules as $schedule) {
        $dayNames = [0=>'Вс',1=>'Пн',2=>'Вт',3=>'Ср',4=>'Чт',5=>'Пт',6=>'Сб'];
        $dayName = $dayNames[$schedule->day_of_week] ?? '??';
        
        // Проверяем, является ли день рабочим
        $shouldBeWorking = in_array($schedule->day_of_week, $workingDays);
        
        if ($shouldBeWorking && !$schedule->is_working) {
            // Обновляем статус на рабочий
            $schedule->update([
                'is_working' => true,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00'
            ]);
            
            echo "✅ {$dayName} (день {$schedule->day_of_week}): ИЗМЕНЕНО на РАБОЧИЙ (09:00 - 18:00)\n";
            $updatedCount++;
            $totalUpdated++;
        } elseif (!$shouldBeWorking && $schedule->is_working) {
            // Обновляем статус на выходной
            $schedule->update([
                'is_working' => false
            ]);
            
            echo "✅ {$dayName} (день {$schedule->day_of_week}): ИЗМЕНЕНО на ВЫХОДНОЙ\n";
            $updatedCount++;
            $totalUpdated++;
        } else {
            echo "ℹ️  {$dayName} (день {$schedule->day_of_week}): БЕЗ ИЗМЕНЕНИЙ\n";
        }
    }
    
    echo "Обновлено записей: {$updatedCount}\n";
}

echo "\n=== ПРОВЕРКА РЕЗУЛЬТАТА ===\n";

foreach($users as $user) {
    echo "\n--- Мастер: {$user->name} (ID: {$user->id}) ---\n";
    
    $schedules = UserSchedule::where('user_id', $user->id)->get();
    echo "Записей расписания: " . $schedules->count() . "\n";
    
    foreach ($schedules as $schedule) {
        $dayNames = [0=>'Вс',1=>'Пн',2=>'Вт',3=>'Ср',4=>'Чт',5=>'Пт',6=>'Сб'];
        $dayName = $dayNames[$schedule->day_of_week] ?? '??';
        $status = $schedule->is_working ? 'Работает' : 'Выходной';
        $timeInfo = $schedule->is_working ? " ({$schedule->start_time} - {$schedule->end_time})" : "";
        echo "  {$dayName} (день {$schedule->day_of_week}): {$status}{$timeInfo}\n";
    }
}

echo "\n✅ РАБОТА ЗАВЕРШЕНА!\n";
echo "Всего обновлено записей: {$totalUpdated}\n";
echo "Расписание мастеров проекта {$projectId} исправлено!\n";
echo "Теперь публичное бронирование должно работать корректно.\n";
echo "Мастера работают с понедельника по пятницу с 09:00 до 18:00.\n"; 