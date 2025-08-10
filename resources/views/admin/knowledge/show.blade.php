@extends('admin.layouts.app')

@section('title', $article->title . ' - База знаний')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $article->title }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.knowledge.index') }}">База знаний</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $article->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.knowledge.edit', $article->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                    <a href="{{ route('admin.knowledge.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад к списку
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Основная информация -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Основная информация</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> {{ $article->id }}</p>
                                    <p><strong>Заголовок:</strong> {{ $article->title }}</p>
                                    <p><strong>Категория:</strong> 
                                        <span class="badge bg-primary">{{ $categories[$article->category] ?? $article->category }}</span>
                                    </p>
                                    <p><strong>Автор:</strong> {{ $article->author }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Статус:</strong> 
                                        @if($article->is_published)
                                            <span class="badge bg-success">Опубликовано</span>
                                        @else
                                            <span class="badge bg-secondary">Черновик</span>
                                        @endif
                                    </p>
                                    <p><strong>Дата создания:</strong> {{ $article->created_at->format('d.m.Y H:i') }}</p>
                                    <p><strong>Дата обновления:</strong> {{ $article->updated_at->format('d.m.Y H:i') }}</p>
                                    @if($article->published_at)
                                        <p><strong>Дата публикации:</strong> {{ $article->published_at->format('d.m.Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Описание -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Описание</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $article->description }}</p>
                        </div>
                    </div>

                    <!-- Содержание -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Содержание статьи</h5>
                        </div>
                        <div class="card-body">
                            <div class="article-content">
                                {!! $article->content !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Изображение -->
                    @if($article->featured_image)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Изображение</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                     alt="{{ $article->title }}" 
                                     class="img-fluid rounded">
                                <div class="mt-3">
                                    <small class="text-muted">{{ $article->featured_image }}</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Мета-теги -->
                    @if($article->meta_tags)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Мета-теги</h5>
                            </div>
                            <div class="card-body">
                                @foreach($article->meta_tags as $key => $value)
                                    <p class="mb-2">
                                        <strong>{{ $key }}:</strong> {{ $value }}
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Действия -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Действия</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.knowledge.edit', $article->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Редактировать
                                </a>
                                
                                @if($article->is_published)
                                    <form action="{{ route('admin.knowledge.update', $article->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_published" value="0">
                                        <button type="submit" class="btn btn-outline-warning w-100">
                                            <i class="fas fa-eye-slash me-2"></i>Снять с публикации
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.knowledge.update', $article->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_published" value="1">
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="fas fa-eye me-2"></i>Опубликовать
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.knowledge.destroy', $article->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Вы уверены, что хотите удалить эту статью?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash me-2"></i>Удалить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.article-content {
    line-height: 1.6;
}

.article-content h1, .article-content h2, .article-content h3,
.article-content h4, .article-content h5, .article-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.article-content p {
    margin-bottom: 1rem;
}

.article-content ul, .article-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.article-content blockquote {
    border-left: 4px solid #dee2e6;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6c757d;
}

.article-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

.article-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    overflow-x: auto;
}
</style>
@endpush
