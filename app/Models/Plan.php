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
        $basePrice = $this->price_monthly;
        
        switch ($periodType) {
            case 'monthly':
                return $basePrice;
            case 'quarterly':
                return $basePrice * 3 * 0.9; // 3 месяца со скидкой 10%
            case 'semiannual':
                return $basePrice * 6 * 0.85; // 6 месяцев со скидкой 15%
            case 'yearly':
                return $basePrice * 12 * 0.75; // 12 месяцев со скидкой 25%
            default:
                return $basePrice;
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
