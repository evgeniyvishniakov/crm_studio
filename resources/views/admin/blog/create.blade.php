@extends('admin.layouts.app')

@section('title', 'Создать статью - Блог')

@section('styles')
<style>
    .tox-tinymce {
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
    }
    
    .content-editor {
        min-height: 400px;
    }

    .img-thumbnail { 
        border: 1px solid #dee2e6; 
        border-radius: 4px; 
        padding: 4px; 
        max-width: 150px;
        height: auto;
    }

    .tag-item {
        display: inline-block;
        background: #e9ecef;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 4px 12px;
        margin: 2px;
        font-size: 0.875rem;
    }

    .tag-item.selected {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .tag-item:hover {
        background: #0056b3;
        color: white;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Создать статью блога</h1>
                <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Назад к списку
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Основной контент -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Основная информация</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Заголовок статьи <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Краткое описание</label>
                                    <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                              id="excerpt" name="excerpt" rows="3" 
                                              placeholder="Краткое описание статьи для превью...">{{ old('excerpt') }}</textarea>
                                    @error('excerpt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Содержание статьи <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror content-editor" 
                                              id="content" name="content" required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SEO настройки -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">SEO настройки</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                           id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                                           placeholder="Заголовок для поисковых систем">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              id="meta_description" name="meta_description" rows="3"
                                              placeholder="Описание для поисковых систем">{{ old('meta_description') }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                           id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}"
                                           placeholder="Ключевые слова через запятую">
                                    @error('meta_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Боковая панель -->
                    <div class="col-lg-4">
                        <!-- Настройки публикации -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Настройки публикации</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">
                                            Опубликовать сразу
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Рекомендуемая статья
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="published_at" class="form-label">Дата публикации</label>
                                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                           id="published_at" name="published_at" value="{{ old('published_at') }}">
                                    @error('published_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="author" class="form-label">Автор</label>
                                    <input type="text" class="form-control @error('author') is-invalid @enderror" 
                                           id="author" name="author" value="{{ old('author', 'Trimora') }}"
                                           placeholder="Имя автора">
                                    @error('author')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Категория -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Категория</h5>
                            </div>
                            <div class="card-body">
                                <select class="form-select @error('blog_category_id') is-invalid @enderror" id="blog_category_id" name="blog_category_id">
                                    <option value="">Выберите категорию</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('blog_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus me-1"></i>Добавить категорию
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Теги -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Теги</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="tag-search" placeholder="Поиск тегов...">
                                </div>
                                <div id="tags-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: 0.375rem; background-color: #f8f9fa;">
                                    @if($tags->count() > 0)
                                        @foreach($tags as $tag)
                                            <span class="tag-item" data-tag-id="{{ $tag->id }}" data-tag-name="{{ $tag->name }}" style="display: inline-block; background: #e9ecef; border: 1px solid #dee2e6; border-radius: 20px; padding: 4px 12px; margin: 2px; font-size: 0.875rem; cursor: pointer;">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <div class="text-muted">Теги не найдены</div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.blog-tags.create') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus me-1"></i>Добавить тег
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Изображение -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Изображение статьи</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                           id="featured_image" name="featured_image" accept="image/*">
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div id="image-preview" class="mt-2" style="display: none;">
                                    <img id="preview-img" src="" alt="Превью" class="img-thumbnail" style="max-width: 100%; height: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Скрытые поля для выбранных тегов -->
                <div id="selected-tags-inputs"></div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Отмена</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Создать статью
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация TinyMCE
    setTimeout(() => {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#content',
                height: 400,
                menubar: false,
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        }
    }, 100);

    // Поиск тегов
    const tagSearch = document.getElementById('tag-search');
    const tagsContainer = document.getElementById('tags-container');
    const selectedTagsInputs = document.getElementById('selected-tags-inputs');
    const selectedTags = new Set();

    if (tagSearch) {
        tagSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tagItems = tagsContainer.querySelectorAll('.tag-item');
            
            tagItems.forEach(item => {
                const tagName = item.getAttribute('data-tag-name').toLowerCase();
                if (tagName.includes(searchTerm)) {
                    item.style.display = 'inline-block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Выбор тегов
    if (tagsContainer) {
        console.log('Tags container found, adding click listener');
        
        // Добавляем обработчик клика на контейнер
        tagsContainer.addEventListener('click', function(e) {
            console.log('Click detected on:', e.target);
            
            // Проверяем, что клик был по тегу
            if (e.target.classList.contains('tag-item')) {
                e.preventDefault();
                e.stopPropagation();
                
                const tagId = e.target.getAttribute('data-tag-id');
                const tagName = e.target.getAttribute('data-tag-name');
                
                console.log('Tag clicked:', tagId, tagName);
                
                if (selectedTags.has(tagId)) {
                    selectedTags.delete(tagId);
                    e.target.classList.remove('selected');
                    e.target.style.backgroundColor = '#e9ecef';
                    e.target.style.color = 'inherit';
                    console.log('Tag deselected');
                } else {
                    selectedTags.add(tagId);
                    e.target.classList.add('selected');
                    e.target.style.backgroundColor = '#007bff';
                    e.target.style.color = 'white';
                    console.log('Tag selected');
                }
                
                updateSelectedTagsInputs();
            }
        });
        
        // Также добавляем обработчики напрямую на каждый тег
        const tagItems = tagsContainer.querySelectorAll('.tag-item');
        console.log('Found', tagItems.length, 'tag items');
        
        tagItems.forEach((tagItem, index) => {
            console.log('Adding click listener to tag', index, tagItem.textContent);
            tagItem.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Direct click on tag:', this.textContent);
                
                const tagId = this.getAttribute('data-tag-id');
                const tagName = this.getAttribute('data-tag-name');
                
                if (selectedTags.has(tagId)) {
                    selectedTags.delete(tagId);
                    this.classList.remove('selected');
                    this.style.backgroundColor = '#e9ecef';
                    this.style.color = 'inherit';
                    console.log('Tag deselected');
                } else {
                    selectedTags.add(tagId);
                    this.classList.add('selected');
                    this.style.backgroundColor = '#007bff';
                    this.style.color = 'white';
                    console.log('Tag selected');
                }
                
                updateSelectedTagsInputs();
            });
        });
    } else {
        console.log('Tags container not found!');
    }

    function updateSelectedTagsInputs() {
        if (!selectedTagsInputs) {
            console.log('Selected tags inputs container not found!');
            return;
        }
        
        selectedTagsInputs.innerHTML = '';
        selectedTags.forEach(tagId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[]';
            input.value = tagId;
            selectedTagsInputs.appendChild(input);
        });
        
        console.log('Selected tags updated:', Array.from(selectedTags));
    }

    // Превью изображения
    const imageInput = document.getElementById('featured_image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });

    // Автозаполнение meta полей из заголовка
    const titleInput = document.getElementById('title');
    const metaTitleInput = document.getElementById('meta_title');
    const metaDescriptionInput = document.getElementById('meta_description');

    titleInput.addEventListener('input', function() {
        if (!metaTitleInput.value) {
            metaTitleInput.value = this.value;
        }
    });

    // Автозаполнение meta description из excerpt
    const excerptInput = document.getElementById('excerpt');
    excerptInput.addEventListener('input', function() {
        if (!metaDescriptionInput.value) {
            metaDescriptionInput.value = this.value;
        }
    });
});
</script>
@endpush
