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
        'sort_order',
        'type'
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
    // Добавьте константы для типов
const TYPE_INFO = 'info';
const TYPE_WARNING = 'warning';
const TYPE_SUCCESS = 'success';
const TYPE_DANGER = 'danger';
const TYPE_PRIMARY = 'primary';
const TYPE_LIGHT = 'light';
const TYPE_DARK = 'dark';

    public static function getTypes()
    {
        return [
            self::TYPE_INFO => 'Информация',
            self::TYPE_WARNING => 'Предупреждение',
            self::TYPE_SUCCESS => 'Успех',
            self::TYPE_DANGER => 'Ошибка',
            self::TYPE_PRIMARY => 'Основной',
            self::TYPE_LIGHT => 'Светлый',
            self::TYPE_DARK => 'Темный',
        ];
    }
    
    /**
     * Получить содержимое совета (с переводом или основное)
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
