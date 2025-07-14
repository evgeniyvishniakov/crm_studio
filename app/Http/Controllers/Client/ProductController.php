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
        $query = Product::with(['category', 'brand'])->where('project_id', $currentProjectId)->orderBy('name');

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
