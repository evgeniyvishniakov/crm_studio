<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Admin\BlogArticle;
use App\Models\Admin\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog articles.
     */
    public function index(Request $request, $lang = null)
    {
        // Устанавливаем язык если передан параметр
        if ($lang) {
            \App\Helpers\LanguageHelper::setLanguage($lang);
        } else {
            // Для fallback маршрута устанавливаем украинский язык по умолчанию
            \App\Helpers\LanguageHelper::setLanguage('ua');
        }
        
        $query = BlogArticle::published()
            ->with(['category.translations', 'tags.translations', 'translations'])
            ->orderBy('published_at', 'desc');
        
        // Фильтрация по категории если передана
        if ($request->has('category')) {
            $categorySlug = $request->get('category');
            $query->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        // Фильтрация по тегу если передан
        if ($request->has('tag')) {
            $tagSlug = $request->get('tag');
            $query->whereHas('tags', function($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }
        
        $articles = $query->paginate(12);
        
        $categories = BlogCategory::active()
            ->with('translations')
            ->get();
        
        // Получаем информацию о текущем фильтре
        $currentFilter = null;
        if ($request->has('category')) {
            $currentFilter = BlogCategory::where('slug', $request->get('category'))->first();
        } elseif ($request->has('tag')) {
            $currentFilter = \App\Models\Admin\BlogTag::where('slug', $request->get('tag'))->first();
        }
        
        return view('landing.pages.blog', compact('articles', 'categories', 'currentFilter'));
    }

    /**
     * Display the specified blog article (with language parameter).
     */
    public function show(Request $request, $lang, $slug)
    {
        // Устанавливаем язык
        \App\Helpers\LanguageHelper::setLanguage($lang);
        
        $article = BlogArticle::published()
            ->with(['category.translations', 'tags.translations', 'translations'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Увеличиваем счетчик просмотров
        $article->increment('views_count');
        
        // Получаем похожие статьи (из той же категории, исключая текущую)
        $similarArticles = BlogArticle::published()
            ->with(['category.translations', 'tags.translations', 'translations'])
            ->where('id', '!=', $article->id)
            ->when($article->blog_category_id, function($query) use ($article) {
                return $query->where('blog_category_id', $article->blog_category_id);
            })
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();
        
        return view('landing.pages.blog-show', compact('article', 'similarArticles'));
    }

    /**
     * Display the specified blog article (fallback for default language).
     */
    public function showFallback(Request $request, $slug)
    {
        // Устанавливаем украинский язык по умолчанию для fallback маршрута
        \App\Helpers\LanguageHelper::setLanguage('ua');
        
        $article = BlogArticle::published()
            ->with(['category.translations', 'tags.translations', 'translations'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Увеличиваем счетчик просмотров
        $article->increment('views_count');
        
        // Получаем похожие статьи (из той же категории, исключая текущую)
        $similarArticles = BlogArticle::published()
            ->with(['category.translations', 'tags.translations', 'translations'])
            ->where('id', '!=', $article->id)
            ->when($article->blog_category_id, function($query) use ($article) {
                return $query->where('blog_category_id', $article->blog_category_id);
            })
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();
        
        return view('landing.pages.blog-show', compact('article', 'similarArticles'));
    }
}
