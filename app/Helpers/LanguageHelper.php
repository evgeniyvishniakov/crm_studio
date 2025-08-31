<?php

namespace App\Helpers;

use App\Models\Language;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageHelper
{
    /**
     * Добавляет текущий язык к URL
     */
    public static function addLanguageToUrl($url, $language = null)
    {
        if (!$language) {
            $language = session('language', 'ua');
        }
        
        // Если URL уже содержит параметр lang, заменяем его
        if (strpos($url, '?') !== false) {
            $url = preg_replace('/[?&]lang=[^&]*/', '', $url);
            $url .= '&lang=' . $language;
        } else {
            $url .= '?lang=' . $language;
        }
        
        return $url;
    }
    
    /**
     * Получает текущий язык из сессии или по умолчанию
     */
    public static function getCurrentLanguage()
    {
        return session('language', 'ua');
    }

    /**
     * Получить язык проекта (для отладки)
     */
    public static function getProjectLanguage()
    {
        if (auth('client')->check()) {
            $user = auth('client')->user();
            if ($user && $user->project_id) {
                $project = \App\Models\Admin\Project::with('language')->find($user->project_id);
                return $project && $project->language ? $project->language->code : null;
            }
        }
        return null;
    }

    /**
     * Установить язык в сессию
     */
    public static function setLanguage($language)
    {
        if (is_string($language)) {
            $languageModel = Language::getByCode($language);
            if ($languageModel) {
                Session::put('language', $language);
                App::setLocale($language);
                
                // Очищаем кэш переводов
                if (function_exists('cache')) {
                    cache()->forget('translations.' . $language);
                }
                
                return true;
            }
        } elseif ($language instanceof Language) {
            Session::put('language', $language->code);
            App::setLocale($language->code);
            
            // Очищаем кэш переводов
            if (function_exists('cache')) {
                cache()->forget('translations.' . $language->code);
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Получить данные языка для JavaScript
     */
    public static function getLanguageData()
    {
        $currentLanguage = self::getCurrentLanguage();
        $defaultLanguage = Language::getDefault();
        $languages = Language::getActive();

        $data = [];
        foreach ($languages as $language) {
            $data[] = [
                'id' => $language->id,
                'code' => $language->code,
                'name' => $language->name,
                'native_name' => $language->native_name,
                'flag' => $language->flag_url,
                'is_default' => $language->is_default,
                'is_active' => $language->is_active,
            ];
        }

        return [
            'languages' => $data,
            'current' => $currentLanguage,
            'default' => $defaultLanguage ? $defaultLanguage->code : 'ru',
        ];
    }

    /**
     * Получить текущий язык как объект
     */
    public static function getCurrentLanguageModel()
    {
        $code = self::getCurrentLanguage();
        return Language::getByCode($code);
    }

    /**
     * Синхронизировать язык между лендингом и CRM
     */
    public static function syncLanguage($languageCode)
    {
        $language = self::getByCode($languageCode);
        if ($language) {
            // Устанавливаем язык в сессию
            self::setLanguage($language);
            
            // Если пользователь авторизован в CRM, обновляем язык в проекте
            if (auth('client')->check()) {
                $user = auth('client')->user();
                if ($user && $user->project_id) {
                    $project = \App\Models\Admin\Project::find($user->project_id);
                    if ($project) {
                        $project->update(['language_id' => $language->id]);
                    }
                }
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Получить название текущего языка
     */
    public static function getCurrentLanguageName()
    {
        $language = self::getCurrentLanguageModel();
        return $language ? $language->name : 'Русский';
    }

    /**
     * Получить родное название текущего языка
     */
    public static function getCurrentLanguageNativeName()
    {
        $language = self::getCurrentLanguageModel();
        return $language ? $language->native_name : 'Русский';
    }

    /**
     * Получить флаг текущего языка
     */
    public static function getCurrentLanguageFlag()
    {
        $language = self::getCurrentLanguageModel();
        return $language ? $language->flag_url : asset('images/flags/default.png');
    }

    /**
     * Проверить, является ли язык активным
     */
    public static function isLanguageActive($code)
    {
        $language = Language::getByCode($code);
        return $language && $language->is_active;
    }

    /**
     * Получить все активные языки
     */
    public static function getActiveLanguages()
    {
        return Language::getActive();
    }

    /**
     * Получить язык по умолчанию
     */
    public static function getDefaultLanguage()
    {
        return Language::getDefault();
    }
} 