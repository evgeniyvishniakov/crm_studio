<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    /**
     * Получает язык по умолчанию
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Получает активный язык по коду
     */
    public static function getByCode(string $code): ?self
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }

    /**
     * Получает все активные языки
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)->orderBy('is_default', 'desc')->orderBy('name')->get();
    }

    /**
     * Устанавливает язык по умолчанию
     */
    public function setAsDefault(): void
    {
        // Снимаем флаг с других языков
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Устанавливаем флаг для текущего языка
        $this->update(['is_default' => true]);
    }

    /**
     * Получает путь к флагу
     */
    public function getFlagUrlAttribute(): string
    {
        if ($this->flag) {
            return asset('storage/flags/' . $this->flag);
        }
        return asset('images/flags/default.png');
    }
} 