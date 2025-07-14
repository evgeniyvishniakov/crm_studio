<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\Product;
use App\Models\Clients\Purchase;
use App\Models\Clients\PurchaseItem;
use App\Models\Clients\Warehouse;
use App\Models\Clients\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = Purchase::with(['supplier', 'items.product'])->where('project_id', $currentProjectId)->latest();

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('notes', 'like', "%$search%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%$search%");
                  });
            });
        }

        if ($request->ajax()) {
            $purchases = $query->paginate(11);
            $products = Product::where('project_id', $currentProjectId)->get();
            $suppliers = Supplier::where('project_id', $currentProjectId)->orderBy('name')->get();
            return response()->json([
                'data' => $purchases->items(),
                'meta' => [
                    'current_page' => $purchases->currentPage(),
                    'last_page' => $purchases->lastPage(),
                    'per_page' => $purchases->perPage(),
                    'total' => $purchases->total(),
                ],
                'products' => $products,
                'suppliers' => $suppliers,
            ]);
        }

        $purchases = $query->paginate(11);
        $products = Product::where('project_id', $currentProjectId)->get();
        $suppliers = Supplier::where('project_id', $currentProjectId)->orderBy('name')->get();
        return view('client.purchases.index', compact('purchases', 'products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
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
                'total_amount' => 0,
                'project_id' => $currentProjectId
            ]);

            $totalAmount = 0;

            foreach (
                $validated['items'] as $item
            ) {
                $itemTotal = $item['purchase_price'] * $item['quantity'];
                $totalAmount += $itemTotal;

                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'purchase_price' => $item['purchase_price'],
                    'retail_price' => $item['retail_price'],
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                    'project_id' => $currentProjectId, // добавлено для мультипроктности
                ]);

                // Обновляем склад через модельный метод
                \App\Models\Clients\Warehouse::increaseQuantity($item['product_id'], $item['quantity']);

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
        $currentProjectId = auth()->user()->project_id;
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
            // Получаем старые и новые позиции закупки
            $oldItems = $purchase->items->keyBy('product_id');
            $newItems = collect($validated['items'])->keyBy('product_id');
            $allProductIds = $oldItems->keys()->merge($newItems->keys())->unique();

            // Корректируем склад только по разнице
            foreach ($allProductIds as $productId) {
                $oldQty = $oldItems[$productId]->quantity ?? 0;
                $newQty = $newItems[$productId]['quantity'] ?? 0;

                if ($oldQty && !$newQty) {
                    // Товар был, но его удалили — уменьшить на oldQty
                    \App\Models\Clients\Warehouse::decreaseQuantity($productId, $oldQty);
                } elseif (!$oldQty && $newQty) {
                    // Новый товар — добавить на склад
                    \App\Models\Clients\Warehouse::increaseQuantity($productId, $newQty);
                } elseif ($oldQty && $newQty && $oldQty != $newQty) {
                    // Изменилось количество — скорректировать разницу
                    $diff = $newQty - $oldQty;
                    if ($diff > 0) {
                        \App\Models\Clients\Warehouse::increaseQuantity($productId, $diff);
                    } else {
                        \App\Models\Clients\Warehouse::decreaseQuantity($productId, abs($diff));
                    }
                }
                // Если oldQty == newQty — ничего не делать
            }

            // Обновляем данные самой закупки
            $purchase->update([
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'],
                'notes' => $validated['notes']
            ]);
            // Удаляем старые позиции закупки
            $purchase->items()->delete();

            $totalAmount = 0;

            // Добавляем новые позиции закупки
            foreach ($validated['items'] as $itemData) {
                $itemTotal = $itemData['purchase_price'] * $itemData['quantity'];
                $totalAmount += $itemTotal;

                $purchase->items()->create([
                    'product_id' => $itemData['product_id'],
                    'purchase_price' => $itemData['purchase_price'],
                    'retail_price' => $itemData['retail_price'],
                    'quantity' => $itemData['quantity'],
                    'total' => $itemTotal,
                    'project_id' => $currentProjectId, // для мультипроктности
                ]);

                // Обновляем цены в Product
                Product::where('id', $itemData['product_id'])
                    ->update([
                        'purchase_price' => $itemData['purchase_price'],
                        'retail_price' => $itemData['retail_price'],
                    ]);
            }

            // Обновляем общую сумму закупки
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
                \App\Models\Clients\Warehouse::decreaseQuantity($item->product_id, $item->quantity);
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
