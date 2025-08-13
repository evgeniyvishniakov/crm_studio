<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LandingAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем аутентификацию через guard 'client'
        if (!Auth::guard('client')->check()) {
            return redirect()->route('landing.account.login');
        }

        // Проверяем, что пользователь имеет роль 'admin'
        $user = Auth::guard('client')->user();
        if ($user->role !== 'admin') {
            Auth::guard('client')->logout();
            return redirect()->route('landing.account.login')
                ->withErrors(['email' => 'Доступ к личному кабинету только для руководителей проектов']);
        }

        // Проверяем, что у пользователя есть проект
        if (!$user->project_id) {
            Auth::guard('client')->logout();
            return redirect()->route('landing.account.login')
                ->withErrors(['email' => 'Пользователь не привязан к проекту']);
        }

        return $next($request);
    }
}
