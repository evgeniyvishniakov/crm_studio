<?php

namespace App\Http\Controllers;

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
        $purchases = Purchase::with(['supplier', 'items.product'])->get();
        $products = Product::all();
        $suppliers = Supplier::orderBy('name')->get();
        return view('purchases.index', compact('purchases', 'products', 'suppliers'));
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
        }

        // Обновляем общую сумму закупки
        $purchase->update(['total_amount' => $totalAmount]);

        return response()->json([
            'success' => true,
            'purchase' => $purchase->load(['supplier', 'items.product'])
        ]);
    }

    public function edit(Purchase $purchase)
    {
        try {
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
            // Сохраняем старые данные для корректировки склада
            $oldItems = $purchase->items;

            $purchase->update([
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'],
                'notes' => $validated['notes']
            ]);

            // Удаляем старые товары
            $purchase->items()->delete();

            $totalAmount = 0;

            // Добавляем новые товары
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

                // Обновляем склад
                $warehouseItem = Warehouse::firstOrNew(['product_id' => $item['product_id']]);

                // Вычитаем старые количества
                foreach ($oldItems as $oldItem) {
                    if ($oldItem->product_id == $item['product_id']) {
                        $warehouseItem->quantity -= $oldItem->quantity;
                    }
                }

                // Добавляем новые количества
                $warehouseItem->quantity += $item['quantity'];
                $warehouseItem->purchase_price = $item['purchase_price'];
                $warehouseItem->retail_price = $item['retail_price'];
                $warehouseItem->save();
            }

            // Удаляем остатки старых товаров, которых нет в новой закупке
            foreach ($oldItems as $oldItem) {
                $exists = false;
                foreach ($validated['items'] as $newItem) {
                    if ($oldItem->product_id == $newItem['product_id']) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    $warehouseItem = Warehouse::where('product_id', $oldItem->product_id)->first();
                    if ($warehouseItem) {
                        $warehouseItem->quantity -= $oldItem->quantity;
                        if ($warehouseItem->quantity <= 0) {
                            $warehouseItem->delete();
                        } else {
                            $warehouseItem->save();
                        }
                    }
                }
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
                $warehouseItem = Warehouse::where('product_id', $item->product_id)->first();
                if ($warehouseItem) {
                    $warehouseItem->quantity -= $item->quantity;
                    if ($warehouseItem->quantity <= 0) {
                        $warehouseItem->delete();
                    } else {
                        $warehouseItem->save();
                    }
                }
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
