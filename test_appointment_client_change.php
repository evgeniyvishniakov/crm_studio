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

echo "=== ТЕСТ ИЗМЕНЕНИЯ КЛИЕНТА В ЗАПИСИ ===\n\n";

try {
    // 1. Создаем тестовых клиентов
    echo "1. Создаем тестовых клиентов...\n";
    
    $client1 = Client::create([
        'name' => 'Тестовый Клиент 1',
        'phone' => '+380991234567',
        'instagram' => 'test_client_1',
        'client_type_id' => 1
    ]);
    
    $client2 = Client::create([
        'name' => 'Тестовый Клиент 2', 
        'phone' => '+380992345678',
        'instagram' => 'test_client_2',
        'client_type_id' => 1
    ]);
    
    echo "   ✅ Клиент 1 создан: {$client1->name} (ID: {$client1->id})\n";
    echo "   ✅ Клиент 2 создан: {$client2->name} (ID: {$client2->id})\n\n";

    // 2. Создаем тестовые товары
    echo "2. Создаем тестовые товары...\n";
    
    $product1 = Product::create([
        'name' => 'Тестовый Товар 1',
        'description' => 'Описание тестового товара 1',
        'price' => 100.00,
        'purchase_price' => 80.00,
        'category_id' => 1,
        'brand_id' => 1
    ]);
    
    $product2 = Product::create([
        'name' => 'Тестовый Товар 2',
        'description' => 'Описание тестового товара 2', 
        'price' => 150.00,
        'purchase_price' => 120.00,
        'category_id' => 1,
        'brand_id' => 1
    ]);
    
    echo "   ✅ Товар 1 создан: {$product1->name} (ID: {$product1->id})\n";
    echo "   ✅ Товар 2 создан: {$product2->name} (ID: {$product2->id})\n\n";

    // 3. Добавляем товары на склад
    echo "3. Добавляем товары на склад...\n";
    
    $warehouse1 = Warehouse::create([
        'product_id' => $product1->id,
        'quantity' => 10,
        'retail_price' => 100.00,
        'wholesale_price' => 80.00,
        'purchase_price' => 80.00
    ]);
    
    $warehouse2 = Warehouse::create([
        'product_id' => $product2->id,
        'quantity' => 15,
        'retail_price' => 150.00,
        'wholesale_price' => 120.00,
        'purchase_price' => 120.00
    ]);
    
    echo "   ✅ Товар 1 добавлен на склад: {$warehouse1->quantity} шт\n";
    echo "   ✅ Товар 2 добавлен на склад: {$warehouse2->quantity} шт\n\n";

    // 4. Создаем тестовую услугу
    echo "4. Создаем тестовую услугу...\n";
    
    $service = Service::where('name', 'Тестовая Услуга')->first();
    if (!$service) {
        $service = Service::create([
            'name' => 'Тестовая Услуга',
            'description' => 'Описание тестовой услуги',
            'price' => 200.00
        ]);
        echo "   ✅ Услуга создана: {$service->name} (ID: {$service->id})\n\n";
    } else {
        echo "   ⚠️  Услуга уже существует: {$service->name} (ID: {$service->id})\n\n";
    }

    // 5. Создаем запись с первым клиентом
    echo "5. Создаем запись с первым клиентом...\n";
    
    $appointment = Appointment::create([
        'client_id' => $client1->id,
        'service_id' => $service->id,
        'date' => now()->format('Y-m-d'),
        'time' => '14:00:00',
        'price' => 200.00,
        'status' => 'completed'
    ]);
    
    echo "   ✅ Запись создана: ID {$appointment->id}, клиент: {$client1->name}\n\n";

    // 6. Создаем продажи (товары в записи)
    echo "6. Добавляем товары в запись (создаем продажи)...\n";
    
    $sale1 = Sale::create([
        'appointment_id' => $appointment->id,
        'client_id' => $client1->id,
        'date' => now()->format('Y-m-d'),
        'total_amount' => 100.00
    ]);
    
    $sale2 = Sale::create([
        'appointment_id' => $appointment->id,
        'client_id' => $client1->id,
        'date' => now()->format('Y-m-d'),
        'total_amount' => 150.00
    ]);
    
    echo "   ✅ Продажа 1 создана: ID {$sale1->id}, клиент: {$client1->name}\n";
    echo "   ✅ Продажа 2 создана: ID {$sale2->id}, клиент: {$client1->name}\n\n";

    // 7. Создаем элементы продаж
    echo "7. Создаем элементы продаж...\n";
    
    $saleItem1 = SaleItem::create([
        'sale_id' => $sale1->id,
        'product_id' => $product1->id,
        'quantity' => 1,
        'price' => 100.00,
        'purchase_price' => 80.00,
        'wholesale_price' => 80.00,
        'retail_price' => 100.00,
        'total' => 100.00
    ]);
    
    $saleItem2 = SaleItem::create([
        'sale_id' => $sale2->id,
        'product_id' => $product2->id,
        'quantity' => 1,
        'price' => 150.00,
        'purchase_price' => 120.00,
        'wholesale_price' => 120.00,
        'retail_price' => 150.00,
        'total' => 150.00
    ]);
    
    echo "   ✅ Элемент продажи 1 создан: товар {$product1->name}\n";
    echo "   ✅ Элемент продажи 2 создан: товар {$product2->name}\n\n";

    // 8. Проверяем состояние до изменения
    echo "8. Проверяем состояние ДО изменения клиента...\n";
    
    $appointmentBefore = Appointment::with(['client', 'sales'])->find($appointment->id);
    $salesBefore = Sale::where('appointment_id', $appointment->id)->get();
    
    echo "   📋 Запись: ID {$appointmentBefore->id}, клиент: {$appointmentBefore->client->name}\n";
    echo "   📋 Продажи в записи: " . $salesBefore->count() . " шт\n";
    foreach ($salesBefore as $sale) {
        echo "      - Продажа ID {$sale->id}: клиент ID {$sale->client_id}\n";
    }
    echo "\n";

    // 9. Изменяем клиента в записи
    echo "9. Изменяем клиента в записи...\n";
    
    $appointment->update([
        'client_id' => $client2->id
    ]);
    
    echo "   ✅ Клиент в записи изменен с {$client1->name} на {$client2->name}\n\n";

    // 10. Проверяем состояние после изменения
    echo "10. Проверяем состояние ПОСЛЕ изменения клиента...\n";
    
    $appointmentAfter = Appointment::with(['client', 'sales'])->find($appointment->id);
    $salesAfter = Sale::where('appointment_id', $appointment->id)->get();
    
    echo "   📋 Запись: ID {$appointmentAfter->id}, клиент: {$appointmentAfter->client->name}\n";
    echo "   📋 Продажи в записи: " . $salesAfter->count() . " шт\n";
    foreach ($salesAfter as $sale) {
        echo "      - Продажа ID {$sale->id}: клиент ID {$sale->client_id}\n";
    }
    echo "\n";

    // 11. Анализ результатов
    echo "11. АНАЛИЗ РЕЗУЛЬТАТОВ:\n";
    
    if ($appointmentAfter->client_id == $client2->id) {
        echo "   ✅ Клиент в записи успешно изменен\n";
    } else {
        echo "   ❌ Клиент в записи НЕ изменен\n";
    }
    
    $allSalesHaveNewClient = true;
    foreach ($salesAfter as $sale) {
        if ($sale->client_id != $client2->id) {
            $allSalesHaveNewClient = false;
            break;
        }
    }
    
    if ($allSalesHaveNewClient) {
        echo "   ✅ Все продажи в записи имеют нового клиента\n";
    } else {
        echo "   ❌ НЕ все продажи в записи имеют нового клиента\n";
        echo "   ⚠️  Это означает, что логика изменения клиента в продажах НЕ работает\n";
    }
    
    echo "\n";

    // 12. Очистка тестовых данных
    echo "12. Очищаем тестовые данные...\n";
    
    SaleItem::whereIn('sale_id', [$sale1->id, $sale2->id])->delete();
    Sale::whereIn('id', [$sale1->id, $sale2->id])->delete();
    Appointment::where('id', $appointment->id)->delete();
    Service::where('id', $service->id)->delete();
    Warehouse::whereIn('id', [$warehouse1->id, $warehouse2->id])->delete();
    Product::whereIn('id', [$product1->id, $product2->id])->delete();
    Client::whereIn('id', [$client1->id, $client2->id])->delete();
    
    echo "   ✅ Тестовые данные удалены\n\n";

    echo "=== ТЕСТ ЗАВЕРШЕН ===\n";

} catch (Exception $e) {
    echo "❌ ОШИБКА: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . "\n";
    echo "Строка: " . $e->getLine() . "\n";
} 