<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Запускаю миграцию...\n";

try {
    $migrator = $app->make('Illuminate\Database\Migrations\Migrator');
    $migrator->run([database_path('migrations/2025_01_15_000000_create_subscriptions_table.php')]);
    echo "Миграция успешно выполнена!\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
