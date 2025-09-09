<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BlogArticle;
use App\Models\Admin\BlogCategory;
use App\Models\Admin\BlogTag;
use App\Models\Admin\BlogArticleTranslation;
use App\Models\Admin\BlogCategoryTranslation;
use App\Models\Admin\BlogTagTranslation;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = BlogArticle::with(['translations', 'category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $categories = BlogCategory::with('translations')->get();
        $tags = BlogTag::with('translations')->get();
        
        return view('admin.blog.index', compact('articles', 'categories', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = BlogCategory::with('translations')->get();
        $tags = BlogTag::with('translations')->get();
        $languages = Language::getActive();
        
        return view('admin.blog.create', compact('categories', 'tags', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id'
        ]);

        // Обработка изображения
        $featuredImage = null;
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/blog', $filename);
            $featuredImage = 'blog/' . $filename;
        }

        // Создание статьи
        $article = BlogArticle::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'blog_category_id' => $request->blog_category_id,
            'author' => $request->author ?: 'Trimora',
            'featured_image' => $featuredImage,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $request->published_at ?: ($request->boolean('is_published') ? now() : null),
        ]);

        // Привязка тегов
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        // Переводы создаются только при необходимости через модальное окно

        return redirect()->route('admin.blog.index')
            ->with('success', 'Статья блога успешно создана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = BlogArticle::with(['translations', 'category', 'tags'])->findOrFail($id);
        return view('admin.blog.show', compact('article'));
    }

    /**
     * Получить данные статьи в JSON формате
     */
    public function getArticleData(string $id)
    {
        $article = BlogArticle::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'article' => [
                'title' => $article->title,
                'excerpt' => $article->excerpt,
                'content' => $article->content,
                'meta_title' => $article->meta_title,
                'meta_description' => $article->meta_description,
                'meta_keywords' => $article->meta_keywords,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = BlogArticle::with(['translations', 'category', 'tags'])->findOrFail($id);
        $categories = BlogCategory::with('translations')->get();
        $tags = BlogTag::with('translations')->get();
        $languages = Language::getActive();
        
        return view('admin.blog.edit', compact('article', 'categories', 'tags', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = BlogArticle::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'remove_image' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id'
        ]);

        // Обработка изображения
        $featuredImage = $article->featured_image;
        
        if ($request->boolean('remove_image')) {
            // Удаляем изображение
            if ($article->featured_image && Storage::exists('public/' . $article->featured_image)) {
                Storage::delete('public/' . $article->featured_image);
            }
            $featuredImage = null;
        } elseif ($request->hasFile('featured_image')) {
            // Загружаем новое изображение
            $file = $request->file('featured_image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/blog', $filename);
            $featuredImage = 'blog/' . $filename;
            
            // Удаляем старое изображение если оно было
            if ($article->featured_image && Storage::exists('public/' . $article->featured_image)) {
                Storage::delete('public/' . $article->featured_image);
            }
        }

        // Обновление статьи
        $article->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'blog_category_id' => $request->blog_category_id,
            'author' => $request->author ?: 'Trimora',
            'featured_image' => $featuredImage,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $request->published_at ?: ($request->boolean('is_published') && !$article->published_at ? now() : $article->published_at),
        ]);

        // Обновление тегов
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        } else {
            $article->tags()->detach();
        }

        // Переводы обновляются только через модальное окно переводов

        return redirect()->route('admin.blog.index')
            ->with('success', 'Статья блога успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = BlogArticle::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Статья блога успешно удалена!');
    }

    /**
     * Переключить статус публикации статьи
     */
    public function togglePublish(string $id)
    {
        $article = BlogArticle::findOrFail($id);
        
        if ($article->is_published) {
            $article->update([
                'is_published' => false,
                'published_at' => null
            ]);
            $message = 'Статья снята с публикации';
        } else {
            $article->update([
                'is_published' => true,
                'published_at' => now()
            ]);
            $message = 'Статья опубликована';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_published' => $article->is_published
        ]);
    }

    /**
     * Получить перевод статьи на определенном языке
     */
    public function getTranslation(string $id, string $language)
    {
        $article = BlogArticle::with('translations')->findOrFail($id);
        
        // Ищем перевод напрямую в загруженных переводах
        $translation = $article->translations->where('locale', $language)->first();
        
        // Если перевода нет, возвращаем оригинальный контент для удобного копирования
        if (!$translation) {
            return response()->json([
                'success' => true,
                'translation' => [
                    'title' => $article->title,
                    'excerpt' => $article->excerpt,
                    'content' => $article->content,
                    'featured_image' => $article->featured_image,
                    'meta_title' => $article->meta_title,
                    'meta_description' => $article->meta_description,
                    'meta_keywords' => $article->meta_keywords,
                ],
                'is_original' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'translation' => [
                'title' => $translation->title,
                'excerpt' => $translation->excerpt,
                'content' => $translation->content,
                'featured_image' => $translation->featured_image,
                'meta_title' => $translation->meta_title,
                'meta_description' => $translation->meta_description,
                'meta_keywords' => $translation->meta_keywords,
            ],
            'is_original' => false
        ]);
    }

    /**
     * Сохранить перевод статьи
     */
    public function saveTranslation(Request $request, string $id)
    {
        $article = BlogArticle::findOrFail($id);
        
        $request->validate([
            'language_code' => 'required|string',
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'remove_featured_image' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
        ]);

        $translation = $article->translations()->where('locale', $request->language_code)->first();
        
        // Обработка изображения для перевода
        $featuredImage = $translation ? $translation->featured_image : null;
        
        if ($request->boolean('remove_featured_image')) {
            // Удаляем изображение перевода
            if ($featuredImage && Storage::exists('public/' . $featuredImage)) {
                Storage::delete('public/' . $featuredImage);
            }
            $featuredImage = null;
        } elseif ($request->hasFile('featured_image')) {
            // Загружаем новое изображение для перевода
            $file = $request->file('featured_image');
            $filename = time() . '_' . Str::slug($request->title) . '_' . $request->language_code . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/blog/translations', $filename);
            $featuredImage = 'blog/translations/' . $filename;
            
            // Удаляем старое изображение перевода если оно было
            if ($translation && $translation->featured_image && Storage::exists('public/' . $translation->featured_image)) {
                Storage::delete('public/' . $translation->featured_image);
            }
        }
        
        if ($translation) {
            $translation->update([
                'title' => $request->title,
                'excerpt' => $request->excerpt,
                'content' => $request->content,
                'featured_image' => $featuredImage,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
            ]);
        } else {
            $article->translations()->create([
                'locale' => $request->language_code,
                'title' => $request->title,
                'excerpt' => $request->excerpt,
                'content' => $request->content,
                'featured_image' => $featuredImage,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Перевод успешно сохранен'
        ]);
    }
}
