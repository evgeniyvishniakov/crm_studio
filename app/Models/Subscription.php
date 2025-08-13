<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'admin_user_id',
        'plan_type',
        'amount',
        'currency',
        'paid_at',
        'starts_at',
        'trial_ends_at',
        'expires_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }

    /**
     * Связь с администратором проекта
     */
    public function adminUser()
    {
        return $this->belongsTo(\App\Models\Admin\AdminUser::class);
    }

    /**
     * Проверить, активна ли подписка
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at && $this->expires_at->isFuture();
    }

    /**
     * Проверить, истекла ли подписка
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Проверить, пробная ли подписка
     */
    public function isTrial()
    {
        return $this->status === 'trial';
    }

    /**
     * Проверить, истек ли пробный период
     */
    public function isTrialExpired()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Получить количество дней до окончания пробного периода
     */
    public function getDaysUntilTrialEnd()
    {
        if (!$this->trial_ends_at) {
            return null;
        }
        
        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }

    /**
     * Получить количество дней до истечения
     */
    public function getDaysUntilExpiration()
    {
        if (!$this->expires_at) {
            return null;
        }
        
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Получить статус подписки с учетом дат
     */
    public function getEffectiveStatusAttribute()
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }
        
        if ($this->isExpired()) {
            return 'expired';
        }
        
        if ($this->isActive()) {
            return 'active';
        }
        
        return $this->status;
    }
}
