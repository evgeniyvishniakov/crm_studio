<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeController extends Controller
{
    /**
     * Получить стандартные категории
     */
    private function getStandardCategories()
    {
        return [
            'getting-started' => 'Начало работы',
            'features' => 'Возможности',
            'integrations' => 'Интеграции',
            'troubleshooting' => 'Решение проблем',
            'tips' => 'Полезные советы'
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = KnowledgeArticle::orderBy('created_at', 'desc')->paginate(20);
        $categories = $this->getStandardCategories();
        
        return view('admin.knowledge.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getStandardCategories();
        return view('admin.knowledge.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'content' => 'required|string',
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['author'] = $request->author ?: 'Команда Trimora';
        
        if ($request->is_published) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('knowledge', 'public');
            $data['featured_image'] = $imagePath;
        }

        KnowledgeArticle::create($data);

        return redirect()->route('admin.knowledge.index')
            ->with('success', 'Статья успешно создана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = KnowledgeArticle::findOrFail($id);
        $categories = $this->getStandardCategories();
        return view('admin.knowledge.show', compact('article', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = KnowledgeArticle::findOrFail($id);
        $categories = $this->getStandardCategories();
        
        return view('admin.knowledge.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'content' => 'required|string',
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean'
        ]);

        $article = KnowledgeArticle::findOrFail($id);
        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        
        if ($request->is_published && !$article->is_published) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('knowledge', 'public');
            $data['featured_image'] = $imagePath;
        }

        $article->update($data);

        return redirect()->route('admin.knowledge.index')
            ->with('success', 'Статья успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = KnowledgeArticle::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.knowledge.index')
            ->with('success', 'Статья успешно удалена!');
    }
}
