@extends('admin.layouts.app')

@section('title', 'Создать статью - База знаний')

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

    /* Стили для подсказок в редакторе */
    .tox .alert {
        margin: 10px 0;
        padding: 12px 16px;
        border-radius: 6px;
        border: 1px solid transparent;
        position: relative;
    }

    .tox .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }

    .tox .alert-warning {
        color: #856404;
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }

    .tox .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .tox .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .tox .alert-primary {
        color: #004085;
        background-color: #cce7ff;
        border-color: #b3d9ff;
    }

    .tox .alert .btn-close {
        position: absolute;
        top: 8px;
        right: 8px;
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        opacity: 0.7;
    }

    .tox .alert .btn-close:hover {
        opacity: 1;
    }

    .tox .alert i {
        margin-right: 8px;
    }

    .tox .alert span[contenteditable="true"] {
        outline: none;
        min-height: 20px;
        display: inline-block;
    }

    .tox .alert span[contenteditable="true"]:focus {
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
        padding: 2px 4px;
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
                                               placeholder="Введите заголовок статьи..."
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
                                                  placeholder="Введите краткое описание статьи..."
                                                  required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Информация о языках -->
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Многоязычность:</strong> Заполните форму на любом языке (рекомендуется русский для удобства работы). 
                                        После создания статьи система автоматически создаст переводы на все доступные языки (русский, английский, украинский). 
                                        Затем вы сможете отредактировать каждый перевод отдельно.
                                    </div>
                                </div>

                                <!-- Шаги -->
                                <div class="mb-4">
                                    <h5 class="card-title">Шаги</h5>
                                    
                                    <!-- Информация о языке заполнения -->
                                    <div class="alert alert-primary mb-3">
                                        <i class="fas fa-language me-2"></i>
                                        <strong>Язык заполнения:</strong> Заполняйте все поля на <strong>русском языке</strong> (язык админ панели). 
                                        После создания статьи система автоматически создаст переводы на все доступные языки.
                                    </div>
                                    
                                    <!-- Кнопки быстрых подсказок -->
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
                                                           placeholder="Введите заголовок шага..." 
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
                                  id="tinymce-step-0"
                                  name="steps[0][content]"
                                  rows="5"
                                  placeholder="Опишите шаг подробно...&#10;&#10;Используйте кнопки выше для быстрой вставки подсказок с готовыми стилями!&#10;&#10;Пример: В системе уже созданы два системных типа: Постоянный клиент, Новый клиент. Эти типы нельзя редактировать или удалять, так как они используются в аналитике." 
                                  ></textarea>
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
                                                          placeholder="Введите полезный совет..."></textarea>
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
                                        <label for="featured_image" class="form-label">Главное изображение</label>
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
                            <button type="submit" class="btn btn-primary">
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
<!-- TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>
<script>
let stepCounter = 1;
let tipCounter = 1;
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
                <label class="form-label">Заголовок шага (на украинском языке) <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control step-title" 
                       name="steps[${stepCounter}][title]" 
                       placeholder="Введіть заголовок кроку українською мовою..." 
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
                      ></textarea>
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
                      placeholder="Введите полезный совет..."></textarea>
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
    console.log('Form submission started...');
    
    const title = document.getElementById('title').value.trim();
    const description = document.getElementById('description').value.trim();
    
    console.log('Form data:', { title, description });
    
    if (!title || !description) {
        e.preventDefault();
        alert('Пожалуйста, заполните все обязательные поля');
        return false;
    }
    
    // Проверяем шаги
    const stepTitles = document.querySelectorAll('.step-title');
    const stepEditors = document.querySelectorAll('.step-content-editor');
    
    console.log('Steps found:', stepTitles.length);
    
    for (let i = 0; i < stepTitles.length; i++) {
        const title = stepTitles[i].value.trim();
        let content = '';
        
        // Получаем содержимое из TinyMCE если доступен, иначе из textarea
        if (tinymce.get(stepEditors[i].id)) {
            content = tinymce.get(stepEditors[i].id).getContent().trim();
        } else {
            content = stepEditors[i].value.trim();
        }
        
        console.log(`Step ${i + 1}:`, { title, content });
        
        if (!title || !content) {
            e.preventDefault();
            alert('Пожалуйста, заполните все обязательные поля для шагов');
            return false;
        }
    }
    
    console.log('Form validation passed, submitting...');
});

// Функция для вставки подсказки в активный редактор
function insertTip(type, className) {
    if (!activeEditor || !activeEditor.id) {
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
</script>
@endpush
