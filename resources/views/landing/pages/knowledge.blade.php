@extends('landing.layouts.app')

@section('title', 'База знаний - Trimora')
@section('description', 'Полезные статьи, руководства и советы по использованию Trimora для салонов красоты')

@section('styles')
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
<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">База знаний</h1>
                <p class="lead">Полезные статьи, руководства и советы по эффективному использованию Trimora</p>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Поиск по статьям..." aria-label="Поиск по статьям">
                    <button class="btn btn-primary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
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

<!-- Articles Section -->
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
                            <h5 class="card-title" style="font-size: 23px!important; font-weight: bold!important; font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;">{{ $article->title }}</h5>
                            <p class="card-text">{{ Str::limit($article->description, 120) }}</p>
                            
                            @if($article->steps->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-list-ol me-1"></i>
                                        {{ $article->steps->count() }} {{ trans_choice('шаг|шага|шагов', $article->steps->count()) }}
                                    </small>
                                </div>
                            @endif
                            
                            @if($article->tips->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        {{ $article->tips->count() }} {{ trans_choice('совет|совета|советов', $article->tips->count()) }}
                                    </small>
                                </div>
                            @endif
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user text-muted me-2"></i>
                                <small class="text-muted">{{ $article->author }}</small>
                            </div>
                            
                            <a href="{{ route('beautyflow.knowledge.show', $article->slug) }}" class="btn btn-primary">Читать статью</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Статьи не найдены</h4>
                    <p class="text-muted">В данный момент в базе знаний нет опубликованных статей.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Empty state for search/filter -->
        <div class="empty-state text-center py-5" style="display: none;">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">По вашему запросу ничего не найдено</h4>
            <p class="text-muted">Попробуйте изменить параметры поиска или выбрать другую категорию</p>
            <button class="btn btn-outline-primary" onclick="clearFilters()">
                <i class="fas fa-times me-2"></i>
                Сбросить фильтры
            </button>
        </div>
    </div>
</section>

<!-- Contact Support Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="fw-bold mb-3">Не нашли ответ на свой вопрос?</h3>
                                 <p class="text-muted mb-4">Наша команда поддержки готова помочь вам с любыми вопросами по использованию Trimora</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('beautyflow.contact') }}" class="btn btn-primary">Связаться с поддержкой</a>
                    <a href="#" class="btn btn-outline-primary">Задать вопрос в чате</a>
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

    // Search functionality
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
        
        // Show/hide empty state
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

    // Search button click
    searchBtn.addEventListener('click', performSearch);
    
    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Real-time search
    searchInput.addEventListener('input', function() {
        if (this.value.length > 2 || this.value.length === 0) {
            performSearch();
        }
    });

    // Category filtering
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            // Remove active class from all filters
            categoryFilters.forEach(f => f.classList.remove('active'));
            
            // Add active class to clicked filter
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
            
            // Clear search when changing category
            searchInput.value = '';
            performSearch();
        });
    });
    
    // Add CSS animations
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
