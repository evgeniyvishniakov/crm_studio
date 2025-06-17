<?php

namespace App\Http\Controllers;

use App\Models\ClientType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientTypeController extends Controller
{
    public function index()
    {
        $types = ClientType::all();
        return response()->json($types);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $type = ClientType::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Тип клиента успешно создан',
                'type' => $type
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании типа клиента: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $type = ClientType::with('clients')->findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $type = ClientType::findOrFail($id);
            $type->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Тип клиента успешно обновлен',
                'type' => $type
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении типа клиента: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $type = ClientType::findOrFail($id);
            
            // Проверяем, есть ли клиенты с этим типом
            if ($type->clients()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно удалить тип клиента, так как существуют клиенты с этим типом'
                ], 422);
            }
            
            $type->delete();
            return response()->json([
                'success' => true,
                'message' => 'Тип клиента успешно удален'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении типа клиента: ' . $e->getMessage()
            ], 500);
        }
    }
}
