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
                <button class="btn btn-outline-primary category-filter" data-category="features">Работа с клиентами</button>
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
            const description = article.querySelector('.card-text').textContent.toLowerCase();
            const category = article.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
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
