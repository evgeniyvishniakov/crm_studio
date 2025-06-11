<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index()
    {
        $brands = ProductBrand::orderBy('created_at', 'desc')->get();
        return view('product-brands.list', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $brand = ProductBrand::create($validated);

        return response()->json([
            'success' => true,
            'brand' => $brand,
            'message' => 'Бренд успешно добавлен'
        ]);
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

        $productBrand->update($validated);

        return response()->json([
            'success' => true,
            'brand' => $productBrand,
            'message' => 'Бренд успешно обновлен'
        ]);
    }

    public function destroy(ProductBrand $productBrand)
    {
        $productBrand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Бренд успешно удален'
        ]);
    }
}
