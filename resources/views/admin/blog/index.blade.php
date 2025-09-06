@extends('admin.layouts.app')

@section('title', 'Блог - Админ панель')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Блог</h1>
                <div>
                    <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-tags me-2"></i>Категории
                    </a>
                    <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-hashtag me-2"></i>Теги
                    </a>
                    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Добавить статью
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="category-filter" class="form-label">Категория</label>
                            <select id="category-filter" class="form-select">
                                <option value="">Все категории</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status-filter" class="form-label">Статус</label>
                            <select id="status-filter" class="form-select">
                                <option value="">Все статусы</option>
                                <option value="1">Опубликовано</option>
                                <option value="0">Черновик</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="featured-filter" class="form-label">Рекомендуемые</label>
                            <select id="featured-filter" class="form-select">
                                <option value="">Все статьи</option>
                                <option value="1">Рекомендуемые</option>
                                <option value="0">Обычные</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Поиск</label>
                            <input type="text" id="search" class="form-control" placeholder="Поиск по названию...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица статей -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Заголовок</th>
                                    <th>Категория</th>
                                    <th>Теги</th>
                                    <th>Переводы</th>
                                    <th>Статус</th>
                                    <th>Просмотры</th>
                                    <th>Дата создания</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                    <tr data-category="{{ $article->blog_category_id }}" data-status="{{ $article->is_published ? '1' : '0' }}" data-featured="{{ $article->is_featured ? '1' : '0' }}" data-title="{{ strtolower($article->title) }}" data-languages="{{ $article->translations->pluck('locale')->implode(',') }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($article->featured_image)
                                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                @endif
                                                <div>
                                                    <strong>{{ $article->title }}</strong>
                                                    @if($article->is_featured)
                                                        <i class="fas fa-star text-warning ms-1" title="Рекомендуемая статья"></i>
                                                    @endif
                                                    @if($article->excerpt)
                                                        <br><small class="text-muted">{{ Str::limit($article->excerpt, 100) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($article->category)
                                                <span class="badge" style="background-color: {{ $article->category->color }}">{{ $article->category->name }}</span>
                                            @else
                                                <span class="text-muted">Без категории</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($article->tags as $tag)
                                                    <span class="badge bg-secondary" style="background-color: {{ $tag->color }}!important">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach(['ru', 'en', 'ua'] as $langCode)
                                                    @php
                                                        $translation = $article->translation($langCode);
                                                        $hasTranslation = $translation && !empty(trim($translation->title));
                                                        $statusClass = $hasTranslation ? 'bg-success' : 'bg-secondary';
                                                        $statusText = $hasTranslation ? '✓' : '✗';
                                                        $langNames = ['ru' => 'RU', 'en' => 'EN', 'ua' => 'UA'];
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}" 
                                                          title="{{ $langNames[$langCode] }}: {{ $hasTranslation ? 'Есть перевод' : 'Нет перевода' }}"
                                                          style="cursor: pointer;"
                                                          onclick="editTranslation({{ $article->id }}, '{{ $langCode }}', '{{ $langNames[$langCode] }}')">
                                                        {{ $langNames[$langCode] }} {{ $statusText }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-publish" type="checkbox" 
                                                       data-article-id="{{ $article->id }}" 
                                                       {{ $article->is_published ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    {{ $article->is_published ? 'Опубликовано' : 'Черновик' }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $article->views_count }}</span>
                                        </td>
                                        <td>
                                            {{ $article->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.blog.show', $article->id) }}" class="btn btn-sm btn-outline-primary" title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.blog.edit', $article->id) }}" class="btn btn-sm btn-outline-warning" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteArticle({{ $article->id }})" title="Удалить">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-newspaper fa-3x mb-3"></i>
                                                <p>Статьи блога не найдены</p>
                                                <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">Создать первую статью</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагинация -->
                    @if($articles->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $articles->links() }}
                        </div>
                    @endif
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
                <div id="translationStatus" class="alert alert-info" style="display: none;">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="statusText"></span>
                </div>
                <form id="translationForm">
                    <div class="mb-3">
                        <label for="translation_title" class="form-label">Заголовок статьи</label>
                        <input type="text" class="form-control" id="translation_title" name="translation_title">
                    </div>
                    
                    <div class="mb-3">
                        <label for="translation_excerpt" class="form-label">Краткое описание</label>
                        <textarea class="form-control" id="translation_excerpt" name="translation_excerpt" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="translation_content" class="form-label">Содержание статьи</label>
                        <textarea class="form-control" id="translation_content" name="translation_content" rows="10"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="translation_meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="translation_meta_title" name="translation_meta_title">
                    </div>
                    
                    <div class="mb-3">
                        <label for="translation_meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="translation_meta_description" name="translation_meta_description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="translation_meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" id="translation_meta_keywords" name="translation_meta_keywords">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" onclick="copyOriginalContent()" id="copyOriginalBtn" style="display: none;">
                    <i class="fas fa-copy me-2"></i>Копировать оригинал
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="saveTranslation()">Сохранить перевод</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить эту статью? Это действие нельзя отменить.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>
<script>
let currentArticleId = null;
let currentLanguage = null;

// Фильтрация таблицы
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('category-filter');
    const statusFilter = document.getElementById('status-filter');
    const featuredFilter = document.getElementById('featured-filter');
    const searchInput = document.getElementById('search');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const categoryValue = categoryFilter.value;
        const statusValue = statusFilter.value;
        const featuredValue = featuredFilter.value;
        const searchValue = searchInput.value.toLowerCase();

        tableRows.forEach(row => {
            const category = row.getAttribute('data-category');
            const status = row.getAttribute('data-status');
            const featured = row.getAttribute('data-featured');
            const title = row.getAttribute('data-title');

            const categoryMatch = !categoryValue || category === categoryValue;
            const statusMatch = !statusValue || status === statusValue;
            const featuredMatch = !featuredValue || featured === featuredValue;
            const searchMatch = !searchValue || title.includes(searchValue);

            if (categoryMatch && statusMatch && featuredMatch && searchMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    categoryFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    featuredFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
});

// Переключение статуса публикации
document.querySelectorAll('.toggle-publish').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const articleId = this.getAttribute('data-article-id');
        const isPublished = this.checked;

        fetch(`/panel/blog/${articleId}/toggle-publish`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_published: isPublished })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем текст статуса
                const label = this.nextElementSibling;
                label.textContent = isPublished ? 'Опубликовано' : 'Черновик';
                
                // Показываем уведомление
                showNotification(data.message, 'success');
            } else {
                // Возвращаем чекбокс в исходное состояние
                this.checked = !isPublished;
                showNotification('Ошибка при изменении статуса', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.checked = !isPublished;
            showNotification('Ошибка при изменении статуса', 'error');
        });
    });
});

