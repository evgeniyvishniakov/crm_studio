<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeArticleStep;
use App\Models\KnowledgeArticleTip;
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
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean'
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

        if ($request->hasFile('featured_image')) {
            try {
                $imagePath = $request->file('featured_image')->store('knowledge', 'public');
                $data['featured_image'] = $imagePath;
                
                // Логируем успешную загрузку
                \Log::info('Image uploaded successfully', [
                    'original_name' => $request->file('featured_image')->getClientOriginalName(),
                    'stored_path' => $imagePath,
                    'full_path' => storage_path('app/public/' . $imagePath)
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload image', [
                    'error' => $e->getMessage(),
                    'file' => $request->file('featured_image')->getClientOriginalName()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['featured_image' => 'Ошибка загрузки изображения: ' . $e->getMessage()]);
            }
        }

        $article = KnowledgeArticle::create($data);

        // Добавляем шаги, если они есть
        if ($request->has('steps') && is_array($request->steps)) {
            foreach ($request->steps as $index => $step) {
                if (!empty($step['title']) && !empty($step['content'])) {
                    $stepData = [
                        'title' => $step['title'],
                        'content' => $step['content'],
                        'sort_order' => $index + 1
                    ];
                    
                    // Обрабатываем картинку для шага, если она есть
                    if (isset($step['image']) && $step['image'] instanceof \Illuminate\Http\UploadedFile) {
                        try {
                            $stepImagePath = $step['image']->store('knowledge/steps', 'public');
                            $stepData['image'] = $stepImagePath;
                            
                            \Log::info('Step image uploaded successfully', [
                                'step_index' => $index,
                                'original_name' => $step['image']->getClientOriginalName(),
                                'stored_path' => $stepImagePath
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to upload step image', [
                                'step_index' => $index,
                                'error' => $e->getMessage()
                            ]);
                            // Продолжаем создание шага без картинки
                        }
                    }
                    
                    $article->steps()->create($stepData);
                }
            }
        }

        // Добавляем полезные советы, если они есть
        if ($request->has('tips') && is_array($request->tips)) {
            foreach ($request->tips as $index => $tip) {
                if (!empty($tip['content'])) {
                    $article->tips()->create([
                        'content' => $tip['content'],
                        'sort_order' => $index + 1
                    ]);
                }
            }
        }

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
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean'
        ]);

        $article = KnowledgeArticle::findOrFail($id);
        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        
        // Логируем данные для отладки
        \Log::info('Updating article', [
            'article_id' => $id,
            'existing_featured_image' => $article->featured_image,
            'request_has_file' => $request->hasFile('featured_image'),
            'request_data' => $request->all()
        ]);
        
        // Обрабатываем статус публикации
        $data['is_published'] = $request->has('is_published');
        if ($data['is_published']) {
            if (!$article->is_published) {
                $data['published_at'] = now();
            }
        } else {
            // Если статья снимается с публикации, очищаем published_at
            $data['published_at'] = null;
        }

        if ($request->hasFile('featured_image')) {
            try {
                $imagePath = $request->file('featured_image')->store('knowledge', 'public');
                $data['featured_image'] = $imagePath;
                
                // Логируем успешную загрузку
                \Log::info('Image uploaded successfully', [
                    'original_name' => $request->file('featured_image')->getClientOriginalName(),
                    'stored_path' => $imagePath,
                    'full_path' => storage_path('app/public/' . $imagePath)
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload image', [
                    'error' => $e->getMessage(),
                    'file' => $request->file('featured_image')->getClientOriginalName()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['featured_image' => 'Ошибка загрузки изображения: ' . $e->getMessage()]);
            }
        } elseif ($request->has('delete_featured_image') && $request->delete_featured_image) {
            // Если отмечен чекбокс удаления, очищаем поле
            $data['featured_image'] = null;
            \Log::info('Deleting featured image as requested');
        } else {
            // Если новое изображение не загружено и не отмечено удаление, сохраняем существующее
            $data['featured_image'] = $article->featured_image;
            \Log::info('Preserving existing featured image', [
                'existing_image' => $article->featured_image,
                'final_data_featured_image' => $data['featured_image']
            ]);
        }
        
        // Логируем финальные данные перед обновлением
        \Log::info('Final data before update', [
            'featured_image' => $data['featured_image'],
            'data_keys' => array_keys($data)
        ]);

        $article->update($data);
        
        // Логируем результат обновления
        \Log::info('Article updated successfully', [
            'article_id' => $id,
            'final_featured_image' => $article->fresh()->featured_image,
            'was_image_preserved' => $article->fresh()->featured_image === $article->getOriginal('featured_image')
        ]);

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
                            
                            \Log::info('Step image uploaded successfully', [
                                'step_index' => $index,
                                'original_name' => $step['image']->getClientOriginalName(),
                                'stored_path' => $stepImagePath
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to upload step image', [
                                'step_index' => $index,
                                'error' => $e->getMessage()
                            ]);
                            // Продолжаем создание шага без картинки
                        }
                    } elseif (isset($step['delete_image']) && $step['delete_image']) {
                        // Если отмечен чекбокс удаления, очищаем поле
                        $stepData['image'] = null;
                        \Log::info('Deleting step image as requested', [
                            'step_index' => $index
                        ]);
                    } else {
                        // Если новое изображение не загружено и не отмечено удаление, сохраняем существующее
                        $existingStep = $existingSteps->get($index + 1);
                        if ($existingStep && $existingStep->image) {
                            $stepData['image'] = $existingStep->image;
                            \Log::info('Preserved existing step image', [
                                'step_index' => $index,
                                'existing_image' => $existingStep->image
                            ]);
                        }
                    }
                    
                    $article->steps()->create($stepData);
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
                    $article->tips()->create([
                        'content' => $tip['content'],
                        'sort_order' => $index + 1
                    ]);
                }
            }
        }

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

    /**
     * Переключить статус публикации статьи
     */
    public function togglePublish(string $id)
    {
        $article = KnowledgeArticle::findOrFail($id);
        
        if ($article->is_published) {
            // Снимаем с публикации
            $article->update([
                'is_published' => false,
                'published_at' => null
            ]);
            $message = 'Статья снята с публикации!';
        } else {
            // Публикуем
            $article->update([
                'is_published' => true,
                'published_at' => now()
            ]);
            $message = 'Статья опубликована!';
        }

        return redirect()->back()->with('success', $message);
    }
}
