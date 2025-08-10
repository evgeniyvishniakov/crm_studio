@extends('landing.layouts.app')

@section('title', $article->title . ' - База знаний - Trimora')
@section('description', $article->description)

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item">
                            <a href="{{ route('beautyflow.knowledge') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>База знаний
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="badge bg-primary">{{ $categories[$article->category] ?? $article->category }}</span>
                        </li>
                    </ol>
                </nav>
                
                <h1 class="display-5 fw-bold mb-3">{{ $article->title }}</h1>
                <p class="lead text-muted mb-4">{{ $article->description }}</p>
                
                <div class="d-flex align-items-center text-muted">
                    <i class="fas fa-user me-2"></i>
                    <span class="me-4">{{ $article->author }}</span>
                    <i class="fas fa-calendar me-2"></i>
                    <span>{{ $article->published_at ? $article->published_at->format('d.m.Y') : $article->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Featured Image -->
                @if($article->featured_image)
                    <div class="mb-5">
                        <img src="{{ asset('storage/' . $article->featured_image) }}" 
                             alt="{{ $article->title }}" 
                             class="img-fluid rounded shadow">
                    </div>
                @endif

                <!-- Steps -->
                @if($article->steps->count() > 0)
                    <div class="mb-5">
                        <h2 class="h3 fw-bold mb-4">Пошаговая инструкция</h2>
                        
                        @foreach($article->steps as $step)
                            <div class="step-item mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="step-number me-3">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="step-content flex-grow-1">
                                        <h4 class="h5 fw-bold mb-3">{{ $step->title }}</h4>
                                        <div class="step-text">
                                            {!! $step->content !!}
                                        </div>
                                        
                                        @if($step->image)
                                            <div class="step-image mt-3">
                                                <img src="{{ asset('storage/' . $step->image) }}" 
                                                     alt="{{ $step->title }}" 
                                                     class="img-fluid rounded shadow-sm">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Tips -->
                @if($article->tips->count() > 0)
                    <div class="mb-5">
                        <h2 class="h3 fw-bold mb-4">Полезные советы</h2>
                        
                        <div class="tips-list">
                            @foreach($article->tips as $tip)
                                <div class="tip-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="tip-icon me-3">
                                            <i class="fas fa-lightbulb text-warning fa-lg"></i>
                                        </div>
                                        <div class="tip-content">
                                            {!! $tip->content !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <!-- Article Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Информация о статье</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Категория:</strong>
                                <span class="badge bg-primary ms-2">{{ $categories[$article->category] ?? $article->category }}</span>
                            </div>
                            
                            @if($article->steps->count() > 0)
                                <div class="mb-3">
                                    <strong>Количество шагов:</strong>
                                    <span class="badge bg-info ms-2">{{ $article->steps->count() }}</span>
                                </div>
                            @endif
                            
                            @if($article->tips->count() > 0)
                                <div class="mb-3">
                                    <strong>Полезных советов:</strong>
                                    <span class="badge bg-warning ms-2">{{ $article->tips->count() }}</span>
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <strong>Автор:</strong>
                                <span class="text-muted ms-2">{{ $article->author }}</span>
                            </div>
                            
                            <div class="mb-0">
                                <strong>Дата публикации:</strong>
                                <span class="text-muted ms-2">
                                    {{ $article->published_at ? $article->published_at->format('d.m.Y') : $article->created_at->format('d.m.Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Related Articles -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Похожие статьи</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">В разработке...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Navigation -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('beautyflow.knowledge') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Вернуться к списку статей
                    </a>
                    
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-share me-2"></i>Поделиться
                        </a>
                        <a href="#" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-print me-2"></i>Распечатать
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* Стили для шагов */
.step-item {
    border-left: 4px solid var(--bs-primary);
    padding-left: 1.5rem;
    margin-left: 1rem;
}

.step-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--bs-primary);
    color: white;
    border-radius: 50%;
    font-weight: bold;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.step-content {
    padding-top: 0.5rem;
}

.step-text {
    line-height: 1.7;
    color: #495057;
}

.step-text p {
    margin-bottom: 1rem;
}

.step-text ul, .step-text ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.step-image {
    border-radius: 0.5rem;
    overflow: hidden;
}

/* Стили для полезных советов */
.tips-list {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.tip-item {
    background: white;
    border-radius: 0.5rem;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tip-icon {
    flex-shrink: 0;
    margin-top: 0.25rem;
}

.tip-content {
    line-height: 1.6;
    color: #495057;
}

.tip-content p {
    margin-bottom: 0.5rem;
}

.tip-content p:last-child {
    margin-bottom: 0;
}

/* Адаптивность */
@media (max-width: 768px) {
    .step-item {
        margin-left: 0;
        padding-left: 1rem;
    }
    
    .step-number {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }
    
    .tips-list {
        padding: 1rem;
    }
    
    .tip-item {
        padding: 0.75rem;
    }
}
</style>
@endpush
