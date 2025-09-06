<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTagTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_tag_id',
        'locale',
        'name'
    ];

    /**
     * Связь с основным тегом
     */
    public function tag()
    {
        return $this->belongsTo(BlogTag::class, 'blog_tag_id');
    }

    /**
     * Связь с языком через locale
     */
    public function language()
    {
        return $this->belongsTo(\App\Models\Language::class, 'locale', 'code');
    }
}
