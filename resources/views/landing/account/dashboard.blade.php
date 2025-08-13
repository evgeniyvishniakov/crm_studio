@extends('landing.layouts.app')

@section('title', 'Личный кабинет')

@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-1">Личный кабинет</h1>
                    <p class="text-muted mb-0">Добро пожаловать, {{ $project->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('landing.account.profile') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user me-2"></i>Профиль
                    </a>
                    <form method="POST" action="{{ route('landing.account.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Выйти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Информация о проекте -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>Информация о проекте
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Название:</strong>
                        <p class="mb-0">{{ $project->name }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <p class="mb-0">{{ $project->email }}</p>
                    </div>
                    @if($project->phone)
                    <div class="mb-3">
                        <strong>Телефон:</strong>
                        <p class="mb-0">{{ $project->phone }}</p>
                    </div>
                    @endif
                    @if($project->website)
                    <div class="mb-3">
                        <strong>Сайт:</strong>
                        <p class="mb-0">
                            <a href="{{ $project->website }}" target="_blank">{{ $project->website }}</a>
                        </p>
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Дата регистрации:</strong>
                        <p class="mb-0">{{ $project->registered_at ? $project->registered_at->format('d.m.Y') : 'Не указано' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Статистика
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <h4 class="mb-1">{{ $project->clients->count() }}</h4>
                            <small class="text-muted">Клиентов</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="fas fa-calendar-check text-success"></i>
                            </div>
                            <h4 class="mb-1">{{ $project->appointments->count() }}</h4>
                            <small class="text-muted">Записей</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Подписка -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Подписка
                    </h5>
                </div>
                <div class="card-body">
                    @if($activeSubscription)
                        <div class="mb-3">
                            <strong>Статус:</strong>
                            @if($activeSubscription->status === 'trial')
                                <span class="badge bg-warning">Пробный период</span>
                            @elseif($activeSubscription->status === 'active')
                                <span class="badge bg-success">Активная</span>
                            @else
                                <span class="badge bg-secondary">{{ $activeSubscription->status }}</span>
                            @endif
                        </div>
                        
                        @if($activeSubscription->status === 'trial' && $activeSubscription->trial_ends_at)
                            <div class="mb-3">
                                <strong>Пробный период до:</strong>
                                <p class="mb-0">{{ $activeSubscription->trial_ends_at->format('d.m.Y') }}</p>
                                @php
                                    $daysLeft = $activeSubscription->getDaysUntilTrialEnd();
                                @endphp
                                @if($daysLeft > 0)
                                    <small class="text-warning">{{ $daysLeft }} дней осталось</small>
                                @else
                                    <small class="text-danger">Пробный период истек</small>
                                @endif
                            </div>
                        @endif
                        
                        @if($activeSubscription->status === 'active' && $activeSubscription->expires_at)
                            <div class="mb-3">
                                <strong>Подписка до:</strong>
                                <p class="mb-0">{{ $activeSubscription->expires_at->format('d.m.Y') }}</p>
                                @php
                                    $daysLeft = $activeSubscription->getDaysUntilExpiration();
                                @endphp
                                @if($daysLeft > 0)
                                    <small class="text-success">{{ $daysLeft }} дней осталось</small>
                                @else
                                    <small class="text-danger">Подписка истекла</small>
                                @endif
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Подписка не найдена</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Переход в CRM -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-rocket fa-3x text-primary mb-3"></i>
                        <h3>Готовы работать с CRM Studio?</h3>
                        <p class="text-muted">Перейдите в вашу CRM систему для управления бизнесом</p>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('landing.account.crm') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-external-link-alt me-2"></i>Перейти в CRM
                        </a>
                        <a href="{{ route('beautyflow.features') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Узнать больше
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
