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
                    <form action="{{ route('admin.knowledge.store') }}" method="POST" enctype="multipart/form-data">
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
                                                <textarea class="form-control step-content" 
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

                                <!-- Предварительный просмотр -->
                                <div class="mb-4">
                                    <h5 class="card-title">Предварительный просмотр</h5>
                                    <div id="preview" class="border rounded p-3 bg-light">
                                        <p class="text-muted text-center mb-0">
                                            Предварительный просмотр появится здесь
                                        </p>
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
<script>
let stepCounter = 1;
let tipCounter = 1;

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
            <textarea class="form-control step-content" 
                      name="steps[${stepCounter}][content]" 
                      rows="5" 
                      placeholder="Опишите шаг подробно..." 
                      required></textarea>
        </div>
    `;
    container.appendChild(stepDiv);
    
    stepCounter++;
}

function removeStep(button) {
    button.closest('.step-item').remove();
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

// Обновляем предварительный просмотр
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const previewDiv = document.getElementById('preview');
    
    function updatePreview() {
        const title = titleInput.value || 'Заголовок статьи';
        const description = descriptionInput.value || 'Описание статьи';

        // Собираем информацию о шагах
        let stepsHtml = '';
        const stepItems = document.querySelectorAll('.step-item');
        stepItems.forEach((item, index) => {
            const stepTitle = item.querySelector('.step-title').value || `Шаг ${index + 1}`;
            const stepContent = item.querySelector('.step-content').value || '';
            
            stepsHtml += `
                <li class="mb-2">
                    <strong>${stepTitle}</strong>
                    <small class="text-muted d-block">${stepContent.length > 100 ? stepContent.substring(0, 100) + '...' : stepContent}</small>
                </li>
            `;
        });

        // Собираем информацию о советах
        let tipsHtml = '';
        const tipItems = document.querySelectorAll('.tip-content');
        tipItems.forEach((tip, index) => {
            const tipContent = tip.value || '';
            if (tipContent.trim()) {
                tipsHtml += `
                    <li class="mb-1">
                        <small class="text-muted">${tipContent.length > 50 ? tipContent.substring(0, 50) + '...' : tipContent}</small>
                    </li>
                `;
            }
        });

        previewDiv.innerHTML = `
            <h4>${title}</h4>
            <p class="text-muted">${description}</p>
            <hr>
            ${stepsHtml ? `<h6>Шаги:</h6><ol class="mb-3">${stepsHtml}</ol>` : ''}
            ${tipsHtml ? `<h6>Полезные советы:</h6><ul class="mb-0">${tipsHtml}</ul>` : ''}
        `;
    }

    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);

    // Обновляем превью при изменении шагов и советов
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('step-title') || e.target.classList.contains('step-content') || e.target.classList.contains('tip-content')) {
            updatePreview();
        }
    });

    // Инициализация предварительного просмотра
    updatePreview();
});
</script>
@endpush
