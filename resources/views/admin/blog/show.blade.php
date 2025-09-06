@extends('admin.layouts.app')

@section('title', 'Просмотр статьи - Блог')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ $article->title }}</h1>
                <div>
                    <a href="{{ route('admin.blog.edit', $article->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад к списку
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Основной контент -->
                <div class="col-lg-8">
                    <!-- Статус и метаинформация -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Статус публикации</h6>
                                    <span class="badge {{ $article->is_published ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $article->is_published ? 'Опубликовано' : 'Черновик' }}
                                    </span>
                                    @if($article->is_featured)
                                        <span class="badge bg-warning ms-2">
                                            <i class="fas fa-star me-1"></i>Рекомендуемая
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6>Дата публикации</h6>
                                    <p class="mb-0">
                                        {{ $article->published_at ? $article->published_at->format('d.m.Y H:i') : 'Не опубликовано' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Изображение статьи -->
                    @if($article->featured_image)
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <img src="{{ Storage::url($article->featured_image) }}" 
                                     alt="{{ $article->title }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 400px; object-fit: cover;">
                            </div>
                        </div>
                    @endif

                    <!-- Краткое описание -->
                    @if($article->excerpt)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Краткое описание</h5>
                            </div>
                            <div class="card-body">
                                <p class="lead">{{ $article->excerpt }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Содержание статьи -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Содержание статьи</h5>
                        </div>
                        <div class="card-body">
                            <div class="content">
                                {!! $article->content !!}
                            </div>
                        </div>
                    </div>

                    <!-- SEO информация -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">SEO информация</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Meta Title</h6>
                                    <p class="text-muted">{{ $article->meta_title ?: 'Не указано' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Meta Description</h6>
                                    <p class="text-muted">{{ $article->meta_description ?: 'Не указано' }}</p>
                                </div>
                            </div>
                            @if($article->meta_keywords)
                                <div class="row">
                                    <div class="col-12">
                                        <h6>Meta Keywords</h6>
                                        <p class="text-muted">{{ $article->meta_keywords }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Боковая панель -->
                <div class="col-lg-4">
                    <!-- Информация о статье -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Информация о статье</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6>Автор</h6>
                                <p class="mb-0">{{ $article->author ?: 'Не указан' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6>Категория</h6>
                                @if($article->category)
                                    <span class="badge" style="background-color: {{ $article->category->color }}">
                                        {{ $article->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Без категории</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <h6>Теги</h6>
                                @if($article->tags->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($article->tags as $tag)
                                            <span class="badge bg-secondary" style="background-color: {{ $tag->color }}!important">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">Теги не указаны</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <h6>Статистика</h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="h4 text-primary">{{ $article->views_count }}</div>
                                        <small class="text-muted">Просмотры</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="h4 text-info">{{ $article->reading_time ?? '?' }}</div>
                                        <small class="text-muted">Мин. чтения</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6>Даты</h6>
                                <div class="small text-muted">
                                    <div>Создано: <strong>{{ $article->created_at->format('d.m.Y H:i') }}</strong></div>
                                    <div>Обновлено: <strong>{{ $article->updated_at->format('d.m.Y H:i') }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Переводы -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Переводы</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(['ru', 'en', 'ua'] as $langCode)
                                    @php
                                        $translation = $article->translation($langCode);
                                        $statusClass = $translation ? 'bg-success' : 'bg-secondary';
                                        $statusText = $translation ? '✓' : '✗';
                                        $langNames = ['ru' => 'RU', 'en' => 'EN', 'ua' => 'UA'];
                                    @endphp
                                    <span class="badge {{ $statusClass }}" title="{{ $langNames[$langCode] }}">
                                        {{ $statusText }} {{ $langNames[$langCode] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Действия -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Действия</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.blog.edit', $article->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Редактировать
                                </a>
                                
                                <button type="button" class="btn {{ $article->is_published ? 'btn-secondary' : 'btn-success' }}" 
                                        onclick="togglePublish({{ $article->id }})">
                                    <i class="fas fa-{{ $article->is_published ? 'eye-slash' : 'eye' }} me-2"></i>
                                    {{ $article->is_published ? 'Снять с публикации' : 'Опубликовать' }}
                                </button>

                                @if($article->is_published)
                                    <a href="{{ route('beautyflow.blog.show.fallback', $article->slug) }}" class="btn btn-info" target="_blank">
                                        <i class="fas fa-external-link-alt me-2"></i>Просмотреть на сайте
                                    </a>
                                @endif

                                <button type="button" class="btn btn-danger" onclick="deleteArticle({{ $article->id }})">
                                    <i class="fas fa-trash me-2"></i>Удалить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
<script>
// Переключение статуса публикации
function togglePublish(articleId) {
    fetch(`/panel/blog/${articleId}/toggle-publish`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification('Ошибка при изменении статуса', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при изменении статуса', 'error');
    });
}

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
</script>
@endpush
