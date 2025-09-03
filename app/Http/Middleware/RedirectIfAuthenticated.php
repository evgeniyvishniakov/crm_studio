<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Если это админка (guard: panel) — редирект на /panel
                if ($guard === 'panel' || $request->is('panel/*') || $request->is('panel')) {
                    return redirect('/panel');
                }
                // Если это клиентская часть — редирект на dashboard
                if ($request->is('login') || $request->is('password/*')) {
                    return redirect('/');
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}