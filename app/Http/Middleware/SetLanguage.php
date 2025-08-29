<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin\Project;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('SetLanguage middleware started', [
            'url' => $request->url(),
            'method' => $request->method(),
            'has_lang_param' => $request->has('lang'),
            'lang_param' => $request->get('lang'),
            'session_language' => session('language'),
            'current_locale' => App::getLocale(),
            'session_id' => session()->getId()
        ]);

        // Сначала проверяем параметр lang из URL (для лендинга)
        if ($request->has('lang')) {
            $langCode = $request->get('lang');
            Log::info('Found lang parameter', ['lang_code' => $langCode]);
            
            $language = Language::where('code', $langCode)->where('is_active', true)->first();
            Log::info('Language query result', [
                'lang_code' => $langCode,
                'language_found' => $language ? $language->code : 'null',
                'is_active' => $language ? $language->is_active : 'null',
                'language_object' => $language ? $language->toArray() : 'null'
            ]);
            
            if ($language) {
                // Сохраняем выбранный язык в сессию
                session(['language' => $language->code]);
                App::setLocale($language->code);
                
                Log::info('Language set from URL parameter', [
                    'lang_code' => $language->code,
                    'session_language' => session('language'),
                    'app_locale' => App::getLocale(),
                    'session_id' => session()->getId()
                ]);
                
                return $next($request);
            } else {
                Log::warning('Language not found or not active', ['lang_code' => $langCode]);
            }
        }
        
        // Затем ВСЕГДА проверяем язык из сессии (даже если параметр lang не указан)
        if (session()->has('language')) {
            $langCode = session('language');
            Log::info('Found language in session', ['session_lang' => $langCode]);
            
            $language = Language::where('code', $langCode)->where('is_active', true)->first();
            if ($language) {
                App::setLocale($language->code);
                
                Log::info('Language set from session', [
                    'lang_code' => $language->code,
                    'app_locale' => App::getLocale()
                ]);
                
                return $next($request);
            } else {
                Log::warning('Session language not found or not active', ['session_lang' => $langCode]);
            }
        }
        
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

        Log::info('Language set to default', [
            'final_locale' => App::getLocale(),
            'session_language' => session('language'),
            'url' => $request->url()
        ]);

        return $next($request);
    }
}
