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
            <!-- Getting Started Articles -->
            <div class="col-lg-4 col-md-6 article-item" data-category="getting-started">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">Начало работы</span>
                        </div>
                        <h5 class="card-title fw-bold">Настройка ролей и доступов</h5>
                        <p class="card-text text-muted">Пошаговая инструкция по настройке ролей и прав доступа для сотрудников вашего салона.</p>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-muted me-2"></i>
                            <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="{{ route('beautyflow.knowledge.roles') }}" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="getting-started">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">Начало работы</span>
                        </div>
                                                 <h5 class="card-title fw-bold">Первые шаги в Trimora</h5>
                         <p class="card-text text-muted">Пошаговое руководство по настройке и первому запуску системы для вашего салона красоты.</p>
                         <div class="d-flex align-items-center">
                             <i class="fas fa-user text-muted me-2"></i>
                             <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="getting-started">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">Начало работы</span>
                        </div>
                        <h5 class="card-title fw-bold">Настройка профиля салона</h5>
                        <p class="card-text text-muted">Как правильно настроить профиль вашего салона, добавить услуги и настроить расписание работы.</p>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-muted me-2"></i>
                            <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="getting-started">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">Начало работы</span>
                        </div>
                        <h5 class="card-title fw-bold">Добавление сотрудников</h5>
                        <p class="card-text text-muted">Пошаговая инструкция по добавлению мастеров и настройке их профилей в системе.</p>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-muted me-2"></i>
                            <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="getting-started">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">Начало работы</span>
                        </div>
                        <h5 class="card-title fw-bold">Создание услуг и цен</h5>
                        <p class="card-text text-muted">Как настроить каталог услуг, установить цены и создать пакетные предложения.</p>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-muted me-2"></i>
                            <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="getting-started">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">Начало работы</span>
                        </div>
                        <h5 class="card-title fw-bold">Настройка рабочего расписания</h5>
                        <p class="card-text text-muted">Как настроить график работы салона, выходные дни и время перерывов.</p>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-muted me-2"></i>
                            <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="getting-started">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">Начало работы</span>
                        </div>
                        <h5 class="card-title fw-bold">Первая запись клиента</h5>
                        <p class="card-text text-muted">Пошаговое руководство по созданию первой записи клиента в системе.</p>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-muted me-2"></i>
                            <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <!-- Features Articles -->
            <div class="col-lg-4 col-md-6 article-item" data-category="features">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-success me-2">Функции</span>
                        </div>
                                                 <h5 class="card-title fw-bold">Управление записями клиентов</h5>
                         <p class="card-text text-muted">Подробный обзор функций для работы с клиентами: создание, редактирование, история посещений.</p>
                         <div class="d-flex align-items-center">
                             <i class="fas fa-user text-muted me-2"></i>
                             <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="features">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-success me-2">Функции</span>
                        </div>
                        <h5 class="card-title fw-bold">Система онлайн-бронирования</h5>
                        <p class="card-text text-muted">Как настроить и использовать систему онлайн-записи для ваших клиентов.</p>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-muted me-2"></i>
                            <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <!-- Tips Articles -->
            <div class="col-lg-4 col-md-6 article-item" data-category="tips">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-info me-2">Советы</span>
                        </div>
                                                 <h5 class="card-title fw-bold">5 способов увеличить продажи</h5>
                         <p class="card-text text-muted">Практические советы по использованию Trimora для роста вашего бизнеса.</p>
                         <div class="d-flex align-items-center">
                             <i class="fas fa-user text-muted me-2"></i>
                             <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="tips">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-info me-2">Советы</span>
                        </div>
                                                 <h5 class="card-title fw-bold">Оптимизация рабочего процесса</h5>
                         <p class="card-text text-muted">Как организовать эффективную работу персонала с помощью Trimora.</p>
                         <div class="d-flex align-items-center">
                             <i class="fas fa-user text-muted me-2"></i>
                             <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting Articles -->
            <div class="col-lg-4 col-md-6 article-item" data-category="troubleshooting">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-warning me-2">Решение проблем</span>
                        </div>
                                                 <h5 class="card-title fw-bold">Частые вопросы и ответы</h5>
                         <p class="card-text text-muted">Ответы на самые популярные вопросы пользователей Trimora.</p>
                         <div class="d-flex align-items-center">
                             <i class="fas fa-user text-muted me-2"></i>
                             <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 article-item" data-category="troubleshooting">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-warning me-2">Решение проблем</span>
                        </div>
                                                 <h5 class="card-title fw-bold">Восстановление данных</h5>
                         <p class="card-text text-muted">Пошаговая инструкция по восстановлению данных и настройке резервного копирования.</p>
                         <div class="d-flex align-items-center">
                             <i class="fas fa-user text-muted me-2"></i>
                             <small class="text-muted">Команда Trimora</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">Читать статью</a>
                    </div>
                </div>
            </div>
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
