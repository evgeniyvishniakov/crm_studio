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
        'sort_order',
        // Поля для цен в разных валютах
        'price_monthly_uah', 'price_quarterly_uah', 'price_six_months_uah', 'price_yearly_uah',
        'price_monthly_usd', 'price_quarterly_usd', 'price_six_months_usd', 'price_yearly_usd',
        'price_monthly_eur', 'price_quarterly_eur', 'price_six_months_eur', 'price_yearly_eur'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'max_employees' => 'integer',
        'price_monthly' => 'decimal:2',
        'price_quarterly' => 'decimal:2',
        'price_six_months' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'sort_order' => 'integer',
        // Кастинг для цен в валютах
        'price_monthly_uah' => 'decimal:2', 'price_quarterly_uah' => 'decimal:2', 'price_six_months_uah' => 'decimal:2', 'price_yearly_uah' => 'decimal:2',
        'price_monthly_usd' => 'decimal:2', 'price_quarterly_usd' => 'decimal:2', 'price_six_months_usd' => 'decimal:2', 'price_yearly_usd' => 'decimal:2',
        'price_monthly_eur' => 'decimal:2', 'price_quarterly_eur' => 'decimal:2', 'price_six_months_eur' => 'decimal:2', 'price_yearly_eur' => 'decimal:2'
    ];

    // Связи
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Получает цену для указанного периода и валюты
     */
    public function getPriceForPeriodAndCurrency($periodType, $currencyCode = 'uah')
    {
        $currencyCode = strtolower($currencyCode);
        $fieldName = "price_{$periodType}_{$currencyCode}";
        
        // Если есть цена в указанной валюте, возвращаем её
        if (isset($this->$fieldName) && $this->$fieldName !== null) {
            return $this->$fieldName;
        }
        
        // Иначе возвращаем цену по умолчанию
        return $this->getPriceForPeriod($periodType);
    }

    /**
     * Получает все цены для указанной валюты
     */
    public function getPricesForCurrency($currencyCode = 'uah')
    {
        $currencyCode = strtolower($currencyCode);
        
        return [
            'monthly' => $this->getPriceForPeriodAndCurrency('monthly', $currencyCode),
            'quarterly' => $this->getPriceForPeriodAndCurrency('quarterly', $currencyCode),
            'six_months' => $this->getPriceForPeriodAndCurrency('six_months', $currencyCode),
            'yearly' => $this->getPriceForPeriodAndCurrency('yearly', $currencyCode),
        ];
    }

    /**
     * Получает валюту для указанного языка
     */
    public function getCurrencyByLanguage($language)
    {
        $languageCurrencyMap = [
            'ua' => 'uah',
            'ru' => 'uah', 
            'en' => 'usd',
            'de' => 'eur'  // Немецкий → евро
        ];
        
        return $languageCurrencyMap[$language] ?? 'uah';
    }

    /**
     * Получает цену для указанного языка и периода
     */
    public function getPriceForLanguage($language, $periodType)
    {
        $currencyCode = $this->getCurrencyByLanguage($language);
        return $this->getPriceForPeriodAndCurrency($periodType, $currencyCode);
    }

    // Методы для расчета цен по периодам
    public function getPriceForPeriod($periodType)
    {
        switch ($periodType) {
            case 'monthly':
                return $this->price_monthly;
            case 'quarterly':
                return $this->price_quarterly ?? ($this->price_monthly * 3 * 0.9);
            case 'six_months':
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
            case 'six_months':
                return 15;
            case 'yearly':
                return 25;
            default:
                return 0;
        }
    }
}
