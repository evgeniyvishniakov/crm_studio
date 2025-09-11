<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminTelegramSetting extends Model
{
    use HasFactory;

    protected $table = 'admin_telegram_settings';

    protected $fillable = [
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_notifications_enabled',
        'notify_new_projects',
        'notify_new_subscriptions',
        'notify_new_messages',
        'notify_subscription_expires',
        'notify_payment_issues',
    ];

    protected $casts = [
        'telegram_notifications_enabled' => 'boolean',
        'notify_new_projects' => 'boolean',
        'notify_new_subscriptions' => 'boolean',
        'notify_new_messages' => 'boolean',
        'notify_subscription_expires' => 'boolean',
        'notify_payment_issues' => 'boolean',
    ];

    /**
     * Получить или создать настройки telegram для админ панели
     */
    public static function getOrCreate()
    {
        return static::firstOrCreate(
            ['id' => 1], // Единственная запись для админ панели
            [
                'telegram_bot_token' => null,
                'telegram_chat_id' => null,
                'telegram_notifications_enabled' => false,
                'notify_new_projects' => true,
                'notify_new_subscriptions' => true,
                'notify_new_messages' => true,
                'notify_subscription_expires' => true,
                'notify_payment_issues' => true,
            ]
        );
    }
}




