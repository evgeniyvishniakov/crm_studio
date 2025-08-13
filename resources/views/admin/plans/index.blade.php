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
                <div class="card shadow h-100 {{ $plan->is_active ? 'border-success' : 'border-secondary' }}">
                    <div class="card-header d-flex justify-content-between align-items-center {{ $plan->is_active ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-tag me-2"></i>
                            {{ $plan->name }}
                        </h5>
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
                    
                    <div class="card-body">
                        @if($plan->description)
                            <p class="card-text text-muted mb-3">{{ $plan->description }}</p>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Макс. сотрудников:</small>
                                <div class="fw-bold">
                                    @if($plan->max_employees)
                                        <span class="badge bg-info">{{ $plan->max_employees }}</span>
                                    @else
                                        <span class="badge bg-success">Без лимита</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Порядок:</small>
                                <div class="fw-bold">{{ $plan->sort_order }}</div>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <div class="h3 text-primary fw-bold">{{ number_format($plan->price_monthly, 0, ',', ' ') }}₴</div>
                            <small class="text-muted">за месяц</small>
                        </div>

                        <!-- Цены по периодам -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">3 месяца</small>
                                    <div class="fw-bold text-success">{{ number_format($plan->getPriceForPeriod('quarterly'), 0, ',', ' ') }}₴</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">Год</small>
                                    <div class="fw-bold text-warning">{{ number_format($plan->getPriceForPeriod('yearly'), 0, ',', ' ') }}₴</div>
                                </div>
                            </div>
                        </div>


                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $plan->is_active ? 'Активен' : 'Неактивен' }}
                            </span>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
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
