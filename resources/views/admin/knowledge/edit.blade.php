@extends('admin.layouts.app')

@section('title', 'Редактировать: ' . $article->title . ' - База знаний')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Редактировать статью</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.knowledge.index') }}">База знаний</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.knowledge.show', $article->id) }}">{{ $article->title }}</a>
                            </li>
                            <li class="breadcrumb-item active">Редактировать</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.knowledge.show', $article->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>Просмотр
                    </a>
                    <a href="{{ route('admin.knowledge.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад к списку
                    </a>
                </div>
            </div>

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
                                        <input type="text" 
                                               class="form-control @error('title') is-invalid @enderror" 
                                               id="title" 
                                               name="title" 
                                               value="{{ old('title', $article->title) }}" 
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
                                                <option value="{{ $key }}" {{ old('category', $article->category) == $key ? 'selected' : '' }}>
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
                                                  required>{{ old('description', $article->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="content" class="form-label">Содержание статьи <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                  id="content" 
                                                  name="content" 
                                                  rows="15" 
                                                  required>{{ old('content', $article->content) }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Поддерживается HTML-разметка. Используйте теги для форматирования текста.
                                        </div>
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
                                               value="{{ old('author', $article->author) }}">
                                        @error('author')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Изображение</label>
                                        @if($article->featured_image)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                                     alt="{{ $article->title }}" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 200px;">
                                                <div class="form-text">Текущее изображение</div>
                                            </div>
                                        @endif
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
                                               value="{{ old('sort_order', $article->sort_order) }}" 
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
                                                   {{ old('is_published', $article->is_published) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_published">
                                                Опубликовать
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            Если отмечено, статья будет опубликована.
                                        </div>
                                    </div>
                                </div>

                                <!-- Предварительный просмотр -->
                                <div class="mb-4">
                                    <h5 class="card-title">Предварительный просмотр</h5>
                                    <div id="preview" class="border rounded p-3 bg-light">
                                        <h4>{{ $article->title }}</h4>
                                        <p class="text-muted">{{ $article->description }}</p>
                                        <hr>
                                        <div>{!! $article->content !!}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.knowledge.show', $article->id) }}" class="btn btn-outline-secondary">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const contentInput = document.getElementById('content');
    const previewDiv = document.getElementById('preview');

    function updatePreview() {
        const title = titleInput.value || 'Заголовок статьи';
        const description = descriptionInput.value || 'Описание статьи';
        const content = contentInput.value || 'Содержание статьи';

        previewDiv.innerHTML = `
            <h4>${title}</h4>
            <p class="text-muted">${description}</p>
            <hr>
            <div>${content}</div>
        `;
    }

    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    contentInput.addEventListener('input', updatePreview);

    // Инициализация предварительного просмотра
    updatePreview();
});
</script>
@endpush
