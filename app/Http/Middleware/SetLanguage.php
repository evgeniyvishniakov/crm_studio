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
            'session_id' => session()->getId(),
            'user_agent' => $request->userAgent(),
            'is_landing' => str_contains($request->url(), '/beautyflow'),
            'is_crm' => str_contains($request->url(), '/') && !str_contains($request->url(), '/beautyflow')
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
                
                // Если пользователь авторизован в CRM, обновляем язык в проекте
                if (Auth::guard('client')->check()) {
                    $user = Auth::guard('client')->user();
                    if ($user && $user->project_id) {
                        $project = Project::find($user->project_id);
                        if ($project) {
                            $project->update(['language_id' => $language->id]);
                            Log::info('Project language updated from landing', [
                                'project_id' => $project->id,
                                'language_id' => $language->id,
                                'language_code' => $language->code
                            ]);
                            
                            // Добавляем флаг в сессию для показа уведомления
                            session(['language_changed_from_landing' => true]);
                        }
                    }
                }
                
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
                    'app_locale' => App::getLocale(),
                    'session_id' => session()->getId()
                ]);
                
                return $next($request);
            } else {
                Log::warning('Session language not found or not active', ['session_lang' => $langCode]);
                // Удаляем неактивный язык из сессии
                session()->forget('language');
            }
        }
        
        // Получаем язык из проекта пользователя (только если язык не установлен в сессии)
        if (!session()->has('language') && Auth::check()) {
            $user = Auth::user();
            $project = Project::where('id', $user->project_id ?? null)->first();
            
            if ($project && $project->language_id) {
                // Получаем язык по ID
                $language = Language::find($project->language_id);
                if ($language && $language->is_active) {
                    // Устанавливаем локаль приложения и сохраняем в сессию
                    App::setLocale($language->code);
                    session(['language' => $language->code]);
                } else {
                    // По умолчанию украинский
                    App::setLocale('ua');
                    session(['language' => 'ua']);
                }
            } else {
                // По умолчанию украинский
                App::setLocale('ua');
                session(['language' => 'ua']);
            }
        } elseif (!session()->has('language')) {
            // Для неавторизованных пользователей без языка в сессии
            App::setLocale('ua');
            session(['language' => 'ua']);
        }

        Log::info('Language set to default', [
            'final_locale' => App::getLocale(),
            'session_language' => session('language'),
            'url' => $request->url(),
            'auth_check' => Auth::check(),
            'user_project_id' => Auth::check() ? Auth::user()->project_id : null,
            'session_id' => session()->getId()
        ]);

        return $next($request);
    }
}
