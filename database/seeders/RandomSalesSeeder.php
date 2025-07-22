<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clients\Appointment;
use App\Models\Clients\Product;
use App\Models\Clients\Sale;
use App\Models\Clients\SaleItem;
use App\Models\Clients\Warehouse;
use Illuminate\Support\Facades\DB;

class RandomSalesSeeder extends Seeder
{
    public function run()
    {
        $projectId = 45; // ID проекта
        $targetSalesCount = 1300; // Целевое количество ШТУК товаров для продажи
        $yearStart = 2024; // Начальный год
        $yearEnd = 2025; // Конечный год

        echo "🚀 Начинаем генерацию случайных продаж для проекта {$projectId}\n";
        echo "📦 Целевое количество ШТУК для продажи: {$targetSalesCount}\n";
        echo "📅 Период: {$yearStart}-{$yearEnd}\n";

        try {
            DB::beginTransaction();

            // 1. Получаем все записи за период 2024-2025
            $appointments = Appointment::where('project_id', $projectId)
                ->where(function($query) use ($yearStart, $yearEnd) {
                    $query->whereYear('date', $yearStart)
                          ->orWhereYear('date', $yearEnd);
                })
                ->get();

            if ($appointments->isEmpty()) {
                echo "❌ Не найдены записи за период {$yearStart}-{$yearEnd} для проекта {$projectId}\n";
                return;
            }

            echo "📋 Найдено записей: " . $appointments->count() . "\n";

            // 2. Получаем все товары со склада (где есть количество)
            echo "🔍 Проверяем склад для проекта {$projectId}...\n";
            
            // Проверим общее количество записей на складе
            $totalWarehouse = Warehouse::where('project_id', $projectId)->count();
            echo "📊 Общее количество записей на складе: {$totalWarehouse}\n";
            
            $warehouseWithQuantity = Warehouse::where('project_id', $projectId)
                ->where('quantity', '>', 0)
                ->count();
            echo "📈 Записей с количеством > 0: {$warehouseWithQuantity}\n";

            $warehouseItems = Warehouse::where('project_id', $projectId)
                ->where('quantity', '>', 0)
                ->with('product')
                ->get();

            echo "🏪 Загружено записей склада с товарами: " . $warehouseItems->count() . "\n";

            $products = $warehouseItems->map(function($warehouseItem) {
                $product = $warehouseItem->product;
                if ($product) {
                    $product->warehouse = $warehouseItem; // Добавляем склад к товару
                    return $product;
                } else {
                    echo "⚠️ Товар не найден для склада ID: {$warehouseItem->id}, product_id: {$warehouseItem->product_id}\n";
                    return null;
                }
            })->filter(function($product) {
                return $product !== null; // Исключаем товары без данных
            });

            echo "✅ Итого товаров с корректными данными: " . $products->count() . "\n";

            if ($products->isEmpty()) {
                echo "❌ Нет товаров на складе для проекта {$projectId}\n";
                return;
            }

            // Подсчитаем общее количество штук на складе
            $totalQuantityAvailable = $products->sum(function($product) {
                return $product->warehouse->quantity;
            });

            echo "🏪 Найдено товаров: " . $products->count() . " видов\n";
            echo "📦 Общее количество штук: {$totalQuantityAvailable}\n";

            if ($totalQuantityAvailable < $targetSalesCount) {
                echo "❌ Недостаточно штук товаров на складе. Доступно: {$totalQuantityAvailable}, требуется: {$targetSalesCount}\n";
                return;
            }

            echo "✅ Товаров достаточно для продажи {$targetSalesCount} штук\n";

            // 4. Перемешиваем записи для случайного распределения
            $shuffledAppointments = $appointments->shuffle();

            // 5. Распределяем товары по записям
            $soldQuantity = 0; // Сколько уже продали штук
            $salesCount = 0;
            $itemsCount = 0;

            foreach ($shuffledAppointments as $appointment) {
                // Если уже продали достаточно, останавливаемся
                if ($soldQuantity >= $targetSalesCount) {
                    break;
                }

                // Не во все записи добавляем товары (40% записей получат товары)  
                if (rand(1, 100) > 40) {
                    continue;
                }

                // Рандомно определяем количество разных товаров в записи (1-3)
                $itemsInAppointment = rand(1, 3);
                
                $appointmentItems = [];
                $remainingToSell = $targetSalesCount - $soldQuantity;
                
                for ($i = 0; $i < $itemsInAppointment && $remainingToSell > 0; $i++) {
                    // Выбираем случайный товар из доступных
                    $product = $products->random();

                    // Рандомное количество товара (1-5 штук, но не больше чем осталось продать)
                    $maxQuantity = min($product->warehouse->quantity, 5, $remainingToSell);
                    if ($maxQuantity < 1) continue;
                    
                    $quantity = rand(1, $maxQuantity);
                    $remainingToSell -= $quantity;

                    $appointmentItems[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'retail_price' => $product->warehouse->retail_price ?: rand(50, 300),
                        'wholesale_price' => $product->warehouse->purchase_price ?: rand(20, 150),
                    ];
                }

                if (empty($appointmentItems)) continue;

                // Создаем продажу для записи
                $sale = Sale::create([
                    'appointment_id' => $appointment->id,
                    'client_id' => $appointment->client_id,
                    'employee_id' => $appointment->user_id,
                    'date' => $appointment->date,
                    'total_amount' => 0,
                    'project_id' => $projectId,
                    'notes' => 'Автоматически сгенерированная продажа'
                ]);

                $totalAmount = 0;

                foreach ($appointmentItems as $item) {
                    $product = $item['product'];
                    $quantity = $item['quantity'];
                    $retailPrice = $item['retail_price'];
                    $wholesalePrice = $item['wholesale_price'];
                    $itemTotal = $retailPrice * $quantity;

                    // Создаем позицию продажи
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'retail_price' => $retailPrice,
                        'wholesale_price' => $wholesalePrice,
                        'total' => $itemTotal,
                        'project_id' => $projectId,
                    ]);

                    // Списываем со склада используя метод из модели
                    Warehouse::decreaseQuantity($product->id, $quantity, $projectId);

                    $totalAmount += $itemTotal;
                    $itemsCount++;
                }

                // Обновляем общую сумму продажи
                $sale->update(['total_amount' => $totalAmount]);
                $salesCount++;

                // Добавляем количество к общему проданному
                $appointmentSoldQuantity = array_sum(array_column($appointmentItems, 'quantity'));
                $soldQuantity += $appointmentSoldQuantity;

                // Показываем прогресс каждые 50 продаж
                if ($salesCount % 50 === 0) {
                    echo "⏳ Создано продаж: {$salesCount}, продано штук: {$soldQuantity}/{$targetSalesCount}\n";
                }
            }

            DB::commit();

            echo "\n✅ Генерация завершена успешно!\n";
            echo "📊 Создано продаж: {$salesCount}\n";
            echo "📦 Продано штук товаров: {$soldQuantity} из цели {$targetSalesCount}\n";
            echo "📋 Добавлено позиций в продажи: {$itemsCount}\n";
            echo "🏪 Товары списаны со склада\n";
            echo "💰 Продажи связаны с записями клиентов\n";

        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Ошибка при генерации продаж: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        }
    }
} 