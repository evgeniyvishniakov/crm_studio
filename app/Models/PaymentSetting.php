<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'payment_method',
        'name',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    /**
     * Получить настройки для конкретного метода
     */
    public static function getSettings($method)
    {
        $setting = static::where('payment_method', $method)
            ->where('is_active', true)
            ->first();
            
        return $setting ? $setting->settings : null;
    }

    /**
     * Получить публичный ключ LiqPay
     */
    public static function getLiqPayPublicKey()
    {
        $settings = static::getSettings('liqpay');
        return $settings['public_key'] ?? null;
    }

    /**
     * Получить приватный ключ LiqPay
     */
    public static function getLiqPayPrivateKey()
    {
        $settings = static::getSettings('liqpay');
        return $settings['private_key'] ?? null;
    }

    /**
     * Проверить, активен ли LiqPay
     */
    public static function isLiqPayActive()
    {
        return static::where('payment_method', 'liqpay')
            ->where('is_active', true)
            ->exists();
    }
}