// Удаление статьи
function deleteArticle(articleId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    
    form.action = `/panel/blog/${articleId}`;
    modal.show();
}

// Функция показа уведомлений
function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}

// Функция редактирования перевода
function editTranslation(articleId, languageCode, languageName) {
    currentArticleId = articleId;
    currentLanguage = languageCode;
    
    // Обновляем заголовок модального окна
    document.getElementById('translationModalLabel').textContent = `Редактирование перевода - ${languageName}`;
    
    // Загружаем данные перевода
    loadTranslationData(languageCode, languageName);
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(document.getElementById('translationModal'));
    modal.show();
}

// Загружаем данные для перевода
function loadTranslationData(languageCode, languageName) {
    // Очищаем поля
    document.getElementById('translation_title').value = '';
    document.getElementById('translation_excerpt').value = '';
    document.getElementById('translation_content').value = '';
    document.getElementById('translation_meta_title').value = '';
    document.getElementById('translation_meta_description').value = '';
    document.getElementById('translation_meta_keywords').value = '';
    
    // Показываем индикатор загрузки
    const statusElement = document.getElementById('translationStatus');
    const statusText = document.getElementById('statusText');
    if (statusElement && statusText) {
        statusElement.style.display = 'block';
        statusText.textContent = 'Загрузка...';
        statusElement.className = 'alert alert-info';
    }
    
    // Загружаем перевод с сервера
    fetch(`/panel/blog/${currentArticleId}/translations/${languageCode}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.translation) {
                // Заполняем поля данными
                if (data.translation.title) {
                    document.getElementById('translation_title').value = data.translation.title;
                }
                if (data.translation.excerpt) {
                    document.getElementById('translation_excerpt').value = data.translation.excerpt;
                }
                if (data.translation.content) {
                    document.getElementById('translation_content').value = data.translation.content;
                }
                if (data.translation.meta_title) {
                    document.getElementById('translation_meta_title').value = data.translation.meta_title;
                }
                if (data.translation.meta_description) {
                    document.getElementById('translation_meta_description').value = data.translation.meta_description;
                }
                if (data.translation.meta_keywords) {
                    document.getElementById('translation_meta_keywords').value = data.translation.meta_keywords;
                }
                
                // Обновляем статус
                if (statusElement && statusText) {
                    if (data.is_original) {
                        statusElement.className = 'alert alert-warning';
                        statusText.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Оригинальный контент (для копирования и перевода)';
                        // Показываем кнопку копирования
                        document.getElementById('copyOriginalBtn').style.display = 'inline-block';
                    } else {
                        statusElement.className = 'alert alert-success';
                        statusText.innerHTML = '<i class="fas fa-check-circle me-2"></i>Перевод существует';
                        // Скрываем кнопку копирования
                        document.getElementById('copyOriginalBtn').style.display = 'none';
                    }
                }
                
                // Инициализируем TinyMCE для поля контента
                setTimeout(() => {
                    initTinyMCEForTranslation();
                }, 100);
            } else {
                showNotification('Ошибка при загрузке данных', 'error');
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки перевода:', error);
            showNotification('Ошибка при загрузке перевода', 'error');
            if (statusElement && statusText) {
                statusElement.className = 'alert alert-danger';
                statusText.innerHTML = '<i class="fas fa-times-circle me-2"></i>Ошибка загрузки';
            }
        });
}

// Инициализация TinyMCE для перевода
function initTinyMCEForTranslation() {
    if (typeof tinymce !== 'undefined') {
        // Уничтожаем существующий редактор
        if (tinymce.get('translation_content')) {
            tinymce.get('translation_content').destroy();
        }
        
        tinymce.init({
            selector: '#translation_content',
            height: 300,
            menubar: false,
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    }
}

// Функция для сохранения перевода
function saveTranslation() {
    const title = document.getElementById('translation_title').value;
    const excerpt = document.getElementById('translation_excerpt').value;
    const contentEditor = document.getElementById('translation_content');
    const metaTitle = document.getElementById('translation_meta_title').value;
    const metaDescription = document.getElementById('translation_meta_description').value;
    const metaKeywords = document.getElementById('translation_meta_keywords').value;
    
    // Получаем содержимое из TinyMCE редактора
    let content = '';
    if (tinymce.get('translation_content')) {
        content = tinymce.get('translation_content').getContent();
    } else {
        content = contentEditor.value;
    }
    
    // Подготавливаем данные для отправки
    const formData = {
        language_code: currentLanguage,
        title: title,
        excerpt: excerpt,
        content: content,
        meta_title: metaTitle,
        meta_description: metaDescription,
        meta_keywords: metaKeywords,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    // Отправляем данные на сервер
    fetch(`/panel/blog/${currentArticleId}/save-translation`, {
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
            showNotification(data.message, 'success');
            
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('translationModal'));
            if (modal) {
                modal.hide();
            }
            
            // Обновляем страницу для обновления индикаторов переводов
            location.reload();
        } else {
            showNotification('Ошибка при сохранении перевода: ' + (data.message || 'Неизвестная ошибка'), 'error');
        }
    })
    .catch(error => {
        console.error('Ошибка сохранения перевода:', error);
        showNotification('Ошибка при сохранении перевода', 'error');
    });
}

// Функция для копирования оригинального контента
function copyOriginalContent() {
    // Загружаем оригинальный контент статьи
    fetch(`/panel/blog/${currentArticleId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.article) {
                // Заполняем поля оригинальным контентом
                document.getElementById('translation_title').value = data.article.title || '';
                document.getElementById('translation_excerpt').value = data.article.excerpt || '';
                document.getElementById('translation_meta_title').value = data.article.meta_title || '';
                document.getElementById('translation_meta_description').value = data.article.meta_description || '';
                document.getElementById('translation_meta_keywords').value = data.article.meta_keywords || '';
                
                // Для контента используем TinyMCE
                if (tinymce.get('translation_content')) {
                    tinymce.get('translation_content').setContent(data.article.content || '');
                } else {
                    document.getElementById('translation_content').value = data.article.content || '';
                }
                
                showNotification('Оригинальный контент скопирован в поля для перевода', 'success');
            } else {
                showNotification('Ошибка при загрузке оригинального контента', 'error');
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки оригинального контента:', error);
            showNotification('Ошибка при загрузке оригинального контента', 'error');
        });
}
</script>
@endpush
