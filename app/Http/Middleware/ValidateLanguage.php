<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Language;

class ValidateLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->route('lang');
        
        // Проверяем, существует ли язык в базе данных
        if ($lang && !Language::where('code', $lang)->where('is_active', true)->exists()) {
            // Если язык не существует, перенаправляем на fallback маршрут
            $path = $request->path();
            $path = str_replace($lang . '/', '', $path);
            
            return redirect('/' . $path);
        }
        
        return $next($request);
    }
}