@extends('admin.layouts.app')

@section('title', 'Редактировать статью')

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

@section('scripts')
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
                    <form action="{{ route('admin.knowledge.update', $article->id) }}" method="POST" enctype="multipart/form-data">
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
                                                                                   id="delete_step_image_{{ $index }}" name="steps[{{ $index }}][delete_image]" value="1">
                                                                            <label class="form-check-label text-danger" for="delete_step_image_{{ $index }}">
                                                                                <i class="fas fa-trash"></i> Удалить
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <input type="file" class="form-control step-image" 
                                                                   name="steps[{{ $index }}][image]" accept="image/*">
                                                            <div class="form-text">Оставьте пустым, чтобы сохранить текущее изображение. Или отметьте чекбокс выше, чтобы удалить существующее.</div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <label class="form-label">Содержание шага <span class="text-danger">*</span></label>
                                                        <textarea class="form-control step-content" 
                                                                  name="steps[{{ $index }}][content]" 
                                                                  rows="5" placeholder="Опишите шаг подробно">{{ $step->content }}</textarea>
                                                        <div class="form-text">Опишите шаг подробно.</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
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
                                                        <input type="text" class="form-control step-title" 
                                                               name="steps[0][title]" placeholder="Введите заголовок шага" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Изображение (опционально)</label>
                                                        <input type="file" class="form-control step-image" 
                                                               name="steps[0][image]" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <label class="form-label">Содержание шага <span class="text-danger">*</span></label>
                                                    <textarea class="form-control step-content" 
                                                              name="steps[0][content]" rows="5" placeholder="Опишите шаг подробно"></textarea>
                                                    <div class="form-text">Опишите шаг подробно.</div>
                                                </div>
                                            </div>
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
                                                                  rows="3" placeholder="Введите полезный совет">{{ $tip->content }}</textarea>
                                                        <button type="button" class="btn btn-outline-danger" onclick="removeTip(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="tip-item mb-3">
                                                <div class="input-group">
                                                    <textarea class="form-control tip-content" 
                                                              name="tips[0][content]" rows="3" placeholder="Введите полезный совет"></textarea>
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeTip(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="addTip()">
                                        <i class="fas fa-plus me-2"></i>Добавить совет
                                    </button>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Сохранить изменения
                                    </button>
                                    <a href="{{ route('admin.knowledge.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Назад к списку
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Предварительный просмотр -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Предварительный просмотр</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="preview">
                                            <h4>{{ $article->title }}</h4>
                                            <p class="text-muted">{{ $article->description }}</p>
                                            <hr>
                                            @if($article->steps->count() > 0)
                                                <h6>Шаги:</h6>
                                                <ul class="list-unstyled">
                                                    @foreach($article->steps as $step)
                                                        <li class="mb-2">
                                                            <strong>{{ $step->title }}</strong>
                                                            <small class="text-muted d-block">{{ Str::limit(strip_tags($step->content), 100) }}</small>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            @if($article->tips->count() > 0)
                                                <h6>Полезные советы:</h6>
                                                <ul class="list-unstyled">
                                                    @foreach($article->tips as $tip)
                                                        <li class="mb-2">
                                                            <small class="text-muted">{{ Str::limit(strip_tags($tip->content), 100) }}</small>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let stepCounter = {{ $article->steps->count() > 0 ? $article->steps->count() : 1 }};
let tipCounter = {{ $article->tips->count() > 0 ? $article->tips->count() : 1 }};

function addStep() {
    const container = document.getElementById('steps-container');
    const stepHtml = `
        <div class="step-item mb-3 p-3 border rounded">
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
                           name="steps[${stepCounter}][title]" placeholder="Введите заголовок шага" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Изображение (опционально)</label>
                    <input type="file" class="form-control step-image" 
                           name="steps[${stepCounter}][image]" accept="image/*">
                    <div class="form-text">Загрузите изображение для шага.</div>
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Содержание шага <span class="text-danger">*</span></label>
                <textarea class="form-control step-content" 
                          name="steps[${stepCounter}][content]" rows="5" placeholder="Опишите шаг подробно"></textarea>
                <div class="form-text">Опишите шаг подробно.</div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', stepHtml);
    
    stepCounter++;
    updatePreview();
}

function removeStep(button) {
    if (document.querySelectorAll('.step-item').length > 1) {
        button.closest('.step-item').remove();
        updatePreview();
    }
}

function addTip() {
    const container = document.getElementById('tips-container');
    const tipHtml = `
        <div class="tip-item mb-3">
            <div class="input-group">
                <textarea class="form-control tip-content" 
                          name="tips[${tipCounter}][content]" rows="3" placeholder="Введите полезный совет"></textarea>
                <button type="button" class="btn btn-outline-danger" onclick="removeTip(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', tipHtml);
    tipCounter++;
    updatePreview();
}

function removeTip(button) {
    if (document.querySelectorAll('.tip-item').length > 1) {
        button.closest('.tip-item').remove();
        updatePreview();
    }
}

function updatePreview() {
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const previewDiv = document.getElementById('preview');
    
    const title = titleInput.value || '{{ $article->title }}';
    const description = descriptionInput.value || '{{ $article->description }}';
    
    let stepsHtml = '';
    let tipsHtml = '';
    
    // Собираем информацию о шагах
    const stepItems = document.querySelectorAll('.step-item');
    if (stepItems.length > 0) {
        stepsHtml = '<h6>Шаги:</h6><ul class="list-unstyled">';
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
        stepsHtml += '</ul>';
    }
    
    // Собираем информацию о советах
    const tipItems = document.querySelectorAll('.tip-item');
    if (tipItems.length > 0) {
        tipsHtml = '<h6>Полезные советы:</h6><ul class="list-unstyled">';
        tipItems.forEach((item) => {
            const tipContent = item.querySelector('.tip-content').value || '';
            if (tipContent.trim()) {
                tipsHtml += `
                    <li class="mb-2">
                        <small class="text-muted">${tipContent.length > 100 ? tipContent.substring(0, 100) + '...' : tipContent}</small>
                    </li>
                `;
            }
        });
        tipsHtml += '</ul>';
    }
    
    previewDiv.innerHTML = `
        <h4>${title}</h4>
        <p class="text-muted">${description}</p>
        <hr>
        ${stepsHtml}
        ${tipsHtml}
    `;
}

document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    
    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    
    // Обновляем превью при изменении шагов и советов
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('step-title') || e.target.classList.contains('step-content') || e.target.classList.contains('tip-content')) {
            updatePreview();
        }
    });
    
    updatePreview();
});
</script>
@endsection
