<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = Supplier::where('project_id', $currentProjectId)->orderBy('name');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }

        if ($request->ajax()) {
            $suppliers = $query->paginate(11);
            return response()->json([
                'data' => $suppliers->items(),
                'meta' => [
                    'current_page' => $suppliers->currentPage(),
                    'last_page' => $suppliers->lastPage(),
                    'per_page' => $suppliers->perPage(),
                    'total' => $suppliers->total(),
                ],
            ]);
        }

        $suppliers = $query->paginate(11);
        return view('client.suppliers.list', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:100',
            'inn' => 'nullable|string|max:20',
            'note' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $supplier = Supplier::create($validated + ['project_id' => $currentProjectId]);

        return response()->json([
            'success' => true,
            'supplier' => $supplier,
            'message' => 'Поставщик успешно добавлен'
        ]);
    }

    public function edit(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $currentProjectId = auth()->user()->project_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:100',
            'inn' => 'nullable|string|max:20',
            'note' => 'nullable|string',
            'status' => 'boolean'
        ]);
        if ($supplier->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к поставщику'], 403);
        }
        $supplier->update($validated);

        return response()->json([
            'success' => true,
            'supplier' => $supplier,
            'message' => 'Поставщик успешно обновлен'
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        $currentProjectId = auth()->user()->project_id;
        if ($supplier->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к поставщику'], 403);
        }
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Поставщик успешно удален'
        ]);
    }
}
