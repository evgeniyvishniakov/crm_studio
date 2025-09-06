<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BlogCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = BlogCategory::with('translations.language')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);
        
        return view('admin.blog.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::getActive();
        return view('admin.blog.categories.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $category = BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color ?: '#007bff',
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?: 0,
        ]);

        // Переводы создаются только при необходимости через модальное окно

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Категория успешно создана!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = BlogCategory::with('translations.language')->findOrFail($id);
        $languages = Language::getActive();
        
        return view('admin.blog.categories.edit', compact('category', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color ?: '#007bff',
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?: 0,
        ]);

        // Обновление переводов на всех языках
        $languages = Language::getActive();
        foreach ($languages as $language) {
            $translation = $category->translations()->where('locale', $language->code)->first();
            if ($translation) {
                $translation->update([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);
            } else {
                $category->translations()->create([
                    'locale' => $language->code,
                    'name' => $request->name,
                    'description' => $request->description,
                ]);
            }
        }

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Категория успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Категория успешно удалена!');
    }

    /**
     * Получить перевод категории на определенном языке
     */
    public function getTranslation(string $id, string $language)
    {
        $category = BlogCategory::findOrFail($id);
        $translation = $category->translation($language);
        
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
                'description' => $translation->description,
            ]
        ]);
    }

    /**
     * Сохранить перевод категории
     */
    public function saveTranslation(Request $request, string $id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            
            $request->validate([
                'language_code' => 'required|string',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $translation = $category->translations()->where('locale', $request->language_code)->first();
            
            if ($translation) {
                $translation->update([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);
            } else {
                $category->translations()->create([
                    'locale' => $request->language_code,
                    'name' => $request->name,
                    'description' => $request->description,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Перевод успешно сохранен'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения перевода: ' . $e->getMessage()
            ], 500);
        }
    }
}
