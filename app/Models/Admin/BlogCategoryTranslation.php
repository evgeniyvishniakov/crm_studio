<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_category_id',
        'locale',
        'name',
        'description'
    ];

    /**
     * Связь с основной категорией
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Связь с языком через locale
     */
    public function language()
    {
        return $this->belongsTo(\App\Models\Language::class, 'locale', 'code');
    }
}
