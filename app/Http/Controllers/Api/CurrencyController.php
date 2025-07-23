<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    /**
     * Получить все активные валюты
     */
    public function index(): JsonResponse
    {
        $currencies = Currency::getActive();
        
        $data = [];
        foreach ($currencies as $currency) {
            $data[] = [
                'id' => $currency->id,
                'code' => $currency->code,
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'symbol_position' => $currency->symbol_position,
                'decimal_places' => $currency->decimal_places,
                'decimal_separator' => $currency->decimal_separator,
                'thousands_separator' => $currency->thousands_separator,
                'is_default' => $currency->is_default,
                'is_active' => $currency->is_active,
            ];
        }

        // Получаем валюту проекта
        $projectCurrency = null;
        if (auth('client')->check()) {
            $user = auth('client')->user();
            if ($user && $user->project_id) {
                $project = \App\Models\Admin\Project::with('currency')->find($user->project_id);
                $projectCurrency = $project && $project->currency ? $project->currency->code : null;
            }
        }

        // Определяем текущую валюту с приоритетом: проект > сессия > по умолчанию
        $currentCurrency = $projectCurrency ?: session('currency') ?: Currency::getDefault()?->code ?: 'UAH';

        // Отладочная информация
        \Log::info('API Currency Debug', [
            'user_id' => auth('client')->id(),
            'project_id' => auth('client')->user()?->project_id,
            'project_currency' => $projectCurrency,
            'session_currency' => session('currency'),
            'current_currency' => $currentCurrency,
            'default_currency' => Currency::getDefault()?->code
        ]);

        return response()->json([
            'success' => true,
            'currencies' => $data,
            'current' => $currentCurrency,
            'default' => Currency::getDefault()?->code,
            'project_currency' => $projectCurrency
        ]);
    }

    /**
     * Получить текущую валюту
     */
    public function current(): JsonResponse
    {
        $currentCode = CurrencyHelper::getCurrentCurrency();
        $currency = Currency::getByCode($currentCode);
        
        if (!$currency) {
            // Fallback на валюту по умолчанию
            $currency = Currency::getDefault();
            if (!$currency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Валюта не найдена'
                ], 404);
            }
        }

        return response()->json([
            'success' => true,
            'currency' => [
                'code' => $currency->code,
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'symbol_position' => $currency->symbol_position,
                'decimal_places' => $currency->decimal_places,
                'decimal_separator' => $currency->decimal_separator,
                'thousands_separator' => $currency->thousands_separator,
                'is_default' => $currency->is_default,
            ]
        ]);
    }

    /**
     * Установить валюту в сессии
     */
    public function setCurrency(string $code): JsonResponse
    {
        $currency = Currency::getByCode($code);
        
        if (!$currency || !$currency->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Валюта не найдена или неактивна'
            ], 404);
        }

        CurrencyHelper::setCurrency($code);
        // Также устанавливаем в сессию напрямую
        session(['currency' => $code]);

        return response()->json([
            'success' => true,
            'currency' => [
                'code' => $currency->code,
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'symbol_position' => $currency->symbol_position,
                'decimal_places' => $currency->decimal_places,
                'decimal_separator' => $currency->decimal_separator,
                'thousands_separator' => $currency->thousands_separator,
            ]
        ]);
    }

    /**
     * Форматировать сумму
     */
    public function formatAmount(float $amount, string $code = null): JsonResponse
    {
        if ($code) {
            $currency = Currency::getByCode($code);
            if (!$currency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Валюта не найдена'
                ], 404);
            }
            $formatted = $currency->formatAmount($amount);
        } else {
            $formatted = CurrencyHelper::format($amount);
        }

        return response()->json([
            'success' => true,
            'formatted' => $formatted,
            'amount' => $amount
        ]);
    }
} 