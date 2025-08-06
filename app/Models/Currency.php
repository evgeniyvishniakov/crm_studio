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
        
        // Определяем количество десятичных знаков
        // Если число целое, не показываем десятичные знаки
        $decimalPlaces = (floor($amount) == $amount) ? 0 : $this->decimal_places;
        
        // Форматируем число с нужным количеством десятичных знаков
        $formatted = number_format(
            $amount,
            $decimalPlaces,
            $this->decimal_separator,
            $this->thousands_separator ?: ''
        );

        // Добавляем символ валюты в нужную позицию
        if ($this->symbol_position === 'before') {
            return $this->symbol . $formatted;
        } else {
            return $formatted . ' ' . $this->symbol;
        }
    }

    /**
     * Форматирует сумму без разделителей тысяч
     */
    public function formatAmountWithoutThousands($amount): string
    {
        $amount = (float) $amount;
        
        // Определяем количество десятичных знаков
        // Если число целое, не показываем десятичные знаки
        $decimalPlaces = (floor($amount) == $amount) ? 0 : $this->decimal_places;
        
        // Форматируем число без разделителей тысяч
        $formatted = number_format(
            $amount,
            $decimalPlaces,
            $this->decimal_separator,
            '' // Пустая строка вместо разделителя тысяч
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
        // Снимаем флаг с других валют (кроме текущей)
        static::where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        // Устанавливаем флаг для текущей валюты
        $this->update(['is_default' => true]);
        
        // Обновляем текущий объект
        $this->refresh();
    }
} 