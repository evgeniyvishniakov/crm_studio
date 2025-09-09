@extends('landing.layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
    $currentLanguage = \App\Helpers\LanguageHelper::getCurrentLanguage();
@endphp

@section('title', $article->localized_meta_title ?: $article->localized_title . ' - ' . __('landing.blog') . ' - Trimora')
@section('description', $article->localized_meta_description ?: $article->localized_excerpt)
@section('keywords', $article->localized_meta_keywords)
@section('canonical', \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug]))
@section('og:title', $article->localized_meta_title ?: $article->localized_title)
@section('og:description', $article->localized_meta_description ?: $article->localized_excerpt)
@section('og:type', 'article')
@section('og:url', \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug]))
@section('og:image', $article->localized_featured_image ? Storage::url($article->localized_featured_image) : asset('images/og-default.jpg'))
@section('og:locale', app()->getLocale())
@section('twitter:title', $article->localized_meta_title ?: $article->localized_title)
@section('twitter:description', $article->localized_meta_description ?: $article->localized_excerpt)
@section('twitter:image', $article->localized_featured_image ? Storage::url($article->localized_featured_image) : asset('images/og-default.jpg'))

@push('head')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $article->localized_title }}",
  "description": "{{ strip_tags($article->localized_excerpt) }}",
  "image": "{{ $article->localized_featured_image ? Storage::url($article->localized_featured_image) : asset('images/og-default.jpg') }}",
  "author": {
    "@type": "Person",
    "name": "{{ $article->author ?? __('landing.blog_author_unknown') }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Trimora",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  },
  "datePublished": "{{ $article->published_at ? $article->published_at->toISOString() : $article->created_at->toISOString() }}",
  "dateModified": "{{ $article->updated_at->toISOString() }}",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug]) }}"
  },
  "articleSection": "{{ $article->category ? $article->category->localized_name : __('landing.blog') }}",
  "keywords": "{{ $article->localized_meta_keywords }}",
  "wordCount": "{{ str_word_count(strip_tags($article->localized_content)) }}",
  "timeRequired": "PT{{ $article->reading_time }}M"
}
</script>
@endpush

