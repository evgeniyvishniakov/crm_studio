<?php

declare(strict_types=1);

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'logo',
        'name', // Имя
        'project_name', // Название проекта
        'email',
        'registered_at',
        'language_id',
        'booking_language_id',
        'currency_id',
        'status',
        'phone',
        'website',
        'address',
        'map_latitude',
        'map_longitude',
        'map_zoom',
        'about',
        'social_links',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'social_links' => 'array',
    ];

    /**
     * Связь с валютой
     */
    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }

    /**
     * Связь с языком
     */
    public function language()
    {
        return $this->belongsTo(\App\Models\Language::class, 'language_id');
    }

    /**
     * Связь с языком веб-записи
     */
    public function bookingLanguage()
    {
        return $this->belongsTo(\App\Models\Language::class, 'booking_language_id');
    }

    /**
     * Получить код валюты (для обратной совместимости)
     */
    public function getCurrencyCodeAttribute()
    {
        return $this->currency ? $this->currency->code : null;
    }

    /**
     * Получить код языка (для обратной совместимости)
     */
    public function getLanguageCodeAttribute()
    {
        return $this->language ? $this->language->code : null;
    }

    /**
     * Получить код языка веб-записи
     */
    public function getBookingLanguageCodeAttribute()
    {
        return $this->bookingLanguage ? $this->bookingLanguage->code : ($this->language ? $this->language->code : null);
    }

    /**
     * Получить slug проекта для URL
     */
    public function getSlugAttribute()
    {
        return Str::slug($this->project_name);
    }

    /**
     * Получить URL для публичной записи
     */
    public function getBookingUrlAttribute()
    {
        $bookingSettings = $this->getOrCreateBookingSettings();
        
        // Если есть сохраненная ссылка - используем её
        if ($bookingSettings->booking_url) {
            return $bookingSettings->booking_url;
        }
        
        // Иначе генерируем новую
        return url('/book/' . $this->slug);
    }

    /**
     * Связь с настройками бронирования
     */
    public function bookingSettings()
    {
        return $this->hasOne(\App\Models\Clients\BookingSetting::class);
    }

    /**
     * Получить или создать настройки бронирования
     */
    public function getOrCreateBookingSettings()
    {
        return $this->bookingSettings()->firstOrCreate([
            'project_id' => $this->id
        ], [
            'booking_enabled' => false,
            'booking_url' => null,
            'booking_interval' => 30,
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '18:00:00',
            'advance_booking_days' => 30,
            'allow_same_day_booking' => true,
            'require_confirmation' => false
        ]);
    }

    /**
     * Связь с настройками виджета
     */
    public function widgetSettings()
    {
        return $this->hasOne(\App\Models\WidgetSetting::class);
    }

    /**
     * Связь с настройками email
     */
    public function emailSettings()
    {
        return $this->hasOne(\App\Models\EmailSetting::class);
    }

    /**
     * Связь с настройками telegram
     */
    public function telegramSettings()
    {
        return $this->hasOne(\App\Models\TelegramSetting::class);
    }

    /**
     * Получить или создать настройки виджета
     */
    public function getOrCreateWidgetSettings()
    {
        return $this->widgetSettings()->firstOrCreate([
            'project_id' => $this->id
        ], [
            'widget_enabled' => false,
            'widget_button_text' => 'Записаться',
            'widget_button_color' => '#007bff',
            'widget_position' => 'bottom-right',
            'widget_size' => 'medium',
            'widget_animation_enabled' => true,
            'widget_animation_type' => 'scale',
            'widget_animation_duration' => 300,
            'widget_border_radius' => 25,
            'widget_text_color' => '#ffffff',
        ]);
    }

    /**
     * Получить или создать настройки email
     */
    public function getOrCreateEmailSettings()
    {
        return $this->emailSettings()->firstOrCreate([
            'project_id' => $this->id
        ], [
            'email_host' => null,
            'email_port' => 587,
            'email_username' => null,
            'email_password' => null,
            'email_encryption' => 'tls',
            'email_from_name' => null,
            'email_notifications_enabled' => false
        ]);
    }

    /**
     * Получить или создать настройки telegram
     */
    public function getOrCreateTelegramSettings()
    {
        return $this->telegramSettings()->firstOrCreate([
            'project_id' => $this->id
        ], [
            'telegram_bot_token' => null,
            'telegram_chat_id' => null,
            'telegram_notifications_enabled' => false
        ]);
    }
} 