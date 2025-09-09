@extends('landing.layouts.app')

@section('title', __('landing.knowledge_page_title') . ' - Trimora')
@section('description', __('landing.knowledge_page_description'))

@push('styles')
<style>
    /* Красивые стили для списка статей с теми же шрифтами что и в лендинге */
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
    
    /* Только изменение цвета активной кнопки */
    .category-filter.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-color: transparent !important;
        color: white !important;
    }
    
    
    .hero-section {
        color: #2c3e50;
    }
    
    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .hero-section .lead {
        font-size: 20px;
        color: #6c757d;
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
@endpush

@section('content')
<!-- Hero -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">{{ __('landing.knowledge_page_title') }}</h1>
                <p class="lead">{{ __('landing.knowledge_page_description') }}</p>
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
                    <input type="text" class="form-control" id="searchInput" placeholder="{{ __('landing.knowledge_search_placeholder') }}" aria-label="{{ __('landing.knowledge_search_placeholder') }}">
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
                <button class="btn btn-outline-primary category-filter active" data-category="all">Все</button>
            </div>
                        <div class="col-auto">
                <button class="btn btn-outline-primary category-filter" data-category="getting-started">Начало работы</button>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary category-filter" data-category="features">Функции</button>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary category-filter" data-category="tips">Советы</button>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary category-filter" data-category="troubleshooting">Решение проблем</button>
            </div>
        </div>
    </div>
</section>

<!-- Articles -->
<section class="py-5">
    <div class="container">

        
        <div class="row g-4" id="articlesContainer">
            @forelse($articles as $article)
                <div class="col-lg-4 col-md-6 article-item" data-category="{{ $article->category }}">
                    <div class="card h-100">
                        @if($article->featured_image)
                            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                 class="card-img-top" 
                                 alt="{{ $article->title }}"
                                 style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary me-2">{{ $categories[$article->category] ?? $article->category }}</span>
                            </div>
                            <h5 class="card-title" style="font-size: 23px!important; font-weight: bold!important; font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;">
                                {{ $article->title }}
                            </h5>
                            <p class="card-text">
                                {!! Str::limit(strip_tags($article->description), 120) !!}
                            </p>
                            
                            @if($article->steps->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-list-ol me-1"></i>
                                        {{ $article->steps->count() }} {{ trans_choice(__('landing.knowledge_steps_count'), $article->steps->count()) }}
                                    </small>
                                </div>
                            @endif
                            
                            @if($article->tips->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        {{ $article->tips->count() }} {{ trans_choice(__('landing.knowledge_tips_count'), $article->tips->count()) }}
                                    </small>
                                </div>
                            @endif
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user text-muted me-2"></i>
                                <small class="text-muted">{{ $article->author }}</small>
                            </div>
                            
                            <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.knowledge.show', ['slug' => $article->slug]) }}" class="btn btn-primary">{{ __('landing.knowledge_read_article') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('landing.knowledge_no_articles') }}</h4>
                    <p class="text-muted">{{ __('landing.knowledge_no_articles_text') }}</p>
                </div>
            @endforelse
        </div>
        
                <!-- Empty State -->
        <div class="empty-state text-center py-5" style="display: none;">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('landing.knowledge_search_no_results') }}</h4>
                    <p class="text-muted">{{ __('landing.knowledge_search_no_results_text') }}</p>
                    <button class="btn btn-outline-primary" onclick="clearFilters()">
                        <i class="fas fa-times me-2"></i>
                        {{ __('landing.knowledge_clear_filters') }}
                    </button>
                </div>
    </div>
</section>

<!-- Contact Support Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="fw-bold mb-3">{{ __('landing.knowledge_contact_support_title') }}</h3>
                <p class="text-muted mb-4">{{ __('landing.knowledge_contact_support_text') }}</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.contact') }}" class="btn btn-primary">{{ __('landing.knowledge_contact_support') }}</a>
                    <a href="#" class="btn btn-outline-primary">{{ __('landing.knowledge_ask_chat') }}</a>
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
            const description = article.querySelector('.card-text').textContent.toLowerCase();
            const category = article.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
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
