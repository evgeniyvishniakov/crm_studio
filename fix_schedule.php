<?php

require_once 'vendor/autoload.php';

// Загружаем Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Clients\UserSchedule;
use App\Models\Admin\User;

echo "=== ИСПРАВЛЕНИЕ РАСПИСАНИЯ МАСТЕРОВ ===\n\n";

// Находим мастера Наташу
$natasha = User::find(54);
if (!$natasha) {
    echo "❌ Мастер Наташа не найден!\n";
    exit;
}

echo "Найден мастер: {$natasha->name} (ID: {$natasha->id})\n";

// Проверяем существующее расписание
$existingSchedules = UserSchedule::where('user_id', $natasha->id)->get();
echo "Существующих записей расписания: " . $existingSchedules->count() . "\n";

if ($existingSchedules->count() > 0) {
    echo "Удаляем существующие записи...\n";
    UserSchedule::where('user_id', $natasha->id)->delete();
}

// Создаем расписание для рабочей недели (понедельник - пятница)
$workingDays = [
    1 => 'Понедельник',
    2 => 'Вторник', 
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница'
];

echo "\nСоздаем расписание для рабочей недели:\n";

foreach ($workingDays as $dayOfWeek => $dayName) {
    $schedule = UserSchedule::create([
        'user_id' => $natasha->id,
        'day_of_week' => $dayOfWeek,
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
        'is_working' => true,
        'notes' => null
    ]);
    
    echo "✅ {$dayName} (день {$dayOfWeek}): 09:00 - 18:00\n";
}

echo "\n=== ПРОВЕРКА РЕЗУЛЬТАТА ===\n";

$newSchedules = UserSchedule::where('user_id', $natasha->id)->get();
echo "Новых записей расписания: " . $newSchedules->count() . "\n";

foreach ($newSchedules as $schedule) {
    $dayNames = [0=>'Вс',1=>'Пн',2=>'Вт',3=>'Ср',4=>'Чт',5=>'Пт',6=>'Сб'];
    $dayName = $dayNames[$schedule->day_of_week] ?? '??';
    echo "- {$dayName}: " . ($schedule->is_working ? 'Работает' : 'Выходной') . 
         " ({$schedule->start_time} - {$schedule->end_time})\n";
}

echo "\n✅ Расписание для мастера Наташа создано!\n";
echo "Теперь публичное бронирование должно работать корректно.\n"; 