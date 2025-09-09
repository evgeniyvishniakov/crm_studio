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
      "image": "{{ $article->localized_featured_image ? Storage::url($article->localized_featured_image) : asset('images/og-default.jpg') }}",
      "articleSection": "{{ $article->category ? $article->category->localized_name : __('landing.blog') }}"
    }{{ $index < $articles->count() - 1 ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush

@push('styles')
<style>
    /* Современная пагинация */
    .pagination-modern {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin: 40px 0;
        font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .pagination-modern .page-item {
        list-style: none;
    }
    
    .pagination-modern .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        color: #6c757d;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
        position: relative;
        overflow: hidden;
    }
    
    .pagination-modern .page-link:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    
    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .pagination-modern .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .pagination-modern .page-link:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .pagination-modern .page-link:hover:before {
        left: 100%;
    }
    
    .pagination-modern .page-link i {
        font-size: 12px;
    }
    
    .pagination-modern .page-item:first-child .page-link,
    .pagination-modern .page-item:last-child .page-link {
        width: auto;
        padding: 0 16px;
        min-width: 44px;
    }
    
    .pagination-modern .page-item.ellipsis .page-link {
        border: none;
        background: transparent;
        cursor: default;
        pointer-events: none;
    }
    
    .pagination-modern .page-item.ellipsis .page-link:hover {
        transform: none;
        box-shadow: none;
    }
    
    .pagination-modern .page-item.ellipsis .page-link:before {
        display: none;
    }
    
    /* Анимация появления */
    .pagination-modern {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Адаптивность для пагинации */
    @media (max-width: 768px) {
        .pagination-modern {
            gap: 4px;
            margin: 30px 0;
        }
        
        .pagination-modern .page-link {
            width: 40px;
            height: 40px;
            font-size: 13px;
        }
        
        .pagination-modern .page-item:first-child .page-link,
        .pagination-modern .page-item:last-child .page-link {
            padding: 0 12px;
            min-width: 40px;
        }
    }
    
    @media (max-width: 480px) {
        .pagination-modern .page-link {
            width: 36px;
            height: 36px;
            font-size: 12px;
        }
        
        .pagination-modern .page-item:first-child .page-link,
        .pagination-modern .page-item:last-child .page-link {
            padding: 0 10px;
            min-width: 36px;
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
                        @if($article->localized_featured_image)
                            <img src="{{ Storage::url($article->localized_featured_image) }}" 
                                 class="card-img-top" 
                                 alt="{{ $article->localized_title }}"
                                 style="height: 280px; object-fit: cover;">
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
        
        <!-- Modern Pagination -->
        @if($needsPagination)
        <nav aria-label="Pagination">
            <ul class="pagination-modern" id="paginationContainer">
                <!-- Пагинация будет генерироваться JavaScript -->
            </ul>
        </nav>
        @endif
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
    const paginationContainer = document.getElementById('paginationContainer');
    
    // Переменные для пагинации
    let currentPage = 1;
    const articlesPerPage = 9;
    let filteredArticles = Array.from(articles);

    // Функция для создания пагинации
    function createPagination(totalPages) {
        if (!paginationContainer || totalPages <= 1) {
            if (paginationContainer) paginationContainer.style.display = 'none';
            return;
        }
        
        paginationContainer.innerHTML = '';
        paginationContainer.style.display = 'flex';
        
        // Кнопка "Предыдущая"
        const prevBtn = document.createElement('li');
        prevBtn.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevBtn.innerHTML = `<span class="page-link"><i class="fas fa-chevron-left"></i></span>`;
        if (currentPage > 1) {
            prevBtn.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></a>`;
        }
        paginationContainer.appendChild(prevBtn);
        
        // Номера страниц
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('li');
            pageBtn.className = `page-item ${i === currentPage ? 'active' : ''}`;
            if (i === currentPage) {
                pageBtn.innerHTML = `<span class="page-link">${i}</span>`;
            } else {
                pageBtn.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>`;
            }
            paginationContainer.appendChild(pageBtn);
        }
        
        // Кнопка "Следующая"
        const nextBtn = document.createElement('li');
        nextBtn.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextBtn.innerHTML = `<span class="page-link"><i class="fas fa-chevron-right"></i></span>`;
        if (currentPage < totalPages) {
            nextBtn.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></a>`;
        }
        paginationContainer.appendChild(nextBtn);
    }
    
    // Функция для перехода на страницу
    window.goToPage = function(page) {
        currentPage = page;
        displayArticles();
    }
    
    // Функция для отображения статей
    function displayArticles() {
        const startIndex = (currentPage - 1) * articlesPerPage;
        const endIndex = startIndex + articlesPerPage;
        const articlesToShow = filteredArticles.slice(startIndex, endIndex);
        
        // Скрываем все статьи
        articles.forEach(article => {
            article.style.display = 'none';
        });
        
        // Показываем нужные статьи
        articlesToShow.forEach(article => {
            article.style.display = 'block';
            article.style.animation = 'fadeIn 0.5s ease-in';
        });
        
        // Создаем пагинацию
        const totalPages = Math.ceil(filteredArticles.length / articlesPerPage);
        createPagination(totalPages);
        
        // Показываем/скрываем empty state
        if (emptyState) {
            if (filteredArticles.length === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
            }
        }
    }
    
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const activeCategory = document.querySelector('.category-filter.active').dataset.category;
        
        filteredArticles = Array.from(articles).filter(article => {
            const title = article.querySelector('.card-title').textContent.toLowerCase();
            const content = article.dataset.content;
            const category = article.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || content.includes(searchTerm);
            const matchesCategory = activeCategory === 'all' || category === activeCategory;
            
            return matchesSearch && matchesCategory;
        });
        
        currentPage = 1;
        displayArticles();
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
            
            searchInput.value = '';
            performSearch();
        });
    });
    
    // Инициализация при загрузке страницы
    displayArticles();
    
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
    
    // Reset to show all articles
    filteredArticles = Array.from(document.querySelectorAll('.article-item'));
    currentPage = 1;
    displayArticles();
}
</script>
@endpush
