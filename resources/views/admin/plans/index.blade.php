@extends('admin.layouts.app')

@section('title', 'Управление тарифами')

@section('content')
<div class="container-fluid">
    <!-- Заголовок страницы -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-tag text-primary me-2"></i>
                        Управление тарифами
                    </h1>
                    <p class="text-muted mb-0">Создание и управление тарифными планами для клиентов</p>
                </div>
                <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Создать тариф
                </a>
            </div>
        </div>
    </div>

    <!-- Уведомления -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Всего тарифов
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $plans->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Активных тарифов
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $plans->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Средняя цена
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $plans->where('is_active', true)->avg('price_monthly') ? number_format($plans->where('is_active', true)->avg('price_monthly'), 0, ',', ' ') . '₴' : '0₴' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Популярный тариф
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $plans->where('is_active', true)->sortBy('sort_order')->first()->name ?? 'Нет' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Список тарифов -->
    <div class="row">
        @forelse($plans as $plan)
            <div class="col-xl-4 col-lg-6 col-md-12 mb-4">
                <div class="plan-card card shadow h-100 {{ $plan->is_active ? 'border-success' : 'border-secondary' }}">
                    <!-- Заголовок карточки -->
                    <div class="plan-header card-header d-flex justify-content-between align-items-center {{ $plan->is_active ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                        <div class="d-flex align-items-center">
                            <div class="plan-icon me-3">
                                <i class="fas fa-tag fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $plan->name }}</h5>
                                <small class="opacity-75">
                                    @if($plan->max_employees)
                                        До {{ $plan->max_employees }} сотрудников
                                    @else
                                        Безлимитный тариф
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.plans.show', $plan) }}">
                                    <i class="fas fa-eye me-2"></i>Просмотр
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.plans.edit', $plan) }}">
                                    <i class="fas fa-edit me-2"></i>Редактировать
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" 
                                          onsubmit="return confirm('Вы уверены, что хотите удалить этот тариф?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2"></i>Удалить
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="plan-content card-body">
                        <!-- Описание -->
                        @if($plan->description)
                            <div class="plan-description mb-4">
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle me-2 text-info"></i>
                                    {{ $plan->description }}
                                </p>
                            </div>
                        @endif
                        
                        <!-- Основная цена -->
                        <div class="main-price text-center mb-4">
                            <div class="price-amount h2 text-primary fw-bold mb-1">
                                {{ number_format($plan->price_monthly, 0, ',', ' ') }}₴
                            </div>
                            <div class="price-period text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                за месяц
                            </div>
                        </div>

                        <!-- Цены по периодам с экономией -->
                        <div class="pricing-periods mb-4">
                            <h6 class="text-muted mb-3 text-center">
                                <i class="fas fa-percentage me-2"></i>
                                Цены по периодам
                            </h6>
                            
                            <div class="row g-3">
                                <!-- 3 месяца -->
                                <div class="col-6">
                                    <div class="period-card text-center p-3 bg-light rounded h-100">
                                        <div class="period-badge mb-2">
                                            <span class="badge bg-success">3 мес</span>
                                        </div>
                                        <div class="period-price h5 text-success fw-bold mb-1">
                                            {{ number_format($plan->getPriceForPeriod('quarterly'), 0, ',', ' ') }}₴
                                        </div>
                                        <div class="period-savings small text-muted">
                                            Экономия: {{ number_format($plan->price_monthly * 3 - $plan->getPriceForPeriod('quarterly'), 0, ',', ' ') }}₴
                                        </div>
                                        <div class="monthly-equivalent small text-muted">
                                            {{ number_format($plan->getPriceForPeriod('quarterly') / 3, 0, ',', ' ') }}₴/мес
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 6 месяцев -->
                                <div class="col-6">
                                    <div class="period-card text-center p-3 bg-light rounded h-100">
                                        <div class="period-badge mb-2">
                                            <span class="badge bg-info">6 мес</span>
                                        </div>
                                        <div class="period-price h5 text-info fw-bold mb-1">
                                            {{ number_format($plan->getPriceForPeriod('semiannual'), 0, ',', ' ') }}₴
                                        </div>
                                        <div class="period-savings small text-muted">
                                            Экономия: {{ number_format($plan->price_monthly * 6 - $plan->getPriceForPeriod('semiannual'), 0, ',', ' ') }}₴
                                        </div>
                                        <div class="monthly-equivalent small text-muted">
                                            {{ number_format($plan->getPriceForPeriod('semiannual') / 6, 0, ',', ' ') }}₴/мес
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Год -->
                                <div class="col-12">
                                    <div class="period-card text-center p-3 bg-warning bg-opacity-10 rounded">
                                        <div class="period-badge mb-2">
                                            <span class="badge bg-warning text-dark">Год</span>
                                        </div>
                                        <div class="period-price h4 text-warning fw-bold mb-1">
                                            {{ number_format($plan->getPriceForPeriod('yearly'), 0, ',', ' ') }}₴
                                        </div>
                                        <div class="period-savings small text-muted">
                                            Экономия: {{ number_format($plan->price_monthly * 12 - $plan->getPriceForPeriod('yearly'), 0, ',', ' ') }}₴
                                        </div>
                                        <div class="monthly-equivalent small text-muted">
                                            {{ number_format($plan->getPriceForPeriod('yearly') / 12, 0, ',', ' ') }}₴/мес
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Дополнительная информация -->
                        <div class="plan-info mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="info-item text-center p-2 bg-light rounded">
                                        <div class="info-icon mb-1">
                                            <i class="fas fa-users text-primary"></i>
                                        </div>
                                        <div class="info-label small text-muted">Сотрудники</div>
                                        <div class="info-value fw-bold">
                                            @if($plan->max_employees)
                                                {{ $plan->max_employees }}
                                            @else
                                                <span class="text-success">∞</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-item text-center p-2 bg-light rounded">
                                        <div class="info-icon mb-1">
                                            <i class="fas fa-sort-numeric-up text-info"></i>
                                        </div>
                                        <div class="info-label small text-muted">Порядок</div>
                                        <div class="info-value fw-bold">{{ $plan->sort_order }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Футер карточки -->
                    <div class="plan-footer card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="plan-status">
                                <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                                    <i class="fas {{ $plan->is_active ? 'fa-check-circle' : 'fa-pause-circle' }} me-1"></i>
                                    {{ $plan->is_active ? 'Активен' : 'Неактивен' }}
                                </span>
                            </div>
                            <div class="plan-actions">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.plans.show', $plan) }}" 
                                       class="btn btn-outline-info btn-sm" 
                                       title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.plans.edit', $plan) }}" 
                                       class="btn btn-outline-warning btn-sm" 
                                       title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Тарифы не найдены</h4>
                    <p class="text-muted">Создайте первый тариф для ваших клиентов</p>
                    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Создать тариф
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.text-gray-800 { color: #5a5c69 !important; }
.text-gray-300 { color: #dddfeb !important; }

/* Стили для карточек тарифов */
.plan-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.plan-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    padding: 1.5rem;
}

.plan-header.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
}

.plan-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.plan-content {
    padding: 1.5rem;
}

.plan-description {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid #17a2b8;
}

.main-price {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    padding: 1.5rem;
    border-radius: 15px;
    border: 2px solid #e3f2fd;
}

.price-amount {
    font-size: 2.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pricing-periods h6 {
    font-weight: 600;
    color: #495057;
}

.period-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.period-card:hover {
    transform: translateY(-2px);
    border-color: #dee2e6;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.period-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #28a745, #20c997);
}

.period-card:nth-child(2)::before {
    background: linear-gradient(90deg, #17a2b8, #6f42c1);
}

.period-card:nth-child(3)::before {
    background: linear-gradient(90deg, #ffc107, #fd7e14);
}

.period-badge .badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.8rem;
    border-radius: 20px;
}

.period-price {
    margin-bottom: 0.5rem;
}

.period-savings {
    color: #28a745 !important;
    font-weight: 600;
}

.monthly-equivalent {
    color: #6c757d !important;
    font-weight: 500;
}

.plan-info {
    margin-top: 1.5rem;
}

.info-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.info-item:hover {
    background: #f8f9fa !important;
    border-color: #dee2e6;
    transform: translateY(-1px);
}

.info-icon {
    font-size: 1.2rem;
}

.info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.1rem;
}

.plan-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
}

.plan-status .badge {
    padding: 0.6rem 1rem;
    border-radius: 25px;
    font-weight: 500;
}

.plan-actions .btn {
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    transition: all 0.3s ease;
}

.plan-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

/* Адаптивность */
@media (max-width: 768px) {
    .plan-header {
        padding: 1rem;
    }
    
    .plan-content {
        padding: 1rem;
    }
    
    .price-amount {
        font-size: 2rem;
    }
    
    .period-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .plan-icon {
        width: 40px;
        height: 40px;
    }
    
    .plan-icon i {
        font-size: 1.5rem !important;
    }
    
    .price-amount {
        font-size: 1.8rem;
    }
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Автоматическое скрытие уведомлений через 5 секунд
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
