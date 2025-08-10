<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use Illuminate\Http\Request;

class KnowledgeController extends Controller
{
    /**
     * Показать страницу базы знаний с опубликованными статьями
     */
    public function index()
    {
        $articles = KnowledgeArticle::where('is_published', true)
            ->with(['steps', 'tips'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Группируем статьи по категориям
        $categories = [
            'getting-started' => 'Начало работы',
            'features' => 'Функции',
            'tips' => 'Советы',
            'troubleshooting' => 'Решение проблем'
        ];

        return view('landing.pages.knowledge', compact('articles', 'categories'));
    }

    /**
     * Показать отдельную статью
     */
    public function show($slug)
    {
        $article = KnowledgeArticle::where('slug', $slug)
            ->where('is_published', true)
            ->with(['steps', 'tips'])
            ->firstOrFail();

        $categories = [
            'getting-started' => 'Начало работы',
            'features' => 'Функции',
            'tips' => 'Советы',
            'troubleshooting' => 'Решение проблем'
        ];

        return view('landing.pages.knowledge-article', compact('article', 'categories'));
    }
}
