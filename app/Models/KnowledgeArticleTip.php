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

    public function article()
    {
        return $this->belongsTo(KnowledgeArticle::class, 'knowledge_article_id');
    }
}
