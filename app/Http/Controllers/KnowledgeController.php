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
    public function index()
    {
        $currentLanguage = LanguageHelper::getCurrentLanguage();
        

        
        $articles = KnowledgeArticle::where('is_published', true)
            ->with(['steps', 'tips', 'translations'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Группируем статьи по категориям с переводами
        $categories = [
            'getting-started' => __('landing.knowledge_category_getting_started'),
            'features' => __('landing.knowledge_category_features'),
            'tips' => __('landing.knowledge_category_tips'),
            'troubleshooting' => __('landing.knowledge_category_troubleshooting')
        ];

        return view('landing.pages.knowledge', compact('articles', 'categories'));
    }

    /**
     * Показать отдельную статью
     */
    public function show($slug)
    {
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
