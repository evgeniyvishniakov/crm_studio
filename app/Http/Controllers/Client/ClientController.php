<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\Clients\Client;
use App\Models\Clients\ClientType;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = Client::with('clientType')->where('project_id', $currentProjectId)->orderByDesc('id');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('instagram', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->ajax()) {
            $clients = $query->paginate(11);
            $clientTypes = ClientType::where('project_id', $currentProjectId)
                ->orWhere('is_global', true)
                ->where('status', true)
                ->get();

            return response()->json([
                'data' => $clients->items(),
                'meta' => [
                    'current_page' => $clients->currentPage(),
                    'last_page' => $clients->lastPage(),
                    'per_page' => $clients->perPage(),
                    'total' => $clients->total(),
                ],
                'clientTypes' => $clientTypes,
            ]);
        }

        // Для обычных запросов используем пагинацию
        $clients = $query->paginate(11);
        $clientTypes = ClientType::where('project_id', $currentProjectId)
            ->orWhere('is_global', true)
            ->where('status', true)
            ->get();
        return view('client.clients.list', compact('clients', 'clientTypes'));
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
                $query->orderBy('date', 'desc');
            },
            'sales.items.product'
        ])->findOrFail($id);

        return response()->json($client);
    }

    public function create()
    {
        return view('client.clients.create'); // если понадобится страница создания
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
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
            $client = Client::create($request->all() + ['project_id' => $currentProjectId]);
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
        $currentProjectId = auth()->user()->project_id;
        try {
            $client = Client::where('project_id', $currentProjectId)->findOrFail($id);
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
        $currentProjectId = auth()->user()->project_id;
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
            $client = Client::where('project_id', $currentProjectId)->findOrFail($id);
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

    public function history($id)
    {
        $currentProjectId = auth()->user()->project_id;
        $client = Client::with(['sales', 'appointments'])->where('project_id', $currentProjectId)->findOrFail($id);
        return view('client.clients.history', compact('client'));
    }

    public function checkUnique(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $field = $request->query('field');
        $value = $request->query('value');

        if (empty($value) || !in_array($field, ['instagram', 'phone', 'email'])) {
            return response()->json(['exists' => false]);
        }

        $exists = \App\Models\Clients\Client::where($field, $value)
            ->where('project_id', $currentProjectId)
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
