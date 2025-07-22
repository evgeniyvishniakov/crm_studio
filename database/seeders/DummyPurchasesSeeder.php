<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clients\Purchase;
use App\Models\Clients\PurchaseItem;
use App\Models\Clients\Product;
use App\Models\Clients\Supplier;
use App\Models\Clients\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummyPurchasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projectId = 45;
        $this->command->info("Starting a full data reset for project {$projectId}...");

        DB::beginTransaction();
        try {
            // 1. Полностью очищаем склад для проекта 45
            Warehouse::where('project_id', $projectId)->delete();
            $this->command->info('Warehouse for project 45 has been cleared.');

            // 2. Удаляем все закупки и их позиции для проекта 45
            PurchaseItem::where('project_id', $projectId)->delete();
            Purchase::where('project_id', $projectId)->delete();
            $this->command->info('All previous purchases for project 45 have been deleted.');
            
            DB::commit();
            $this->command->info('Cleanup complete. Starting fresh seeding.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('An error occurred during cleanup: ' . $e->getMessage());
            return;
        }

        $this->command->info("Seeding new purchases for project {$projectId}...");
        
        $startDate = Carbon::create(2024, 8, 20);
        $endDate = Carbon::create(2025, 8, 20);

        $productIds = Product::where('project_id', $projectId)->pluck('id')->toArray();
        $supplierIds = Supplier::where('project_id', $projectId)->pluck('id')->toArray();

        if (empty($productIds)) {
            $this->command->error("No products found for project ID {$projectId}. Please seed products first.");
            return;
        }

        if (empty($supplierIds)) {
            $this->command->error("No suppliers found for project ID {$projectId}. Please seed suppliers first.");
            return;
        }

        $currentDate = $startDate->copy();

        DB::beginTransaction();
        try {
            while ($currentDate->lessThanOrEqualTo($endDate)) {
                $purchasesPerMonth = rand(3, 4);

                for ($i = 0; $i < $purchasesPerMonth; $i++) {
                    $purchaseDate = $currentDate->copy()->addDays(rand(0, $currentDate->daysInMonth - 1));

                    $purchase = Purchase::create([
                        'project_id' => $projectId,
                        'supplier_id' => $supplierIds[array_rand($supplierIds)],
                        'date' => $purchaseDate,
                        'notes' => 'Автоматически сгенерированная закупка',
                        'total_amount' => 0,
                    ]);

                    $totalAmount = 0;
                    $itemsPerPurchase = rand(20, 30);
                    $usedProductIds = [];

                    for ($j = 0; $j < $itemsPerPurchase; $j++) {
                        $productId = $productIds[array_rand($productIds)];
                        while (in_array($productId, $usedProductIds)) {
                            $productId = $productIds[array_rand($productIds)];
                        }
                        $usedProductIds[] = $productId;

                        $product = Product::find($productId);

                        $purchasePrice = $product->purchase_price > 0 ? $product->purchase_price : rand(100, 1000) / 10;
                        $retailPrice = $product->retail_price > 0 ? $product->retail_price : $purchasePrice * 1.5;
                        $quantity = rand(1, 3); // Количество 1-3
                        $total = $purchasePrice * $quantity;

                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'project_id' => $projectId,
                            'product_id' => $productId,
                            'purchase_price' => $purchasePrice,
                            'retail_price' => $retailPrice,
                            'quantity' => $quantity,
                            'total' => $total,
                        ]);

                        Warehouse::increaseQuantity($productId, $quantity, $projectId);
                        Product::where('id', $productId)->update([
                            'purchase_price' => $purchasePrice,
                            'retail_price' => $retailPrice,
                        ]);

                        $totalAmount += $total;
                    }

                    $purchase->update(['total_amount' => $totalAmount]);
                }

                $this->command->info("Seeded {$purchasesPerMonth} purchases for " . $currentDate->format('F Y'));
                $currentDate->addMonth();
            }

            DB::commit();
            $this->command->info('Dummy purchases seeding completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('An error occurred during seeding: ' . $e->getMessage());
        }
    }
}
