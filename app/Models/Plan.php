<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'max_employees',
        'price_monthly',
        'price_quarterly',
        'price_six_months',
        'price_yearly',
        'description',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'max_employees' => 'integer',
        'price_monthly' => 'decimal:2',
        'price_quarterly' => 'decimal:2',
        'price_six_months' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    // Связи
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Методы для расчета цен по периодам
    public function getPriceForPeriod($periodType)
    {
        switch ($periodType) {
            case 'monthly':
                return $this->price_monthly;
            case 'quarterly':
                return $this->price_quarterly ?? ($this->price_monthly * 3 * 0.9);
            case 'semiannual':
                return $this->price_six_months ?? ($this->price_monthly * 6 * 0.85);
            case 'yearly':
                return $this->price_yearly ?? ($this->price_monthly * 12 * 0.75);
            default:
                return $this->price_monthly;
        }
    }

    public function getDiscountForPeriod($periodType)
    {
        switch ($periodType) {
            case 'monthly':
                return 0;
            case 'quarterly':
                return 10;
            case 'semiannual':
                return 15;
            case 'yearly':
                return 25;
            default:
                return 0;
        }
    }
}
