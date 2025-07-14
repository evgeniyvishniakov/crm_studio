<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\Sale;
use App\Models\Clients\SaleItem;
use App\Models\Clients\Client;
use App\Models\Clients\Product;
use App\Models\Clients\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = Sale::with(['client', 'items.product'])
            ->where('project_id', $currentProjectId)
            ->orderBy('date', 'desc');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('notes', 'like', "%$search%")
                  ->orWhereHas('client', function($sq) use ($search) {
                      $sq->where('name', 'like', "%$search%")
                         ->orWhere('instagram', 'like', "%$search%");
                  });
            });
        }

        if ($request->ajax()) {
            // Получаем все продажи с товарами
            $sales = $query->get();
            
            // Собираем все товары из всех продаж
            $allItems = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $allItems[] = [
                        'sale' => $sale,
                        'item' => $item
                    ];
                }
            }
            
            // Пагинируем товары (11 на страницу)
            $perPage = 11;
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $itemsOnPage = array_slice($allItems, $offset, $perPage);
            $totalItems = count($allItems);
            $lastPage = ceil($totalItems / $perPage);
            
            // Группируем товары обратно по продажам для отображения
            $groupedSales = [];
            foreach ($itemsOnPage as $itemData) {
                $saleId = $itemData['sale']->id;
                if (!isset($groupedSales[$saleId])) {
                    $groupedSales[$saleId] = $itemData['sale'];
                    $groupedSales[$saleId]->items = collect([]);
                }
                $groupedSales[$saleId]->items->push($itemData['item']);
            }
            
            $clients = Client::where('project_id', $currentProjectId)->select('id', 'name', 'instagram', 'phone', 'email')->get();

            // Получаем только товары, которые есть на складе
            $products = Product::where('project_id', $currentProjectId)
                ->whereHas('warehouse', function($query) {
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

            return response()->json([
                'data' => array_values($groupedSales),
                'meta' => [
                    'current_page' => (int)$currentPage,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $totalItems,
                ],
                'clients' => $clients,
                'products' => $products,
            ]);
        }

        // Для обычного запроса (не AJAX) возвращаем все продажи
        $sales = $query->get();
        $clients = Client::where('project_id', $currentProjectId)->select('id', 'name', 'instagram', 'phone', 'email')->get();

        // Получаем только товары, которые есть на складе
        $products = Product::where('project_id', $currentProjectId)
            ->whereHas('warehouse', function($query) {
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

        return view('client.sales.index', compact('sales', 'clients', 'products'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
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
                'total_amount' => 0,
                'project_id' => $currentProjectId
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
                    'total' => $itemTotal,
                    'project_id' => $sale->project_id,
                ]);

                // Уменьшаем количество на складе
                Warehouse::decreaseQuantity($item['product_id'], $item['quantity']);
            }

            $sale->total_amount = $totalAmount;
            $sale->save();

            // Подгружаем связи для корректного ответа
            $sale->load(['client', 'items.product']);

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => [
                    ...$sale->toArray(),
                    'client' => $sale->client,
                    'items' => $sale->items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product' => $item->product,
                            'wholesale_price' => $item->wholesale_price,
                            'retail_price' => $item->retail_price,
                            'quantity' => $item->quantity,
                            'total' => $item->total,
                        ];
                    }),
                ]
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
            // Получаем старые товары из продажи
            $oldItems = $sale->items->keyBy(fn($i) => (string)$i->product_id);
            $newItems = collect($validated['items'])->keyBy('product_id');

            // 1. Обрабатываем новые и изменённые товары
            foreach ($newItems as $productId => $item) {
                $oldSaleItem = $oldItems[(string)$productId] ?? null;
                $newQty = $item['quantity'];
                $oldQty = $oldSaleItem ? $oldSaleItem->quantity : 0;

                if ($oldQty) {
                    if ($newQty > $oldQty) {
                        $delta = $newQty - $oldQty;
                        Warehouse::checkAvailability($productId, $delta);
                        Warehouse::decreaseQuantity($productId, $delta);
                    } elseif ($newQty < $oldQty) {
                        $delta = $oldQty - $newQty;
                        Warehouse::increaseQuantity($productId, $delta);
                    }
                    unset($oldItems[(string)$productId]);
                } else {
                    Warehouse::checkAvailability($productId, $newQty);
                    Warehouse::decreaseQuantity($productId, $newQty);
                }
            }

            // 2. Все товары, которые были в старой продаже, но их нет в новых — вернуть на склад
            foreach ($oldItems as $oldItem) {
                Warehouse::increaseQuantity($oldItem->product_id, $oldItem->quantity);
            }

            // 3. Обновить продажу
            $sale->update([
                'date' => $validated['date'],
                'client_id' => $validated['client_id'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0
            ]);

            // 4. Удалить старые позиции
            $sale->items()->delete();

            $totalAmount = 0;

            // 5. Добавить новые позиции
            foreach ($validated['items'] as $item) {
                $oldSaleItem = $oldItems[(string)$item['product_id']] ?? null;
                $warehouseItem = Warehouse::where('product_id', $item['product_id'])->first();
                $product = \App\Models\Clients\Product::find($item['product_id']);
                $wholesalePrice = $oldSaleItem ? $oldSaleItem->wholesale_price : ($warehouseItem ? $warehouseItem->purchase_price : ($product ? $product->purchase_price : 0));
                $itemTotal = ($warehouseItem ? $warehouseItem->retail_price : ($product ? $product->retail_price : 0)) * $item['quantity'];
                $totalAmount += $itemTotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'wholesale_price' => $wholesalePrice,
                    'retail_price' => $item['retail_price'],
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                    'project_id' => $sale->project_id,
                ]);
            }

            $sale->total_amount = $totalAmount;
            $sale->save();

            // Подгружаем связи для корректного ответа
            $sale->load(['client', 'items.product']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Продажа успешно обновлена!',
                'sale' => [
                    ...$sale->toArray(),
                    'client' => $sale->client,
                    'items' => $sale->items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product' => $item->product,
                            'wholesale_price' => $item->wholesale_price,
                            'retail_price' => $item->retail_price,
                            'quantity' => $item->quantity,
                            'total' => $item->total,
                        ];
                    }),
                ]
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
                'sale' => [
                    ...$sale->toArray(),
                    'items' => $sale->items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product' => $item->product,
                            'wholesale_price' => $item->wholesale_price,
                            'retail_price' => $item->retail_price,
                            'quantity' => $item->quantity,
                            'total' => $item->total,
                        ];
                    }),
                ],
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
