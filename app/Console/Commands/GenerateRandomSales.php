<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clients\Appointment;
use App\Models\Clients\Product;
use App\Models\Clients\Sale;
use App\Models\Clients\SaleItem;
use App\Models\Clients\Warehouse;
use Illuminate\Support\Facades\DB;

class GenerateRandomSales extends Command
{
    protected $signature = 'sales:generate-random 
                          {--project_id=1 : ID проекта} 
                          {--products=1300 : Количество товаров для распределения}
                          {--year=2024 : Год для распределения продаж}';
    
    protected $description = 'Генерирует случайные продажи товаров через записи за указанный год';

    public function handle()
    {
        $projectId = $this->option('project_id');
        $productsCount = $this->option('products');
        $year = $this->option('year');

        $this->info("Начинаем генерацию случайных продаж для проекта {$projectId}");
        $this->info("Количество товаров: {$productsCount}");
        $this->info("Год: {$year}");

        try {
            DB::beginTransaction();

            // 1. Получаем все записи за год
            $appointments = Appointment::where('project_id', $projectId)
                ->whereYear('date', $year)
                ->get();

            if ($appointments->isEmpty()) {
                $this->error("Не найдены записи за {$year} год для проекта {$projectId}");
                return;
            }

            $this->info("Найдено записей: " . $appointments->count());

            // 2. Получаем все товары со склада (где есть количество)
            $products = Product::where('project_id', $projectId)
                ->whereHas('warehouse', function($query) {
                    $query->where('quantity', '>', 0);
                })
                ->with('warehouse')
                ->get();

            if ($products->count() < $productsCount) {
                $this->error("Недостаточно товаров на складе. Доступно: " . $products->count() . ", требуется: {$productsCount}");
                return;
            }

            $this->info("Найдено товаров на складе: " . $products->count());

            // 3. Рандомно выбираем товары для продажи
            $selectedProducts = $products->random($productsCount);
            $this->info("Выбрано товаров для продажи: " . $selectedProducts->count());

            // 4. Перемешиваем записи для случайного распределения
            $shuffledAppointments = $appointments->shuffle();

            // 5. Распределяем товары по записям
            $productIndex = 0;
            $salesCount = 0;
            $itemsCount = 0;

            foreach ($shuffledAppointments as $appointment) {
                // Не во все записи добавляем товары (30-50% записей получат товары)
                if (rand(1, 100) > 40) {
                    continue;
                }

                // Рандомно определяем количество товаров в записи (1-2)
                $itemsInAppointment = rand(1, 2);
                
                $appointmentItems = [];
                
                for ($i = 0; $i < $itemsInAppointment && $productIndex < $selectedProducts->count(); $i++) {
                    $product = $selectedProducts[$productIndex];
                    $productIndex++;

                    // Рандомное количество товара (1-3 штуки)
                    $maxQuantity = min($product->warehouse->quantity, 3);
                    if ($maxQuantity < 1) continue;
                    
                    $quantity = rand(1, $maxQuantity);

                    $appointmentItems[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'retail_price' => $product->warehouse->retail_price ?: 100,
                        'wholesale_price' => $product->warehouse->purchase_price ?: 50,
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
                    'notes' => 'Сгенерировано автоматически'
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

                    // Списываем со склада
                    Warehouse::where('product_id', $product->id)
                        ->where('project_id', $projectId)
                        ->decrement('quantity', $quantity);

                    $totalAmount += $itemTotal;
                    $itemsCount++;
                }

                // Обновляем общую сумму продажи
                $sale->update(['total_amount' => $totalAmount]);
                $salesCount++;

                if ($productIndex >= $selectedProducts->count()) {
                    break;
                }
            }

            DB::commit();

            $this->info("✅ Генерация завершена успешно!");
            $this->info("📊 Создано продаж: {$salesCount}");
            $this->info("📦 Добавлено позиций товаров: {$itemsCount}");
            $this->info("🏪 Товары списаны со склада");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Ошибка при генерации продаж: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
} 