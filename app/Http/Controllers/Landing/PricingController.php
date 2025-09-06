<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Currency;

class PricingController extends Controller
{
    public function index($lang = null)
    {
        // Устанавливаем язык если передан параметр
        if ($lang) {
            \App\Helpers\LanguageHelper::setLanguage($lang);
        } else {
            // Для fallback маршрута устанавливаем украинский язык по умолчанию
            \App\Helpers\LanguageHelper::setLanguage('ua');
        }
        // Получаем все активные тарифы, отсортированные по порядку
        $plans = Plan::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();

        // Группируем тарифы по slug для удобства
        $plansBySlug = $plans->keyBy('slug');

        // Определяем цвета для каждого тарифа
        $planColors = [
            'small' => [
                'border' => 'border-success',
                'badge' => 'bg-success',
                'icon' => 'text-success',
                'button' => 'btn-tab-green',
                'icon_class' => 'fas fa-users'
            ],
            'medium' => [
                'border' => 'border-primary',
                'badge' => 'bg-primary',
                'icon' => 'text-primary',
                'button' => 'btn-tab-blue',
                'icon_class' => 'fas fa-users'
            ],
            'unlimited' => [
                'border' => 'border-warning',
                'badge' => 'bg-warning',
                'icon' => 'text-bright-yellow',
                'button' => 'btn-tab-yellow',
                'icon_class' => 'fas fa-infinity'
            ]
        ];

        // Получаем все активные валюты
        $currencies = Currency::where('is_active', true)->get();

        // Определяем валюту по умолчанию для каждого языка
        $defaultCurrencies = [
            'ua' => 'UAH',
            'ru' => 'UAH', 
            'en' => 'USD',
            'pl' => 'PLN'
        ];

        return view('landing.pages.pricing', compact('plans', 'plansBySlug', 'planColors', 'currencies', 'defaultCurrencies'));
    }
}
