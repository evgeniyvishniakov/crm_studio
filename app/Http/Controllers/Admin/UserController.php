<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('id')->get();
        $projects = \App\Models\Admin\Project::orderBy('project_name')->get();
        return view('admin.users.index', compact('users', 'projects'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);
        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'Пользователь обновлён');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Пользователь удалён');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin_users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string',
            'status' => 'required|string',
            'project_id' => 'required|exists:projects,id',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'registered_at' => now(),
            'project_id' => $validated['project_id'],
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Пользователь создан',
            'user' => $user
        ]);
    }
} 