<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with(['user', 'items.product'])
            ->latest()
            ->get();

        $users = User::all();
        $products = Product::all();

        return view('client.inventories.index', compact('inventories', 'users', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.warehouse_qty' => 'required|integer|min:0',
            'items.*.actual_qty' => 'required|integer|min:0',
        ]);

        // Рассчитываем разницу для каждого товара
        $items = collect($validated['items'])->map(function ($item) {
            $item['difference'] = $item['actual_qty'] - $item['warehouse_qty'];
            return $item;
        });

        // Создаем инвентаризацию
        $inventory = Inventory::create([
            'date' => $validated['date'],
            'user_id' => $validated['user_id'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Добавляем товары
        $inventory->items()->createMany($items);

        // Загружаем отношения для ответа
        $inventory->load(['user', 'items.product']);

        return response()->json([
            'success' => true,
            'message' => 'Инвентаризация успешно сохранена',
            'inventory' => $inventory,
        ]);
    }

    public function edit($id)
    {
        $inventory = Inventory::with(['user', 'items.product'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'inventory' => $inventory,
        ]);
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.warehouse_qty' => 'required|integer|min:0',
            'items.*.actual_qty' => 'required|integer|min:0',
        ]);

        // Рассчитываем разницу для каждого товара
        $items = collect($validated['items'])->map(function ($item) {
            $item['difference'] = $item['actual_qty'] - $item['warehouse_qty'];
            return $item;
        });

        // Обновляем инвентаризацию
        $inventory->update([
            'date' => $validated['date'],
            'user_id' => $validated['user_id'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Удаляем старые товары и добавляем новые
        $inventory->items()->delete();
        $inventory->items()->createMany($items);

        // Загружаем отношения для ответа
        $inventory->load(['user', 'items.product']);

        return response()->json([
            'success' => true,
            'message' => 'Инвентаризация успешно обновлена',
            'inventory' => $inventory,
        ]);
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Инвентаризация успешно удалена',
        ]);
    }

    public function items($id)
    {
        $inventory = Inventory::with(['items.product'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'items' => $inventory->items,
        ]);
    }
}
