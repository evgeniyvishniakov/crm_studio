<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Project;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
        /**
     * Показать список всех подписок
     */
    public function index()
    {
        // Получаем все подписки с проектами и админами
        $subscriptions = Subscription::with(['project.currency', 'project.language', 'adminUser'])->get();

        // Группируем по статусу
        $trialSubscriptions = $subscriptions->filter(function ($subscription) {
            return $subscription->status === 'trial';
        });

        $activeSubscriptions = $subscriptions->filter(function ($subscription) {
            return $subscription->isActive();
        });

        $expiredSubscriptions = $subscriptions->filter(function ($subscription) {
            return $subscription->isExpired();
        });

        $cancelledSubscriptions = $subscriptions->filter(function ($subscription) {
            return $subscription->status === 'cancelled';
        });

        // Статистика
        $stats = [
            'total_subscriptions' => $subscriptions->count(),
            'trial_subscriptions' => $trialSubscriptions->count(),
            'active_subscriptions' => $activeSubscriptions->count(),
            'expired_subscriptions' => $expiredSubscriptions->count(),
            'cancelled_subscriptions' => $cancelledSubscriptions->count(),
            'revenue_this_month' => $subscriptions->where('paid_at', '>=', now()->startOfMonth())->sum('amount'),
            'trial_conversion_rate' => $activeSubscriptions->count() > 0 ?
                round(($activeSubscriptions->count() / ($trialSubscriptions->count() + $activeSubscriptions->count())) * 100, 1) : 0
        ];

        return view('admin.subscriptions.index', compact(
            'trialSubscriptions',
            'activeSubscriptions',
            'expiredSubscriptions',
            'cancelledSubscriptions',
            'stats'
        ));
    }
    
    /**
     * Показать детали подписки
     */
    public function show($id)
    {
        $subscription = Subscription::with([
            'project.currency', 
            'project.language', 
            'project.clients',
            'project.appointments',
            'adminUser'
        ])->findOrFail($id);
        
        return view('admin.subscriptions.show', compact('subscription'));
    }
    
    /**
     * Продлить подписку
     */
    public function extend(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        
        if ($subscription->status === 'trial') {
            // Если это пробная подписка, конвертируем в активную
            $subscription->update([
                'status' => 'active',
                'expires_at' => now()->addDays(30), // Устанавливаем дату окончания активной подписки
                'trial_ends_at' => now() // Завершаем пробный период
            ]);
            
            $message = 'Пробная подписка успешно конвертирована в активную на 30 дней';
        } else {
            // Продлеваем существующую активную подписку на 30 дней
            $subscription->update([
                'expires_at' => $subscription->expires_at ? $subscription->expires_at->addDays(30) : now()->addDays(30)
            ]);
            
            $message = 'Подписка успешно продлена на 30 дней';
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    /**
     * Отменить подписку
     */
    public function cancel(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Отмечаем подписку как отмененную
        $subscription->update([
            'status' => 'cancelled'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Подписка успешно отменена'
        ]);
    }
}
