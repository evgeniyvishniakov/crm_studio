<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_article_id',
        'locale',
        'title',
        'description'
    ];

    /**
     * Связь с основной статьей
     */
    public function article()
    {
        return $this->belongsTo(KnowledgeArticle::class, 'knowledge_article_id');
    }

    /**
     * Связь с языком через locale
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'locale', 'code');
    }
}
