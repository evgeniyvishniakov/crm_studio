<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    // Статусы подписок
    const STATUS_TRIAL = 'trial';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PENDING = 'pending';

    // Периоды подписок
    const PERIOD_MONTH = 'month';
    const PERIOD_QUARTERLY = 'quarterly';
    const PERIOD_SEMIANNUAL = 'semiannual';
    const PERIOD_YEARLY = 'yearly';

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
        'notes',
        'plan_id',
        'period_type',
        'amount_paid',
        'discount_percent',
        'payment_status',
        'current_period_start',
        'current_period_end'
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
        return $this->belongsTo(\App\Models\Admin\Project::class, 'project_id');
    }

    /**
     * Связь с администратором проекта
     */
    public function adminUser()
    {
        return $this->belongsTo(\App\Models\Admin\User::class);
    }

    /**
     * Связь с тарифом
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Связь с платежами
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
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

    /**
     * Получить тип периода
     */
    public function getPeriodTypeAttribute($value)
    {
        return $value ?: 'monthly';
    }

    /**
     * Получить скидку для текущего периода
     */
    public function getDiscountAttribute()
    {
        if (!$this->plan) {
            return 0;
        }
        
        return $this->plan->getDiscountForPeriod($this->period_type);
    }

    /**
     * Получить цену с учетом скидки
     */
    public function getDiscountedPriceAttribute()
    {
        if (!$this->plan) {
            return $this->amount;
        }
        
        return $this->plan->getPriceForPeriod($this->period_type);
    }

    /**
     * Проверить, не превышен ли лимит сотрудников
     */
    public function checkEmployeeLimit()
    {
        if (!$this->plan || !$this->plan->max_employees) {
            return true; // Без лимита
        }
        
        $currentEmployees = $this->project->adminUsers()->count();
        return $currentEmployees <= $this->plan->max_employees;
    }

    /**
     * Получить количество дней до окончания текущего периода
     */
    public function getDaysUntilPeriodEnd()
    {
        if (!$this->current_period_end) {
            return null;
        }
        
        return max(0, now()->diffInDays($this->current_period_end, false));
    }
}
