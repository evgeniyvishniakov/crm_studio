<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class KnowledgeController extends Controller
{
    /**
     * Показать страницу базы знаний с опубликованными статьями
     */
    public function index($lang = null)
    {
        // Устанавливаем язык если передан параметр
        if ($lang) {
            LanguageHelper::setLanguage($lang);
        } else {
            // Для fallback маршрута устанавливаем украинский язык по умолчанию
            LanguageHelper::setLanguage('ua');
        }
        
        $currentLanguage = LanguageHelper::getCurrentLanguage();
        

        
        // Всегда загружаем все статьи для фильтрации на клиенте
        $articles = KnowledgeArticle::where('is_published', true)
            ->with(['steps', 'tips', 'translations'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Определяем нужна ли пагинация для отображения
        $needsPagination = $articles->count() > 9;

        // Группируем статьи по категориям с переводами
        $categories = [
            'getting-started' => __('landing.knowledge_category_getting_started'),
            'features' => __('landing.knowledge_category_features'),
            'tips' => __('landing.knowledge_category_tips'),
            'troubleshooting' => __('landing.knowledge_category_troubleshooting')
        ];

        return view('landing.pages.knowledge', compact('articles', 'categories', 'needsPagination'));
    }

    /**
     * Показать отдельную статью
     */
    public function show($slug)
    {
        // Устанавливаем украинский язык по умолчанию
        LanguageHelper::setLanguage('ua');
        
        $currentLanguage = LanguageHelper::getCurrentLanguage();
        

        
        $article = KnowledgeArticle::where('slug', $slug)
            ->where('is_published', true)
            ->with(['steps', 'tips', 'translations'])
            ->firstOrFail();

        $categories = [
            'getting-started' => __('landing.knowledge_category_getting_started'),
            'features' => __('landing.knowledge_category_features'),
            'tips' => __('landing.knowledge_category_tips'),
            'troubleshooting' => __('landing.knowledge_category_troubleshooting')
        ];

        return view('landing.pages.knowledge-show', compact('article', 'categories'));
    }
}
