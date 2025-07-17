<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $login = $credentials['login'];
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::guard('client')->attempt([
            $field => $login,
            'password' => $credentials['password'],
        ], $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('client')->user();
            $isAdmin = $user->role === 'admin';
            $permissions = $isAdmin ? [
                'dashboard', 'clients', 'appointments', 'analytics', 'warehouse', 'purchases', 'sales', 'expenses', 'inventory',
                'services', 'products', 'product-categories', 'product-brands', 'suppliers', 'client-types',
                'client.users', 'roles', 'settings', 'email-templates', 'security',
            ] : $user->permissions()->pluck('name')->toArray();

            // Порядок пунктов меню (route name => permission)
            $menu = [
                'dashboard' => 'dashboard',
                'clients.list' => 'clients',
                'appointments.index' => 'appointments',
                'reports.clients.index' => 'analytics',
                'warehouses.index' => 'warehouse',
                'purchases.index' => 'purchases',
                'sales.index' => 'sales',
                'expenses.index' => 'expenses',
                'inventories.index' => 'inventory',
                'reports.turnover' => 'analytics',
                'services.index' => 'services',
                'products.index' => 'products',
                'product-categories.index' => 'product-categories',
                'product-brands.index' => 'product-brands',
                'suppliers.index' => 'suppliers',
                'client-types.index' => 'client-types',
                'client.users.index' => 'client.users',
                'roles.index' => 'roles',
                'admin.settings.index' => 'settings',
                'admin.email-templates.index' => 'email-templates',
                'admin.security.index' => 'security',
            ];

            foreach ($menu as $route => $perm) {
                if ($isAdmin || in_array($perm, $permissions)) {
                    return redirect()->intended(route($route));
                }
            }
            // Если нет ни одного права — fallback
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'login' => 'Неверный email/логин или пароль.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
} 