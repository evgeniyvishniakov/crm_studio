<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouseItems = Warehouse::with('product')
            ->where('quantity', '>', 0)
            ->get();
        $products = Product::select('id', 'name', 'photo', 'purchase_price', 'retail_price')->get();
        return view('warehouse.index', compact('warehouseItems', 'products'));
    }

    public function getProducts()
    {
        $products = Product::select('id', 'name', 'photo', 'purchase_price', 'retail_price')->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'purchase_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);

        $warehouse = \App\Models\Warehouse::firstOrNew(['product_id' => $validated['product_id']]);
        $warehouse->purchase_price = $validated['purchase_price'];
        $warehouse->retail_price = $validated['retail_price'];
        $warehouse->quantity = ($warehouse->quantity ?? 0) + $validated['quantity'];
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
