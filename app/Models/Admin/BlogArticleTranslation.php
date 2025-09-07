<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogArticleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_article_id',
        'locale',
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    /**
     * Связь с основной статьей
     */
    public function article()
    {
        return $this->belongsTo(BlogArticle::class, 'blog_article_id');
    }

    /**
     * Связь с языком через locale
     */
    public function language()
    {
        return $this->belongsTo(\App\Models\Language::class, 'locale', 'code');
    }
}

