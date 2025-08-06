<?php

namespace App\Helpers;

use App\Models\Currency;
use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    /**
     * Получить текущую валюту из сессии, проекта или по умолчанию
     */
    public static function getCurrentCurrency()
    {
        // Сначала проверяем валюту проекта (если пользователь авторизован)
        if (auth('client')->check()) {
            $user = auth('client')->user();
            if ($user && $user->project_id) {
                $project = \App\Models\Admin\Project::with('currency')->find($user->project_id);
                if ($project && $project->currency && $project->currency->is_active) {
                    return $project->currency->code;
                }
            }
        }

        // Затем проверяем сессию
        $sessionCurrency = Session::get('currency');
        if ($sessionCurrency) {
            return $sessionCurrency;
        }

        // Наконец, возвращаем валюту по умолчанию
        $default = Currency::getDefault();
        return $default ? $default->code : 'UAH';
    }

    /**
     * Получить валюту проекта (для отладки)
     */
    public static function getProjectCurrency()
    {
        if (auth('client')->check()) {
            $user = auth('client')->user();
            if ($user && $user->project_id) {
                $project = \App\Models\Admin\Project::with('currency')->find($user->project_id);
                return $project && $project->currency ? $project->currency->code : null;
            }
        }
        return null;
    }

    /**
     * Получить символ валюты
     */
    public static function getSymbol($currency = null)
    {
        if ($currency === null) {
            $currency = self::getCurrentCurrency();
        }

        $currencyModel = Currency::getByCode($currency);
        if ($currencyModel) {
            return $currencyModel->symbol;
        }

        // Fallback для старых валют
        $symbols = [
            'UAH' => '₴',
            'USD' => '$',
            'EUR' => '€',
        ];

        return $symbols[$currency] ?? '₴';
    }

    /**
     * Получить полное название валюты
     */
    public static function getName($currency = null)
    {
        if ($currency === null) {
            $currency = self::getCurrentCurrency();
        }

        $currencyModel = Currency::getByCode($currency);
        if ($currencyModel) {
            return $currencyModel->name;
        }

        // Fallback для старых валют
        $names = [
            'UAH' => 'Украинская гривна',
            'USD' => 'Доллар США',
            'EUR' => 'Евро',
        ];

        return $names[$currency] ?? 'Украинская гривна';
    }

    /**
     * Форматировать сумму с валютой
     */
    public static function format($amount, $currency = null)
    {
        if ($currency === null) {
            $currency = self::getCurrentCurrency();
        }

        $currencyModel = Currency::getByCode($currency);
        if ($currencyModel) {
            return $currencyModel->formatAmount($amount);
        }

        // Fallback для старых валют
        $symbol = self::getSymbol($currency);
        // Если число целое, не показываем десятичные знаки
        $decimalPlaces = (floor($amount) == $amount) ? 0 : 2;
        $formattedAmount = number_format($amount, $decimalPlaces, '.', ' ');
        return $formattedAmount . ' ' . $symbol;
    }

    /**
     * Форматировать сумму с валютой без разделителей тысяч
     */
    public static function formatWithoutThousands($amount, $currency = null)
    {
        if ($currency === null) {
            $currency = self::getCurrentCurrency();
        }

        $currencyModel = Currency::getByCode($currency);
        if ($currencyModel) {
            return $currencyModel->formatAmountWithoutThousands($amount);
        }

        // Fallback для старых валют
        $symbol = self::getSymbol($currency);
        // Если число целое, не показываем десятичные знаки
        $decimalPlaces = (floor($amount) == $amount) ? 0 : 2;
        $formattedAmount = number_format($amount, $decimalPlaces, '.', '');
        return $formattedAmount . ' ' . $symbol;
    }

    /**
     * Форматировать сумму с валютой (для отладки)
     */
    public static function formatDebug($amount, $currency = null)
    {
        if ($currency === null) {
            $currency = self::getCurrentCurrency();
        }

        $currencyModel = Currency::getByCode($currency);
        
        $debug = [
            'amount' => $amount,
            'currency_code' => $currency,
            'currency_model' => $currencyModel ? 'найден' : 'не найден',
            'symbol' => self::getSymbol($currency),
            'result' => ''
        ];

        if ($currencyModel) {
            $debug['result'] = $currencyModel->formatAmount($amount);
        } else {
            $symbol = self::getSymbol($currency);
            $formattedAmount = number_format($amount, 0, '.', ' ');
            $debug['result'] = $formattedAmount . ' ' . $symbol;
        }

        return $debug;
    }

    /**
     * Получить все доступные валюты
     */
    public static function getAvailableCurrencies()
    {
        $currencies = Currency::getActive();
        $result = [];
        
        foreach ($currencies as $currency) {
            $result[$currency->code] = $currency->code . ' (' . $currency->symbol . ')';
        }

        // Fallback для старых валют, если нет в базе
        if (empty($result)) {
            $result = [
                'UAH' => 'UAH (₴)',
                'USD' => 'USD ($)',
                'EUR' => 'EUR (€)',
            ];
        }

        return $result;
    }

    /**
     * Установить валюту в сессию
     */
    public static function setCurrency($currency)
    {
        Session::put('currency', $currency);
    }

    /**
     * Получить данные валюты для JavaScript
     */
    public static function getCurrencyData()
    {
        $current = self::getCurrentCurrency();
        $currencyModel = Currency::getByCode($current);
        
        $data = [
            'current' => $current,
            'symbol' => self::getSymbol($current),
            'available' => self::getAvailableCurrencies()
        ];

        // Добавляем настройки форматирования, если есть модель валюты
        if ($currencyModel) {
            $data['formatting'] = [
                'symbol_position' => $currencyModel->symbol_position,
                'decimal_places' => $currencyModel->decimal_places,
                'decimal_separator' => $currencyModel->decimal_separator,
                'thousands_separator' => $currencyModel->thousands_separator,
            ];
        }

        return $data;
    }
} 