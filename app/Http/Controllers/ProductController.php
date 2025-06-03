<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.list', compact('products'));
    }

    public function edit(Product $product)
    {
        try {
            return response()->json([
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'brand' => $product->brand,
                'photo' => $product->photo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load product data'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
            $validated['photo'] = $path;
        }

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
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
            'product' => $product
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
