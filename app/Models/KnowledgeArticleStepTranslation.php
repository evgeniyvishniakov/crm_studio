<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticleStepTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_article_step_id',
        'locale',
        'title',
        'content'
    ];

    /**
     * Связь с основным шагом
     */
    public function step()
    {
        return $this->belongsTo(KnowledgeArticleStep::class, 'knowledge_article_step_id');
    }

    /**
     * Связь с языком через locale
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'locale', 'code');
    }
}
