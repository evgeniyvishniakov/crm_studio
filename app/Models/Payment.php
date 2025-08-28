<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'project_id',
        'user_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'external_id',
        'metadata',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime'
    ];

    // Связи
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }
    
    public function user()
    {
        return $this->belongsTo(\App\Models\Admin\User::class);
    }

    // Статусы платежей
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    // Методы платежей
    const METHOD_STRIPE = 'stripe';
    const METHOD_LIQPAY = 'liqpay';
    const METHOD_PAYPAL = 'paypal';
}
