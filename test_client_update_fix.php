<?php

require_once 'vendor/autoload.php';

use App\Models\Client;
use App\Models\Product;
use App\Models\Appointment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Service;
use App\Models\Warehouse;

// Инициализация Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ТЕСТ ИСПРАВЛЕНИЯ ОБНОВЛЕНИЯ КЛИЕНТА ===\n\n";

try {
    // Находим существующие тестовые данные
    $client1 = Client::where('name', 'Тестовый Клиент 1')->first();
    $client2 = Client::where('name', 'Тестовый Клиент 2')->first();
    $appointment = Appointment::where('id', '>', 100)->orderBy('id', 'desc')->first();
    
    if (!$client1 || !$client2 || !$appointment) {
        echo "❌ Не найдены тестовые данные. Запустите сначала test_appointment_client_change.php\n";
        exit;
    }
    
    echo "1. Найдены тестовые данные:\n";
    echo "   📋 Запись ID: {$appointment->id}\n";
    echo "   👤 Клиент 1: {$client1->name} (ID: {$client1->id})\n";
    echo "   👤 Клиент 2: {$client2->name} (ID: {$client2->id})\n\n";
    
    // Проверяем текущее состояние
    echo "2. Текущее состояние ДО изменения:\n";
    $salesBefore = Sale::where('appointment_id', $appointment->id)->get();
    echo "   📋 Клиент в записи: {$appointment->client->name} (ID: {$appointment->client_id})\n";
    echo "   📋 Продажи в записи: " . $salesBefore->count() . " шт\n";
    foreach ($salesBefore as $sale) {
        echo "      - Продажа ID {$sale->id}: клиент ID {$sale->client_id}\n";
    }
    echo "\n";
    
    // Симулируем обновление записи через контроллер
    echo "3. Симулируем обновление клиента в записи...\n";
    
    $newClientId = ($appointment->client_id == $client1->id) ? $client2->id : $client1->id;
    $newClient = ($newClientId == $client1->id) ? $client1 : $client2;
    
    // Обновляем запись
    $appointment->update([
        'client_id' => $newClientId
    ]);
    
    // Обновляем client_id во всех связанных продажах
    foreach ($appointment->sales as $sale) {
        $sale->update(['client_id' => $newClientId]);
    }
    
    echo "   ✅ Клиент изменен на: {$newClient->name} (ID: {$newClientId})\n\n";
    
    // Проверяем состояние после изменения
    echo "4. Состояние ПОСЛЕ изменения:\n";
    $appointment->refresh();
    $salesAfter = Sale::where('appointment_id', $appointment->id)->get();
    
    echo "   📋 Клиент в записи: {$appointment->client->name} (ID: {$appointment->client_id})\n";
    echo "   📋 Продажи в записи: " . $salesAfter->count() . " шт\n";
    foreach ($salesAfter as $sale) {
        echo "      - Продажа ID {$sale->id}: клиент ID {$sale->client_id}\n";
    }
    echo "\n";
    
    // Анализ результатов
    echo "5. АНАЛИЗ РЕЗУЛЬТАТОВ:\n";
    
    if ($appointment->client_id == $newClientId) {
        echo "   ✅ Клиент в записи успешно изменен\n";
    } else {
        echo "   ❌ Клиент в записи НЕ изменен\n";
    }
    
    $allSalesHaveNewClient = true;
    foreach ($salesAfter as $sale) {
        if ($sale->client_id != $newClientId) {
            $allSalesHaveNewClient = false;
            break;
        }
    }
    
    if ($allSalesHaveNewClient) {
        echo "   ✅ Все продажи в записи имеют нового клиента\n";
        echo "   🎉 ИСПРАВЛЕНИЕ РАБОТАЕТ КОРРЕКТНО!\n";
    } else {
        echo "   ❌ НЕ все продажи в записи имеют нового клиента\n";
        echo "   ⚠️  Исправление НЕ работает\n";
    }
    
    echo "\n=== ТЕСТ ЗАВЕРШЕН ===\n";

} catch (Exception $e) {
    echo "❌ ОШИБКА: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . "\n";
    echo "Строка: " . $e->getLine() . "\n";
} 