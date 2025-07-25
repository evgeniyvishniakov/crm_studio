<?php

namespace App\Http\Controllers\Client;

use App\Models\Clients\Product;
use App\Models\Clients\ProductCategory;
use App\Models\Clients\ProductBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = Product::with(['category', 'brand'])
            ->where('project_id', $currentProjectId)
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }

        if ($request->ajax()) {
            $products = $query->paginate(11);
            return response()->json([
                'data' => $products->items(),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ]);
        }

        $products = $query->paginate(11);
        $categories = ProductCategory::orderBy('name')->get();
        $brands = ProductBrand::orderBy('name')->get();
        return view('client.products.list', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        // Этот метод не используется, так как у нас модальное окно
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        // Этот метод не используется, так как у нас нет отдельной страницы товара
        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        try {
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'brand_id' => $product->brand_id,
                    'photo' => $product->photo,
                    'purchase_price' => $product->purchase_price,
                    'retail_price' => $product->retail_price,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки данных товара'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:product_categories,id',
                'brand_id' => 'required|exists:product_brands,id',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'purchase_price' => 'required|numeric|min:0',
                'retail_price' => 'required|numeric|min:0',
            ]);

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('products', 'public');
                $validated['photo'] = $path;
            }

            $product = Product::create($validated + ['project_id' => $currentProjectId]);

            return response()->json([
                'success' => true,
                'product' => $product->load(['category', 'brand'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ProductController@store',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'create_product',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:product_categories,id',
                'brand_id' => 'required|exists:product_brands,id',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'purchase_price' => 'required|numeric|min:0',
                'retail_price' => 'required|numeric|min:0',
            ]);

            if ($request->hasFile('photo')) {
                if ($product->photo) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($product->photo);
                }
                $path = $request->file('photo')->store('products', 'public');
                $validated['photo'] = $path;
            }

            $product->update($validated);

            return response()->json([
                'success' => true,
                'product' => $product->load(['category', 'brand'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ProductController@update',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'update_product',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            // ВСЕГДА используем мягкое удаление для сохранения исторических данных
            // Связанные записи (продажи, закупки) остаются в базе для отчетов
            
            // Удаляем фото только если товар не имеет связанных данных
            $hasRelatedData = $product->saleItems()->exists() || 
                             $product->purchaseItems()->exists() || 
                             $product->warehouse()->exists();

            if (!$hasRelatedData && $product->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->photo);
            }
            
            // Мягкое удаление товара
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => $hasRelatedData 
                    ? 'Товар перемещен в корзину (сохранены исторические данные)' 
                    : 'Товар успешно удален',
                'soft_deleted' => true
            ]);
            
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ProductController@destroy',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'delete_product',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }

    // Метод для восстановления товара
    public function restore($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            
            // Проверяем, существует ли файл изображения
            if ($product->photo && !Storage::disk('public')->exists($product->photo)) {
                // Если файл не существует, очищаем поле photo
                $product->photo = null;
            }
            
            $product->restore();

            return response()->json([
                'success' => true,
                'message' => 'Товар успешно восстановлен'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при восстановлении товара'
            ], 500);
        }
    }

    // Метод для принудительного удаления
    public function forceDelete($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Товар полностью удален'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при полном удалении товара'
            ], 500);
        }
    }

    // Метод для просмотра удаленных товаров
    public function trashed()
    {
        try {
            $currentProjectId = auth()->user()->project_id;
            
            // Логируем для диагностики
            \Log::info('Loading trashed products for project: ' . $currentProjectId);
            
            // Проверяем есть ли project_id
            if (!$currentProjectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось определить проект пользователя'
                ], 400);
            }
            
            $deletedProducts = Product::withTrashed()
                ->where('project_id', $currentProjectId)
                ->whereNotNull('deleted_at')
                ->with(['category', 'brand'])
                ->get();

            \Log::info('Found ' . $deletedProducts->count() . ' deleted products');

            return response()->json([
                'success' => true,
                'products' => $deletedProducts
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in trashed method: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке удаленных товаров: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removePhoto(Product $product)
    {
        try {
            if ($product->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->photo);
                $product->photo = null;
                $product->save();
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ProductController@removePhoto',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'remove_photo',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }

    // Метод для удаления всех удаленных товаров навсегда
    public function forceDeleteAll()
    {
        try {
            $currentProjectId = auth()->user()->project_id;
            
            if (!$currentProjectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось определить проект пользователя'
                ], 400);
            }
            
            // Получаем все удаленные товары для текущего проекта
            $deletedProducts = Product::withTrashed()
                ->where('project_id', $currentProjectId)
                ->whereNotNull('deleted_at')
                ->get();
            
            $deletedCount = $deletedProducts->count();
            
            if ($deletedCount === 0) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.no_products_to_delete')
                ]);
            }
            
            // Удаляем каждый товар навсегда
            foreach ($deletedProducts as $product) {
                $product->forceDelete();
            }
            
            \Log::info("Удалено {$deletedCount} товаров навсегда для проекта {$currentProjectId}");
            
            return response()->json([
                'success' => true,
                'message' => "Удалено {$deletedCount} товаров навсегда"
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in forceDeleteAll method: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении всех товаров: ' . $e->getMessage()
            ], 500);
        }
    }

}
