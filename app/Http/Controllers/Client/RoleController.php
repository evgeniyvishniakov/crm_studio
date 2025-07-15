<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('client')->check()) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('client.login'); // замените на ваш маршрут логина, если другой
        }
        $projectId = auth('client')->user()->project_id;
        $roles = DB::table('roles')
            ->where('project_id', $projectId)
            ->get();
        $permissions = DB::table('permissions')->get();
        $rolePermissions = DB::table('role_permission')->get();
        if ($request->ajax()) {
            // Вернуть JSON для фронта
            $rolesArr = $roles->map(function($role) use ($rolePermissions, $permissions) {
                $perms = $rolePermissions->where('role_id', $role->id)->pluck('permission_id')->toArray();
                $permNames = $permissions->whereIn('id', $perms)->pluck('name')->toArray();
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'label' => $role->label,
                    'is_system' => $role->is_system,
                    'permissions' => $permNames,
                ];
            });
            return response()->json([
                'roles' => $rolesArr,
                'permissions' => $permissions
            ]);
        }
        return view('client.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $projectId = auth('client')->user()->project_id;
        $validated = $request->validate([
            'name' => 'required|string|max:64',
            'label' => 'required|string|max:128',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
        // Если выбрана роль, но не выбраны доступы
        if (!isset($validated['permissions']) || empty($validated['permissions'])) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо выбрать хотя бы один доступ.'
            ], 422);
        }
        DB::beginTransaction();
        try {
            $roleId = DB::table('roles')->insertGetId([
                'project_id' => $projectId,
                'name' => $validated['name'],
                'label' => $validated['label'],
                'is_system' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $permissionIds = DB::table('permissions')->whereIn('name', $validated['permissions'] ?? [])->pluck('id');
            foreach ($permissionIds as $pid) {
                DB::table('role_permission')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $pid
                ]);
            }
            // Получаем имена permissions для ответа
            $permNames = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
            DB::commit();
            return response()->json([
                'success' => true,
                'role' => [
                    'id' => $roleId,
                    'name' => $validated['name'],
                    'label' => $validated['label'],
                    'is_system' => 0,
                    'permissions' => $permNames,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Обработка дубликата роли
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Роль с таким названием уже существует в этом проекте.'
                ], 422);
            }
            return response()->json(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $projectId = auth('client')->user()->project_id;
        $validated = $request->validate([
            'label' => 'required|string|max:128',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
        // Если выбрана роль, но не выбраны доступы
        if (!isset($validated['permissions']) || empty($validated['permissions'])) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо выбрать хотя бы один доступ.'
            ], 422);
        }
        DB::beginTransaction();
        try {
            $role = DB::table('roles')->where('id', $id)->where('project_id', $projectId)->first();
            if (!$role || $role->is_system) {
                return response()->json(['success' => false, 'message' => 'Системную роль нельзя редактировать'], 403);
            }
            DB::table('roles')->where('id', $id)->update([
                'label' => $validated['label'],
                'updated_at' => now(),
            ]);
            // Обновить доступы
            DB::table('role_permission')->where('role_id', $id)->delete();
            $permissionIds = DB::table('permissions')->whereIn('name', $validated['permissions'] ?? [])->pluck('id');
            foreach ($permissionIds as $pid) {
                DB::table('role_permission')->insert([
                    'role_id' => $id,
                    'permission_id' => $pid
                ]);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'role' => [
                    'id' => $id,
                    'name' => $role->name,
                    'label' => $validated['label'],
                    'is_system' => $role->is_system,
                    'permissions' => $validated['permissions'],
                ],
                'message' => 'Роль обновлена'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Обработка дубликата роли
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Роль с таким названием уже существует в этом проекте.'
                ], 422);
            }
            return response()->json(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $projectId = auth('client')->user()->project_id;
        $role = DB::table('roles')->where('id', $id)->where('project_id', $projectId)->first();
        if (!$role || $role->is_system) {
            return response()->json(['success' => false, 'message' => 'Системную роль нельзя удалить'], 403);
        }
        DB::beginTransaction();
        try {
            DB::table('role_permission')->where('role_id', $id)->delete();
            DB::table('roles')->where('id', $id)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Роль удалена']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $projectId = auth('client')->user()->project_id;
        $role = DB::table('roles')->where('id', $id)->where('project_id', $projectId)->first();
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Роль не найдена'], 404);
        }
        $permissionIds = DB::table('role_permission')->where('role_id', $role->id)->pluck('permission_id')->toArray();
        $permissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
        return response()->json([
            'success' => true,
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'label' => $role->label,
                'is_system' => $role->is_system,
                'permissions' => $permissions,
            ]
        ]);
    }
}
