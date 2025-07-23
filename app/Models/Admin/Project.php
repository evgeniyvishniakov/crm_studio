<?php

declare(strict_types=1);

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'currency_id',
        'status',
        'phone',
        'website',
        'address',
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
} 