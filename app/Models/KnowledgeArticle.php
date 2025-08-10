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
