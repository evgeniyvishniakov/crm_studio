<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_description',
        'admin_email',
        'timezone',
        'landing_logo',
        'favicon',
    ];

    /**
     * Получить настройки системы (создать если не существуют)
     */
    public static function getSettings()
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'CRM Studio',
                'site_description' => 'Система управления клиентами и записями',
                'admin_email' => 'admin@example.com',
                'timezone' => 'Europe/Moscow',
                'landing_logo' => null,
                'favicon' => null,
            ]
        );
    }

    /**
     * Обновить настройки системы
     */
    public static function updateSettings($data)
    {
        $settings = static::getSettings();
        $settings->update($data);
        return $settings;
    }

    /**
     * Получить значение конкретной настройки
     */
    public static function getValue($key, $default = null)
    {
        $settings = static::getSettings();
        return $settings->$key ?? $default;
    }
}



