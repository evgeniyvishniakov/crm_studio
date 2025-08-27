<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * Показать форму оплаты
     */
    public function showPaymentForm(Request $request)
    {
        $planId = $request->get('plan_id');
        $period = $request->get('period'); // month, quarterly, semiannual, yearly
        
        $plan = Plan::findOrFail($planId);
        
        // Получаем цену для выбранного периода
        $price = $plan->getPriceForPeriod($period);
        
        return view('landing.payment.form', compact('plan', 'period', 'price'));
    }

    /**
     * Создать платеж и перенаправить на LiqPay
     */
    public function createPayment(Request $request)
    {
        // Логируем входящие данные
        Log::info('Payment request data:', $request->all());
        
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'period' => 'required|in:month,quarterly,semiannual,yearly'
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $user = Auth::guard('client')->user();
        
        // Проверяем, что план найден
        if (!$plan) {
            Log::error('Plan not found for ID: ' . $request->plan_id);
            return redirect()->back()->with('error', 'План не найден!');
        }
        
        // Проверяем период
        $period = $request->period;
        if (!in_array($period, ['month', 'quarterly', 'semiannual', 'yearly'])) {
            Log::error('Invalid period: ' . $period);
            return redirect()->back()->with('error', 'Неверный период!');
        }
        
        Log::info('Creating subscription for plan: ' . $plan->name . ', period: ' . $period);
        
        // Создаем подписку
        $subscription = Subscription::create([
            'project_id' => $user->project_id ?? 1, // Временно используем ID 1
            'admin_user_id' => $user->id,
            'plan_id' => $plan->id,
            'plan_type' => $plan->slug,
            'status' => Subscription::STATUS_PENDING,
            'period_type' => $period,
            'amount' => $plan->getPriceForPeriod($period),
            'currency' => 'UAH',
            'paid_at' => now(),
            'starts_at' => now(),
            'expires_at' => $this->calculateEndDate($period),
            'current_period_start' => now(),
            'current_period_end' => $this->calculateEndDate($period)
        ]);

        // Создаем платеж
        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'amount' => $plan->getPriceForPeriod($period),
            'currency' => 'UAH',
            'status' => 'pending',
            'payment_method' => 'liqpay',
            'external_id' => Str::uuid()
        ]);

        // Генерируем данные для LiqPay
        $liqpayData = $this->generateLiqPayData($payment, $plan, $period);
        
        \Log::info('Payment created successfully', [
            'subscription_id' => $subscription->id,
            'payment_id' => $payment->id,
            'plan_name' => $plan->name,
            'period' => $period,
            'amount' => $payment->amount
        ]);

        // Сначала показываем отладочную информацию
        $debugHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Отладка LiqPay</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .debug { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0; }
                .error { background: #ffe6e6; border-left: 4px solid #ff0000; }
                .success { background: #e6ffe6; border-left: 4px solid #00ff00; }
                pre { background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto; }
            </style>
        </head>
        <body>
            <h2>Отладка интеграции с LiqPay</h2>
            
            <div class="debug">
                <h3>Настройки LiqPay:</h3>
                <p><strong>Публичный ключ:</strong> ' . (empty($publicKey) ? '<span style="color:red">НЕ НАСТРОЕН</span>' : '<span style="color:green">Настроен</span>') . '</p>
                <p><strong>Приватный ключ:</strong> ' . (empty($privateKey) ? '<span style="color:red">НЕ НАСТРОЕН</span>' : '<span style="color:green">Настроен</span>') . '</p>
            </div>
            
            <div class="debug">
                <h3>Данные платежа:</h3>
                <p><strong>План:</strong> ' . $plan->name . '</p>
                <p><strong>Период:</strong> ' . $period . '</p>
                <p><strong>Сумма:</strong> ' . $payment->amount . ' ₴</p>
                <p><strong>ID платежа:</strong> ' . $payment->external_id . '</p>
            </div>
            
            <div class="debug">
                <h3>Данные для LiqPay:</h3>
                <pre>' . json_encode($liqpayData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>
            </div>
            
            <div class="debug">
                <h3>Закодированные данные:</h3>
                <p><strong>Data:</strong> ' . $liqpayData['data'] . '</p>
                <p><strong>Signature:</strong> ' . $liqpayData['signature'] . '</p>
            </div>';
            
        if (!empty($publicKey) && !empty($privateKey)) {
            $debugHtml .= '
            <div class="debug success">
                <h3>✅ Готово к отправке на LiqPay!</h3>
                <p>Нажмите кнопку ниже для перехода на LiqPay:</p>
                <form id="liqpay-form" method="POST" action="https://www.liqpay.ua/api/3/checkout" accept-charset="utf-8">
                    <input type="hidden" name="data" value="' . $liqpayData['data'] . '">
                    <input type="hidden" name="signature" value="' . $liqpayData['signature'] . '">
                    <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                        Перейти к оплате на LiqPay
                    </button>
                </form>
            </div>';
        } else {
            $debugHtml .= '
            <div class="debug error">
                <h3>❌ Ошибка: LiqPay не настроен!</h3>
                <p>Необходимо настроить ключи LiqPay в админ панели:</p>
                <ul>
                    <li>Зайдите в Админ панель → Настройки платежей → LiqPay</li>
                    <li>Введите публичный и приватный ключи</li>
                    <li>Включите LiqPay</li>
                </ul>
                <a href="/admin/payment-settings" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                    Настроить LiqPay
                </a>
            </div>';
        }
        
        $debugHtml .= '</body></html>';
        
        return response($debugHtml);
    }

    /**
     * Обработка успешной оплаты (callback от LiqPay)
     */
    public function paymentSuccess(Request $request)
    {
        // Здесь будет логика проверки подписи от LiqPay
        // Пока просто обновляем статус
        
        $paymentId = $request->get('payment_id');
        $payment = Payment::where('external_id', $paymentId)->first();
        
        if ($payment) {
            $payment->update(['status' => 'completed']);
            $payment->subscription->update(['status' => Subscription::STATUS_ACTIVE]);
            
            // Перенаправляем в личный кабинет на вкладку подписок
            return redirect()->route('landing.account.dashboard')
                ->with('success', 'Оплата прошла успешно! Ваша подписка активирована.');
        }
        
        return redirect()->route('landing.account.dashboard')
            ->with('error', 'Ошибка при обработке платежа.');
    }

    /**
     * Обработка неудачной оплаты
     */
    public function paymentFailure(Request $request)
    {
        return redirect()->route('landing.account.dashboard')
            ->with('error', 'Оплата не прошла. Попробуйте еще раз.');
    }

    /**
     * Рассчитать дату окончания подписки
     */
    private function calculateEndDate($period)
    {
        switch ($period) {
            case 'month':
                return now()->addMonth();
            case 'quarterly':
                return now()->addMonths(3);
            case 'semiannual':
                return now()->addMonths(6);
            case 'yearly':
                return now()->addYear();
            default:
                return now()->addMonth();
        }
    }

    /**
     * Генерировать данные для LiqPay
     */
    private function generateLiqPayData($payment, $plan, $period)
    {
        $periodNames = [
            'month' => 'месяц',
            'quarterly' => '3 месяца',
            'semiannual' => '6 месяцев',
            'yearly' => 'год'
        ];

        // Получаем настройки LiqPay из базы
        $liqpaySettings = \App\Models\PaymentSetting::getSettings('liqpay');
        
        // Временно используем тестовые ключи, если настройки не найдены
        if (!$liqpaySettings || empty($liqpaySettings['public_key']) || empty($liqpaySettings['private_key'])) {
            Log::warning('LiqPay не настроен, используем тестовые ключи');
            $publicKey = 'sandbox_i123456789';
            $privateKey = 'sandbox_987654321';
        } else {
            $publicKey = $liqpaySettings['public_key'];
            $privateKey = $liqpaySettings['private_key'];
        }

        // Данные для LiqPay
        $liqpayData = [
            'public_key' => $publicKey,
            'version' => '3',
            'action' => 'pay',
            'amount' => $payment->amount,
            'currency' => 'UAH',
            'description' => "Подписка на план '{$plan->name}' на {$periodNames[$period]}",
            'order_id' => $payment->external_id,
            'result_url' => route('landing.payment.success'),
            'server_url' => route('landing.payment.webhook'),
            'language' => 'uk',
            'sandbox' => 1 // Тестовый режим
        ];

        // Кодируем данные в base64
        $data = base64_encode(json_encode($liqpayData));
        
        // Создаем подпись
        $signature = base64_encode(sha1($privateKey . $data . $privateKey, 1));

        return [
            'data' => $data,
            'signature' => $signature
        ];
    }
}
