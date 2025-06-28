<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Client;
use App\Models\ClientType;

echo "=== Проверка данных клиентов ===\n";
echo "Всего клиентов: " . Client::count() . "\n";
echo "Клиентов с типом: " . Client::whereNotNull('client_type_id')->count() . "\n";
echo "Клиентов без типа: " . Client::whereNull('client_type_id')->count() . "\n\n";

echo "=== Типы клиентов ===\n";
$types = ClientType::all();
foreach ($types as $type) {
    echo "ID: {$type->id}, Название: {$type->name}, Цвет: {$type->color}\n";
}

echo "\n=== Клиенты с типами ===\n";
$clients = Client::with('clientType')->get();
foreach ($clients as $client) {
    $typeName = $client->clientType ? $client->clientType->name : 'NULL';
    echo "ID: {$client->id}, Имя: {$client->name}, Тип: {$typeName}\n";
} 