<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'decimal_places',
        'decimal_separator',
        'thousands_separator',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'decimal_places' => 'integer'
    ];

    /**
     * Форматирует сумму в соответствии с настройками валюты
     */
    public function formatAmount($amount): string
    {
        $amount = (float) $amount;
        
        // Форматируем число с нужным количеством десятичных знаков
        $formatted = number_format(
            $amount,
            $this->decimal_places,
            $this->decimal_separator,
            $this->thousands_separator
        );

        // Добавляем символ валюты в нужную позицию
        if ($this->symbol_position === 'before') {
            return $this->symbol . $formatted;
        } else {
            return $formatted . ' ' . $this->symbol;
        }
    }

    /**
     * Получает валюту по умолчанию
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Получает активную валюту по коду
     */
    public static function getByCode(string $code): ?self
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }

    /**
     * Получает все активные валюты
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)->orderBy('is_default', 'desc')->orderBy('name')->get();
    }

    /**
     * Устанавливает валюту по умолчанию
     */
    public function setAsDefault(): void
    {
        // Снимаем флаг с других валют
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Устанавливаем флаг для текущей валюты
        $this->update(['is_default' => true]);
    }
} 