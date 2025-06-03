<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['client', 'items.product'])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($sale) {
                $sale->formatted_date = $sale->date->format('d.m.Y');
                return $sale;
            });

        $clients = Client::select('id', 'name', 'instagram', 'phone', 'email')->get();

        // Получаем только товары, которые есть на складе
        $products = Product::whereHas('warehouse', function($query) {
            $query->where('quantity', '>', 0);
        })
            ->with(['warehouse'])
            ->select('id', 'name', 'photo')
            ->get()
            ->map(function($product) {
                // Добавляем информацию о ценах и количестве
                $product->available_quantity = $product->warehouse->quantity;
                $product->retail_price = $product->warehouse->retail_price;
                $product->wholesale_price = $product->warehouse->purchase_price;
                return $product;
            });

        return view('sales.index', compact('sales', 'clients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.retail_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Проверяем наличие всех товаров на складе перед созданием продажи
            foreach ($validated['items'] as $item) {
                Warehouse::checkAvailability($item['product_id'], $item['quantity']);
            }

            $sale = Sale::create([
                'date' => $validated['date'],
                'client_id' => $validated['client_id'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0
            ]);

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                // Получаем текущие цены со склада
                $warehouseItem = Warehouse::where('product_id', $item['product_id'])->first();

                $itemTotal = $warehouseItem->retail_price * $item['quantity'];
                $totalAmount += $itemTotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'wholesale_price' => $warehouseItem->purchase_price,
                    'retail_price' => $item['retail_price'],
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal
                ]);

                // Уменьшаем количество на складе
                Warehouse::decreaseQuantity($item['product_id'], $item['quantity']);
            }

            $sale->total_amount = $totalAmount;
            $sale->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale->load(['client', 'items.product'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.retail_price' => 'required|numeric',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Проверяем наличие всех товаров на складе
            foreach ($validated['items'] as $item) {
                Warehouse::checkAvailability($item['product_id'], $item['quantity']);
            }

            // Возвращаем старые товары на склад
            foreach ($sale->items as $oldItem) {
                Warehouse::increaseQuantity($oldItem->product_id, $oldItem->quantity);
            }

            // Обновляем продажу
            $sale->update([
                'date' => $validated['date'],
                'client_id' => $validated['client_id'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0
            ]);

            // Удаляем старые позиции
            $sale->items()->delete();

            $totalAmount = 0;

            // Добавляем новые позиции
            foreach ($validated['items'] as $item) {
                // Получаем текущие цены со склада
                $warehouseItem = Warehouse::where('product_id', $item['product_id'])->first();

                $itemTotal = $warehouseItem->retail_price * $item['quantity'];
                $totalAmount += $itemTotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'wholesale_price' => $warehouseItem->purchase_price,
                    'retail_price' => $item['retail_price'],
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal
                ]);

                // Уменьшаем количество на складе
                Warehouse::decreaseQuantity($item['product_id'], $item['quantity']);
            }

            $sale->total_amount = $totalAmount;
            $sale->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale->load(['client', 'items.product'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Sale $sale)
    {
        DB::beginTransaction();

        try {
            // Возвращаем товары на склад
            foreach ($sale->items as $item) {
                Warehouse::increaseQuantity($item->product_id, $item->quantity);
            }

            // Удаляем продажу
            $sale->items()->delete();
            $sale->delete();

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении продажи',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function edit(Sale $sale)
    {
        try {
            $sale->load(['client', 'items.product']);
            $clients = Client::select('id', 'name', 'instagram', 'phone', 'email')->get();

            $products = Product::whereHas('warehouse', function($query) {
                $query->where('quantity', '>', 0);
            })
                ->with(['warehouse'])
                ->select('id', 'name', 'photo')
                ->get()
                ->map(function($product) {
                    $product->available_quantity = $product->warehouse->quantity;
                    $product->retail_price = $product->warehouse->retail_price;
                    $product->wholesale_price = $product->warehouse->purchase_price;
                    return $product;
                });

            return response()->json([
                'success' => true,
                'sale' => $sale,
                'clients' => $clients,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteItem($saleId, $itemId)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($saleId);
            $item = $sale->items()->findOrFail($itemId);

            // Возвращаем товар на склад
            Warehouse::increaseQuantity($item->product_id, $item->quantity);

            $item->delete();

            // Проверяем, остались ли товары в продаже
            $itemsRemaining = $sale->items()->count();
            $saleDeleted = $itemsRemaining === 0;

            if ($saleDeleted) {
                $sale->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'sale_deleted' => $saleDeleted,
                'message' => 'Товар успешно удален' . ($saleDeleted ? ', продажа удалена' : '')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении товара: ' . $e->getMessage()
            ], 500);
        }
    }
}
