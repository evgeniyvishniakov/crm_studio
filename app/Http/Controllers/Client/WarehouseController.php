<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\Product;
use App\Models\Clients\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = Warehouse::with('product')
            ->where('project_id', $currentProjectId)
            ->where('quantity', '>', 0)
            ->orderByDesc('id');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        if ($request->ajax()) {
            $warehouseItems = $query->paginate(11);
            $products = Product::where('project_id', $currentProjectId)->select('id', 'name', 'photo', 'purchase_price', 'retail_price')->get();

            return response()->json([
                'data' => $warehouseItems->items(),
                'meta' => [
                    'current_page' => $warehouseItems->currentPage(),
                    'last_page' => $warehouseItems->lastPage(),
                    'per_page' => $warehouseItems->perPage(),
                    'total' => $warehouseItems->total(),
                ],
                'products' => $products,
            ]);
        }

        // Для обычных запросов используем пагинацию
        $warehouseItems = $query->paginate(11);
        $products = Product::where('project_id', $currentProjectId)->select('id', 'name', 'photo', 'purchase_price', 'retail_price')->get();
        return view('client.warehouse.index', compact('warehouseItems', 'products'));
    }

    public function getProducts()
    {
        $products = Product::select('id', 'name', 'photo', 'purchase_price', 'retail_price')->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'purchase_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);

        $warehouse = \App\Models\Clients\Warehouse::firstOrNew(['product_id' => $validated['product_id'], 'project_id' => $currentProjectId]);
        $warehouse->purchase_price = $validated['purchase_price'];
        $warehouse->retail_price = $validated['retail_price'];
        $warehouse->quantity = ($warehouse->quantity ?? 0) + $validated['quantity'];
        $warehouse->project_id = $currentProjectId;
        $warehouse->save();

        // Обновляем цены в Product (как в закупках)
        Product::where('id', $validated['product_id'])
            ->update([
                'purchase_price' => $validated['purchase_price'],
                'retail_price' => $validated['retail_price'],
            ]);

        return response()->json([
            'success' => true,
            'warehouse' => $warehouse->load('product')
        ]);
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'purchase_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);

        $warehouse->update($validated);

        // Обновляем цены в Product
        Product::where('id', $warehouse->product_id)
            ->update([
                'purchase_price' => $validated['purchase_price'],
                'retail_price' => $validated['retail_price'],
            ]);

        return response()->json([
            'success' => true,
            'warehouse' => $warehouse->load('product')
        ]);
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return response()->json(['success' => true]);
    }

    // Fixed: Changed parameter from $id to Warehouse $warehouse for model binding
    public function edit(Warehouse $warehouse)
    {
        try {
            // Load the product relationship
            $warehouse->load('product');

            return response()->json([
                'success' => true,
                'warehouse' => [
                    'id' => $warehouse->id,
                    'product_id' => $warehouse->product_id,
                    'product_name' => $warehouse->product->name,
                    'purchase_price' => $warehouse->purchase_price,
                    'retail_price' => $warehouse->retail_price,
                    'quantity' => $warehouse->quantity,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Item not found'
            ], 404);
        }
    }
}
