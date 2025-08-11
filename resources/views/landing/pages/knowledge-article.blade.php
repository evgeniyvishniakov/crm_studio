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
                
                <div class="article-meta">
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-user me-2"></i>
                        <span class="me-4">{{ $article->author }}</span>
                        <i class="fas fa-calendar me-2"></i>
                        <span>{{ $article->published_at ? $article->published_at->format('d.m.Y') : $article->created_at->format('d.m.Y') }}</span>
                    </div>
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
                        <h2 class="h3 fw-bold mb-4 text-primary">Пошаговая инструкция</h2>
                        
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
                    <!-- Related Articles -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-book-open me-2"></i>
                                Похожие статьи
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Функция в разработке...
                            </p>
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
                        <i class="fas fa-arrow-left me-2"></i>
                        Назад к базе знаний
                    </a>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            Печать
                        </button>
                        <button class="btn btn-outline-secondary" onclick="shareArticle()">
                            <i class="fas fa-share-alt me-2"></i>
                            Поделиться
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $article->title }}',
            text: '{{ $article->description }}',
            url: window.location.href
        });
    } else {
        // Fallback для браузеров без поддержки Web Share API
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Ссылка скопирована в буфер обмена!');
        });
    }
}
</script>
@endsection

@push('styles')
<style>
    /* Красивые стили для базы знаний с теми же шрифтами что и в лендинге */
    .step-item {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .step-item:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .step-number {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .step-content h4 {
        color: #2c3e50;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .step-text {
        font-size: 16px;
        line-height: 1.7;
        color: #495057;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .step-text p {
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 15px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .step-text h1, .step-text h2, .step-text h3, .step-text h4, .step-text h5, .step-text h6 {
        color: #2c3e50;
        font-weight: 600;
        margin-top: 20px;
        margin-bottom: 15px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .step-text ul, .step-text ol {
        margin-bottom: 15px;
        padding-left: 20px;
    }
    
    .step-text li {
        margin-bottom: 8px;
        font-size: 15px;
        line-height: 1.6;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .step-image {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .tip-item {
        background: white;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .tip-icon {
        flex-shrink: 0;
        margin-top: 0.25rem;
        color: #ffc107;
    }
    
    .tip-content {
        line-height: 1.6;
        color: #495057;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .tip-content p {
        margin-bottom: 0.5rem;
        font-size: 15px;
        line-height: 1.6;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .tip-content p:last-child {
        margin-bottom: 0;
    }
    
    .article-meta {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 30px;
    }
    
    .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
        transition: color 0.3s ease;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .breadcrumb-item a:hover {
        color: #764ba2;
    }
    
    .display-5 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .lead {
        font-size: 18px;
        line-height: 1.6;
        color: #6c757d;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .card-title {
        font-size: 23px;
        font-weight: bold;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    /* Адаптивность для мобильных устройств */
    @media (max-width: 768px) {
        .step-item {
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .step-number {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }
        
        .step-content h4 {
            font-size: 18px;
        }
        
        .step-text, .tip-content {
            font-size: 15px;
        }
        
        .display-5 {
            font-size: 2rem;
        }
        
        .lead {
            font-size: 16px;
        }
    }
</style>
@endpush
