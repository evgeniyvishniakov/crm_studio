<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ClientType;

echo "=== Обновление цветов типов клиентов ===\n";

$types = [
    1 => ['name' => 'Постоянный клиент', 'color' => '#10b981'], // Зеленый
    2 => ['name' => 'Новый клиент', 'color' => '#3b82f6'], // Синий
    3 => ['name' => 'Vip клиент', 'color' => '#f59e0b'], // Оранжевый
    4 => ['name' => 'Корпоративный клиент', 'color' => '#8b5cf6'] // Фиолетовый
];

foreach ($types as $id => $data) {
    $type = ClientType::find($id);
    if ($type) {
        $type->update(['color' => $data['color']]);
        echo "Обновлен тип: {$type->name} - цвет: {$data['color']}\n";
    }
}

echo "Обновление завершено!\n"; 