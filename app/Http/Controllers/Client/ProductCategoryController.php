<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\ProductCategory;
use Illuminate\Http\Request;

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

        $category = ProductCategory::create($validated + ['project_id' => $currentProjectId]);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Категория успешно добавлена'
        ]);
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

        $productCategory->update($validated);

        return response()->json([
            'success' => true,
            'category' => $productCategory,
            'message' => 'Категория успешно обновлена'
        ]);
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Категория успешно удалена'
        ]);
    }
}
