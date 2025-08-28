<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Payment;

class SubscriptionsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $project = $user->project;
        
        // Получаем только активную подписку из базы данных (pending не показываем)
        $subscription = Subscription::where('project_id', $project->id)
            ->where('admin_user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->latest()
            ->first();
        
        $subscriptionData = null;
        $paymentHistory = [];
        
        if ($subscription) {
            // Формируем данные о подписке
            $subscriptionData = [
                'status' => $subscription->status,
                'plan' => $subscription->plan ? $subscription->plan->name : 'Не указан',
                'end_date' => $subscription->expires_at ? $subscription->expires_at->format('d.m.Y') : 'Не указана',
                'days_left' => $subscription->getDaysUntilExpiration() ?? 0,
                'price' => $subscription->amount,
                'currency' => $subscription->currency,
                'auto_renewal' => $subscription->status === 'active',
                'starts_at' => $subscription->starts_at ? $subscription->starts_at->format('d.m.Y') : 'Не указана',
                'trial_ends_at' => $subscription->trial_ends_at ? $subscription->trial_ends_at->format('d.m.Y') : null,
                'payment_status' => $subscription->payment_status ?? 'pending',
                'is_trial' => $subscription->status === 'trial',
                'trial_days_left' => $subscription->getDaysUntilTrialEnd() ?? 0
            ];
            
            // Получаем историю платежей
            $paymentHistory = Payment::where('subscription_id', $subscription->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'date' => $payment->created_at->format('d.m.Y'),
                        'amount' => $payment->amount,
                        'currency' => $payment->currency ?? 'USD',
                        'status' => $payment->status,
                        'description' => $payment->description ?? 'Платеж по подписке'
                    ];
                })
                ->toArray();
        } else {
            // Если подписки нет, показываем базовую информацию
            $subscriptionData = [
                'status' => 'no_subscription',
                'plan' => 'Не выбрано',
                'end_date' => 'Не указана',
                'days_left' => 0,
                'price' => 0,
                'currency' => 'USD',
                'auto_renewal' => false,
                'starts_at' => 'Не указана',
                'trial_ends_at' => null,
                'payment_status' => 'no_subscription'
            ];
        }
        
        // Получаем доступные планы
        $availablePlans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price_monthly,
                    'currency' => 'USD',
                    'period' => 'month',
                    'features' => $plan->features ?? []
                ];
            })
            ->toArray();
        
        return view('client.subscriptions.index', compact('subscriptionData', 'paymentHistory', 'availablePlans'));
    }
    
    public function renew()
    {
        $user = Auth::user();
        $project = $user->project;
        
        // Получаем текущую подписку
        $subscription = Subscription::where('project_id', $project->id)
            ->where('admin_user_id', $user->id)
            ->latest()
            ->first();
        
        if (!$subscription) {
            return redirect()->back()->with('error', 'Подписка не найдена');
        }
        
        // Здесь будет логика продления подписки
        // Редирект на платежную систему
        return redirect()->away('https://your-payment-system.com/renew');
    }
    
    public function changePlan(Request $request)
    {
        $planId = $request->input('plan_id');
        
        if (!$planId) {
            return redirect()->back()->with('error', 'План не выбран');
        }
        
        $plan = Plan::find($planId);
        if (!$plan) {
            return redirect()->back()->with('error', 'План не найден');
        }
        
        $user = Auth::user();
        $project = $user->project;
        
        // Проверяем, есть ли уже активная подписка
        $existingSubscription = Subscription::where('project_id', $project->id)
            ->where('admin_user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->first();
        
        if ($existingSubscription) {
            // Если есть активная подписка, обновляем её
            $existingSubscription->update([
                'plan_type' => $plan->slug,
                'amount' => $plan->price_monthly,
                'status' => 'pending',
                'payment_status' => 'pending',
                'starts_at' => null, // Дата начала устанавливается только после оплаты
                'expires_at' => null, // Дата окончания устанавливается только после оплаты
                'notes' => 'План изменен на: ' . $plan->name
            ]);
        } else {
            // Создаем новую подписку
            Subscription::create([
                'project_id' => $project->id,
                'admin_user_id' => $user->id,
                'plan_type' => $plan->slug,
                'amount' => $plan->price_monthly,
                'currency' => 'USD',
                'paid_at' => null, // Дата оплаты устанавливается только после успешной оплаты
                'starts_at' => null, // Дата начала устанавливается только после оплаты
                'trial_ends_at' => null, // Для платных планов нет пробного периода
                'expires_at' => null, // Дата окончания устанавливается только после оплаты
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => 'Создана подписка на план: ' . $plan->name
            ]);
        }
        
        // Логика смены тарифного плана
        // Редирект на платежную систему
        return redirect()->away("https://your-payment-system.com/change-plan?plan={$planId}");
    }
    
    public function cancel()
    {
        $user = Auth::user();
        $project = $user->project;
        
        // Получаем текущую подписку
        $subscription = Subscription::where('project_id', $project->id)
            ->where('admin_user_id', $user->id)
            ->latest()
            ->first();
        
        if (!$subscription) {
            return redirect()->back()->with('error', 'Подписка не найдена');
        }
        
        // Логика отмены автопродления
        // Здесь будет обновление в базе данных
        
        return redirect()->back()->with('success', 'Автопродление отменено');
    }
    
    /**
     * Активация подписки после успешной оплаты
     * Этот метод вызывается webhook'ом от платежной системы
     */
    public function activateSubscription(Request $request)
    {
        $subscriptionId = $request->input('subscription_id');
        $paymentId = $request->input('payment_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency', 'USD');
        
        // Находим подписку
        $subscription = Subscription::findOrFail($subscriptionId);
        
        // Проверяем, что подписка в статусе pending
        if ($subscription->status !== 'pending') {
            return response()->json(['error' => 'Подписка уже активирована или имеет другой статус'], 400);
        }
        
        // Активируем подписку
        $subscription->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'starts_at' => now(), // Дата начала устанавливается при активации
            'expires_at' => now()->addMonth(), // Дата окончания через месяц
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
            'notes' => 'Подписка активирована после успешной оплаты. ID платежа: ' . $paymentId
        ]);
        
        // Здесь можно добавить логику для создания записи о платеже
        // Payment::create([...]);
        
        return response()->json(['success' => true, 'message' => 'Подписка активирована']);
    }
    
    /**
     * Обработка неудачной оплаты
     */
    public function handleFailedPayment(Request $request)
    {
        $subscriptionId = $request->input('subscription_id');
        $paymentId = $request->input('payment_id');
        $errorMessage = $request->input('error_message', 'Неизвестная ошибка');
        
        // Находим подписку
        $subscription = Subscription::findOrFail($subscriptionId);
        
        // Обновляем статус на failed
        $subscription->update([
            'payment_status' => 'failed',
            'notes' => 'Ошибка оплаты: ' . $errorMessage . '. ID платежа: ' . $paymentId
        ]);
        
        return response()->json(['success' => true, 'message' => 'Статус подписки обновлен']);
    }
} 