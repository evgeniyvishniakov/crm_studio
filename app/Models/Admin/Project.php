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
        'language',
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
     * Получить код валюты (для обратной совместимости)
     */
    public function getCurrencyCodeAttribute()
    {
        return $this->currency ? $this->currency->code : null;
    }
} 