@extends('landing.layouts.app')

@php
    $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
@endphp

@section('title', $article->title . ' - База знаний - Trimora')
@section('description', $article->description)

@section('content')
<!-- Hero -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item">
                            <a href="{{ \App\Helpers\LanguageHelper::addLanguageToUrl(route('beautyflow.knowledge')) }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('landing.knowledge_base') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="badge bg-primary">{{ $categories[$article->category] ?? $article->category }}</span>
                        </li>
                    </ol>
                </nav>
                

                <h1 class="display-5 fw-bold mb-3">{{ $article->title }}</h1>
                <p class="lead text-muted mb-4">{!! $article->description !!}</p>
                
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

                <!-- Content -->
                @if($article->content)
                    <div class="article-content">
                        {!! $article->content !!}
                    </div>
                @endif

                <!-- Steps -->
                @if($article->steps->count() > 0)
                    <div class="mb-5">
                        <h2 class="h3 fw-bold mb-4 text-primary">{{ __('landing.knowledge_steps_instruction') }}</h2>
                        
                        @foreach($article->steps as $step)
                            <div class="step-item mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="step-number me-3">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="step-content flex-grow-1">
                                        @if($step->title || $step->content)
                                            <h4 class="h5 fw-bold mb-3">{{ $step->title }}</h4>
                                            <div class="step-text">
                                                {!! $step->content !!}
                                            </div>
                                        @endif
                                        
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
                        <h2 class="h3 fw-bold mb-4">{{ __('landing.knowledge_useful_tips') }}</h2>
                        
                        <div class="tips-list">
                            @foreach($article->tips as $tip)
                                <div class="tip-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="tip-icon me-3">
                                            <i class="fas fa-lightbulb text-warning fa-lg"></i>
                                        </div>
                                        <div class="tip-content">
                                            @if($tip->content)
                                                {!! $tip->content !!}
                                            @endif
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
                                {{ __('landing.knowledge_similar_articles') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $relatedArticles = collect();
                                if ($article->related_articles && is_array($article->related_articles)) {
                                    $relatedArticles = \App\Models\KnowledgeArticle::published()
                                        ->whereIn('id', $article->related_articles)
                                        ->with('translations')
                                        ->get();
                                }
                                
                                if ($relatedArticles->isEmpty()) {
                                    $relatedArticles = \App\Models\KnowledgeArticle::published()
                                        ->where('category', $article->category)
                                        ->where('id', '!=', $article->id)
                                        ->with('translations')
                                        ->limit(3)
                                        ->get();
                                }
                            @endphp
                            
                            @forelse($relatedArticles as $relatedArticle)
                                <div class="related-article-card mb-3">
                                    <a href="{{ \App\Helpers\LanguageHelper::addLanguageToUrl(route('beautyflow.knowledge.show', $relatedArticle->slug)) }}" 
                                       class="related-article-link">
                                        <div class="related-article-icon">
                                            <i class="fas fa-file-alt text-primary"></i>
                                        </div>
                                        <div class="related-article-content">
                                            <h6 class="related-article-title mb-2">
                                                {{ $relatedArticle->title }}
                                            </h6>
                                            <p class="related-article-description mb-0">
                                                {!! Str::limit(strip_tags($relatedArticle->description), 80) !!}
                                            </p>
                                        </div>
                                        <div class="related-article-arrow">
                                            <i class="fas fa-arrow-right text-muted"></i>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="related-article-empty">
                                    <i class="fas fa-info-circle text-muted me-2"></i>
                                    <span class="text-muted">{{ __('landing.knowledge_no_similar_articles') }}</span>
                                </div>
                            @endforelse
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
                    <a href="{{ \App\Helpers\LanguageHelper::addLanguageToUrl(route('beautyflow.knowledge')) }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        {{ __('landing.knowledge_back_to_knowledge') }}
                    </a>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            {{ __('landing.knowledge_print') }}
                        </button>
                        <button class="btn btn-outline-secondary" onclick="shareArticle()">
                            <i class="fas fa-share-alt me-2"></i>
                            {{ __('landing.knowledge_share') }}
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
        navigator.clipboard.writeText(window.location.href).then(function() {
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
        font-size: 16px;
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
    
    .article-content {
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        line-height: 1.8;
        color: #2c3e50;
    
        
    }
    
    .article-content h2 {
        color: #2c3e50;
        font-weight: 600;
        margin: 30px 0 20px;
        font-size: 28px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-content h3 {
        color: #34495e;
        font-weight: 600;
        margin: 25px 0 15px;
        font-size: 22px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-content p {
        margin-bottom: 20px;
        font-size: 16px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-content ul, .article-content ol {
        margin-bottom: 20px;
        padding-left: 25px;
    }
    
    .article-content li {
        margin-bottom: 10px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    /* Стили для похожих статей */
    .related-article-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .related-article-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #667eea;
    }
    
    .related-article-link {
        display: flex;
        align-items: center;
        padding: 20px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
    }
    
    .related-article-link:hover {
        text-decoration: none;
        color: inherit;
    }
    
    .related-article-icon {
        flex-shrink: 0;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        transition: all 0.3s ease;
    }
    
    .related-article-icon i {
        color: #667eea;
        font-size: 20px;
    }
    
    .related-article-card:hover .related-article-icon i {
        color: #764ba2;
        transform: scale(1.1);
    }
    
    .related-article-content {
        flex-grow: 1;
        min-width: 0;
    }
    
    .related-article-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 16px;
        line-height: 1.4;
        margin-bottom: 8px;
        transition: color 0.3s ease;
    }
    
    .related-article-card:hover .related-article-title {
        color: #667eea;
    }
    
    .related-article-description {
        color: #6c757d;
        font-size: 14px;
        line-height: 1.5;
        margin: 0;
    }
    
    .related-article-arrow {
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 15px;
        transition: all 0.3s ease;
    }
    
    .related-article-arrow i {
        color: #6c757d;
        font-size: 14px;
    }
    
    .related-article-card:hover .related-article-arrow i {
        color: #667eea;
    }
    
    .related-article-card:hover .related-article-arrow {
        transform: translateX(3px);
    }
    
    .related-article-empty {
        text-align: center;
        padding: 30px 20px;
        color: #6c757d;
        font-size: 14px;
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
        
        /* Адаптивность для похожих статей */
        .related-article-link {
            padding: 15px;
        }
        
        .related-article-icon {
            width: 40px;
            height: 40px;
            margin-right: 12px;
        }
        
        .related-article-icon i {
            font-size: 16px;
        }
        
        .related-article-title {
            font-size: 15px;
        }
        
        .related-article-description {
            font-size: 13px;
        }
        
        .related-article-arrow {
            width: 25px;
            height: 25px;
            margin-left: 10px;
        }
    }
</style>
@endpush
