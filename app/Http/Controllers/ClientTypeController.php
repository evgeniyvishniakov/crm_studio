<?php

namespace App\Http\Controllers;

use App\Models\ClientType;
use Illuminate\Http\Request;

class ClientTypeController extends Controller
{
    public function index()
    {
        $clientTypes = ClientType::orderBy('created_at', 'desc')->get();
        return view('client-types.list', compact('clientTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'boolean'
        ]);

        $clientType = ClientType::create($validated);

        return response()->json([
            'success' => true,
            'clientType' => $clientType,
            'message' => 'Тип клиента успешно добавлен'
        ]);
    }

    public function edit(ClientType $clientType)
    {
        return response()->json($clientType);
    }

    public function update(Request $request, ClientType $clientType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'boolean'
        ]);

        $clientType->update($validated);

        return response()->json([
            'success' => true,
            'clientType' => $clientType,
            'message' => 'Тип клиента успешно обновлен'
        ]);
    }

    public function destroy(ClientType $clientType)
    {
        $clientType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Тип клиента успешно удален'
        ]);
    }
}
