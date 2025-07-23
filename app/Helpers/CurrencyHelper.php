<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    /**
     * Получить текущую валюту из сессии или по умолчанию
     */
    public static function getCurrentCurrency()
    {
        return Session::get('currency', 'UAH');
    }

    /**
     * Получить символ валюты
     */
    public static function getSymbol($currency = null)
    {
        if ($currency === null) {
            $currency = self::getCurrentCurrency();
        }

        $symbols = [
            'UAH' => 'грн',
            'USD' => '$',
            'EUR' => '€',
        ];

        return $symbols[$currency] ?? 'грн';
    }

    /**
     * Получить полное название валюты
     */
    public static function getName($currency = null)
    {
        if ($currency === null) {
            $currency = self::getCurrentCurrency();
        }

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

        $symbol = self::getSymbol($currency);
        
        // Форматируем число с разделителями тысяч
        $formattedAmount = number_format($amount, 0, '.', ' ');
        
        return $formattedAmount . ' ' . $symbol;
    }

    /**
     * Получить все доступные валюты
     */
    public static function getAvailableCurrencies()
    {
        return [
            'UAH' => 'UAH (₴)',
            'USD' => 'USD ($)',
            'EUR' => 'EUR (€)',
        ];
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
        return [
            'current' => $current,
            'symbol' => self::getSymbol($current),
            'available' => self::getAvailableCurrencies()
        ];
    }
} 