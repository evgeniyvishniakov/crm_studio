<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    /**
     * Показать список языков
     */
    public function index()
    {
        $languages = Language::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.languages.index', compact('languages'));
    }

    /**
     * Сохранить новый язык
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:5|unique:languages,code',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'flag' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $language = Language::create($request->all());

        if ($request->boolean('is_default')) {
            $language->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Язык успешно добавлен',
            'language' => $language
        ]);
    }

    /**
     * Получить данные языка для редактирования (AJAX)
     */
    public function getLanguageData(Language $language): JsonResponse
    {
        return response()->json([
            'success' => true,
            'language' => $language
        ]);
    }

    /**
     * Обновить язык
     */
    public function update(Request $request, Language $language): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:5|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'flag' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $language->update($request->all());

        if ($request->boolean('is_default')) {
            $language->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Язык успешно обновлен',
            'language' => $language
        ]);
    }

    /**
     * Удалить язык
     */
    public function destroy(Language $language): JsonResponse
    {
        if ($language->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить язык по умолчанию'
            ], 400);
        }

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Язык успешно удален'
        ]);
    }

    /**
     * Установить язык по умолчанию
     */
    public function setDefault(Language $language): JsonResponse
    {
        $language->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Язык установлен по умолчанию'
        ]);
    }

    /**
     * Переключить активность языка
     */
    public function toggleActive(Language $language): JsonResponse
    {
        if ($language->is_default && $language->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя деактивировать язык по умолчанию'
            ], 400);
        }

        $language->update(['is_active' => !$language->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Статус языка изменен'
        ]);
    }

    /**
     * Очистить кэш переводов
     */
    public function clearCache(): JsonResponse
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        return response()->json([
            'success' => true,
            'message' => 'Кэш переводов очищен'
        ]);
    }
} 