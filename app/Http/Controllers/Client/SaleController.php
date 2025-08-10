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
use App\Models\Admin\User;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Отключаем автоматическую обработку ошибок валидации
            if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
                $request->setLaravelSession(app('session.store'));
            }
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $query = Sale::with(['client', 'employee', 'items.product'])
            ->where('project_id', $currentProjectId)
            ->orderBy('date', 'desc');

        // Если пользователь не админ, показываем только его продажи
        if ($currentUser->role !== 'admin') {
            $query->where('employee_id', $currentUser->id);
        }

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
        $employees = User::where('project_id', $currentProjectId)->get(['id', 'name']);

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

        return view('client.sales.index', compact('sales', 'clients', 'products', 'employees'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $validated = $request->validate([
            'date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:admin_users,id',
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
                Warehouse::checkAvailability($item['product_id'], $item['quantity'], $currentProjectId);
            }

            $sale = Sale::create([
                'date' => $validated['date'],
                'client_id' => $validated['client_id'],
                'employee_id' => $validated['employee_id'],
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
                Warehouse::decreaseQuantity($item['product_id'], $item['quantity'], $currentProjectId);
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
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        
        // Проверяем доступ к проекту
        if ($sale->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к продаже'], 403);
        }
        
        // Если пользователь не админ, проверяем что это его продажа
        if ($currentUser->role !== 'admin' && $sale->employee_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к продаже'], 403);
        }
        
        // Логируем входящие данные для отладки
        \Log::info('Sale update request data:', $request->all());
        
        $validator = \Validator::make($request->all(), [
            'date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:admin_users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.wholesale_price' => 'required|numeric',
            'items.*.retail_price' => 'required|numeric',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            \Log::error('Sale validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();

        try {
            // Получаем старые товары из продажи
            $oldItems = $sale->items->keyBy('id');
            $newItems = collect($validated['items']);
            $totalAmount = 0;

            // 1. Обновляем существующие позиции и обрабатываем изменения количества
            foreach ($newItems as $index => $item) {
                $productId = $item['product_id'];
                $newQty = (int)$item['quantity'];
                
                // Ищем существующую позицию по product_id
                $existingItem = $sale->items->where('product_id', $productId)->first();
                
                if ($existingItem) {
                    $oldQty = $existingItem->quantity;
                    
                    // Если количество изменилось, корректируем склад
                    if ($newQty !== $oldQty) {
                        if ($newQty > $oldQty) {
                            // Увеличили количество - берем со склада
                            $delta = $newQty - $oldQty;
                            Warehouse::checkAvailability($productId, $delta, $sale->project_id);
                            Warehouse::decreaseQuantity($productId, $delta, $sale->project_id);
                        } else {
                            // Уменьшили количество - возвращаем на склад
                            $delta = $oldQty - $newQty;
                            Warehouse::increaseQuantity($productId, $delta, $sale->project_id);
                        }
                    }
                    
                    // Обновляем существующую позицию
                    $existingItem->update([
                        'wholesale_price' => $item['wholesale_price'],
                        'retail_price' => $item['retail_price'],
                        'quantity' => $newQty,
                        'total' => $item['retail_price'] * $newQty
                    ]);
                    
                    $totalAmount += $item['retail_price'] * $newQty;
                    
                    // Убираем из списка старых позиций
                    $oldItems->forget($existingItem->id);
                    
                } else {
                    // Новый товар - берем со склада
                    Warehouse::checkAvailability($productId, $newQty, $sale->project_id);
                    Warehouse::decreaseQuantity($productId, $newQty, $sale->project_id);
                    
                    // Создаем новую позицию
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'wholesale_price' => $item['wholesale_price'],
                        'retail_price' => $item['retail_price'],
                        'quantity' => $newQty,
                        'total' => $item['retail_price'] * $newQty,
                        'project_id' => $sale->project_id,
                    ]);
                    
                    $totalAmount += $item['retail_price'] * $newQty;
                }
            }

            // 2. Удаляем позиции, которых больше нет в продаже, и возвращаем товары на склад
            foreach ($oldItems as $oldItem) {
                Warehouse::increaseQuantity($oldItem->product_id, $oldItem->quantity, $sale->project_id);
                $oldItem->delete();
            }

            // 3. Обновляем основную информацию о продаже
            $sale->update([
                'date' => $validated['date'],
                'client_id' => $validated['client_id'],
                'employee_id' => $validated['employee_id'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount
            ]);

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
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        
        // Проверяем доступ к проекту
        if ($sale->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к продаже'], 403);
        }
        
        // Если пользователь не админ, проверяем что это его продажа
        if ($currentUser->role !== 'admin' && $sale->employee_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к продаже'], 403);
        }
        
        DB::beginTransaction();

        try {
            // Возвращаем товары на склад
            foreach ($sale->items as $item) {
                Warehouse::increaseQuantity($item->product_id, $item->quantity, $sale->project_id);
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
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        
        // Проверяем доступ к проекту
        if ($sale->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к продаже'], 403);
        }
        
        // Если пользователь не админ, проверяем что это его продажа
        if ($currentUser->role !== 'admin' && $sale->employee_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к продаже'], 403);
        }
        
        try {
            $sale->load(['client', 'employee', 'items.product']);
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
            $employees = User::where('project_id', $sale->project_id)->get(['id', 'name']);
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
                'products' => $products,
                'employees' => $employees,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteItem($sale, $item)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        
        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($sale);
            
            // Проверяем доступ к проекту
            if ($sale->project_id !== $currentProjectId) {
                return response()->json(['success' => false, 'message' => 'Нет доступа к продаже'], 403);
            }
            
            // Если пользователь не админ, проверяем что это его продажа
            if ($currentUser->role !== 'admin' && $sale->employee_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Нет доступа к продаже'], 403);
            }
            
            $saleItem = $sale->items()->findOrFail($item);

            // Возвращаем товар на склад
            Warehouse::increaseQuantity($saleItem->product_id, $saleItem->quantity, $sale->project_id);

            $saleItem->delete();

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
