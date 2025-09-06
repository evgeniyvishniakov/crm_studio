<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BlogTag;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = BlogTag::with('translations.language')
            ->orderBy('name')
            ->paginate(20);
        
        return view('admin.blog.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::getActive();
        return view('admin.blog.tags.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean'
        ]);

        $tag = BlogTag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color ?: '#6c757d',
            'is_active' => $request->boolean('is_active'),
        ]);

        // Переводы создаются только при необходимости через модальное окно

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Тег успешно создан!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tag = BlogTag::with('translations.language')->findOrFail($id);
        $languages = Language::getActive();
        
        return view('admin.blog.tags.edit', compact('tag', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tag = BlogTag::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean'
        ]);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color ?: '#6c757d',
            'is_active' => $request->boolean('is_active'),
        ]);

        // Обновление переводов на всех языках
        $languages = Language::getActive();
        foreach ($languages as $language) {
            $translation = $tag->translations()->where('locale', $language->code)->first();
            if ($translation) {
                $translation->update([
                    'name' => $request->name,
                ]);
            } else {
                $tag->translations()->create([
                    'locale' => $language->code,
                    'name' => $request->name,
                ]);
            }
        }

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Тег успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tag = BlogTag::findOrFail($id);
        $tag->delete();

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Тег успешно удален!');
    }

    /**
     * Получить перевод тега на определенном языке
     */
    public function getTranslation(string $id, string $language)
    {
        $tag = BlogTag::findOrFail($id);
        $translation = $tag->translation($language);
        
        if (!$translation) {
            return response()->json([
                'success' => false,
                'translation' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'translation' => [
                'name' => $translation->name,
            ]
        ]);
    }

    /**
     * Сохранить перевод тега
     */
    public function saveTranslation(Request $request, string $id)
    {
        $tag = BlogTag::findOrFail($id);
        
        $request->validate([
            'language_code' => 'required|string',
            'name' => 'required|string|max:255',
        ]);

        $translation = $tag->translations()->where('locale', $request->language_code)->first();
        
        if ($translation) {
            $translation->update([
                'name' => $request->name,
            ]);
        } else {
            $tag->translations()->create([
                'locale' => $request->language_code,
                'name' => $request->name,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Перевод успешно сохранен'
        ]);
    }
}
