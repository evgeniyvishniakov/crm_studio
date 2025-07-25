<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'is_active_for_booking',
        'price',
        'duration',
        'description'
    ];

    protected $casts = [
        'is_active_for_booking' => 'boolean',
        'price' => 'decimal:2',
        'duration' => 'integer'
    ];

    /**
     * Связь с пользователем (мастером)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Admin\User::class);
    }

    /**
     * Связь с услугой
     */
    public function service()
    {
        return $this->belongsTo(\App\Models\Clients\Service::class);
    }

    /**
     * Получить активную цену (если не указана индивидуальная, то базовая цена услуги)
     */
    public function getActivePriceAttribute()
    {
        return $this->price ?? $this->service->price;
    }

    /**
     * Получить активную длительность (если не указана индивидуальная, то базовая длительность услуги)
     */
    public function getActiveDurationAttribute()
    {
        return $this->duration ?? $this->service->duration ?? 60; // По умолчанию 60 минут
    }
} 