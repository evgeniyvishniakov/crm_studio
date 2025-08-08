<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'calculation_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
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
     * Связь с расчетом
     */
    public function calculation()
    {
        return $this->belongsTo(\App\Models\Clients\SalaryCalculation::class);
    }

    /**
     * Связь с создателем
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Admin\User::class, 'created_by');
    }

    /**
     * Связь с утвердившим
     */
    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\Admin\User::class, 'approved_by');
    }

    /**
     * Получить текстовое представление метода выплаты
     */
    public function getPaymentMethodTextAttribute()
    {
        return match($this->payment_method) {
            'cash' => __('messages.cash'),
            'bank' => __('messages.bank_transfer'),
            'card' => __('messages.card'),
            default => __('messages.unknown')
        };
    }

    /**
     * Получить текстовое представление статуса
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => __('messages.pending'),
            'approved' => __('messages.paid'),
            'cancelled' => __('messages.cancelled'),
            default => __('messages.unknown')
        };
    }

    /**
     * Получить цвет статуса
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Проверить, можно ли отменить
     */
    public function canCancel()
    {
        return $this->status === 'pending';
    }

    /**
     * Проверить, можно ли утвердить
     */
    public function canApprove()
    {
        return $this->status === 'pending';
    }

    /**
     * Проверить, можно ли удалить
     */
    public function canDelete()
    {
        // Можно удалить только если статус "pending" или "approved"
        return in_array($this->status, ['pending', 'approved']);
    }
}
