<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\Inventory;
use App\Models\Clients\Product;
use App\Models\Admin\User;
use App\Models\Admin\Project;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryController extends Controller
{
    public function index()
    {
        $currentProjectId = auth()->user()->project_id;
        $inventories = Inventory::with(['user', 'items.product'])
            ->where('project_id', $currentProjectId)
            ->orderBy('date', 'asc')
            ->get();

        $users = User::where('project_id', $currentProjectId)->get();
        $products = Product::with('warehouse')
            ->where('project_id', $currentProjectId)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'photo' => $product->photo,
                    'stock' => $product->warehouse ? $product->warehouse->quantity : 0,
                ];
            });
        $adminUser = $users->firstWhere('role', 'admin');
        $adminUserId = $adminUser ? $adminUser->id : null;
        return view('client.inventories.index', compact('inventories', 'users', 'products', 'adminUserId'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $validated = $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:admin_users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.warehouse_qty' => 'required|integer|min:0',
            'items.*.actual_qty' => 'required|integer|min:0',
        ]);

        // Рассчитываем разницу для каждого товара
        $items = collect($validated['items'])->map(function ($item) use ($currentProjectId) {
            $item['difference'] = $item['actual_qty'] - $item['warehouse_qty'];
            $item['project_id'] = $currentProjectId;
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

        // Добавляем вычисляемые поля для фронта
        $inventory->discrepancies_count = $inventory->discrepancies_count;
        $inventory->shortages_count = $inventory->shortages_count;
        $inventory->overages_count = $inventory->overages_count;
        $inventory->formatted_date = $inventory->formatted_date;

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
        $currentProjectId = auth()->user()->project_id;
        $inventory = Inventory::findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:admin_users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.warehouse_qty' => 'required|integer|min:0',
            'items.*.actual_qty' => 'required|integer|min:0',
        ]);

        // Рассчитываем разницу для каждого товара
        $items = collect($validated['items'])->map(function ($item) use ($currentProjectId) {
            $item['difference'] = $item['actual_qty'] - $item['warehouse_qty'];
            $item['project_id'] = $currentProjectId;
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

        // Добавляем вычисляемые поля для фронта
        $inventory->discrepancies_count = $inventory->discrepancies_count;
        $inventory->shortages_count = $inventory->shortages_count;
        $inventory->overages_count = $inventory->overages_count;
        $inventory->formatted_date = $inventory->formatted_date;

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

    /**
     * Генерация PDF с расхождениями по инвентаризации
     */
    public function pdf($id)
    {
        $inventory = Inventory::with(['user', 'items.product'])->findOrFail($id);
        $discrepancies = $inventory->items->where('difference', '!=', 0);
        
        // Получаем название проекта
        $project = auth()->user()->project;
        $projectName = $project ? $project->project_name : 'Проект';
        
        $pdf = Pdf::loadView('client.inventories.pdf', [
            'inventory' => $inventory,
            'discrepancies' => $discrepancies,
            'projectName' => $projectName,
        ]);
        $filename = $projectName.'_'.__('messages.inventory').'_'.($inventory->formatted_date ?? $inventory->date).'.pdf';
        return $pdf->download($filename);
    }
}
