<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\ProductBrand;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductBrand::orderBy('name');

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
