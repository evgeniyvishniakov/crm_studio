<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\ProductBrand;
use Illuminate\Http\Request;
use App\Models\SystemLog;

class ProductBrandController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = ProductBrand::where('project_id', $currentProjectId)->orderBy('name');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }

        if ($request->ajax()) {
            $brands = $query->paginate(11);
            return response()->json([
                'data' => $brands->items(),
                'meta' => [
                    'current_page' => $brands->currentPage(),
                    'last_page' => $brands->lastPage(),
                    'per_page' => $brands->perPage(),
                    'total' => $brands->total(),
                ],
            ]);
        }

        $brands = $query->paginate(11);
        return view('client.product-brands.list', compact('brands'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);
        try {
            $data = $validated;
            $data['project_id'] = $currentProjectId;
            $brand = ProductBrand::create($data);
            return response()->json([
                'success' => true,
                'brand' => $brand,
                'message' => 'Бренд успешно добавлен'
            ]);
        } catch (\Exception $e) {
            SystemLog::create([
                'level' => 'error',
                'module' => 'ProductBrandController@store',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'create_brand',
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

    public function edit(ProductBrand $productBrand)
    {
        return response()->json($productBrand);
    }

    public function update(Request $request, ProductBrand $productBrand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);
        try {
            $productBrand->update($validated);
            return response()->json([
                'success' => true,
                'brand' => $productBrand,
                'message' => 'Бренд успешно обновлен'
            ]);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ProductBrandController@update',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'update_brand',
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

    public function destroy(ProductBrand $productBrand)
    {
        try {
            $productBrand->delete();
            return response()->json([
                'success' => true,
                'message' => 'Бренд успешно удален'
            ]);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ProductBrandController@destroy',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'delete_brand',
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
