<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticleTip extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_article_id',
        'content',
        'sort_order'
    ];

    /**
     * Связь с переводами
     */
    public function translations()
    {
        return $this->hasMany(KnowledgeArticleTipTranslation::class);
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
}
