<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Здесь будет логика получения данных о подписке
        // Пока используем заглушки для демонстрации
        
        $endDate = Carbon::now()->addDays(45);
        $daysLeft = Carbon::now()->diffInDays($endDate, false);
        
        $subscription = [
            'status' => $daysLeft > 0 ? 'active' : 'expired', // active, expired, pending
            'plan' => 'Premium',
            'end_date' => $endDate->format('d.m.Y'),
            'days_left' => $daysLeft > 0 ? $daysLeft : 0,
            'price' => 2990,
            'currency' => 'RUB',
            'auto_renewal' => true,
        ];
        
        $paymentHistory = [
            [
                'id' => 1,
                'date' => '01.12.2024',
                'amount' => 2990,
                'currency' => 'RUB',
                'status' => 'completed',
                'description' => 'Подписка Premium на 1 месяц'
            ],
            [
                'id' => 2,
                'date' => '01.11.2024',
                'amount' => 2990,
                'currency' => 'RUB',
                'status' => 'completed',
                'description' => 'Подписка Premium на 1 месяц'
            ],
            [
                'id' => 3,
                'date' => '01.10.2024',
                'amount' => 2990,
                'currency' => 'RUB',
                'status' => 'completed',
                'description' => 'Подписка Premium на 1 месяц'
            ],
            [
                'id' => 4,
                'date' => '01.09.2024',
                'amount' => 1990,
                'currency' => 'RUB',
                'status' => 'completed',
                'description' => 'Подписка Basic на 1 месяц'
            ]
        ];
        
        $availablePlans = [
            [
                'id' => 'basic',
                'name' => 'Basic',
                'price' => 1990,
                'currency' => 'RUB',
                'period' => 'month',
                'features' => [
                    'Базовый функционал CRM',
                    'До 100 клиентов',
                    'Email поддержка',
                    'Базовая аналитика',
                    'Мобильная версия'
                ]
            ],
            [
                'id' => 'premium',
                'name' => 'Premium',
                'price' => 2990,
                'currency' => 'RUB',
                'period' => 'month',
                'features' => [
                    'Полный функционал CRM',
                    'Неограниченное количество клиентов',
                    'Приоритетная поддержка',
                    'Расширенная аналитика',
                    'Интеграции с внешними сервисами',
                    'API доступ',
                    'Экспорт данных'
                ]
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise',
                'price' => 5990,
                'currency' => 'RUB',
                'period' => 'month',
                'features' => [
                    'Все функции Premium',
                    'Персональный менеджер',
                    'Кастомные интеграции',
                    'Белый лейбл',
                    'Выделенный сервер',
                    'Обучение персонала',
                    'Техническая поддержка 24/7'
                ]
            ]
        ];
        
        return view('client.subscriptions.index', compact('subscription', 'paymentHistory', 'availablePlans'));
    }
    
    public function renew()
    {
        // Логика продления подписки
        // Редирект на платежную систему
        return redirect()->away('https://your-payment-system.com/renew');
    }
    
    public function changePlan(Request $request)
    {
        $planId = $request->input('plan_id');
        // Логика смены тарифного плана
        // Редирект на платежную систему
        return redirect()->away("https://your-payment-system.com/change-plan?plan={$planId}");
    }
    
    public function cancel()
    {
        // Логика отмены автопродления
        // Здесь будет обновление в базе данных
        
        return redirect()->back()->with('success', 'Автопродление отменено');
    }
} 