<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticleTipTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_article_tip_id',
        'locale',
        'content'
    ];

    /**
     * Связь с основным советом
     */
    public function tip()
    {
        return $this->belongsTo(KnowledgeArticleTip::class, 'knowledge_article_tip_id');
    }

    /**
     * Связь с языком через locale
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'locale', 'code');
    }
}
