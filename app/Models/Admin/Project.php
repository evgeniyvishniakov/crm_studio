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
        'booking_url', // Ссылка для онлайн-записи
        'address',
        'map_latitude',
        'map_longitude',
        'map_zoom',
        'about',
        'social_links',
        'booking_enabled',
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_notifications_enabled',
        'email_host',
        'email_port',
        'email_username',
        'email_password',
        'email_encryption',
        'email_from_name',
        'email_notifications_enabled',
        'widget_enabled',
        'widget_button_text',
        'widget_button_color',
        'widget_position',
        'widget_size',
        'widget_animation_enabled',
        'widget_animation_type',
        'widget_animation_duration',
        'widget_border_radius',
        'widget_text_color',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'social_links' => 'array',
        'booking_enabled' => 'boolean',
        'telegram_notifications_enabled' => 'boolean',
        'email_notifications_enabled' => 'boolean',
        'widget_enabled' => 'boolean',
        'widget_animation_enabled' => 'boolean',
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
        // Если есть сохраненная ссылка - используем её
        if ($this->attributes['booking_url'] ?? null) {
            return $this->attributes['booking_url'];
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
            'booking_interval' => 30,
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '18:00:00',
            'advance_booking_days' => 30,
            'allow_same_day_booking' => true,
            'require_confirmation' => false
        ]);
    }
} 