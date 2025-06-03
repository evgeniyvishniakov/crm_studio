<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('items.product')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($purchase) {
                $purchase->formatted_date = $purchase->date->format('d.m.Y');
                return $purchase;
            });

        $products = Product::select('id', 'name', 'photo')->get();
        return view('purchases.index', compact('purchases', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.purchase_price' => 'required|numeric|min:0.01',
            'items.*.retail_price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'date' => $validated['date'],
                'supplier' => $validated['supplier'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0
            ]);

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $itemTotal = $item['purchase_price'] * $item['quantity'];
                $totalAmount += $itemTotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'purchase_price' => $item['purchase_price'],
                    'retail_price' => $item['retail_price'],
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal
                ]);

                // Обновляем склад
                $warehouseItem = Warehouse::firstOrNew(['product_id' => $item['product_id']]);

                if ($warehouseItem->exists) {
                    $warehouseItem->quantity += $item['quantity'];
                } else {
                    $warehouseItem->quantity = $item['quantity'];
                }

                $warehouseItem->purchase_price = $item['purchase_price'];
                $warehouseItem->retail_price = $item['retail_price'];
                $warehouseItem->save();
            }

            $purchase->total_amount = $totalAmount;
            $purchase->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'purchase' => $purchase->load('items.product')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании закупки',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Purchase $purchase)
    {
        return response()->json([
            'success' => true,
            'purchase' => $purchase->load('items.product'),
            'products' => Product::select('id', 'name')->get()
        ]);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.purchase_price' => 'required|numeric|min:0.01',
            'items.*.retail_price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Сохраняем старые данные для корректировки склада
            $oldItems = $purchase->items;

            // Обновляем закупку
            $purchase->update([
                'date' => $validated['date'],
                'supplier' => $validated['supplier'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0
            ]);

            // Удаляем старые позиции
            $purchase->items()->delete();

            $totalAmount = 0;

            // Добавляем новые позиции
            foreach ($validated['items'] as $item) {
                $itemTotal = $item['purchase_price'] * $item['quantity'];
                $totalAmount += $itemTotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
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

            $purchase->total_amount = $totalAmount;
            $purchase->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'purchase' => $purchase->load('items.product')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении закупки',
                'error' => $e->getMessage()
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