@section('content')
<!-- Hero -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item">
                            <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog') }}" class="text-decoration-none">
                                {{ __('landing.blog') }}
                            </a>
                        </li>
                        @if($article->category)
                            <li class="breadcrumb-item">
                                <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', ['category' => $article->category->slug]) }}" class="text-decoration-none">
                                    {{ $article->category->localized_name }}
                                </a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $article->localized_title }}
                        </li>
                    </ol>
                </nav>

                <h1 class="display-5 fw-bolder mb-3" style="font-weight: 900 !important;">{{ $article->localized_title }}</h1>
                
                <div class="article-meta">
                    <div class="d-flex align-items-center flex-wrap text-muted gap-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user"></i>
                            <span>{{ $article->author ?? __('landing.blog_author_unknown') }}</span>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $article->published_at ? $article->published_at->format('d.m.Y') : $article->created_at->format('d.m.Y') }}</span>
                        </div>
                        
                        @if($article->tags->count() > 0)
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <i class="fas fa-tags"></i>
                                @foreach($article->tags as $tag)
                                    <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', ['tag' => $tag->slug]) }}" 
                                       class="badge text-decoration-none" 
                                       style="background-color: {{ $tag->color }}!important; color: white;">
                                        #{{ $tag->localized_name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <!-- Featured Image -->
                @if($article->localized_featured_image)
                    <div class="mb-5">
                        <img src="{{ Storage::url($article->localized_featured_image) }}" 
                             alt="{{ $article->localized_title }}" 
                             class="img-fluid rounded shadow">
                    </div>
                @endif

                <!-- Content -->
                @if($article->localized_content)
                    <div class="article-content">
                        {!! \App\Helpers\SystemHelper::removeH1FromContent($article->localized_content, $article->localized_title) !!}
                    </div>
                @endif

                <!-- CTA Button -->
                <div class="text-center mt-5 mb-5">
                    @if(Auth::guard('client')->check())
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg animate-pulse">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('landing.enter_system') }}
                        </a>
                    @else
                        <a href="#" class="btn btn-primary btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="fas fa-rocket me-2"></i>{{ __('landing.try_free_7_days') }}
                        </a>
                    @endif
                </div>

            </div>

        </div>
    </div>

    <!-- Similar Articles - Full Width -->
    @if($similarArticles && $similarArticles->count() > 0)
    <section class="py-5 bg-light">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h3 class="h4 fw-bolder mb-4 text-center" style="font-weight: 800 !important;">{{ __('landing.blog_similar_articles') }}</h3>
                    <div class="row">
                        @foreach($similarArticles as $similarArticle)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                @if($similarArticle->localized_featured_image)
                                <div class="card-img-top-container" style="height: 200px; overflow: hidden;">
                                    <img src="{{ Storage::url($similarArticle->localized_featured_image) }}" 
                                         alt="{{ $similarArticle->localized_title }}" 
                                         class="card-img-top" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold">
                                        <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $similarArticle->slug]) }}" 
                                           class="text-decoration-none text-dark">
                                            {{ $similarArticle->localized_title }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted flex-grow-1">
                                        {{ Str::limit(strip_tags($similarArticle->localized_excerpt), 120) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $similarArticle->published_at ? $similarArticle->published_at->format('d.m.Y') : $similarArticle->created_at->format('d.m.Y') }}
                                        </small>
                                        <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $similarArticle->slug]) }}" 
                                           class="btn btn-outline-primary" style="font-size: 11px; padding: 4px 12px;">
                                            {{ __('landing.blog_read_more') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
</section>

<!-- Navigation -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        {{ __('landing.blog_back_to_blog') }}
                    </a>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="shareArticle()">
                            <i class="fas fa-share-alt me-2"></i>
                            {{ __('landing.blog_share') }}
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
            title: '{{ $article->localized_title }}',
            text: '{{ $article->localized_excerpt }}',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href).then(function() {
            // Показать уведомление о копировании
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                {{ __('landing.blog_link_copied') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        });
    }
}
</script>
@endsection

@push('styles')
<style>
    /* Красивые стили для статьи блога с теми же шрифтами что и в лендинге */
    .article-meta {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 30px;
    }
    
    .article-meta .badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .article-meta .badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    
    .article-meta .d-flex { а вор
        gap: 5px;
    }
    
    @media (max-width: 768px) {
        
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
        text-transform: none !important;
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
        max-width: 100%;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin: 20px 0;
    }
    
    .article-content h2 {
        color: #2c3e50;
        font-weight: 600;
        margin: 30px 0 20px;
        font-size: 28px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        text-transform: none !important;
    }
    
    .article-content h3 {
        color: #34495e;
        font-weight: 600;
        margin: 25px 0 15px;
        font-size: 22px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        text-transform: none !important;
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
    
    /* Similar Articles Styles */
    .similar-articles .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
    }
    
    .similar-articles .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .similar-articles .card-title a {
        color: #2c3e50;
        font-weight: 600;
        font-size: 16px;
        line-height: 1.4;
        transition: color 0.3s ease;
    }
    
    .similar-articles .card-title a:hover {
        color: #667eea;
    }
    
    .similar-articles .card-text {
        font-size: 14px;
        line-height: 1.5;
        color: #6c757d;
    }
    
    .similar-articles .btn-outline-primary {
        border-radius: 15px;
        font-size: 11px;
        padding: 4px 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .similar-articles .btn-outline-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    /* Адаптивность для мобильных устройств */
    @media (max-width: 768px) {
        .display-5 {
            font-size: 2rem;
        }
        
        .lead {
            font-size: 16px;
        }
    }
</style>
@endpush
