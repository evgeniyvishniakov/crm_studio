@extends('admin.layouts.app')

@section('title', 'Создать статью - База знаний')

@section('styles')
<style>
    /* Стили для категорий, шагов и кнопок */
    .category-icon { 
        width: 20px; 
        height: 20px; 
        margin-right: 8px; 
        vertical-align: middle; 
    }
    .step-item { 
        border: 1px solid #dee2e6; 
        border-radius: 8px; 
        padding: 20px; 
        margin-bottom: 20px; 
        background: #fff; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
    }
    .step-item:hover { 
        box-shadow: 0 4px 8px rgba(0,0,0,0.15); 
    }
    .btn-sm { 
        padding: 0.375rem 0.75rem; 
        font-size: 0.875rem; 
    }
    .img-thumbnail { 
        border: 1px solid #dee2e6; 
        border-radius: 4px; 
        padding: 4px; 
    }
    
    /* Стили для TinyMCE */
    .tox-tinymce {
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
    }
    
    .step-content-editor {
        min-height: 200px;
    }

    /* Стили для переключателя языков */
    .language-switcher {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .language-switcher select {
        max-width: 200px;
    }
    
    .language-info {
        margin-top: 10px;
        padding: 10px;
        background: #e9ecef;
        border-radius: 5px;
        font-size: 14px;
    }

    .translation-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .translation-status.saved {
        background: #d4edda;
        color: #155724;
    }

    .translation-status.unsaved {
        background: #f8d7da;
        color: #721c24;
    }

    .translation-status.partial {
        background: #fff3cd;
        color: #856404;
    }

    .language-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }

    .save-translation-btn {
        min-width: 120px;
    }

    .saved-translations {
        margin-top: 15px;
        padding: 10px;
        background: #e9ecef;
        border-radius: 5px;
    }

    .saved-translation-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }

    .saved-translation-item .badge {
        font-size: 11px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Создать статью</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.knowledge.index') }}">База знаний</a>
                            </li>
                            <li class="breadcrumb-item active">Создать статью</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.knowledge.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Назад к списку
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.knowledge.store') }}" method="POST" enctype="multipart/form-data" id="knowledge-form">
                        @csrf
                        
                        <!-- Переключатель языков -->
                        <div class="language-switcher">
                            <h6 class="mb-3">Выберите язык для создания контента</h6>
                            <div class="language-row">
                                <div class="flex-grow-1">
                                    <label for="language_id" class="form-label">Язык <span class="text-danger">*</span></label>
                                    <select class="form-select @error('language_id') is-invalid @enderror" 
                                            id="language_id" 
                                            name="language_id" 
                                            required>
                                        <option value="">Выберите язык</option>
                                        @foreach($languages as $language)
                                            <option value="{{ $language->id }}" 
                                                    data-code="{{ $language->code }}"
                                                    data-name="{{ $language->name }}">
                                                {{ $language->name }} ({{ $language->native_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('language_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex align-items-end">
                                    <button type="button" class="btn btn-success save-translation-btn" id="save-translation-btn" disabled>
                                        <i class="fas fa-save me-2"></i>Сохранить перевод
                                    </button>
                                </div>
                            </div>
                            <div class="language-info">
                                <strong>Текущий язык:</strong> <span id="current-language">Не выбран</span>
                                <br>
                                <strong>Статус:</strong> <span id="translation-status">Не выбран</span>
                            </div>
                            
                            <!-- Список сохраненных переводов -->
                            <div class="saved-translations" id="saved-translations" style="display: none;">
                                <h6 class="mb-2">Сохраненные переводы:</h6>
                                <div id="saved-translations-list"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Основная информация -->
                                <div class="mb-4">
                                    <h5 class="card-title">Основная информация</h5>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Заголовок статьи <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('title') is-invalid @enderror" 
                                               id="title" 
                                               name="title" 
                                               value="{{ old('title') }}" 
                                               required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="category" class="form-label">Категория <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" 
                                                id="category" 
                                                name="category" 
                                                required>
                                            <option value="">Выберите категорию</option>
                                            @foreach($categories as $key => $name)
                                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Краткое описание <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3" 
                                                  required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Шаги -->
                                <div class="mb-4">
                                    <h5 class="card-title">Шаги</h5>
                                    <div id="steps-container">
                                        <div class="step-item mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Шаг 1</h6>
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeStep(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Заголовок шага <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           class="form-control step-title" 
                                                           name="steps[0][title]" 
                                                           placeholder="Введите заголовок шага" 
                                                           required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Изображение (опционально)</label>
                                                    <input type="file" 
                                                           class="form-control step-image" 
                                                           name="steps[0][image]" 
                                                           accept="image/*">
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="form-label">Содержание шага <span class="text-danger">*</span></label>
                                                <textarea class="form-control step-content step-content-editor" 
                                                          name="steps[0][content]" 
                                                          rows="5" 
                                                          placeholder="Опишите шаг подробно..." 
                                                          required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addStep()">
                                        <i class="fas fa-plus me-2"></i>Добавить шаг
                                    </button>
                                </div>

                                <!-- Полезные советы -->
                                <div class="mb-4">
                                    <h5 class="card-title">Полезные советы</h5>
                                    <div id="tips-container">
                                        <div class="tip-item mb-3">
                                            <div class="input-group">
                                                <textarea class="form-control tip-content" 
                                                          name="tips[0][content]" 
                                                          rows="3" 
                                                          placeholder="Введите полезный совет"></textarea>
                                                <button type="button" class="btn btn-outline-danger" onclick="removeTip(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="addTip()">
                                        <i class="fas fa-plus me-2"></i>Добавить совет
                                    </button>
                                </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Дополнительные настройки -->
                                <div class="mb-4">
                                    <h5 class="card-title">Дополнительные настройки</h5>
                                    
                                    <div class="mb-3">
                                        <label for="author" class="form-label">Автор</label>
                                        <input type="text" 
                                               class="form-control @error('author') is-invalid @enderror" 
                                               id="author" 
                                               name="author" 
                                               value="{{ old('author', 'Команда Trimora') }}">
                                        @error('author')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Изображение</label>
                                        <input type="file" 
                                               class="form-control @error('featured_image') is-invalid @enderror" 
                                               id="featured_image" 
                                               name="featured_image" 
                                               accept="image/*">
                                        @error('featured_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Рекомендуемый размер: 800x600px. Максимальный размер: 2MB.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Порядок сортировки</label>
                                        <input type="number" 
                                               class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" 
                                               name="sort_order" 
                                               value="{{ old('sort_order', 0) }}" 
                                               min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_published" 
                                                   name="is_published" 
                                                   value="1" 
                                                   {{ old('is_published') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_published">
                                                Опубликовать сразу
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            Если не отмечено, статья будет сохранена как черновик.
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.knowledge.index') }}" class="btn btn-outline-secondary">
                                Отмена
                            </a>
                            <button type="submit" class="btn btn-primary" id="create-article-btn">
                                <i class="fas fa-save me-2"></i>Создать статью
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- TinyMCE - бесплатная версия -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>
<script>
// Данные о сохраненных переводах
let savedTranslations = {};
let currentLanguage = null;

// Проверка загрузки TinyMCE
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM загружен, проверяем TinyMCE...');
    
    // Ждем немного, чтобы TinyMCE успел загрузиться
    setTimeout(() => {
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE не загружен после загрузки DOM!');
        } else {
            console.log('TinyMCE успешно загружен, версия:', tinymce.majorVersion);
            
            // Инициализируем TinyMCE для существующих полей
            const existingEditors = document.querySelectorAll('.step-content-editor');
            console.log('Найдено полей для инициализации:', existingEditors.length);
            existingEditors.forEach(editor => {
                initTinyMCE(editor);
            });
        }
    }, 500);

    // Инициализация переключателя языков
    initLanguageSwitcher();
    
    // Активируем кнопку создания статьи
    checkCanCreateArticle();
    
    // Автоматически выбираем первый язык
    const languageSelect = document.getElementById('language_id');
    if (languageSelect && languageSelect.options.length > 1) {
        // Выбираем первый доступный язык (пропускаем пустую опцию)
        languageSelect.selectedIndex = 1;
        // Вызываем событие change для активации логики
        languageSelect.dispatchEvent(new Event('change'));
        
        // Активируем кнопку сохранения перевода
        const saveTranslationBtn = document.getElementById('save-translation-btn');
        if (saveTranslationBtn) {
            saveTranslationBtn.disabled = false;
        }
    }
});

// Инициализация переключателя языков
function initLanguageSwitcher() {
    const languageSelect = document.getElementById('language_id');
    const currentLanguageSpan = document.getElementById('current-language');
    const translationStatusSpan = document.getElementById('translation-status');
    const saveTranslationBtn = document.getElementById('save-translation-btn');
    
    if (languageSelect) {
        languageSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const languageName = selectedOption.getAttribute('data-name');
                const languageCode = selectedOption.getAttribute('data-code');
                
                currentLanguage = languageCode;
                currentLanguageSpan.textContent = languageName;
                
                // Проверяем, есть ли уже сохраненный перевод для этого языка
                if (savedTranslations[languageCode]) {
                    translationStatusSpan.textContent = 'Перевод сохранен';
                    translationStatusSpan.className = 'translation-status saved';
                    loadSavedTranslation(languageCode);
                } else {
                    translationStatusSpan.textContent = 'Перевод не сохранен';
                    translationStatusSpan.className = 'translation-status unsaved';
                    clearFormFields();
                }
                
                saveTranslationBtn.disabled = false;
            } else {
                currentLanguage = null;
                currentLanguageSpan.textContent = 'Не выбран';
                translationStatusSpan.textContent = 'Не выбран';
                saveTranslationBtn.disabled = true;
            }
        });
    }
}

// Загрузка сохраненного перевода
function loadSavedTranslation(languageCode) {
    const translation = savedTranslations[languageCode];
    if (translation) {
        document.getElementById('title').value = translation.title || '';
        document.getElementById('description').value = translation.description || '';
        document.getElementById('author').value = translation.author || 'Команда Trimora';
        
        // Загружаем шаги
        if (translation.steps) {
            // Удаляем существующие шаги
            const stepsContainer = document.getElementById('steps-container');
            stepsContainer.innerHTML = '';
            
            // Добавляем сохраненные шаги
            translation.steps.forEach((step, index) => {
                addStep();
                const lastStep = stepsContainer.lastElementChild;
                lastStep.querySelector('.step-title').value = step.title || '';
                
                const contentField = lastStep.querySelector('.step-content');
                if (tinymce.get(contentField.id)) {
                    tinymce.get(contentField.id).setContent(step.content || '');
                } else {
                    contentField.value = step.content || '';
                }
            });
        }
        
        // Загружаем советы
        if (translation.tips) {
            // Удаляем существующие советы
            const tipsContainer = document.getElementById('tips-container');
            tipsContainer.innerHTML = '';
            
            // Добавляем сохраненные советы
            translation.tips.forEach((tip, index) => {
                addTip();
                const lastTip = tipsContainer.lastElementChild;
                lastTip.querySelector('.tip-content').value = tip.content || '';
            });
        }
    }
}

// Очистка полей формы при смене языка
function clearFormFields() {
    // Очищаем основные поля
    document.getElementById('title').value = '';
    document.getElementById('description').value = '';
    document.getElementById('author').value = 'Команда Trimora';
    
    // Очищаем поля шагов
    const stepTitles = document.querySelectorAll('.step-title');
    const stepContents = document.querySelectorAll('.step-content');
    
    stepTitles.forEach(field => field.value = '');
    stepContents.forEach(field => {
        if (tinymce.get(field.id)) {
            tinymce.get(field.id).setContent('');
        } else {
            field.value = '';
        }
    });
    
    // Очищаем поля советов
    const tipContents = document.querySelectorAll('.tip-content');
    tipContents.forEach(field => field.value = '');
}

// Сохранение перевода для текущего языка
function saveTranslation() {
    if (!currentLanguage) {
        alert('Сначала выберите язык!');
        return;
    }
    
    // Собираем данные перевода
    const translation = {
        title: document.getElementById('title').value.trim(),
        description: document.getElementById('description').value.trim(),
        author: document.getElementById('author').value.trim(),
        steps: [],
        tips: []
    };
    
    // Проверяем обязательные поля
    if (!translation.title || !translation.description) {
        alert('Заполните заголовок и описание!');
        return false;
    }
    
    // Собираем шаги
    const stepTitles = document.querySelectorAll('.step-title');
    const stepContents = document.querySelectorAll('.step-content');
    
    for (let i = 0; i < stepTitles.length; i++) {
        const title = stepTitles[i].value.trim();
        let content = '';
        
        if (tinymce.get(stepContents[i].id)) {
            content = tinymce.get(stepContents[i].id).getContent().trim();
        } else {
            content = stepContents[i].value.trim();
        }
        
        if (title && content) {
            translation.steps.push({ title, content });
        }
    }
    
    // Собираем советы
    const tipContents = document.querySelectorAll('.tip-content');
    tipContents.forEach(field => {
        const content = field.value.trim();
        if (content) {
            translation.tips.push({ content });
        }
    });
    
    // Отправляем данные на сервер
    const formData = new FormData();
    formData.append('language_id', document.getElementById('language_id').value);
    formData.append('title', translation.title);
    formData.append('description', translation.description);
    formData.append('author', translation.author);
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    
    // Добавляем шаги
    translation.steps.forEach((step, index) => {
        formData.append(`steps[${index}][title]`, step.title);
        formData.append(`steps[${index}][content]`, step.content);
    });
    
    // Добавляем советы
    translation.tips.forEach((tip, index) => {
        formData.append(`tips[${index}][content]`, tip.content);
    });
    
    // Показываем индикатор загрузки
    const saveBtn = document.getElementById('save-translation-btn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Сохранение...';
    saveBtn.disabled = true;
    
    // Отправляем запрос
    fetch('{{ route("admin.knowledge.save-translation-draft") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Сохраняем перевод локально
            savedTranslations[currentLanguage] = translation;
            
            // Обновляем статус
            document.getElementById('translation-status').textContent = 'Перевод сохранен';
            document.getElementById('translation-status').className = 'translation-status saved';
            
            // Обновляем список сохраненных переводов
            updateSavedTranslationsList();
            
            // Проверяем, можно ли активировать кнопку создания статьи
            checkCanCreateArticle();
            
            alert(data.message);
        } else {
            alert('Ошибка при сохранении перевода: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при сохранении перевода. Проверьте консоль для деталей.');
    })
    .finally(() => {
        // Восстанавливаем кнопку
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Обновление списка сохраненных переводов
function updateSavedTranslationsList() {
    const container = document.getElementById('saved-translations');
    const list = document.getElementById('saved-translations-list');
    
    if (Object.keys(savedTranslations).length > 0) {
        container.style.display = 'block';
        list.innerHTML = '';
        
        Object.keys(savedTranslations).forEach(langCode => {
            const translation = savedTranslations[langCode];
            const langNames = { 'ru': 'Русский', 'en': 'English', 'ua': 'Українська' };
            
            const item = document.createElement('div');
            item.className = 'saved-translation-item';
            item.innerHTML = `
                <span class="badge bg-success">${langNames[langCode] || langCode}</span>
                <span>${translation.title}</span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTranslation('${langCode}')">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            list.appendChild(item);
        });
    } else {
        container.style.display = 'none';
    }
}

// Удаление перевода
function deleteTranslation(langCode) {
    if (confirm(`Удалить перевод для языка ${langCode}?`)) {
        delete savedTranslations[langCode];
        updateSavedTranslationsList();
        checkCanCreateArticle();
        
        // Если удаляем текущий язык, очищаем поля
        if (currentLanguage === langCode) {
            clearFormFields();
            document.getElementById('translation-status').textContent = 'Перевод не сохранен';
            document.getElementById('translation-status').className = 'translation-status unsaved';
        }
    }
}

// Проверка возможности создания статьи
function checkCanCreateArticle() {
    const createBtn = document.getElementById('create-article-btn');
    const savedCount = Object.keys(savedTranslations).length;
    
    // Кнопка всегда активна, но показываем информацию о переводах
    createBtn.disabled = false;
    
    if (savedCount >= 1) {
        createBtn.title = `Можно создать статью (сохранено переводов: ${savedCount})`;
    } else {
        createBtn.title = 'Создать статью (переводы не сохранены)';
    }
}

// Инициализация TinyMCE для всех полей содержания шагов
function initTinyMCE(element) {
    console.log('Инициализация TinyMCE для элемента:', element);
    
    // Если передан DOM элемент, получаем его селектор
    let selector = element;
    if (element instanceof HTMLElement) {
        if (element.id) {
            selector = '#' + element.id;
        } else {
            // Создаем уникальный ID для элемента
            element.id = 'tinymce-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            selector = '#' + element.id;
        }
    }
    
    console.log('Используемый селектор:', selector);
    
    tinymce.init({
        selector: selector,
        height: 200,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        branding: false,
        promotion: false,
                       setup: function(editor) {
                   console.log('TinyMCE редактор создан:', editor.id);
               }
    });
}

function addStep() {
    const container = document.getElementById('steps-container');
    const stepDiv = document.createElement('div');
    stepDiv.className = 'step-item mb-3 p-3 border rounded';
    stepDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Шаг ${stepCounter + 1}</h6>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeStep(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Заголовок шага <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control step-title" 
                       name="steps[${stepCounter}][title]" 
                       placeholder="Введите заголовок шага" 
                       required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Изображение (опционально)</label>
                <input type="file" 
                       class="form-control step-image" 
                       name="steps[${stepCounter}][image]" 
                       accept="image/*">
            </div>
        </div>
        <div class="mt-3">
            <label class="form-label">Содержание шага <span class="text-danger">*</span></label>
            <textarea class="form-control step-content step-content-editor" 
                      name="steps[${stepCounter}][content]" 
                      rows="5" 
                      placeholder="Опишите шаг подробно..." 
                      required></textarea>
        </div>
    `;
    container.appendChild(stepDiv);
    
    // Инициализируем TinyMCE для нового поля
    const newEditor = stepDiv.querySelector('.step-content-editor');
    initTinyMCE(newEditor);
    
    stepCounter++;
}

function removeStep(button) {
    const stepItem = button.closest('.step-item');
    const editor = stepItem.querySelector('.step-content-editor');
    
    // Удаляем редактор TinyMCE перед удалением элемента
    if (editor && tinymce.get(editor.id)) {
        tinymce.remove(editor.id);
    }
    
    stepItem.remove();
}

function addTip() {
    const container = document.getElementById('tips-container');
    const tipDiv = document.createElement('div');
    tipDiv.className = 'tip-item mb-3';
    tipDiv.innerHTML = `
        <div class="input-group">
            <textarea class="form-control tip-content" 
                      name="tips[${tipCounter}][content]" 
                      rows="3" 
                      placeholder="Введите полезный совет"></textarea>
            <button type="button" class="btn btn-outline-danger" onclick="removeTip(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(tipDiv);
    tipCounter++;
}

function removeTip(button) {
    button.closest('.tip-item').remove();
}

// Привязываем функцию сохранения к кнопке
document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.getElementById('save-translation-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', saveTranslation);
    }
});

// Валидация формы перед отправкой
document.getElementById('knowledge-form').addEventListener('submit', function(e) {
    // Проверяем, что все обязательные поля заполнены
    const title = document.getElementById('title').value.trim();
    const description = document.getElementById('description').value.trim();
    
    if (!title || !description) {
        e.preventDefault();
        alert('Пожалуйста, заполните все обязательные поля');
        return false;
    }
    
    // Проверяем шаги
    const stepTitles = document.querySelectorAll('.step-title');
    const stepContents = document.querySelectorAll('.step-content');
    
    for (let i = 0; i < stepTitles.length; i++) {
        const title = stepTitles[i].value.trim();
        let content = '';
        
        if (tinymce.get(stepContents[i].id)) {
            content = tinymce.get(stepContents[i].id).getContent().trim();
        } else {
            content = stepContents[i].value.trim();
        }
        
        if (!title || !content) {
            e.preventDefault();
            alert('Пожалуйста, заполните все обязательные поля для шагов');
            return false;
        }
    }
});

let stepCounter = 1;
let tipCounter = 1;

</script>
@endpush
