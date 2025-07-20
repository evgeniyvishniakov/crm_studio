<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\ProductCategory;
use Illuminate\Http\Request;
use App\Models\SystemLog;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = ProductCategory::where('project_id', $currentProjectId)->orderBy('name');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }

        if ($request->ajax()) {
            $categories = $query->paginate(11);
            return response()->json([
                'data' => $categories->items(),
                'meta' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                ],
            ]);
        }

        $categories = $query->paginate(11);
        return view('client.product-categories.list', compact('categories'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);
        try {
            $data = $validated;
            $data['project_id'] = $currentProjectId;
            $category = ProductCategory::create($data);
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Категория успешно добавлена'
            ]);
        } catch (\Exception $e) {
            SystemLog::create([
                'level' => 'error',
                'module' => 'ProductCategoryController@store',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'create_category',
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

    public function edit(ProductCategory $productCategory)
    {
        return response()->json($productCategory);
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);
        try {
            $productCategory->update($validated);
            return response()->json([
                'success' => true,
                'category' => $productCategory,
                'message' => 'Категория успешно обновлена'
            ]);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ProductCategoryController@update',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'update_category',
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

    public function destroy(ProductCategory $productCategory)
    {
        try {
            $productCategory->delete();
            return response()->json([
                'success' => true,
                'message' => 'Категория успешно удалена'
            ]);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ProductCategoryController@destroy',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'delete_category',
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
}
