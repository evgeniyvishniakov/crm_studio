<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    /**
     * Показать страницу настроек платежей
     */
    public function index()
    {
        $liqpay = PaymentSetting::where('payment_method', 'liqpay')->first();
        $stripe = PaymentSetting::where('payment_method', 'stripe')->first();
        $paypal = PaymentSetting::where('payment_method', 'paypal')->first();
        
        return view('admin.payment-settings.index', compact('liqpay', 'stripe', 'paypal'));
    }

    /**
     * Обновить настройки LiqPay
     */
    public function updateLiqPay(Request $request)
    {
        $request->validate([
            'liqpay_public_key' => 'required|string',
            'liqpay_private_key' => 'required|string'
        ]);

        PaymentSetting::updateOrCreate(
            ['payment_method' => 'liqpay'],
            [
                'name' => 'LiqPay',
                'is_active' => true,
                'settings' => [
                    'public_key' => $request->liqpay_public_key,
                    'private_key' => $request->liqpay_private_key
                ]
            ]
        );

        return redirect()->back()->with('success', 'Настройки LiqPay обновлены!');
    }

    /**
     * Обновить настройки Stripe
     */
    public function updateStripe(Request $request)
    {
        $request->validate([
            'stripe_public_key' => 'required|string',
            'stripe_secret_key' => 'required|string',
            'stripe_sandbox' => 'boolean'
        ]);

        PaymentSetting::updateOrCreate(
            ['payment_method' => 'stripe'],
            [
                'name' => 'Stripe',
                'is_active' => true,
                'settings' => [
                    'public_key' => $request->stripe_public_key,
                    'secret_key' => $request->stripe_secret_key,
                    'sandbox' => $request->has('stripe_sandbox')
                ]
            ]
        );

        return redirect()->back()->with('success', 'Настройки Stripe обновлены!');
    }

    /**
     * Обновить настройки PayPal
     */
    public function updatePayPal(Request $request)
    {
        $request->validate([
            'paypal_client_id' => 'required|string',
            'paypal_secret' => 'required|string',
            'paypal_sandbox' => 'boolean'
        ]);

        PaymentSetting::updateOrCreate(
            ['payment_method' => 'paypal'],
            [
                'name' => 'PayPal',
                'is_active' => true,
                'settings' => [
                    'client_id' => $request->paypal_client_id,
                    'secret' => $request->paypal_secret,
                    'sandbox' => $request->has('paypal_sandbox')
                ]
            ]
        );

        return redirect()->back()->with('success', 'Настройки PayPal обновлены!');
    }

    /**
     * Переключить активность платежной системы
     */
    public function toggleActive(Request $request, $method)
    {
        $setting = PaymentSetting::where('payment_method', $method)->first();
        
        if (!$setting) {
            // Создаем настройки по умолчанию при первом включении
            $defaultSettings = [
                'liqpay' => [
                    'name' => 'LiqPay',
                    'settings' => [
                        'public_key' => '',
                        'private_key' => ''
                    ]
                ],
                'stripe' => [
                    'name' => 'Stripe',
                    'settings' => [
                        'public_key' => '',
                        'secret_key' => ''
                    ]
                ],
                'paypal' => [
                    'name' => 'PayPal',
                    'settings' => [
                        'client_id' => '',
                        'secret' => ''
                    ]
                ]
            ];
            
            $setting = PaymentSetting::create([
                'payment_method' => $method,
                'name' => $defaultSettings[$method]['name'],
                'is_active' => true,
                'settings' => $defaultSettings[$method]['settings']
            ]);
            
            return redirect()->back()->with('success', "Платежная система {$setting->name} создана и активирована! Теперь введите ключи.");
        }
        
        $setting->update(['is_active' => !$setting->is_active]);
        $status = $setting->is_active ? 'активирована' : 'деактивирована';
        return redirect()->back()->with('success', "Платежная система {$setting->name} {$status}!");
    }
}
