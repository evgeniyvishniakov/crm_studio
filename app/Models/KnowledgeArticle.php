<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'title',
        'slug',
        'description',
        'author',
        'featured_image',
        'meta_tags',
        'sort_order',
        'is_published',
        'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'meta_tags' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Связь с переводами
     */
    public function translations()
    {
        return $this->hasMany(KnowledgeArticleTranslation::class);
    }

    /**
     * Получить перевод на определенном языке
     */
    public function translation($languageCode)
    {
        return $this->translations()->where('locale', $languageCode)->first();
    }

    /**
     * Получить перевод на языке по умолчанию
     */
    public function defaultTranslation()
    {
        $defaultLanguage = Language::getDefault();
        if ($defaultLanguage) {
            return $this->translation($defaultLanguage->code);
        }
        return null;
    }

    /**
     * Получить статьи на определенном языке
     */
    public function scopeByLanguage($query, $languageCode)
    {
        return $query->whereHas('translations', function($q) use ($languageCode) {
            $q->where('locale', $languageCode);
        });
    }

    /**
     * Получить статьи на языке по умолчанию
     */
    public function scopeDefaultLanguage($query)
    {
        $defaultLanguage = Language::getDefault();
        if ($defaultLanguage) {
            return $query->whereHas('translations', function($q) use ($defaultLanguage) {
                $q->where('locale', $defaultLanguage->code);
            });
        }
        return $query;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function steps()
    {
        return $this->hasMany(KnowledgeArticleStep::class)->orderBy('sort_order');
    }

    public function tips()
    {
        return $this->hasMany(KnowledgeArticleTip::class)->orderBy('sort_order');
    }
}
