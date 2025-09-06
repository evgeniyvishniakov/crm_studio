@extends('admin.layouts.app')

@section('title', 'Панель управления - Админ')
@section('page-title', 'Панель управления')

@section('content')
<div class="row g-4">
    <!-- Основная статистика -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Пользователи</h6>
                        <h4 class="mb-0">{{ $totalUsers }}</h4>
                        <small class="text-success">+{{ $newUsersThisMonth }} за месяц</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-friends fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Клиенты</h6>
                        <h4 class="mb-0">{{ $totalClients }}</h4>
                        <small class="text-success">+{{ $newClientsThisMonth }} за месяц</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Проекты</h6>
                        <h4 class="mb-0">{{ $totalProjects }}</h4>
                        <small class="text-info">{{ $activeProjects }} активных</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-bell fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Уведомления</h6>
                        <h4 class="mb-0">{{ $unreadNotifications }}</h4>
                        <small class="text-warning">Непрочитанных</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Дополнительная статистика -->
<div class="row g-4 mt-2">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-ticket-alt fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Тикеты поддержки</h6>
                        <h4 class="mb-0">{{ $totalTickets }}</h4>
                        <small class="text-danger">{{ $openTickets }} открытых</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Рост за месяц</h6>
                        <h4 class="mb-0">{{ $newUsersThisMonth + $newClientsThisMonth }}</h4>
                        <small class="text-info">Новых пользователей</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-percentage fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Активность</h6>
                        <h4 class="mb-0">{{ $totalTickets > 0 ? round((($totalTickets - $openTickets) / $totalTickets) * 100) : 100 }}%</h4>
                        <small class="text-success">Тикетов решено</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-bell fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Уведомления</h6>
                        <h4 class="mb-0">{{ $unreadNotifications }}</h4>
                        <small class="text-warning">Непрочитанных</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Статистика по месяцам -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Статистика за последние 6 месяцев</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Месяц</th>
                                <th class="text-center">Пользователи</th>
                                <th class="text-center">Клиенты</th>
                                <th class="text-center">Проекты</th>
                                <th class="text-center">Тикеты</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyStats as $stat)
                                <tr>
                                    <td><strong>{{ $stat['month'] }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $stat['users'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $stat['clients'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $stat['projects'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $stat['tickets'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Активные тикеты поддержки -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Активные тикеты поддержки</h5>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-primary">Все тикеты</a>
            </div>
            <div class="card-body">
                @if($openTickets > 0 || $pendingTickets > 0)
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h3 class="text-danger mb-1">{{ $openTickets }}</h3>
                                <small class="text-muted">Открытых</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 class="text-warning mb-1">{{ $pendingTickets }}</h3>
                            <small class="text-muted">В ожидании</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ $totalTickets > 0 ? ($openTickets / $totalTickets) * 100 : 0 }}%"></div>
                            <div class="progress-bar bg-warning" style="width: {{ $totalTickets > 0 ? ($pendingTickets / $totalTickets) * 100 : 0 }}%"></div>
                        </div>
                        <small class="text-muted">Всего тикетов: {{ $totalTickets }}</small>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="text-muted mb-0">Нет активных тикетов поддержки</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Быстрые действия -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Быстрые действия</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-building me-2"></i>Управление проектами
                    </a>
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-success">
                        <i class="fas fa-blog me-2"></i>Управление блогом
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-users me-2"></i>Управление пользователями
                    </a>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-warning">
                        <i class="fas fa-ticket-alt me-2"></i>Тикеты поддержки
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cog me-2"></i>Настройки системы
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Системная информация -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Системная информация</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Версия PHP:</strong></td>
                                <td>{{ phpversion() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Версия Laravel:</strong></td>
                                <td>{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <td><strong>ОС сервера:</strong></td>
                                <td>{{ php_uname('s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Время сервера:</strong></td>
                                <td>{{ now()->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>База данных:</strong></td>
                                <td>{{ config('database.default') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Кэш:</strong></td>
                                <td>{{ config('cache.default') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Очереди:</strong></td>
                                <td>{{ config('queue.default') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Режим:</strong></td>
                                <td>
                                    <span class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                        {{ app()->environment() }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Общая статистика системы -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Общая статистика системы</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-database fa-2x text-primary mb-2"></i>
                            <h4 class="mb-1">{{ $totalUsers + $totalClients }}</h4>
                            <small class="text-muted">Всего пользователей</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-building fa-2x text-info mb-2"></i>
                            <h4 class="mb-1">{{ $totalProjects }}</h4>
                            <small class="text-muted">Проектов в системе</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-bell fa-2x text-warning mb-2"></i>
                            <h4 class="mb-1">{{ $unreadNotifications }}</h4>
                            <small class="text-muted">Непрочитанных уведомлений</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-ticket-alt fa-2x text-danger mb-2"></i>
                            <h4 class="mb-1">{{ $totalTickets }}</h4>
                            <small class="text-muted">Тикетов поддержки</small>
                        </div>
                    </div>
                </div>
                
                <!-- Дополнительная информация -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Статус системы</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Активные проекты:</span>
                            <span class="badge bg-success">{{ $activeProjects }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>В ожидании тикетов:</span>
                            <span class="badge bg-warning">{{ $pendingTickets }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Открытые тикеты:</span>
                            <span class="badge bg-danger">{{ $openTickets }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Рост за месяц</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Новые пользователи:</span>
                            <span class="badge bg-success">+{{ $newUsersThisMonth }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Новые клиенты:</span>
                            <span class="badge bg-success">+{{ $newClientsThisMonth }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Непрочитанные уведомления:</span>
                            <span class="badge bg-warning">{{ $unreadNotifications }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
