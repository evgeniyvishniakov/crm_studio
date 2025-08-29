<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Helpers\LanguageHelper;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    /**
     * Получить все активные языки
     */
    public function index(): JsonResponse
    {
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

        // Получаем язык проекта
        $projectLanguage = null;
        if (auth('client')->check()) {
            $user = auth('client')->user();
            if ($user && $user->project_id) {
                $project = \App\Models\Admin\Project::with('language')->find($user->project_id);
                $projectLanguage = $project && $project->language ? $project->language->code : null;
            }
        }

        // Определяем текущий язык с приоритетом: проект > сессия > по умолчанию
        $currentLanguage = $projectLanguage ?: session('language') ?: Language::getDefault()?->code ?: 'ru';

        return response()->json([
            'success' => true,
            'languages' => $data,
            'current' => $currentLanguage,
            'default' => Language::getDefault()?->code ?: 'ru',
        ]);
    }

    /**
     * Получить текущий язык
     */
    public function current(): JsonResponse
    {
        $currentLanguage = LanguageHelper::getCurrentLanguage();
        $language = Language::getByCode($currentLanguage);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Язык не найден'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'language' => [
                'id' => $language->id,
                'code' => $language->code,
                'name' => $language->name,
                'native_name' => $language->native_name,
                'flag' => $language->flag_url,
                'is_default' => $language->is_default,
                'is_active' => $language->is_active,
            ]
        ]);
    }

    /**
     * Установить язык
     */
    public function setLanguage(string $code): JsonResponse
    {
        $language = Language::getByCode($code);
        
        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Язык не найден'
            ], 404);
        }

        if (!$language->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Язык неактивен'
            ], 400);
        }

        // Устанавливаем язык в сессию
        LanguageHelper::setLanguage($language);

        // Если пользователь авторизован, обновляем язык в проекте
        if (auth('client')->check()) {
            $user = auth('client')->user();
            if ($user && $user->project_id) {
                $project = \App\Models\Admin\Project::find($user->project_id);
                if ($project) {
                    $language = Language::getByCode($code);
                    if ($language) {
                        $project->update(['language_id' => $language->id]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'language' => [
                'id' => $language->id,
                'code' => $language->code,
                'name' => $language->name,
                'native_name' => $language->native_name,
                'flag' => $language->flag_url,
            ]
        ]);
    }

    /**
     * Получить переводы для текущего языка
     */
    public function translations(): JsonResponse
    {
        $currentLanguage = LanguageHelper::getCurrentLanguage();
        
        // Получаем переводы для лендинга
        $landingTranslations = trans('landing');
        
        // Получаем общие переводы
        $messagesTranslations = trans('messages');

        return response()->json([
            'success' => true,
            'language' => $currentLanguage,
            'translations' => array_merge($landingTranslations, $messagesTranslations)
        ]);
    }
} 