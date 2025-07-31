<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Clients\Supplier;
use Illuminate\Http\Request;
use App\Models\SystemLog;

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
            'name' => 'required|string|max:255|unique:suppliers,name,NULL,id,project_id,' . $currentProjectId,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:100',
            'inn' => 'nullable|string|max:20',
            'note' => 'nullable|string',
            'status' => 'boolean'
        ], [
            'name.unique' => 'Поставщик с таким названием уже существует в вашем проекте.'
        ]);
        try {
            $data = $validated;
            $data['project_id'] = $currentProjectId;
            $supplier = Supplier::create($data);
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Поставщик успешно добавлен'
            ]);
        } catch (\Exception $e) {
            SystemLog::create([
                'level' => 'error',
                'module' => 'SupplierController@store',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'create_supplier',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }

    public function edit(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $currentProjectId = auth()->user()->project_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id . ',id,project_id,' . $currentProjectId,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:100',
            'inn' => 'nullable|string|max:20',
            'note' => 'nullable|string',
            'status' => 'boolean'
        ], [
            'name.unique' => 'Поставщик с таким названием уже существует в вашем проекте.'
        ]);
        if ($supplier->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к поставщику'], 403);
        }
        try {
            $supplier->update($validated);
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Поставщик успешно обновлен'
            ]);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'SupplierController@update',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'update_supplier',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        $currentProjectId = auth()->user()->project_id;
        if ($supplier->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к поставщику'], 403);
        }
        try {
            $supplier->delete();
            return response()->json([
                'success' => true,
                'message' => 'Поставщик успешно удален'
            ]);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'SupplierController@destroy',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'delete_supplier',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }
}
