@extends('landing.layouts.app')

@section('title', 'База знаний - Trimora')
@section('description', 'Полезные статьи, руководства и советы по использованию Trimora для салонов красоты')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">База знаний</h1>
                <p class="lead text-muted">Полезные статьи, руководства и советы по эффективному использованию Trimora</p>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="py-4">
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
                    <div class="card h-100 border-0 shadow-sm">
                        @if($article->featured_image)
                            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                 class="card-img-top" 
                                 alt="{{ $article->title }}"
                                 style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary me-2">{{ $categories[$article->category] ?? $article->category }}</span>
                            </div>
                            <h5 class="card-title fw-bold">{{ $article->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($article->description, 120) }}</p>
                            
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
                            
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-muted me-2"></i>
                                <small class="text-muted">{{ $article->author }}</small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4 pt-0">
                            <a href="{{ route('beautyflow.knowledge.show', $article->slug) }}" class="btn btn-outline-primary btn-sm">Читать статью</a>
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

@push('styles')
<style>
/* Дополнительные стили для страницы */
.category-filter.active {
    background: var(--gradient-primary);
    color: white;
    box-shadow: 0 8px 25px rgba(0, 194, 146, 0.3);
    border-color: transparent;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const categoryFilters = document.querySelectorAll('.category-filter');
    const articles = document.querySelectorAll('.article-item');

    // Search functionality
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        articles.forEach(article => {
            const title = article.querySelector('.card-title').textContent.toLowerCase();
            const description = article.querySelector('.card-text').textContent.toLowerCase();
            const category = article.getAttribute('data-category');
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = article.classList.contains('active-category') || !document.querySelector('.category-filter.active').dataset.category !== 'all';
            
            if (matchesSearch && (matchesCategory || document.querySelector('.category-filter.active').dataset.category === 'all')) {
                article.style.display = 'block';
            } else {
                article.style.display = 'none';
            }
        });
    }

    // Search button click
    searchBtn.addEventListener('click', performSearch);
    
    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
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
                } else {
                    article.style.display = 'none';
                }
            });
            
            // Clear search when changing category
            searchInput.value = '';
        });
    });
});
</script>
@endpush
