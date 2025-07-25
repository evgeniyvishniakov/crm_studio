<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin\Project;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Получаем язык из проекта пользователя
        if (Auth::check()) {
            $user = Auth::user();
            $project = Project::where('id', $user->project_id ?? null)->first();
            
            if ($project && $project->language_id) {
                // Получаем язык по ID
                $language = Language::find($project->language_id);
                if ($language && $language->is_active) {
                    // Устанавливаем локаль приложения
                    App::setLocale($language->code);
                } else {
                    // По умолчанию украинский
                    App::setLocale('ua');
                }
            } else {
                // По умолчанию украинский
                App::setLocale('ua');
            }
        } else {
            // Для неавторизованных пользователей
            App::setLocale('ua');
        }

        return $next($request);
    }
}
