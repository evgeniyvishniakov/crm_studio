<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\User;

class ClientUserController extends Controller
{
    public function index(Request $request)
    {
        $projectId = auth('client')->user()->project_id;
        $users = User::where('project_id', $projectId)->orderBy('id', 'asc')->get();
        $roles = config('roles');
        return view('client.users.list', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $projectId = auth('client')->user()->project_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'min:6',
                'max:255',
                'unique:admin_users,username',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:6',
            'role' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (in_array(strtolower($value), User::FIXED_ROLES)) {
                        $fail('Роль admin зарезервирована и не может быть назначена.');
                    }
                },
            ],
        ], [
            'username.regex' => 'Логин может содержать только латинские буквы и цифры.',
            'username.min' => 'Логин должен быть не менее 6 символов.',
        ]);
        try {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
                'project_id' => $projectId,
                'registered_at' => now(),
                'status' => 'active',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно добавлен',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении пользователя: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $projectId = auth('client')->user()->project_id;
        $user = User::where('project_id', $projectId)->findOrFail($id);
        // Запрещаем удаление пользователей с уникальной ролью admin
        if (in_array($user->role, User::FIXED_ROLES)) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь с ролью Администратор не может быть удалён.'
            ], 403);
        }
        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно удалён'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении пользователя: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $projectId = auth('client')->user()->project_id;
        $user = User::where('project_id', $projectId)->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $projectId = auth('client')->user()->project_id;
        $user = User::where('project_id', $projectId)->findOrFail($id);
        // Запрещаем любые изменения для пользователей с уникальной ролью admin
        if (in_array($user->role, User::FIXED_ROLES)) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь с ролью Администратор не может быть изменён.'
            ], 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'min:6',
                'max:255',
                'unique:admin_users,username,' . $id,
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'email' => 'nullable|email|max:255',
            'role' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    // Запрещаем назначать роль admin кому-либо, кроме уже существующего admin
                    if (in_array(strtolower($value), User::FIXED_ROLES) && $user->role !== 'admin') {
                        $fail('Роль admin зарезервирована и не может быть назначена.');
                    }
                },
            ],
            'status' => 'required|in:active,inactive',
        ], [
            'username.regex' => 'Логин может содержать только латинские буквы и цифры.',
            'username.min' => 'Логин должен быть не менее 6 символов.',
        ]);
        try {
            $user->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'role' => $validated['role'],
                'status' => $validated['status'],
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно обновлён',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении пользователя: ' . $e->getMessage()
            ], 500);
        }
    }
} 