<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticleStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_article_id',
        'title',
        'content',
        'image',
        'sort_order'
    ];

    /**
     * Связь с переводами
     */
    public function translations()
    {
        return $this->hasMany(KnowledgeArticleStepTranslation::class);
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

    public function article()
    {
        return $this->belongsTo(KnowledgeArticle::class, 'knowledge_article_id');
    }
    
    /**
     * Получить заголовок шага (с переводом или основной)
     */
    public function getTitleAttribute($value)
    {
        // Если есть перевод для текущего языка, используем его
        $locale = app()->getLocale();
        $translation = $this->translation($locale);
        
        if ($translation && $translation->title) {
            return $translation->title;
        }
        
        return $value;
    }
    
    /**
     * Получить содержимое шага (с переводом или основное)
     */
    public function getContentAttribute($value)
    {
        // Если есть перевод для текущего языка, используем его
        $locale = app()->getLocale();
        $translation = $this->translation($locale);
        
        if ($translation && $translation->content) {
            return $translation->content;
        }
        
        return $value;
    }
}
