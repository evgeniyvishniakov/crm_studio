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
                        <input type="hidden" name="editing_language" value="{{ app()->getLocale() }}">
                        <input type="hidden" name="is_editing_translation" value="false">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Основная информация -->
                                <div class="mb-4">
                                    <h5 class="card-title">Основная информация</h5>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Заголовок статьи <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $article->original_title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="category" class="form-label">Категория <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                            <option value="">Выберите категорию</option>
                                            @foreach($categories as $key => $category)
                                                <option value="{{ $key }}" {{ old('category', $article->getRawOriginal('category')) == $key ? 'selected' : '' }}>
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
                                                  id="description" name="description" rows="3" required>{{ old('description', $article->original_description) }}</textarea>
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
                                        @if($article->getRawOriginal('featured_image'))
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $article->getRawOriginal('featured_image')) }}" alt="Текущее изображение" class="img-thumbnail" style="max-width: 200px;">
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
                                               id="meta_tags" name="meta_tags" value="{{ old('meta_tags', $article->getRawOriginal('meta_tags')) }}" 
                                               placeholder="тег1, тег2, тег3">
                                        @error('meta_tags')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Разделяйте теги запятыми.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Порядок сортировки</label>
                                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', $article->getRawOriginal('sort_order') ?? 0) }}" min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input @error('is_published') is-invalid @enderror" 
                                                   id="is_published" name="is_published" value="1" 
                                                   {{ old('is_published', $article->getRawOriginal('is_published')) ? 'checked' : '' }}>
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
                                               id="author" name="author" value="{{ old('author', $article->getRawOriginal('author')) }}">
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
                        <textarea class="form-control translation-editor" id="translation_description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Переводы шагов:</label>
                        
                        <!-- Кнопки быстрых подсказок для переводов -->
                        <div class="mb-3">
                            <label class="form-label">Быстрые подсказки:</label>
                            <div class="d-flex flex-wrap gap-2 quick-tips-buttons">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="insertTipInFirstAvailable('info', 'info-tip')">
                                    <i class="fas fa-info-circle me-1"></i>Информация
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="insertTipInFirstAvailable('warning', 'warning-tip')">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Предупреждение
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="insertTipInFirstAvailable('success', 'success-tip')">
                                    <i class="fas fa-check-circle me-1"></i>Успех
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="insertTipInFirstAvailable('danger', 'danger-tip')">
                                    <i class="fas fa-times-circle me-1"></i>Ошибка
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="insertTipInFirstAvailable('primary', 'primary-tip')">
                                    <i class="fas fa-lightbulb me-1"></i>Совет
                                </button>
                            </div>
                            <small class="form-text text-muted">
                                Нажмите на кнопку, и подсказка вставится в первое доступное поле
                            </small>
                        </div>
                        
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
let articleId = {{ $article->id }}; // Глобальная переменная для хранения ID статьи
let currentLanguage = null; // Глобальная переменная для хранения текущего языка перевода

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
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
            
            /* Стили для подсказок в TinyMCE */
            .alert {
                margin: 10px 0;
                padding: 12px 16px;
                border-radius: 6px;
                border: 1px solid transparent;
                position: relative;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        // Настройки для очистки стилей
        paste_as_text: true,
        paste_enable_default_filters: true,
        paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,h5,h6",
        paste_retain_style_properties: "none",
        paste_remove_styles_if_webkit: true,
        paste_remove_styles: true,
        paste_filter_drop: true,
        paste_data_images: false,
        paste_auto_cleanup_on_paste: true,
        paste_convert_word_fake_lists: true,
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

        // Инициализация TinyMCE для упрощенного редактора (в модальном окне)
        function initTinyMCESimple(element) {
            if (!element) {
                console.error('Элемент не найден для инициализации TinyMCE');
                return;
            }
            
            if (element instanceof HTMLElement) {
                if (!element.id) {
                    element.id = 'tinymce-simple-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                }
            }
            
            console.log('Инициализируем TinyMCE для элемента:', element.id);
            console.log('Содержимое элемента перед инициализацией:', element.value);
            
            // Сохраняем содержимое перед инициализацией
            const content = element.value;
            
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
                    /* Стили для подсказок в TinyMCE */
                    .alert {
                        margin: 10px 0;
                        padding: 12px 16px;
                        border-radius: 6px;
                        border: 1px solid transparent;
                        position: relative;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
                    console.log('TinyMCE редактор создан для:', element.id);
                    editor.on('init', function() {
                        console.log('TinyMCE редактор инициализирован для:', element.id);
                        // Устанавливаем сохраненное содержимое
                        if (content) {
                            console.log('Устанавливаем содержимое в TinyMCE:', content);
                            editor.setContent(content);
                        }
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
            tipContent = 'Полезная информация для пользователя';
            break;
        case 'warning':
            icon = 'fas fa-exclamation-triangle';
            tipContent = 'Важное предупреждение';
            break;
        case 'success':
            icon = 'fas fa-check-circle';
            tipContent = 'Успешное выполнение действия';
            break;
        case 'danger':
            icon = 'fas fa-times-circle';
            tipContent = 'Ошибка или проблема';
            break;
        case 'primary':
            icon = 'fas fa-lightbulb';
            tipContent = 'Полезный совет';
            break;
        default:
            icon = 'fas fa-info-circle';
            tipContent = 'Дополнительная информация';
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

// Функция для вставки подсказки в модальном окне переводов
function insertTipInTranslation(type, className) {
    console.log('insertTipInTranslation вызвана для типа:', type);
    
    // Находим активный редактор в модальном окне
    const activeElement = document.activeElement;
    console.log('Активный элемент:', activeElement);
    console.log('Классы активного элемента:', activeElement ? activeElement.className : 'нет');
    console.log('ID активного элемента:', activeElement ? activeElement.id : 'нет');
    
    let targetEditor = null;
    
    // Проверяем, является ли активный элемент редактором TinyMCE
    if (activeElement && activeElement.classList.contains('step-translation-editor')) {
        targetEditor = activeElement;
        console.log('Найден редактор шага:', targetEditor);
    } else if (activeElement && activeElement.classList.contains('tip-translation-editor')) {
        targetEditor = activeElement;
        console.log('Найден редактор совета:', targetEditor);
    } else if (activeElement && activeElement.id === 'translation_description') {
        targetEditor = activeElement;
        console.log('Найден редактор описания:', targetEditor);
    } else {
        console.log('Активный элемент не является редактором');
        
        // Попробуем найти любой редактор в модальном окне
        const stepEditors = document.querySelectorAll('.step-translation-editor');
        const tipEditors = document.querySelectorAll('.tip-translation-editor');
        const descEditor = document.getElementById('translation_description');
        
        console.log('Найдено редакторов шагов:', stepEditors.length);
        console.log('Найдено редакторов советов:', tipEditors.length);
        console.log('Редактор описания:', descEditor);
        
        // Если есть только один редактор, используем его
        if (stepEditors.length === 1) {
            targetEditor = stepEditors[0];
            console.log('Используем единственный редактор шага:', targetEditor);
        } else if (tipEditors.length === 1) {
            targetEditor = tipEditors[0];
            console.log('Используем единственный редактор совета:', targetEditor);
        } else if (descEditor) {
            targetEditor = descEditor;
            console.log('Используем редактор описания:', targetEditor);
        }
    }
    
    if (!targetEditor) {
        alert('Не удалось найти редактор! Убедитесь, что TinyMCE инициализирован.');
        return;
    }
    
    let tipContent = '';
    let icon = '';
    
    switch(type) {
        case 'info':
            icon = 'fas fa-info-circle';
            tipContent = 'Полезная информация для пользователя';
            break;
        case 'warning':
            icon = 'fas fa-exclamation-triangle';
            tipContent = 'Важное предупреждение';
            break;
        case 'success':
            icon = 'fas fa-check-circle';
            tipContent = 'Успешное выполнение действия';
            break;
        case 'danger':
            icon = 'fas fa-times-circle';
            tipContent = 'Ошибка или проблема';
            break;
        case 'primary':
            icon = 'fas fa-lightbulb';
            tipContent = 'Полезный совет';
            break;
        default:
            icon = 'fas fa-info-circle';
            tipContent = 'Дополнительная информация';
    }
    
    const tipHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>
            <span contenteditable="true">${tipContent}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <br>
    `;
    
    console.log('Вставляем подсказку в редактор:', targetEditor.id);
    
    // Проверяем, есть ли TinyMCE редактор
    if (tinymce.get(targetEditor.id)) {
        // Вставляем в TinyMCE
        tinymce.get(targetEditor.id).insertContent(tipHtml);
        console.log(`Подсказка вставлена в TinyMCE редактор: ${targetEditor.id}`);
    } else {
        // Вставляем в обычное поле
        const currentValue = targetEditor.value;
        const cursorPos = targetEditor.selectionStart;
        const newValue = currentValue.substring(0, cursorPos) + tipHtml + currentValue.substring(cursorPos);
        targetEditor.value = newValue;
        console.log(`Подсказка вставлена в обычное поле: ${targetEditor.id}`);
    }
    
    // Показываем уведомление
    showNotification(`Подсказка типа "${type}" вставлена! Теперь отредактируйте текст внутри.`, 'success');
}

// Альтернативная функция для вставки подсказки в первое доступное поле
function insertTipInFirstAvailable(type, className) {
    console.log('insertTipInFirstAvailable вызвана для типа:', type);
    
    // Ищем первое доступное поле
    let targetEditor = null;
    
    // Сначала пробуем редактор описания
    const descEditor = document.getElementById('translation_description');
    if (descEditor) {
        targetEditor = descEditor;
        console.log('Используем редактор описания:', targetEditor);
    } else {
        // Потом пробуем первый редактор шага
        const stepEditors = document.querySelectorAll('.step-translation-editor');
        if (stepEditors.length > 0) {
            targetEditor = stepEditors[0];
            console.log('Используем первый редактор шага:', targetEditor);
        } else {
            // Потом пробуем первый редактор совета
            const tipEditors = document.querySelectorAll('.tip-translation-editor');
            if (tipEditors.length > 0) {
                targetEditor = tipEditors[0];
                console.log('Используем первый редактор совета:', targetEditor);
            }
        }
    }
    
    if (!targetEditor) {
        alert('Не найдено ни одного редактора! Подождите, пока TinyMCE инициализируется.');
        return;
    }
    
    let tipContent = '';
    let icon = '';
    
    switch(type) {
        case 'info':
            icon = 'fas fa-info-circle';
            tipContent = 'Полезная информация для пользователя';
            break;
        case 'warning':
            icon = 'fas fa-exclamation-triangle';
            tipContent = 'Важное предупреждение';
            break;
        case 'success':
            icon = 'fas fa-check-circle';
            tipContent = 'Успешное выполнение действия';
            break;
        case 'danger':
            icon = 'fas fa-times-circle';
            tipContent = 'Ошибка или проблема';
            break;
        case 'primary':
            icon = 'fas fa-lightbulb';
            tipContent = 'Полезный совет';
            break;
        default:
            icon = 'fas fa-info-circle';
            tipContent = 'Дополнительная информация';
    }
    
    const tipHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>
            <span contenteditable="true">${tipContent}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <br>
    `;
    
    console.log('Вставляем подсказку в редактор:', targetEditor.id);
    
    // Проверяем, есть ли TinyMCE редактор
    if (tinymce.get(targetEditor.id)) {
        // Вставляем в TinyMCE
        tinymce.get(targetEditor.id).insertContent(tipHtml);
        console.log(`Подсказка вставлена в TinyMCE редактор: ${targetEditor.id}`);
    } else {
        // Вставляем в обычное поле
        const currentValue = targetEditor.value;
        const newValue = currentValue + tipHtml;
        targetEditor.value = newValue;
        console.log(`Подсказка вставлена в обычное поле: ${targetEditor.id}`);
    }
    
    // Показываем уведомление
    showNotification(`Подсказка типа "${type}" вставлена в первое доступное поле! Теперь отредактируйте текст внутри.`, 'success');
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
    loadTranslationData(languageCode, languageName);
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(document.getElementById('translationModal'));
    modal.show();
}

        // Функция для очистки HTML-кода и извлечения чистого текста
        function stripHtml(html) {
            if (!html) return '';
            
            console.log('stripHtml вызвана с:', html);
            
            // Создаем временный элемент для извлечения текста
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Получаем чистый текст
            let text = tempDiv.textContent || tempDiv.innerText || '';
            
            // Убираем лишние пробелы и переносы, но сохраняем структуру
            text = text.replace(/\s+/g, ' ').trim();
            
            console.log('stripHtml результат:', text);
            
            return text;
        }
        
        // Функция для безопасного экранирования HTML в тексте
        function escapeHtml(text) {
            if (!text) return '';
            
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Функция для безопасного вставки текста в HTML
        function safeHtmlInsert(text) {
            if (!text) return '';
            
            // Заменяем специальные символы на HTML-сущности
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }
        
        // Загружаем данные для перевода
        function loadTranslationData(languageCode, languageName) {
            console.log('Загружаем перевод для языка:', languageCode);
            
            // ОЧИЩАЕМ ПОЛЕ ОПИСАНИЯ ПЕРЕД ЗАГРУЗКОЙ НОВОГО ПЕРЕВОДА
            const descriptionField = document.getElementById('translation_description');
            if (descriptionField.id && tinymce.get(descriptionField.id)) {
                console.log('Уничтожаем существующий TinyMCE редактор:', descriptionField.id);
                tinymce.get(descriptionField.id).destroy();
            }
            
            // Очищаем содержимое поля
            descriptionField.value = '';
            
            currentLanguage = languageCode;
            document.getElementById('current-language-name').textContent = languageName;
            document.getElementById('current-language-name-2').textContent = languageName;
            
            // Загружаем переводы с сервера
            fetch(`/panel/knowledge/${articleId}/translations/${languageCode}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Получены данные с сервера:', data);
                    
                    if (data.success) {
                        console.log('Перевод найден, заполняем поля:', data.translation);
                        console.log('Данные с сервера:', {
                            title: data.translation.title,
                            description: data.translation.description,
                            step_translations: data.step_translations,
                            tip_translations: data.tip_translations
                        });
                        
                        // Заполняем поля перевода
                        const titleField = document.getElementById('translation_title');
                        const descriptionField = document.getElementById('translation_description');
                        
                        console.log('Перед заполнением - заголовок:', titleField.value);
                        console.log('Перед заполнением - описание:', descriptionField.value);
                        
                        titleField.value = data.translation.title || '';
                        descriptionField.value = data.translation.description || '';
                        
                        console.log('После заполнения - заголовок:', titleField.value);
                        console.log('После заполнения - описание:', descriptionField.value);
                        
                        console.log('Поле заголовка заполнено:', titleField.value);
                        console.log('Поле описания заполнено:', descriptionField.value);
                        
                        // Загружаем переводы шагов
                        console.log('Передаем переводы шагов в loadStepTranslations:', data.step_translations);
                        loadStepTranslations(data.step_translations);
                        
                        // Загружаем переводы советов
                        console.log('Передаем переводы советов в loadTipTranslations:', data.tip_translations);
                        loadTipTranslations(data.tip_translations);
                    } else {
                        console.log('Перевод не найден, используем данные из основной формы');
                        
                        // Если перевод не найден, используем данные из основной формы
                        const title = document.getElementById('title').value;
                        const description = document.getElementById('description').value;
                        
                        document.getElementById('translation_title').value = title;
                        document.getElementById('translation_description').value = description;
                        
                        // Загружаем переводы шагов из основной формы
                        loadStepTranslations();
                        
                        // Загружаем переводы советов из основной формы
                        loadTipTranslations();
                    }
                    
                    // Инициализируем TinyMCE для полей перевода
                    setTimeout(() => {
                        const descriptionEditor = document.getElementById('translation_description');
                        console.log('Инициализируем TinyMCE для описания:', descriptionEditor.id);
                        console.log('Содержимое поля перед инициализацией TinyMCE:', descriptionEditor.value);
                        
                        // УНИЧТОЖАЕМ СТАРЫЙ TINYMCE РЕДАКТОР ЕСЛИ ОН СУЩЕСТВУЕТ
                        if (descriptionEditor.id && tinymce.get(descriptionEditor.id)) {
                            console.log('Уничтожаем старый TinyMCE редактор:', descriptionEditor.id);
                            tinymce.get(descriptionEditor.id).destroy();
                        }
                        
                        initTinyMCESimple(descriptionEditor);
                        
                        // Проверяем содержимое после инициализации TinyMCE
                        setTimeout(() => {
                            if (tinymce.get(descriptionEditor.id)) {
                                console.log('Содержимое TinyMCE после инициализации:', tinymce.get(descriptionEditor.id).getContent());
                            }
                        }, 100);
                    }, 300); // Увеличиваем задержку
                })
                .catch(error => {
                    console.error('Ошибка загрузки перевода:', error);
                    
                    // В случае ошибки используем данные из основной формы
                    const title = document.getElementById('title').value;
                    const description = document.getElementById('description').value;
                    
                    document.getElementById('translation_title').value = title;
                    document.getElementById('translation_description').value = description;
                    
                    // Загружаем переводы шагов из основной формы
                    loadStepTranslations();
                    
                    // Загружаем переводы советов из основной формы
                    loadTipTranslations();
                    
                    // Инициализируем TinyMCE для полей перевода
                    setTimeout(() => {
                        const descriptionEditor = document.getElementById('translation_description');
                        console.log('Инициализируем TinyMCE для описания (fallback):', descriptionEditor.id);
                        initTinyMCESimple(descriptionEditor);
                    }, 300); // Увеличиваем задержку
                });
        }
        
        // Загружаем переводы шагов
        function loadStepTranslations(translations) {
            const container = document.getElementById('translation-steps-container');
            const steps = document.querySelectorAll('.step-item');
            
            console.log('loadStepTranslations вызвана');
            console.log('Переводы с сервера:', translations);
            console.log('Тип переводов:', typeof translations);
            console.log('Шаги в основной форме:', steps.length);
            
            container.innerHTML = '';
            
            if (translations && Array.isArray(translations) && translations.length > 0) {
                // Если есть переводы с сервера, используем их
                console.log('Используем переводы с сервера для шагов:', translations);
                translations.forEach((step, index) => {
                    console.log(`Шаг ${index + 1} перевод:`, step);
                    console.log(`Шаг ${index + 1} заголовок:`, step.title);
                    console.log(`Шаг ${index + 1} содержимое:`, step.content);
                    const stepDiv = document.createElement('div');
                    stepDiv.className = 'mb-3 p-3 border rounded';
                    stepDiv.innerHTML = `
                        <h6>Шаг ${index + 1}: ${escapeHtml(step.title)}</h6>
                        <div class="mb-2">
                            <label class="form-label">Заголовок шага:</label>
                            <input type="text" class="form-control" name="step_translations[${index}][title]" 
                                   value="${escapeHtml(step.title)}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Содержание шага:</label>
                            <textarea class="form-control step-translation-editor" 
                                      name="step_translations[${index}][content]" 
                                      rows="4" required>${step.content}</textarea>
                        </div>
                    `;
                    container.appendChild(stepDiv);
                });
            } else {
                // Если переводов нет, копируем из основной формы
                console.log('Переводов нет, копируем из основной формы');
                console.log('Копируем шаги из основной формы:', steps);
                steps.forEach((step, index) => {
                    const stepTitle = step.querySelector('.step-title').value;
                    const stepContent = step.querySelector('.step-content').value;
                    
                    console.log(`Шаг ${index + 1}:`, { title: stepTitle, content: stepContent });
                    
                    // Очищаем HTML из содержимого шага
                    const cleanStepContent = stripHtml(stepContent);
                    console.log(`Шаг ${index + 1} очищенное содержимое:`, cleanStepContent);
                    
                    const stepDiv = document.createElement('div');
                    stepDiv.className = 'mb-3 p-3 border rounded';
                    stepDiv.innerHTML = `
                        <h6>Шаг ${index + 1}: ${escapeHtml(stepTitle)}</h6>
                        <div class="mb-2">
                            <label class="form-label">Заголовок шага:</label>
                            <input type="text" class="form-control" name="step_translations[${index}][title]" 
                                   value="${escapeHtml(stepTitle)}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Содержание шага:</label>
                            <textarea class="form-control step-translation-editor" 
                                      name="step_translations[${index}][content]" 
                                      rows="4" required>${cleanStepContent}</textarea>
                        </div>
                    `;
                    container.appendChild(stepDiv);
                });
            }
            
            // Инициализируем TinyMCE для новых полей
            setTimeout(() => {
                const editors = document.querySelectorAll('.step-translation-editor');
                console.log('Найдено полей для TinyMCE:', editors.length);
                
                editors.forEach((element, index) => {
                    if (!element.id) {
                        element.id = 'step-translation-' + Date.now() + '-' + index;
                    }
                    console.log(`Инициализируем TinyMCE для шага ${index + 1}:`, element.id);
                    
                    // Проверяем, что элемент существует и имеет правильный класс
                    if (element && element.classList.contains('step-translation-editor')) {
                        initTinyMCESimple(element);
                    } else {
                        console.error(`Элемент ${index + 1} не найден или имеет неправильный класс:`, element);
                    }
                });
            }, 500); // Увеличиваем задержку еще больше
        }
        
        // Загружаем переводы советов
        function loadTipTranslations(translations) {
            const container = document.getElementById('translation-tips-container');
            const tips = document.querySelectorAll('.tip-content');
            
            console.log('loadTipTranslations вызвана');
            console.log('Переводы с сервера:', translations);
            console.log('Тип переводов:', typeof translations);
            
            container.innerHTML = '';
            
            if (translations && Array.isArray(translations) && translations.length > 0) {
                // Если есть переводы с сервера, используем их
                console.log('Используем переводы с сервера для советов:', translations);
                translations.forEach((tip, index) => {
                    const tipDiv = document.createElement('div');
                    tipDiv.className = 'mb-3 p-3 border rounded';
                    tipDiv.innerHTML = `
                        <h6>Совет ${index + 1}</h6>
                        <div class="mb-2">
                            <label class="form-label">Содержание совета:</label>
                            <textarea class="form-control tip-translation-editor" 
                                      name="tip_translations[${index}][content]" 
                                      rows="3" required>${tip.content}</textarea>
                        </div>
                    `;
                    container.appendChild(tipDiv);
                });
            } else {
                // Если переводов нет, копируем из основной формы
                tips.forEach((tip, index) => {
                    const tipContent = tip.value;
                    
                    // Очищаем HTML из содержимого совета
                    const cleanTipContent = stripHtml(tipContent);
                    
                    const tipDiv = document.createElement('div');
                    tipDiv.className = 'mb-3 p-3 border rounded';
                    tipDiv.innerHTML = `
                        <h6>Совет ${index + 1}</h6>
                        <div class="mb-2">
                            <label class="form-label">Содержание совета:</label>
                            <textarea class="form-control tip-translation-editor" 
                                      name="tip_translations[${index}][content]" 
                                      rows="3" required>${cleanTipContent}</textarea>
                        </div>
                    `;
                    container.appendChild(tipDiv);
                });
            }
            
            // Инициализируем TinyMCE для новых полей
            setTimeout(() => {
                const editors = document.querySelectorAll('.tip-translation-editor');
                console.log('Найдено полей советов для TinyMCE:', editors.length);
                
                editors.forEach((element, index) => {
                    if (!element.id) {
                        element.id = 'tip-translation-' + Date.now() + '-' + index;
                    }
                    console.log('Инициализируем TinyMCE для совета:', element.id);
                    initTinyMCESimple(element);
                });
            }, 200); // Увеличиваем задержку
        }

        // Функция для сохранения перевода
        function saveTranslation() {
            console.log('Начинаем сохранение перевода...');
            
            // Получаем данные из формы
            const title = document.getElementById('translation_title').value;
            const descriptionEditor = document.getElementById('translation_description');
            
            // Получаем содержимое описания из TinyMCE редактора
            let description = '';
            if (tinymce.get(descriptionEditor.id)) {
                description = tinymce.get(descriptionEditor.id).getContent();
                console.log('Описание из TinyMCE:', description);
            } else {
                description = descriptionEditor.value;
                console.log('Описание из обычного поля:', description);
            }
            
            console.log('Заголовок:', title);
            console.log('Описание:', description);
            
            // Получаем переводы шагов
            const stepTranslations = [];
            const stepEditors = document.querySelectorAll('.step-translation-editor');
            console.log('Найдено редакторов шагов:', stepEditors.length);
            
            stepEditors.forEach((editor, index) => {
                console.log(`Обрабатываем шаг ${index + 1}:`, editor);
                console.log(`ID редактора:`, editor.id);
                console.log(`Классы редактора:`, editor.className);
                
                const stepTitle = document.querySelector(`input[name="step_translations[${index}][title]"]`).value;
                console.log(`Заголовок шага ${index + 1}:`, stepTitle);
                
                // Получаем содержимое из TinyMCE редактора
                let stepContent = '';
                if (tinymce.get(editor.id)) {
                    stepContent = tinymce.get(editor.id).getContent();
                    console.log(`Шаг ${index + 1} - TinyMCE содержимое:`, stepContent);
                    console.log(`Шаг ${index + 1} - TinyMCE длина:`, stepContent.length);
                } else {
                    stepContent = editor.value;
                    console.log(`Шаг ${index + 1} - обычное содержимое:`, stepContent);
                    console.log(`Шаг ${index + 1} - длина содержимого:`, stepContent.length);
                    console.log(`Шаг ${index + 1} - первые 100 символов:`, stepContent.substring(0, 100));
                    console.log(`Шаг ${index + 1} - последние 100 символов:`, stepContent.substring(Math.max(0, stepContent.length - 100)));
                }
                
                // Проверяем, что содержимое не пустое
                if (!stepContent || stepContent.trim().length === 0) {
                    console.warn(`Шаг ${index + 1} - содержимое пустое!`);
                }
                
                stepTranslations.push({
                    title: stepTitle,
                    content: stepContent
                });
            });
            
            // Получаем переводы советов
            const tipTranslations = [];
            const tipEditors = document.querySelectorAll('.tip-translation-editor');
            console.log('Найдено редакторов советов:', tipEditors.length);
            
            tipEditors.forEach((editor, index) => {
                // Получаем содержимое из TinyMCE редактора
                let tipContent = '';
                if (tinymce.get(editor.id)) {
                    tipContent = tinymce.get(editor.id).getContent();
                    console.log(`Совет ${index + 1} - TinyMCE содержимое:`, tipContent);
                } else {
                    tipContent = editor.value;
                    console.log(`Совет ${index + 1} - обычное содержимое:`, tipContent);
                }
                
                tipTranslations.push({
                    content: tipContent
                });
            });
            
            // Подготавливаем данные для отправки
            const formData = {
                language_code: currentLanguage,
                title: title,
                description: description,
                step_translations: stepTranslations,
                tip_translations: tipTranslations,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };
            
            console.log('Отправляем данные:', formData);
            
            // Отправляем данные на сервер
            fetch(`/panel/knowledge/${articleId}/save-translation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Показываем уведомление об успехе
                    showNotification(data.message, 'success');
                    
                    // Закрываем модальное окно
                    const modal = bootstrap.Modal.getInstance(document.getElementById('translationModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Обновляем статус кнопки языка (делаем её зеленой)
                    updateLanguageButtonStatus(currentLanguage, true);
                } else {
                    showNotification('Ошибка при сохранении перевода: ' + (data.message || 'Неизвестная ошибка'), 'danger');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showNotification('Ошибка при сохранении перевода. Попробуйте еще раз.', 'danger');
            });
        }
        
        // Функция для обновления статуса кнопки языка
        function updateLanguageButtonStatus(languageCode, hasTranslation) {
            const button = document.querySelector(`button[onclick*="${languageCode}"]`);
            if (button) {
                if (hasTranslation) {
                    button.className = button.className.replace('btn-outline-secondary', 'btn-success');
                    button.innerHTML = button.innerHTML.replace('Создать перевод', '✓ Редактировать');
                } else {
                    button.className = button.className.replace('btn-success', 'btn-outline-secondary');
                    button.innerHTML = button.innerHTML.replace('✓ Редактировать', 'Создать перевод');
                }
            }
        }

        // Функция для добавления нового шага
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
                        <input type="text" class="form-control step-title" 
                               name="steps[${stepCounter}][title]" 
                               placeholder="Введите заголовок шага" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Изображение (опционально)</label>
                        <input type="file" class="form-control step-image" 
                               name="steps[${stepCounter}][image]" accept="image/*">
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

        // Функция для удаления шага
        function removeStep(button) {
            const stepItem = button.closest('.step-item');
            const editor = stepItem.querySelector('.step-content-editor');
            
            // Удаляем редактор TinyMCE перед удалением элемента
            if (editor && tinymce.get(editor.id)) {
                tinymce.remove(editor.id);
            }
            
            stepItem.remove();
        }

        // Функция для добавления нового совета
        function addTip() {
            const container = document.getElementById('tips-container');
            const tipDiv = document.createElement('div');
            tipDiv.className = 'tip-item mb-3';
            tipDiv.innerHTML = `
                <div class="input-group">
                    <textarea class="form-control tip-content" 
                              name="tips[${tipCounter}][content]" 
                              rows="3" 
                              placeholder="Введите полезный совет..."></textarea>
                    <button type="button" class="btn btn-outline-danger" onclick="removeTip(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(tipDiv);
            tipCounter++;
        }

        // Функция для удаления совета
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