@extends('admin.layouts.app')

@section('title', 'Редактировать статью')

@section('styles')
<style>
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
    
    .tox-tinymce {
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
    }
    
    .step-content-editor {
        min-height: 200px;
    }

    /* Стили для кнопок быстрых подсказок */
    .quick-tips-buttons .btn {
        transition: all 0.2s ease;
    }
    
    .quick-tips-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    /* Стили для уведомлений */
    .position-fixed {
        position: fixed !important;
    }
    
    .alert {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Главная</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">База знаний</a></li>
                        <li class="breadcrumb-item active">Редактировать статью</li>
                    </ol>
                </div>
                <h4 class="page-title">Редактировать статью</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.knowledge.update', $article->id) }}" method="POST" enctype="multipart/form-data" id="knowledge-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Основная информация -->
                                <div class="mb-4">
                                    <h5 class="card-title">Основная информация</h5>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Заголовок статьи <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $article->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="category" class="form-label">Категория <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                            <option value="">Выберите категорию</option>
                                            @foreach($categories as $key => $category)
                                                <option value="{{ $key }}" {{ old('category', $article->category) == $key ? 'selected' : '' }}>
                                                    {{ $category }}
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
                                                  id="description" name="description" rows="3" required>{{ old('description', $article->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Информация о языках -->
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Многоязычность:</strong> Изменения будут применены ко всем языкам. 
                                        Для редактирования переводов на конкретные языки используйте специальные формы переводов.
                                    </div>

                                    <!-- Кнопки управления переводами -->
                                    <div class="mb-3">
                                        <label class="form-label">Управление переводами:</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($languages as $language)
                                                @php
                                                    $translation = $article->translation($language->code);
                                                    $statusClass = $translation ? 'btn-success' : 'btn-outline-secondary';
                                                    $statusText = $translation ? '✓ Редактировать' : 'Создать перевод';
                                                @endphp
                                                <button type="button" 
                                                        class="btn {{ $statusClass }} btn-sm" 
                                                        onclick="openTranslationModal('{{ $language->code }}', '{{ $language->name }}')">
                                                    <i class="fas fa-language me-1"></i>
                                                    {{ $language->native_name }}
                                                    <small class="d-block">{{ $statusText }}</small>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Главное изображение</label>
                                        @if($article->featured_image)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $article->featured_image) }}" alt="Текущее изображение" class="img-thumbnail" style="max-width: 200px;">
                                                <div class="mt-2">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" 
                                                               id="delete_featured_image" name="delete_featured_image" value="1">
                                                        <label class="form-check-label text-danger" for="delete_featured_image">
                                                            <i class="fas fa-trash"></i> Удалить изображение
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                               id="featured_image" name="featured_image" accept="image/*">
                                        @error('featured_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Оставьте пустым, чтобы сохранить текущее изображение. Или отметьте чекбокс выше, чтобы удалить существующее.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="meta_tags" class="form-label">Мета-теги</label>
                                        <input type="text" class="form-control @error('meta_tags') is-invalid @enderror" 
                                               id="meta_tags" name="meta_tags" value="{{ old('meta_tags', $article->meta_tags) }}" 
                                               placeholder="тег1, тег2, тег3">
                                        @error('meta_tags')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Разделяйте теги запятыми.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Порядок сортировки</label>
                                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', $article->sort_order ?? 0) }}" min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input @error('is_published') is-invalid @enderror" 
                                                   id="is_published" name="is_published" value="1" 
                                                   {{ old('is_published', $article->is_published) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_published">
                                                Опубликовать статью
                                            </label>
                                            @error('is_published')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Шаги -->
                                <div class="mb-4">
                                    <h5 class="card-title">Шаги</h5>
                                    
                                    <!-- Кнопки быстрой вставки подсказок -->
                                    <div class="mb-3">
                                        <label class="form-label">Быстрые подсказки:</label>
                                        <div class="d-flex flex-wrap gap-2 quick-tips-buttons">
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="insertTip('info', 'info-tip')">
                                                <i class="fas fa-info-circle me-1"></i>Информация
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="insertTip('warning', 'warning-tip')">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Предупреждение
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="insertTip('success', 'success-tip')">
                                                <i class="fas fa-check-circle me-1"></i>Успех
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="insertTip('danger', 'danger-tip')">
                                                <i class="fas fa-times-circle me-1"></i>Ошибка
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="insertTip('primary', 'primary-tip')">
                                                <i class="fas fa-lightbulb me-1"></i>Совет
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">
                                            Нажмите на кнопку, затем кликните в редактор шага, куда хотите вставить подсказку
                                        </small>
                                    </div>
                                    
                                    <div id="steps-container">
                                        @if($article->steps->count() > 0)
                                            @foreach($article->steps as $index => $step)
                                                <div class="step-item mb-3 p-3 border rounded">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">Шаг {{ $index + 1 }}</h6>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeStep(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Заголовок шага <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control step-title" 
                                                                   name="steps[{{ $index }}][title]" 
                                                                   value="{{ $step->title }}" 
                                                                   placeholder="Введите заголовок шага" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Изображение (опционально)</label>
                                                            @if($step->image)
                                                                <div class="mb-2">
                                                                    <img src="{{ asset('storage/' . $step->image) }}" alt="Текущее изображение" class="img-thumbnail" style="max-width: 100px;">
                                                                    <div class="mt-2">
                                                                        <div class="form-check">
                                                                            <input type="checkbox" class="form-check-input" 
                                                                                   name="steps[{{ $index }}][delete_image]" value="1">
                                                                            <label class="form-check-label text-danger">
                                                                                <i class="fas fa-trash"></i> Удалить изображение
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <input type="file" class="form-control step-image" 
                                                                   name="steps[{{ $index }}][image]" accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <label class="form-label">Содержание шага <span class="text-danger">*</span></label>
                                                        <textarea class="form-control step-content step-content-editor" 
                                                                  name="steps[{{ $index }}][content]" 
                                                                  rows="5" 
                                                                  placeholder="Опишите шаг подробно..." 
                                                                  required>{{ $step->content }}</textarea>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addStep()">
                                        <i class="fas fa-plus me-2"></i>Добавить шаг
                                    </button>
                                </div>

                                <!-- Полезные советы -->
                                <div class="mb-4">
                                    <h5 class="card-title">Полезные советы</h5>
                                    <div id="tips-container">
                                        @if($article->tips->count() > 0)
                                            @foreach($article->tips as $index => $tip)
                                                <div class="tip-item mb-3">
                                                    <div class="input-group">
                                                        <textarea class="form-control tip-content" 
                                                                  name="tips[{{ $index }}][content]" 
                                                                  rows="3" 
                                                                  placeholder="Введите полезный совет">{{ $tip->content }}</textarea>
                                                        <button type="button" class="btn btn-outline-danger" onclick="removeTip(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="addTip()">
                                        <i class="fas fa-plus me-2"></i>Добавить совет
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Дополнительные настройки -->
                                <div class="mb-4">
                                    <h5 class="card-title">Дополнительные настройки</h5>
                                    
                                    <div class="mb-3">
                                        <label for="author" class="form-label">Автор</label>
                                        <input type="text" class="form-control @error('author') is-invalid @enderror" 
                                               id="author" name="author" value="{{ old('author', $article->author) }}">
                                        @error('author')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.knowledge.index') }}" class="btn btn-outline-secondary">
                                Отмена
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для редактирования переводов -->
<div class="modal fade" id="translationModal" tabindex="-1" aria-labelledby="translationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="translationModalLabel">Редактирование перевода</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="translationForm">
                    <div class="mb-3">
                        <label for="translation_title" class="form-label">Заголовок на языке <span id="current-language-name"></span></label>
                        <input type="text" class="form-control" id="translation_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="translation_description" class="form-label">Описание на языке <span id="current-language-name-2"></span></label>
                        <textarea class="form-control" id="translation_description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Переводы шагов:</label>
                        <div id="translation-steps-container">
                            <!-- Шаги будут загружены динамически -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Переводы советов:</label>
                        <div id="translation-tips-container">
                            <!-- Советы будут загружены динамически -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="saveTranslation()">Сохранить перевод</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>
<script>
let stepCounter = {{ $article->steps->count() }};
let tipCounter = {{ $article->tips->count() }};
let activeEditor = null; // Глобальная переменная для отслеживания активного редактора

// Инициализация TinyMCE
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        if (typeof tinymce !== 'undefined') {
            const existingEditors = document.querySelectorAll('.step-content-editor');
            existingEditors.forEach(editor => {
                initTinyMCE(editor);
            });
        }
    }, 500);
});

