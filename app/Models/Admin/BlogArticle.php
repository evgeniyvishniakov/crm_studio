<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'author',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_tags',
        'sort_order',
        'is_published',
        'is_featured',
        'published_at',
        'views_count',
        'reading_time'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'meta_tags' => 'array',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'reading_time' => 'integer',
    ];

    /**
     * Связь с переводами
     */
    public function translations()
    {
        return $this->hasMany(BlogArticleTranslation::class);
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
        $defaultLanguage = \App\Models\Language::getDefault();
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
     * Получить опубликованные статьи
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', now());
    }

    /**
     * Получить рекомендуемые статьи
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Связь с категорией
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Связь с тегами
     */
    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_article_tags');
    }

    /**
     * Автоматическое создание slug из заголовка
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = \Str::slug($value);
        }
    }

    /**
     * Получить URL статьи
     */
    public function getUrlAttribute()
    {
        return route('beautyflow.blog.show.fallback', $this->slug);
    }

    /**
     * Получить время чтения статьи
     */
    public function getReadingTimeAttribute()
    {
        if ($this->attributes['reading_time']) {
            return $this->attributes['reading_time'];
        }
        
        $wordCount = str_word_count(strip_tags($this->content));
        $readingTime = ceil($wordCount / 200); // 200 слов в минуту
        
        $this->update(['reading_time' => $readingTime]);
        return $readingTime;
    }

    /**
     * Получить локализованный заголовок
     */
    public function getLocalizedTitleAttribute()
    {
        $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
        
        // Если переводы не загружены, загружаем их
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }
        
        $translation = $this->translation($currentLanguage);
        
        return $translation ? $translation->title : $this->title;
    }

    /**
     * Получить локализованное описание
     */
    public function getLocalizedExcerptAttribute()
    {
        $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
        
        // Если переводы не загружены, загружаем их
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }
        
        $translation = $this->translation($currentLanguage);
        
        return $translation ? $translation->excerpt : $this->excerpt;
    }

    /**
     * Получить локализованный контент
     */
    public function getLocalizedContentAttribute()
    {
        $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
        
        // Если переводы не загружены, загружаем их
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }
        
        $translation = $this->translation($currentLanguage);
        
        return $translation ? $translation->content : $this->content;
    }

    /**
     * Получить локализованный meta_title
     */
    public function getLocalizedMetaTitleAttribute()
    {
        $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
        
        // Если переводы не загружены, загружаем их
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }
        
        $translation = $this->translation($currentLanguage);
        
        return $translation ? $translation->meta_title : $this->meta_title;
    }

    /**
     * Получить локализованный meta_description
     */
    public function getLocalizedMetaDescriptionAttribute()
    {
        $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
        
        // Если переводы не загружены, загружаем их
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }
        
        $translation = $this->translation($currentLanguage);
        
        return $translation ? $translation->meta_description : $this->meta_description;
    }

    /**
     * Получить локализованный meta_keywords
     */
    public function getLocalizedMetaKeywordsAttribute()
    {
        $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
        
        // Если переводы не загружены, загружаем их
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }
        
        $translation = $this->translation($currentLanguage);
        
        return $translation ? $translation->meta_keywords : $this->meta_keywords;
    }
}
