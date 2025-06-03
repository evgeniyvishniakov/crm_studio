<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all(); // или пагинация: Client::paginate(10);
        return view('clients.list', compact('clients'));
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.show', compact('client'));
    }

    public function create()
    {
        return view('clients.create'); // если понадобится страница создания
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'instagram' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'status' => 'nullable|string|in:new,regular,vip'
            ], [
                'name.required' => 'Поле "Имя" обязательно для заполнения.',
                'email.email' => 'Укажите корректный email.',
                'status.in' => 'Недопустимый статус.'
            ]);

            // Проверка уникальности только для заполненных полей
            $errors = [];

            if (!empty($validated['instagram'])) {
                $exists = Client::where('instagram', $validated['instagram'])
                    ->whereNotNull('instagram')
                    ->exists();
                if ($exists) {
                    $errors['instagram'] = ['Этот Instagram уже используется'];
                }
            }

            if (!empty($validated['phone'])) {
                $exists = Client::where('phone', $validated['phone'])
                    ->whereNotNull('phone')
                    ->exists();
                if ($exists) {
                    $errors['phone'] = ['Этот номер телефона уже используется'];
                }
            }

            if (!empty($validated['email'])) {
                $exists = Client::where('email', $validated['email'])
                    ->whereNotNull('email')
                    ->exists();
                if ($exists) {
                    $errors['email'] = ['Этот email уже используется'];
                }
            }

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'errors' => $errors
                ], 422);
            }

            $client = Client::create($validated);

            return response()->json([
                'success' => true,
                'client' => $client
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Client $client)
    {
        try {
            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Клиент успешно удален'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении клиента'
            ], 500);
        }
    }
    public function edit(Client $client)
    {
        return response()->json($client);
    }
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255|unique:clients,instagram,'.$client->id,
            'phone' => 'nullable|string|max:20|unique:clients,phone,'.$client->id,
            'email' => 'nullable|email|max:255|unique:clients,email,'.$client->id,
            'status' => 'nullable|string|in:new,regular,vip'
        ], [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'email.email' => 'Укажите корректный email.',
            'instagram.unique' => 'Этот Instagram уже используется.',
            'phone.unique' => 'Этот номер телефона уже используется.',
            'email.unique' => 'Этот email уже используется.',
            'status.in' => 'Недопустимый статус.'
        ]);

        try {
            $client->update($validated);

            return response()->json([
                'success' => true,
                'client' => $client
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении клиента'
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


}
