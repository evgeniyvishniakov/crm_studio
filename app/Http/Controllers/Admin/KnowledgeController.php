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
            'features' => 'Работа с клиентами',
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
            'tips.*.content' => 'nullable|string'
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
        try {
            $article = KnowledgeArticle::create($data);
            \Log::info('Knowledge Article Created Successfully', [
                'article_id' => $article->id,
                'article_data' => $article->toArray()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error Creating Knowledge Article', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Ошибка создания статьи: ' . $e->getMessage()]);
        }

        // Получаем все активные языки
        $languages = Language::getActive();
        
        // Создаем переводы для статьи на всех языках
        foreach ($languages as $language) {
            $translation = $article->translations()->where('locale', $language->code)->first();
            if (!$translation) {
                $article->translations()->create([
                    'locale' => $language->code,
                    'title' => $request->title, // Пока используем основной заголовок
                    'description' => $request->description // Пока используем основное описание
                ]);
            }
            // Если перевод уже существует - НЕ ТРОГАЕМ его!
        }

        // Добавляем шаги
        if ($request->has('steps') && is_array($request->steps)) {
            \Log::info('Processing steps', ['steps_count' => count($request->steps)]);
            foreach ($request->steps as $index => $step) {
                \Log::info('Processing step', ['index' => $index, 'step_data' => $step]);
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
                    
                    try {
                        $stepModel = $article->steps()->create($stepData);
                        \Log::info('Step created successfully', ['step_id' => $stepModel->id, 'step_data' => $stepData]);
                    } catch (\Exception $e) {
                        \Log::error('Error creating step', ['error' => $e->getMessage(), 'step_data' => $stepData]);
                    }
                    
                    // Создаем переводы для шага на всех языках
                    foreach ($languages as $language) {
                        $stepTranslation = $stepModel->translations()->where('locale', $language->code)->first();
                        if (!$stepTranslation) {
                            $stepModel->translations()->create([
                                'locale' => $language->code,
                                'title' => $step['title'], // Пока используем основной заголовок
                                'content' => $step['content'] // Пока используем основное содержание
                            ]);
                        }
                        // Если перевод уже существует - НЕ ТРОГАЕМ его!
                    }
                }
            }
        } else {
            \Log::info('No steps provided or steps is not an array');
        }

        // Добавляем полезные советы
        if ($request->has('tips') && is_array($request->tips)) {
            // Фильтруем только tips с непустым content
            $validTips = array_filter($request->tips, function($tip) {
                return !empty($tip['content']);
            });
            
            foreach ($validTips as $index => $tip) {
                $tipModel = $article->tips()->create([
                    'content' => $tip['content'],
                    'sort_order' => $index + 1
                ]);
                
                // Создаем переводы для совета на всех языках
                foreach ($languages as $language) {
                    $tipTranslation = $tipModel->translations()->where('locale', $language->code)->first();
                    if (!$tipTranslation) {
                        $tipModel->translations()->create([
                            'locale' => $language->code,
                            'content' => $tip['content'] // Пока используем основное содержание
                        ]);
                    }
                    // Если перевод уже существует - НЕ ТРОГАЕМ его!
                }
            }
        }

        \Log::info('=== STORE METHOD COMPLETED SUCCESSFULLY ===');
        
        return redirect()->route('admin.knowledge.index')
            ->with('success', 'Статья успешно создана на всех языках! Теперь вы можете отредактировать переводы для каждого языка.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = KnowledgeArticle::with(['steps', 'tips'])->findOrFail($id);
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
        // Логируем начало выполнения метода
        \Log::info('Knowledge Article Update Method Started', [
            'method' => 'update',
            'article_id' => $id,
            'request_method' => $request->method(),
            'request_url' => $request->url()
        ]);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'related_articles' => 'nullable|array',
            'related_articles.*' => 'integer|exists:knowledge_articles,id',
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.content' => 'required_with:steps|string',
            'tips' => 'nullable|array',
            'tips.*.content' => 'nullable|string'
        ]);

        $article = KnowledgeArticle::findOrFail($id);
        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        
        // Логируем данные для отладки
        \Log::info('Knowledge Article Update Request', [
            'article_id' => $id,
            'request_data' => $request->all(),
            'description_from_request' => $request->input('description'),
            'current_description' => $article->description
        ]);
        
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
        
        // Логируем результат обновления
        \Log::info('Knowledge Article Update Result', [
            'article_id' => $id,
            'updated_description' => $article->fresh()->description,
            'update_success' => $article->wasChanged('description')
        ]);
        
        // Получаем все активные языки
        $languages = Language::getActive();
        
        // Обновляем или создаем переводы для статьи
        // Определяем текущий язык редактирования из формы
        $editingLanguage = $request->input('editing_language', app()->getLocale());
        
        // Проверяем, редактируем ли мы перевод или основную статью
        $isEditingTranslation = $request->boolean('is_editing_translation', false);
        
        if (!$isEditingTranslation) {
            // Если редактируем основную статью, НЕ обновляем переводы
            // Переводы должны редактироваться отдельно через модальные окна
            // foreach ($languages as $language) { ... } - убираем этот код
        }
        // Если редактируем перевод, НЕ обновляем переводы здесь

        // Обновляем шаги
        if ($request->has('steps') && is_array($request->steps) && !$isEditingTranslation) {
            // Получаем существующие шаги для сохранения изображений и переводов
            $existingSteps = $article->steps()->with('translations')->get()->keyBy('sort_order');
            
            // Обновляем или создаем шаги
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
                    
                    // Проверяем, есть ли уже шаг с таким порядком
                    $existingStep = $existingSteps->get($index + 1);
                    if ($existingStep) {
                        // Обновляем существующий шаг
                        $existingStep->update($stepData);
                        $stepModel = $existingStep;
                    } else {
                        // Создаем новый шаг
                        $stepModel = $article->steps()->create($stepData);
                    }
                    
                    // Обновляем или создаем переводы для шага
                    foreach ($languages as $language) {
                        $stepTranslation = $stepModel->translations()->where('locale', $language->code)->first();
                        if (!$stepTranslation) {
                            $stepModel->translations()->create([
                                'locale' => $language->code,
                                'title' => $step['title'],
                                'content' => $step['content']
                            ]);
                        } else {
                            // Обновляем ТОЛЬКО перевод на языке редактирования
                            // Остальные языки НЕ трогаем
                            if ($language->code === $editingLanguage) {
                                $stepTranslation->update([
                                    'title' => $step['title'],
                                    'content' => $step['content']
                                ]);
                            }
                        }
                    }
                }
            }
            
            // Удаляем шаги, которых больше нет в форме
            $usedStepOrders = collect($request->steps)->keys()->map(function($key) { return $key + 1; });
            $article->steps()->whereNotIn('sort_order', $usedStepOrders)->delete();
        }

        // Обновляем полезные советы
        if ($request->has('tips') && is_array($request->tips) && !$isEditingTranslation) {
            // Получаем существующие советы для сохранения переводов
            $existingTips = $article->tips()->with('translations')->get()->keyBy('sort_order');
            
            // Логируем для отладки
            \Log::info('Tips Update Debug', [
                'article_id' => $id,
                'request_tips' => $request->tips,
                'existing_tips_count' => $existingTips->count(),
                'existing_tips' => $existingTips->toArray()
            ]);
            
            // Обновляем или создаем советы
            foreach ($request->tips as $index => $tip) {
                if (!empty($tip['content'])) {
                    $tipData = [
                        'content' => $tip['content'],
                        'sort_order' => $index + 1
                    ];
                    
                    // Проверяем, есть ли уже совет с таким порядком
                    $existingTip = $existingTips->get($index + 1);
                    if ($existingTip) {
                        // Обновляем существующий совет
                        $existingTip->update($tipData);
                        $tipModel = $existingTip;
                    } else {
                        // Создаем новый совет
                        $tipModel = $article->tips()->create($tipData);
                    }
                    
                    // Обновляем или создаем переводы для совета
                    foreach ($languages as $language) {
                        $tipTranslation = $tipModel->translations()->where('locale', $language->code)->first();
                        if (!$tipTranslation) {
                            $tipModel->translations()->create([
                                'locale' => $language->code,
                                'content' => $tip['content']
                            ]);
                        } else {
                            // Обновляем ТОЛЬКО перевод на языке редактирования
                            // Остальные языки НЕ трогаем
                            if ($language->code === $editingLanguage) {
                                $tipTranslation->update([
                                    'content' => $tip['content']
                                ]);
                            }
                        }
                    }
                }
            }
            
            // Удаляем советы, которых больше нет в форме
            $usedTipOrders = [];
            if (!empty($request->tips)) {
                $usedTipOrders = collect($request->tips)->keys()->map(function($key) { return $key + 1; });
            }
            
            // Логируем для отладки
            \Log::info('Tips Delete Debug', [
                'article_id' => $id,
                'used_tip_orders' => $usedTipOrders,
                'tips_to_delete_count' => $article->tips()->whereNotIn('sort_order', $usedTipOrders)->count()
            ]);
            
            // Удаляем советы, которых нет в форме (включая случай, когда форма пустая)
            $deletedCount = $article->tips()->whereNotIn('sort_order', $usedTipOrders)->delete();
            
            \Log::info('Tips Delete Result', [
                'article_id' => $id,
                'deleted_count' => $deletedCount
            ]);
        } else {
            // Логируем, если поле tips отсутствует
            \Log::info('Tips Field Missing', [
                'article_id' => $id,
                'has_tips' => $request->has('tips'),
                'tips_is_array' => is_array($request->tips),
                'is_editing_translation' => $isEditingTranslation,
                'request_all' => $request->all()
            ]);
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

    /**
     * Получить перевод статьи
     */
    public function getTranslation(string $id, string $language)
    {
        \Log::info('getTranslation called', ['article_id' => $id, 'language' => $language]);
        
        $article = KnowledgeArticle::with(['translations', 'steps.translations', 'tips.translations'])->findOrFail($id);
        
        // Получаем перевод статьи
        $translation = $article->translations()->where('locale', $language)->first();
        
        \Log::info('Translation search result', [
            'article_id' => $id,
            'language' => $language,
            'translation_found' => $translation ? true : false,
            'translation_data' => $translation ? $translation->toArray() : null
        ]);
        
        if (!$translation) {
            \Log::warning('Translation not found', ['article_id' => $id, 'language' => $language]);
            return response()->json([
                'success' => false,
                'message' => 'Перевод не найден'
            ]);
        }
        
        // Получаем переводы шагов
        $stepTranslations = [];
        foreach ($article->steps as $step) {
            $stepTranslation = $step->translations()->where('locale', $language)->first();
            if ($stepTranslation) {
                $stepTranslations[] = [
                    'id' => $step->id,
                    'title' => $stepTranslation->title,
                    'content' => $stepTranslation->content
                ];
            }
        }
        
        // Получаем переводы советов
        $tipTranslations = [];
        foreach ($article->tips as $tip) {
            $tipTranslation = $tip->translations()->where('locale', $language)->first();
            if ($tipTranslation) {
                $tipTranslations[] = [
                    'id' => $tip->id,
                    'content' => $tipTranslation->content
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'translation' => [
                'title' => $translation->title,
                'description' => $translation->description
            ],
            'step_translations' => $stepTranslations,
            'tip_translations' => $tipTranslations
        ]);
    }

    /**
     * Сохранить перевод статьи
     */
    public function saveTranslation(Request $request, string $id)
    {
        \Log::info('saveTranslation called', [
            'article_id' => $id,
            'request_data' => $request->all()
        ]);
        
        $request->validate([
            'language_code' => 'required|string|max:5',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'step_translations' => 'nullable|array',
            'step_translations.*.title' => 'required|string|max:255',
            'step_translations.*.content' => 'required|string',
            'tip_translations' => 'nullable|array',
            'tip_translations.*.content' => 'required|string'
        ]);

        $article = KnowledgeArticle::findOrFail($id);
        $languageCode = $request->language_code;

        // Обновляем или создаем перевод статьи
        $translation = $article->translations()->where('locale', $languageCode)->first();
        if ($translation) {
            $translation->update([
                'title' => $request->title,
                'description' => $request->description
            ]);
        } else {
            $article->translations()->create([
                'locale' => $languageCode,
                'title' => $request->title,
                'description' => $request->description
            ]);
        }

        // Обновляем переводы шагов
        if ($request->has('step_translations')) {
            foreach ($request->step_translations as $index => $stepData) {
                $step = $article->steps()->skip($index)->first();
                if ($step) {
                    $stepTranslation = $step->translations()->where('locale', $languageCode)->first();
                    if ($stepTranslation) {
                        $stepTranslation->update([
                            'title' => $stepData['title'],
                            'content' => $stepData['content']
                        ]);
                    } else {
                        $step->translations()->create([
                            'locale' => $languageCode,
                            'title' => $stepData['title'],
                            'content' => $stepData['content']
                        ]);
                    }
                }
            }
        }

        // Обновляем переводы советов
        if ($request->has('tip_translations')) {
            foreach ($request->tip_translations as $index => $tipData) {
                $tip = $article->tips()->skip($index)->first();
                if ($tip) {
                    $tipTranslation = $tip->translations()->where('locale', $languageCode)->first();
                    if ($tipTranslation) {
                        $tipTranslation->update([
                            'content' => $tipData['content']
                        ]);
                    } else {
                        $tip->translations()->create([
                            'locale' => $languageCode,
                            'content' => $tipData['content']
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Перевод успешно сохранен!'
        ]);
    }
}
