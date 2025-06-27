<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand'])->get();
        $categories = \App\Models\ProductCategory::orderBy('name')->get();
        $brands = \App\Models\ProductBrand::orderBy('name')->get();
        return view('products.list', compact('products', 'categories', 'brands'));
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

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'product' => $product->load(['category', 'brand'])
        ]);
    }

    public function update(Request $request, Product $product)
    {
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
                Storage::disk('public')->delete($product->photo);
            }
            $path = $request->file('photo')->store('products', 'public');
            $validated['photo'] = $path;
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'product' => $product->load(['category', 'brand'])
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->photo) {
            Storage::disk('public')->delete($product->photo);
        }
        $product->delete();

        return response()->json(['success' => true]);
    }

    public function removePhoto(Product $product)
    {
        if ($product->photo) {
            Storage::disk('public')->delete($product->photo);
            $product->photo = null;
            $product->save();
        }

        return response()->json(['success' => true]);
    }

}
