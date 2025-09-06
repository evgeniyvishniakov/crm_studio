@extends('landing.layouts.app')

@section('title', __('landing.blog_page_title') . ' - Trimora')
@section('description', __('landing.blog_page_description'))
@section('canonical', \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog'))
@section('og:title', __('landing.blog_page_title') . ' - Trimora')
@section('og:description', __('landing.blog_page_description'))
@section('og:type', 'website')
@section('og:url', \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog'))
@section('og:image', asset('images/og-blog.jpg'))
@section('og:locale', app()->getLocale())
@section('twitter:title', __('landing.blog_page_title') . ' - Trimora')
@section('twitter:description', __('landing.blog_page_description'))
@section('twitter:image', asset('images/og-blog.jpg'))

@push('head')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "{{ __('landing.blog_page_title') }}",
  "description": "{{ __('landing.blog_page_description') }}",
        "url": "{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', []) }}",
  "publisher": {
    "@type": "Organization",
    "name": "Trimora",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  },
  "blogPost": [
    @foreach($articles as $index => $article)
    {
      "@type": "BlogPosting",
      "headline": "{{ $article->localized_title }}",
      "description": "{{ strip_tags($article->localized_excerpt) }}",
        "url": "{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug]) }}",
      "datePublished": "{{ $article->published_at ? $article->published_at->toISOString() : $article->created_at->toISOString() }}",
      "dateModified": "{{ $article->updated_at->toISOString() }}",
      "author": {
        "@type": "Person",
        "name": "{{ $article->author ?? __('landing.blog_author_unknown') }}"
      },
      "image": "{{ $article->featured_image ? Storage::url($article->featured_image) : asset('images/og-default.jpg') }}",
      "articleSection": "{{ $article->category ? $article->category->localized_name : __('landing.blog') }}"
    }{{ $index < $articles->count() - 1 ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush

@section('styles')
<style>
    /* Красивые стили для списка статей блога с теми же шрифтами что и в лендинге */
    .article-item .card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 16px;
        overflow: hidden;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-item .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .article-item .card-img-top {
        transition: transform 0.3s ease;
    }
    
    .article-item .card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .article-item .card-body {
        padding: 25px;
    }
    
    .article-item .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.4;
        margin-bottom: 15px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-item .card-text {
        font-size: 15px;
        line-height: 1.6;
        color: #6c757d;
        margin-bottom: 20px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-item .badge {
        font-size: 12px;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 20px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-item small {
        font-size: 13px;
        color: #6c757d;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-item .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 20px;
        transition: all 0.3s ease;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    h5.card-title {
        font-size: 23px!important;
        font-weight: bold!important;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .card h5.card-title {
        font-size: 23px!important;
        font-weight: bold!important;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-item .card .card-body h5.card-title {
        font-size: 23px!important;
        font-weight: bold!important;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .article-item .card .card-body h5.card-title a {
        transition: color 0.3s ease;
    }
    
    .article-item .card .card-body h5.card-title a:hover {
        color: #667eea !important;
    }
    
    .category-filter {
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .category-filter:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
    }
    
    .category-filter.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .search-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .search-section .input-group {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .search-section .form-control {
        border: none;
        padding: 15px 20px;
        font-size: 16px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .search-section .form-control:focus {
        box-shadow: none;
    }
    
    .search-section .btn {
        padding: 15px 25px;
        border: none;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-weight: 500;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }
    
    .hero-section .container {
        position: relative;
        z-index: 1;
    }
    
    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .hero-section .lead {
        font-size: 20px;
        opacity: 0.9;
        line-height: 1.6;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    /* Адаптивность */
    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2.5rem;
        }
        
        .hero-section .lead {
            font-size: 18px;
        }
        
        .search-section {
            padding: 20px;
        }
        
        .article-item .card-body {
            padding: 20px;
        }
        
        .category-filter {
            padding: 8px 16px;
            font-size: 14px;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">{{ __('landing.blog_page_title') }}</h1>
                <p class="lead">{{ __('landing.blog_page_description') }}</p>
                
                @if($currentFilter)
                    <div class="alert alert-info d-inline-flex align-items-center mt-4" role="alert">
                        <i class="fas fa-filter me-2"></i>
                        <span>{{ __('landing.blog_filtered_by') }}: 
                            <strong>
                                @if($currentFilter instanceof \App\Models\Admin\BlogCategory)
                                    {{ $currentFilter->name }}
                                @else
                                    #{{ $currentFilter->name }}
                                @endif
                            </strong>
                        </span>
                        <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog') }}" 
                           class="btn btn-sm btn-outline-primary ms-3">
                            <i class="fas fa-times me-1"></i>
                            {{ __('landing.blog_clear_filter') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Search -->
<section class="search-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="{{ __('landing.blog_search_placeholder') }}" aria-label="{{ __('landing.blog_search_placeholder') }}">
                    <button class="btn btn-primary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-4">
    <div class="container">
        <div class="row g-3 justify-content-center">
            <div class="col-auto">
                <button class="btn btn-outline-primary category-filter active" data-category="all">{{ __('landing.blog_all_categories') }}</button>
            </div>
            @foreach($categories as $category)
                <div class="col-auto">
                    <button class="btn btn-outline-primary category-filter" data-category="{{ $category->id }}" style="background-color: {{ $category->color }}20; border-color: {{ $category->color }};">
                        {{ $category->localized_name }}
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Articles -->
<section class="py-5">
    <div class="container">
        <div class="row g-4" id="articlesContainer">
            @forelse($articles as $article)
                <div class="col-lg-4 col-md-6 article-item" data-category="{{ $article->blog_category_id }}" data-title="{{ strtolower($article->localized_title) }}" data-content="{{ strtolower(strip_tags($article->localized_excerpt)) }}">
                    <div class="card h-100">
                        @if($article->featured_image)
                            <img src="{{ Storage::url($article->featured_image) }}" 
                                 class="card-img-top" 
                                 alt="{{ $article->localized_title }}"
                                 style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if($article->category)
                                    <span class="badge me-2" style="background-color: {{ $article->category->color }}">{{ $article->category->localized_name }}</span>
                                @endif
                                @if($article->is_featured)
                                    <span class="badge bg-warning text-dark">{{ __('landing.blog_featured') }}</span>
                                @endif
                            </div>
                            <h5 class="card-title" style="font-size: 23px!important; font-weight: bold!important; font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;">
                                <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug]) }}" 
                                   class="text-decoration-none text-dark">
                                    {{ $article->localized_title }}
                                </a>
                            </h5>
                            <p class="card-text">
                                {!! Str::limit(strip_tags($article->localized_excerpt), 120) !!}
                            </p>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user text-muted me-2"></i>
                                <small class="text-muted">{{ $article->author ?? __('landing.blog_author_unknown') }}</small>
                                <span class="mx-2">•</span>
                                <i class="fas fa-calendar text-muted me-2"></i>
                                <small class="text-muted">{{ $article->published_at ? $article->published_at->format('d.m.Y') : $article->created_at->format('d.m.Y') }}</small>
                            </div>
                            
                            <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug]) }}" class="btn btn-primary">{{ __('landing.blog_read_article') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('landing.blog_no_articles') }}</h4>
                    <p class="text-muted">{{ __('landing.blog_no_articles_text') }}</p>
                </div>
            @endforelse
        </div>
        
        <!-- Empty State -->
        <div class="empty-state text-center py-5" style="display: none;">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">{{ __('landing.blog_search_no_results') }}</h4>
            <p class="text-muted">{{ __('landing.blog_search_no_results_text') }}</p>
            <button class="btn btn-outline-primary" onclick="clearFilters()">
                <i class="fas fa-times me-2"></i>
                {{ __('landing.blog_clear_filters') }}
            </button>
        </div>
    </div>
</section>

<!-- Contact Support Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="fw-bold mb-3">{{ __('landing.blog_contact_support_title') }}</h3>
                <p class="text-muted mb-4">{{ __('landing.blog_contact_support_text') }}</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.contact') }}" class="btn btn-primary">{{ __('landing.blog_contact_support') }}</a>
                    <a href="#" class="btn btn-outline-primary">{{ __('landing.blog_ask_chat') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const categoryFilters = document.querySelectorAll('.category-filter');
    const articles = document.querySelectorAll('.article-item');
    const emptyState = document.querySelector('.empty-state');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const activeCategory = document.querySelector('.category-filter.active').dataset.category;
        
        articles.forEach(article => {
            const title = article.querySelector('.card-title').textContent.toLowerCase();
            const content = article.dataset.content;
            const category = article.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || content.includes(searchTerm);
            const matchesCategory = activeCategory === 'all' || category === activeCategory;
            
            if (matchesSearch && matchesCategory) {
                article.style.display = 'block';
                article.style.animation = 'fadeIn 0.5s ease-in';
            } else {
                article.style.display = 'none';
            }
        });
        
        if (emptyState) {
            if (Array.from(articles).filter(article => 
                article.style.display !== 'none'
            ).length === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
            }
        }
    }

    searchBtn.addEventListener('click', performSearch);
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    searchInput.addEventListener('input', function() {
        if (this.value.length > 2 || this.value.length === 0) {
            performSearch();
        }
    });

    categoryFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            categoryFilters.forEach(f => f.classList.remove('active'));
            
            this.classList.add('active');
            
            const category = this.dataset.category;
            
            articles.forEach(article => {
                if (category === 'all' || article.dataset.category === category) {
                    article.style.display = 'block';
                    article.style.animation = 'slideIn 0.5s ease-out';
                } else {
                    article.style.display = 'none';
                }
            });
            
            searchInput.value = '';
            performSearch();
        });
    });
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .article-item {
            animation: fadeIn 0.5s ease-in;
        }
    `;
    document.head.appendChild(style);
});

// Function to clear all filters
function clearFilters() {
    // Reset category filter to "Все"
    const allFilter = document.querySelector('.category-filter[data-category="all"]');
    if (allFilter) {
        document.querySelectorAll('.category-filter').forEach(f => f.classList.remove('active'));
        allFilter.classList.add('active');
    }
    
    // Clear search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Show all articles
    const articles = document.querySelectorAll('.article-item');
    articles.forEach(article => {
        article.style.display = 'block';
        article.style.animation = 'fadeIn 0.5s ease-in';
    });
    
    // Hide empty state
    const emptyState = document.querySelector('.empty-state');
    if (emptyState) {
        emptyState.style.display = 'none';
    }
}
</script>
@endpush