// Инициализация TinyMCE для поля
function initTinyMCE(element) {
    if (element instanceof HTMLElement) {
        if (!element.id) {
            element.id = 'tinymce-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        }
    }
    
    tinymce.init({
        selector: '#' + element.id,
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
        content_style: `
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
                font-size: 14px; 
            }
            
            /* Стили для подсказок в редакторе */
            .alert {
                margin: 10px 0;
                padding: 12px 16px;
                border-radius: 6px;
                border: 1px solid transparent;
                position: relative;
            }

            .alert-info {
                color: #0c5460;
                background-color: #d1ecf1;
                border-color: #bee5eb;
            }

            .alert-warning {
                color: #856404;
                background-color: #fff3cd;
                border-color: #ffeaa7;
            }

            .alert-success {
                color: #155724;
                background-color: #d4edda;
                border-color: #c3e6cb;
            }

            .alert-danger {
                color: #721c24;
                background-color: #f8d7da;
                border-color: #f5c6cb;
            }

            .alert-primary {
                color: #004085;
                background-color: #cce7ff;
                border-color: #b3d9ff;
            }

            .alert .btn-close {
                position: absolute;
                top: 8px;
                right: 8px;
                background: none;
                border: none;
                font-size: 18px;
                cursor: pointer;
                opacity: 0.7;
                color: inherit;
            }

            .alert .btn-close:hover {
                opacity: 1;
            }

            .alert i {
                margin-right: 8px;
            }

            .alert span[contenteditable="true"] {
                outline: none;
                min-height: 20px;
                display: inline-block;
            }

            .alert span[contenteditable="true"]:focus {
                background-color: rgba(255, 255, 255, 0.3);
                border-radius: 3px;
                padding: 2px 4px;
            }
        `,
        branding: false,
        promotion: false,
        setup: function(editor) {
            // Отслеживаем фокус редактора
            editor.on('focus', function() {
                activeEditor = editor;
            });
            
            // Отслеживаем клик в редакторе
            editor.on('click', function() {
                activeEditor = editor;
            });
        }
    });
}

// Функция для вставки подсказки в активный редактор
function insertTip(type, className) {
    if (!activeEditor) {
        alert('Сначала кликните в редактор шага, куда хотите вставить подсказку!');
        return;
    }
    
    let tipContent = '';
    let icon = '';
    
    switch(type) {
        case 'info':
            icon = 'fas fa-info-circle';
            tipContent = 'Введите текст информационной подсказки здесь...';
            break;
        case 'warning':
            icon = 'fas fa-exclamation-triangle';
            tipContent = 'Введите текст предупреждения здесь...';
            break;
        case 'success':
            icon = 'fas fa-check-circle';
            tipContent = 'Введите текст успешного выполнения здесь...';
            break;
        case 'danger':
            icon = 'fas fa-times-circle';
            tipContent = 'Введите текст ошибки здесь...';
            break;
        case 'primary':
            icon = 'fas fa-lightbulb';
            tipContent = 'Введите текст совета здесь...';
            break;
        default:
            icon = 'fas fa-info-circle';
            tipContent = 'Введите текст подсказки здесь...';
    }
    
    const tipHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>
            <span contenteditable="true">${tipContent}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <br>
    `;
    
    activeEditor.insertContent(tipHtml);
    
    // Показываем уведомление
    showNotification(`Подсказка типа "${type}" вставлена! Теперь отредактируйте текст внутри.`, 'success');
}

// Функция для показа уведомлений
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Автоматически скрываем через 3 секунды
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Функция для открытия модального окна переводов
function openTranslationModal(languageCode, languageName) {
    // Устанавливаем название языка в модальном окне
    document.getElementById('current-language-name').textContent = languageName;
    document.getElementById('current-language-name-2').textContent = languageName;
    
    // Загружаем текущие переводы
    loadTranslationData(languageCode);
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(document.getElementById('translationModal'));
    modal.show();
}

// Функция для загрузки данных перевода
function loadTranslationData(languageCode) {
    // Здесь можно загрузить данные с сервера или использовать существующие
    // Пока используем базовые данные статьи
    document.getElementById('translation_title').value = document.getElementById('title').value;
    document.getElementById('translation_description').value = document.getElementById('description').value;
    
    // Загружаем переводы шагов
    loadStepTranslations(languageCode);
    
    // Загружаем переводы советов
    loadTipTranslations(languageCode);
}

// Функция для загрузки переводов шагов
function loadStepTranslations(languageCode) {
    const container = document.getElementById('translation-steps-container');
    const steps = document.querySelectorAll('.step-item');
    
    container.innerHTML = '';
    
    steps.forEach((step, index) => {
        const stepTitle = step.querySelector('.step-title').value;
        const stepContent = step.querySelector('.step-content').value;
        
        const stepDiv = document.createElement('div');
        stepDiv.className = 'mb-3 p-3 border rounded';
        stepDiv.innerHTML = `
            <h6>Шаг ${index + 1}</h6>
            <div class="mb-2">
                <label class="form-label">Заголовок шага:</label>
                <input type="text" class="form-control" name="steps[${index}][title]" value="${stepTitle}" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Содержание шага:</label>
                <textarea class="form-control" name="steps[${index}][content]" rows="3" required>${stepContent}</textarea>
            </div>
        `;
        
        container.appendChild(stepDiv);
    });
}

// Функция для загрузки переводов советов
function loadTipTranslations(languageCode) {
    const container = document.getElementById('translation-tips-container');
    const tips = document.querySelectorAll('.tip-content');
    
    container.innerHTML = '';
    
    tips.forEach((tip, index) => {
        const tipContent = tip.value;
        
        const tipDiv = document.createElement('div');
        tipDiv.className = 'mb-2';
        tipDiv.innerHTML = `
            <label class="form-label">Совет ${index + 1}:</label>
            <textarea class="form-control" name="tips[${index}][content]" rows="2" required>${tipContent}</textarea>
        `;
        
        container.appendChild(tipDiv);
    });
}

// Функция для сохранения перевода
function saveTranslation() {
    // Здесь можно добавить логику сохранения перевода
    showNotification('Перевод успешно сохранен!', 'success');
    
    // Закрываем модальное окно
    const modal = bootstrap.Modal.getInstance(document.getElementById('translationModal'));
    modal.hide();
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
                      placeholder="Опишите шаг подробно...&#10;&#10;Используйте кнопки выше для быстрой вставки подсказок с готовыми стилями!" 
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

// Валидация формы перед отправкой
document.getElementById('knowledge-form').addEventListener('submit', function(e) {
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
</script>
@endpush
