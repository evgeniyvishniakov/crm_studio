<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeArticleStep;
use App\Models\KnowledgeArticleTip;
use App\Models\KnowledgeArticleTranslation;
use App\Models\KnowledgeArticleStepTranslation;
use App\Models\KnowledgeArticleTipTranslation;
use App\Models\Language;
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
        $articles = KnowledgeArticle::with(['translations.language'])->orderBy('created_at', 'desc')->paginate(20);
        $categories = $this->getStandardCategories();
        
        return view('admin.knowledge.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getStandardCategories();
        $languages = Language::getActive();
        return view('admin.knowledge.create', compact('categories', 'languages'));
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
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.content' => 'required_with:steps|string',
            'tips' => 'nullable|array',
            'tips.*.content' => 'required_with:tips|string'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['author'] = $request->author ?: 'Команда Trimora';
        
        // Обрабатываем статус публикации
        $data['is_published'] = $request->has('is_published');
        if ($data['is_published']) {
            $data['published_at'] = now();
        } else {
            $data['published_at'] = null;
        }

        // Обрабатываем главное изображение
        if ($request->hasFile('featured_image')) {
            try {
                $imagePath = $request->file('featured_image')->store('knowledge', 'public');
                $data['featured_image'] = $imagePath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['featured_image' => 'Ошибка загрузки изображения: ' . $e->getMessage()]);
            }
        }

        // Создаем статью
        $article = KnowledgeArticle::create($data);

        // Получаем все активные языки
        $languages = Language::getActive();
        
        // Создаем переводы для статьи на всех языках
        foreach ($languages as $language) {
            $article->translations()->create([
                'locale' => $language->code,
                'title' => $request->title, // Пока используем основной заголовок
                'description' => $request->description // Пока используем основное описание
            ]);
        }

        // Добавляем шаги
        if ($request->has('steps') && is_array($request->steps)) {
            foreach ($request->steps as $index => $step) {
                if (!empty($step['title']) && !empty($step['content'])) {
                    $stepData = [
                        'title' => $step['title'],
                        'content' => $step['content'],
                        'sort_order' => $index + 1
                    ];
                    
                    // Обрабатываем картинку для шага
                    if (isset($step['image']) && $step['image'] instanceof \Illuminate\Http\UploadedFile) {
                        try {
                            $stepImagePath = $step['image']->store('knowledge/steps', 'public');
                            $stepData['image'] = $stepImagePath;
                        } catch (\Exception $e) {
                            // Продолжаем создание шага без картинки
                        }
                    }
                    
                    $stepModel = $article->steps()->create($stepData);
                    
                    // Создаем переводы для шага на всех языках
                    foreach ($languages as $language) {
                        $stepModel->translations()->create([
                            'locale' => $language->code,
                            'title' => $step['title'], // Пока используем основной заголовок
                            'content' => $step['content'] // Пока используем основное содержание
                        ]);
                    }
                }
            }
        }

        // Добавляем полезные советы
        if ($request->has('tips') && is_array($request->tips)) {
            foreach ($request->tips as $index => $tip) {
                if (!empty($tip['content'])) {
                    $tipModel = $article->tips()->create([
                        'content' => $tip['content'],
                        'sort_order' => $index + 1
                    ]);
                    
                    // Создаем переводы для совета на всех языках
                    foreach ($languages as $language) {
                        $tipModel->translations()->create([
                            'locale' => $language->code,
                            'content' => $tip['content'] // Пока используем основное содержание
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.knowledge.index')
            ->with('success', 'Статья успешно создана на всех языках! Теперь вы можете отредактировать переводы для каждого языка.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = KnowledgeArticle::with(['translations.language', 'steps.translations.language', 'tips.translations.language'])->findOrFail($id);
        $categories = $this->getStandardCategories();
        return view('admin.knowledge.show', compact('article', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = KnowledgeArticle::with(['translations.language', 'steps.translations.language', 'tips.translations.language'])->findOrFail($id);
        $categories = $this->getStandardCategories();
        $languages = Language::getActive();
        
        return view('admin.knowledge.edit', compact('article', 'categories', 'languages'));
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
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.content' => 'required_with:steps|string',
            'tips' => 'nullable|array',
            'tips.*.content' => 'required_with:tips|string'
        ]);

        $article = KnowledgeArticle::findOrFail($id);
        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        
        // Обрабатываем статус публикации
        $data['is_published'] = $request->has('is_published');
        if ($data['is_published']) {
            if (!$article->is_published) {
                $data['published_at'] = now();
            }
        } else {
            $data['published_at'] = null;
        }

        // Обрабатываем главное изображение
        if ($request->hasFile('featured_image')) {
            try {
                $imagePath = $request->file('featured_image')->store('knowledge', 'public');
                $data['featured_image'] = $imagePath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['featured_image' => 'Ошибка загрузки изображения: ' . $e->getMessage()]);
            }
        } elseif ($request->has('delete_featured_image') && $request->delete_featured_image) {
            $data['featured_image'] = null;
        } else {
            $data['featured_image'] = $article->featured_image;
        }

        $article->update($data);
        
        // Получаем все активные языки
        $languages = Language::getActive();
        
        // Обновляем или создаем переводы для статьи на всех языках
        foreach ($languages as $language) {
            $translation = $article->translations()->where('locale', $language->code)->first();
            if ($translation) {
                $translation->update([
                    'title' => $request->title,
                    'description' => $request->description
                ]);
            } else {
                $article->translations()->create([
                    'locale' => $language->code,
                    'title' => $request->title,
                    'description' => $request->description
                ]);
            }
        }

        // Обновляем шаги
        if ($request->has('steps') && is_array($request->steps)) {
            // Получаем существующие шаги для сохранения изображений
            $existingSteps = $article->steps()->get()->keyBy('sort_order');
            
            // Удаляем старые шаги
            $article->steps()->delete();
            
            // Добавляем новые
            foreach ($request->steps as $index => $step) {
                if (!empty($step['title']) && !empty($step['content'])) {
                    $stepData = [
                        'title' => $step['title'],
                        'content' => $step['content'],
                        'sort_order' => $index + 1
                    ];
                    
                    // Обрабатываем картинку для шага
                    if (isset($step['image']) && $step['image'] instanceof \Illuminate\Http\UploadedFile) {
                        try {
                            $stepImagePath = $step['image']->store('knowledge/steps', 'public');
                            $stepData['image'] = $stepImagePath;
                        } catch (\Exception $e) {
                            // Продолжаем создание шага без картинки
                        }
                    } elseif (isset($step['delete_image']) && $step['delete_image']) {
                        $stepData['image'] = null;
                    } else {
                        // Сохраняем существующее изображение
                        $existingStep = $existingSteps->get($index + 1);
                        if ($existingStep && $existingStep->image) {
                            $stepData['image'] = $existingStep->image;
                        }
                    }
                    
                    $stepModel = $article->steps()->create($stepData);
                    
                    // Создаем переводы для шага на всех языках
                    foreach ($languages as $language) {
                        $stepModel->translations()->create([
                            'locale' => $language->code,
                            'title' => $step['title'],
                            'content' => $step['content']
                        ]);
                    }
                }
            }
        }

        // Обновляем полезные советы
        if ($request->has('tips') && is_array($request->tips)) {
            // Удаляем старые советы
            $article->tips()->delete();
            
            // Добавляем новые
            foreach ($request->tips as $index => $tip) {
                if (!empty($tip['content'])) {
                    $tipModel = $article->tips()->create([
                        'content' => $tip['content'],
                        'sort_order' => $index + 1
                    ]);
                    
                    // Создаем переводы для совета на всех языках
                    foreach ($languages as $language) {
                        $tipModel->translations()->create([
                            'locale' => $language->code,
                            'content' => $tip['content']
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.knowledge.index')
            ->with('success', 'Статья успешно обновлена на всех языках!');
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

    /**
     * Переключить статус публикации статьи
     */
    public function togglePublish(string $id)
    {
        $article = KnowledgeArticle::findOrFail($id);
        
        if ($article->is_published) {
            $article->update([
                'is_published' => false,
                'published_at' => null
            ]);
            $message = 'Статья снята с публикации!';
        } else {
            $article->update([
                'is_published' => true,
                'published_at' => now()
            ]);
            $message = 'Статья опубликована!';
        }

        return redirect()->back()->with('success', $message);
    }
}
