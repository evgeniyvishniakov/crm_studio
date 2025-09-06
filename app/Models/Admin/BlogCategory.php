<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Связь с переводами
     */
    public function translations()
    {
        return $this->hasMany(BlogCategoryTranslation::class);
    }

    /**
     * Получить перевод на определенном языке
     */
    public function translation($languageCode)
    {
        return $this->translations()->where('locale', $languageCode)->first();
    }

    /**
     * Связь со статьями
     */
    public function articles()
    {
        return $this->hasMany(BlogArticle::class);
    }

    /**
     * Получить активные категории
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Автоматическое создание slug из названия
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = \Str::slug($value);
        }
    }
}
