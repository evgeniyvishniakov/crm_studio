<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Связь с переводами
     */
    public function translations()
    {
        return $this->hasMany(BlogTagTranslation::class);
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
        return $this->belongsToMany(BlogArticle::class, 'blog_article_tags');
    }

    /**
     * Получить активные теги
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

    /**
     * Получить локализованное название
     */
    public function getLocalizedNameAttribute()
    {
        $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
        
        // Если переводы не загружены, загружаем их
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }
        
        $translation = $this->translation($currentLanguage);
        
        return $translation ? $translation->name : $this->name;
    }
}
