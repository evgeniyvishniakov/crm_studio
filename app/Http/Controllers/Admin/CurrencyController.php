<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    /**
     * Показать список валют
     */
    public function index()
    {
        $currencies = Currency::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.currencies.index', compact('currencies'));
    }

    /**
     * Показать форму создания валюты
     */
    public function create()
    {
        return view('admin.currencies.create');
    }

    /**
     * Сохранить новую валюту
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:4',
            'decimal_separator' => 'required|string|max:1',
            'thousands_separator' => 'required|string|max:1',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $currency = Currency::create($request->all());

        if ($request->boolean('is_default')) {
            $currency->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Валюта успешно добавлена',
            'currency' => $currency
        ]);
    }

    /**
     * Показать форму редактирования валюты
     */
    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    /**
     * Получить данные валюты для редактирования (AJAX)
     */
    public function getCurrencyData(Currency $currency): JsonResponse
    {
        return response()->json([
            'success' => true,
            'currency' => $currency
        ]);
    }

    /**
     * Обновить валюту
     */
    public function update(Request $request, Currency $currency): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:4',
            'decimal_separator' => 'required|string|max:1',
            'thousands_separator' => 'required|string|max:1',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $currency->update($request->all());

        if ($request->boolean('is_default')) {
            $currency->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Валюта успешно обновлена',
            'currency' => $currency
        ]);
    }

    /**
     * Удалить валюту
     */
    public function destroy(Currency $currency): JsonResponse
    {
        // Нельзя удалить валюту по умолчанию
        if ($currency->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить валюту по умолчанию'
            ], 400);
        }

        $currency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Валюта успешно удалена'
        ]);
    }

    /**
     * Установить валюту по умолчанию
     */
    public function setDefault(Currency $currency): JsonResponse
    {
        $currency->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Валюта установлена по умолчанию'
        ]);
    }

    /**
     * Переключить активность валюты
     */
    public function toggleActive(Currency $currency): JsonResponse
    {
        // Нельзя деактивировать валюту по умолчанию
        if ($currency->is_default && !$currency->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя деактивировать валюту по умолчанию'
            ], 400);
        }

        $currency->update(['is_active' => !$currency->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Статус валюты изменен',
            'is_active' => $currency->is_active
        ]);
    }

    /**
     * Очистить кэш валют
     */
    public function clearCache(): JsonResponse
    {
        // Очищаем кэш валют в сессиях
        \Illuminate\Support\Facades\Cache::forget('currencies');
        
        // Можно также очистить кэш в Redis/Memcached если используется
        if (config('cache.default') !== 'file') {
            \Illuminate\Support\Facades\Cache::tags(['currencies'])->flush();
        }

        return response()->json([
            'success' => true,
            'message' => 'Кэш валют очищен'
        ]);
    }
} 