<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'period_start',
        'period_end',
        'services_count',
        'services_amount',
        'sales_count',
        'sales_amount',
        'fixed_salary',
        'percentage_salary',
        'bonuses',
        'penalties',
        'total_salary',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'services_amount' => 'decimal:2',
        'sales_amount' => 'decimal:2',
        'fixed_salary' => 'decimal:2',
        'percentage_salary' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'penalties' => 'decimal:2',
        'total_salary' => 'decimal:2',
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Admin\User::class);
    }

    /**
     * Связь с выплатами
     */
    public function payments()
    {
        return $this->hasMany(\App\Models\Clients\SalaryPayment::class, 'calculation_id');
    }

    /**
     * Получить текстовое представление статуса
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'calculated' => 'Рассчитано',
            'approved' => 'Утверждено',
            'paid' => 'Выплачено',
            default => 'Неизвестно'
        };
    }

    /**
     * Получить цвет статуса
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'calculated' => 'warning',
            'approved' => 'success',
            'paid' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Проверить, можно ли редактировать
     */
    public function canEdit()
    {
        return $this->status === 'calculated';
    }

    /**
     * Проверить, можно ли утвердить
     */
    public function canApprove()
    {
        return $this->status === 'calculated';
    }

    /**
     * Проверить, можно ли выплатить
     */
    public function canPay()
    {
        return $this->status === 'approved';
    }
}
