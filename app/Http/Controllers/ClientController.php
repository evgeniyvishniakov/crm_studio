<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientType;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('clientType')->get();
        $clientTypes = ClientType::where('status', true)->get();
        return view('clients.list', compact('clients', 'clientTypes'));
    }

    public function show($id)
    {
        $client = Client::with([
            'clientType',
            'appointments' => function($query) {
                $query->orderBy('date', 'desc')
                      ->orderBy('time', 'desc');
            },
            'appointments.service',
            'sales' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'sales.items.product'
        ])->findOrFail($id);
        
        return response()->json($client);
    }

    public function create()
    {
        return view('clients.create'); // если понадобится страница создания
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'client_type_id' => 'nullable|exists:client_types,id',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = Client::create($request->all());
            $client = Client::with('clientType')->find($client->id);
            return response()->json([
                'success' => true,
                'message' => 'Клиент успешно добавлен',
                'client' => $client
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении клиента: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();
            return response()->json([
                'success' => true,
                'message' => 'Клиент успешно удален'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении клиента: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Client $client)
    {
        return response()->json($client);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'client_type_id' => 'nullable|exists:client_types,id',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = Client::findOrFail($id);
            $client->update($request->all());
            $client = Client::with('clientType')->find($id);
            return response()->json([
                'success' => true,
                'message' => 'Клиент успешно обновлен',
                'client' => $client
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении клиента: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkExisting(Request $request)
    {
        $field = $request->query('field');
        $value = $request->query('value');

        if (empty($value) || !in_array($field, ['instagram', 'phone', 'email'])) {
            return response()->json(['exists' => false]);
        }

        $exists = Client::where($field, $value)
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function history($id)
    {
        $client = Client::with(['sales', 'appointments'])->findOrFail($id);
        return view('clients.history', compact('client'));
    }
}
