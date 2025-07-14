<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\Inventory;
use App\Models\Clients\Product;
use App\Models\Clients\User;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $currentProjectId = auth()->user()->project_id;
        $inventories = Inventory::with(['user', 'items.product'])
            ->where('project_id', $currentProjectId)
            ->latest()
            ->get();

        $users = User::where('project_id', $currentProjectId)->get();
        $products = Product::where('project_id', $currentProjectId)->get();

        return view('client.inventories.index', compact('inventories', 'users', 'products'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
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
            'project_id' => $currentProjectId
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
