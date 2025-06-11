<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('created_at', 'desc')->get();
        return view('product-categories.list', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $category = ProductCategory::create($validated);

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
