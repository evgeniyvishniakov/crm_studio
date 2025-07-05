<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Warehouse;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'items.product'])->latest()->get();
        $products = Product::all();
        $suppliers = Supplier::orderBy('name')->get();
        return view('client.purchases.index', compact('purchases', 'products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.purchase_price' => 'required|numeric|min:0.01',
            'items.*.retail_price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'],
                'notes' => $validated['notes'],
                'total_amount' => 0
            ]);

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $itemTotal = $item['purchase_price'] * $item['quantity'];
                $totalAmount += $itemTotal;

                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'purchase_price' => $item['purchase_price'],
                    'retail_price' => $item['retail_price'],
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal
                ]);

                // Обновляем склад через модельный метод
                \App\Models\Warehouse::increaseQuantity($item['product_id'], $item['quantity']);

                // Обновляем цены в Product
                Product::where('id', $item['product_id'])
                    ->update([
                        'purchase_price' => $item['purchase_price'],
                        'retail_price' => $item['retail_price'],
                    ]);
            }

            // Обновляем общую сумму закупки
            $purchase->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'purchase' => $purchase->load(['supplier', 'items.product'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании закупки: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Purchase $purchase)
    {
        try {
            // Загружаем связанные товары, чтобы получить их имена
            $purchase->load('items.product');

            return response()->json([
                'success' => true,
                'purchase' => [
                    'id' => $purchase->id,
                    'date' => $purchase->date->format('Y-m-d'),
                    'supplier_id' => $purchase->supplier_id,
                    'notes' => $purchase->notes,
                    'items' => $purchase->items->map(function($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name,
                            'purchase_price' => $item->purchase_price,
                            'retail_price' => $item->retail_price,
                            'quantity' => $item->quantity
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки данных закупки'
            ], 500);
        }
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.purchase_price' => 'required|numeric|min:0.01',
            'items.*.retail_price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            // 1. Отменяем старое состояние на складе
            foreach ($purchase->items as $item) {
                \App\Models\Warehouse::decreaseQuantity($item->product_id, $item->quantity);
            }

            // 2. Обновляем данные самой закупки и удаляем старые товары
            $purchase->update([
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'],
                'notes' => $validated['notes']
            ]);
            $purchase->items()->delete();

            $totalAmount = 0;

            // 3. Добавляем новые товары и обновляем склад
            foreach ($validated['items'] as $itemData) {
                $itemTotal = $itemData['purchase_price'] * $itemData['quantity'];
                $totalAmount += $itemTotal;

                // Создаем новую запись о товаре в закупке
                $purchase->items()->create([
                    'product_id' => $itemData['product_id'],
                    'purchase_price' => $itemData['purchase_price'],
                    'retail_price' => $itemData['retail_price'],
                    'quantity' => $itemData['quantity'],
                    'total' => $itemTotal
                ]);

                // Обновляем склад через модельный метод
                \App\Models\Warehouse::increaseQuantity($itemData['product_id'], $itemData['quantity']);

                // Обновляем цены в Product
                Product::where('id', $itemData['product_id'])
                    ->update([
                        'purchase_price' => $itemData['purchase_price'],
                        'retail_price' => $itemData['retail_price'],
                    ]);
            }

            // 4. Обновляем общую сумму закупки
            $purchase->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'purchase' => $purchase->fresh()->load(['supplier', 'items.product'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении закупки: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();

        try {
            // Возвращаем товары на склад (уменьшаем количество)
            foreach ($purchase->items as $item) {
                \App\Models\Warehouse::decreaseQuantity($item->product_id, $item->quantity);
            }

            // Удаляем закупку
            $purchase->items()->delete();
            $purchase->delete();

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении закупки',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
