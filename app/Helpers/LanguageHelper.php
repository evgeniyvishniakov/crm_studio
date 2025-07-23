<?php

namespace App\Helpers;

use App\Models\Language;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageHelper
{
    /**
     * Получить текущий язык из сессии, проекта или по умолчанию
     */
    public static function getCurrentLanguage()
    {
        // Сначала проверяем язык проекта (если пользователь авторизован)
        if (auth('client')->check()) {
            $user = auth('client')->user();
            if ($user && $user->project_id) {
                $project = \App\Models\Admin\Project::with('language')->find($user->project_id);
                if ($project && $project->language) {
                    $language = $project->language;
                    if ($language && $language->is_active) {
                        return $language->code;
                    }
                }
            }
        }

        // Затем проверяем сессию
        $sessionLanguage = Session::get('language');
        if ($sessionLanguage) {
            return $sessionLanguage;
        }

        // Наконец, возвращаем язык по умолчанию
        $default = Language::getDefault();
        return $default ? $default->code : 'ru';
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
                return true;
            }
        } elseif ($language instanceof Language) {
            Session::put('language', $language->code);
            App::setLocale($language->code);
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