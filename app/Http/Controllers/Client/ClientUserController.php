<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\User;
use App\Models\SystemLog;

class ClientUserController extends Controller
{
    public function index(Request $request)
    {
        $projectId = auth('client')->user()->project_id;
        $users = User::where('project_id', $projectId)->orderBy('id', 'asc')->get();
        $roles = \DB::table('roles')
            ->where('project_id', $projectId)
            ->where('name', '!=', 'admin')
            ->pluck('label', 'name');
        return view('client.users.list', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $projectId = auth('client')->user()->project_id;
        $user = auth('client')->user();
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'username.regex' => 'Логин может содержать только латинские буквы и цифры.',
            'username.min' => 'Логин должен быть не менее 6 символов.',
        ]);
        try {
            $userData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
                'project_id' => $projectId,
                'registered_at' => now(),
                'status' => 'active',
            ];

            // Обработка загрузки аватарки
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = 'avatar_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('avatars', $fileName, 'public');
                $userData['avatar'] = $path;
            }

            $userModel = User::create($userData);
            
            // Логирование создания пользователя (если таблица существует)
            try {
                SystemLog::create([
                    'level' => 'info',
                    'module' => 'users',
                    'user_email' => $user->email ?? null,
                    'user_id' => $user->id ?? null,
                    'ip' => $request->ip(),
                    'action' => 'create_user',
                    'message' => 'Создан пользователь: ' . $userModel->name,
                    'context' => json_encode([
                        'user_id' => $userModel->id,
                        'name' => $userModel->name,
                        'username' => $userModel->username,
                        'email' => $userModel->email,
                        'role' => $userModel->role,
                        'project_id' => $userModel->project_id,
                        'avatar' => $userModel->avatar,
                    ]),
                ]);
            } catch (\Exception $logError) {
                // Игнорируем ошибки логирования
            }
            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно добавлен',
                'user' => $userModel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении пользователя: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $projectId = auth('client')->user()->project_id;
        $user = auth('client')->user();
        $userModel = User::where('project_id', $projectId)->findOrFail($id);
        // Запрещаем удаление пользователей с уникальной ролью admin
        if (in_array($userModel->role, User::FIXED_ROLES)) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь с ролью Администратор не может быть удалён.'
            ], 403);
        }
        try {
            $userModel->delete();
            
            // Логирование удаления пользователя (если таблица существует)
            try {
                SystemLog::create([
                    'level' => 'warning',
                    'module' => 'users',
                    'user_email' => $user->email ?? null,
                    'user_id' => $user->id ?? null,
                    'ip' => $request->ip(),
                    'action' => 'delete_user',
                    'message' => 'Удалён пользователь: ' . $userModel->name,
                    'context' => json_encode([
                        'user_id' => $userModel->id,
                        'name' => $userModel->name,
                        'username' => $userModel->username,
                        'email' => $userModel->email,
                        'role' => $userModel->role,
                        'project_id' => $userModel->project_id,
                    ]),
                ]);
            } catch (\Exception $logError) {
                // Игнорируем ошибки логирования
            }
            
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

    public function check($id)
    {
        $projectId = auth('client')->user()->project_id;
        $user = User::where('project_id', $projectId)->find($id);
        
        if (!$user) {
            return response()->json(['exists' => false], 404);
        }
        
        return response()->json(['exists' => true]);
    }

    public function update(Request $request, $id)
    {
        $projectId = auth('client')->user()->project_id;
        $user = auth('client')->user();
        $userModel = User::where('project_id', $projectId)->findOrFail($id);
        // Запрещаем любые изменения для пользователей с уникальной ролью admin
        if (in_array($userModel->role, User::FIXED_ROLES)) {
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
                function ($attribute, $value, $fail) use ($userModel) {
                    // Запрещаем назначать роль admin кому-либо, кроме уже существующего admin
                    if (in_array(strtolower($value), User::FIXED_ROLES) && $userModel->role !== 'admin') {
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
            $userModel->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'role' => $validated['role'],
                'status' => $validated['status'],
            ]);
            // Логирование обновления пользователя (если таблица существует)
            try {
                SystemLog::create([
                    'level' => 'info',
                    'module' => 'users',
                    'user_email' => $user->email ?? null,
                    'user_id' => $user->id ?? null,
                    'ip' => $request->ip(),
                    'action' => 'update_user',
                    'message' => 'Изменён пользователь: ' . $userModel->name,
                    'context' => json_encode([
                        'user_id' => $userModel->id,
                        'name' => $userModel->name,
                        'username' => $userModel->username,
                        'email' => $userModel->email,
                        'role' => $userModel->role,
                        'status' => $userModel->status,
                        'project_id' => $userModel->project_id,
                    ]),
                ]);
            } catch (\Exception $logError) {
                // Игнорируем ошибки логирования
            }
            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно обновлён',
                'user' => $userModel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении пользователя: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadAvatar(Request $request, $id)
    {
        $projectId = auth('client')->user()->project_id;
        $user = auth('client')->user();
        $userModel = User::where('project_id', $projectId)->findOrFail($id);
        
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = 'avatar_' . $userModel->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('avatars', $fileName, 'public');
                
                // Удаляем старую аватарку если есть
                if ($userModel->avatar && \Storage::disk('public')->exists($userModel->avatar)) {
                    \Storage::disk('public')->delete($userModel->avatar);
                }
                
                $userModel->update(['avatar' => $path]);
                
                // Логирование загрузки аватарки (если таблица существует)
                try {
                    SystemLog::create([
                        'level' => 'info',
                        'module' => 'users',
                        'user_email' => $user->email ?? null,
                        'user_id' => $user->id ?? null,
                        'ip' => $request->ip(),
                        'action' => 'upload_avatar',
                        'message' => 'Загружена аватарка для пользователя: ' . $userModel->name,
                        'context' => json_encode([
                            'user_id' => $userModel->id,
                            'name' => $userModel->name,
                            'avatar_path' => $path,
                        ]),
                    ]);
                } catch (\Exception $logError) {
                    // Игнорируем ошибки логирования
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Аватарка успешно загружена',
                    'avatar_url' => \Storage::disk('public')->url($path)
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Файл не найден'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке аватарки: ' . $e->getMessage()
            ], 500);
        }
    }
} 