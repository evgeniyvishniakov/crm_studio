@extends('admin.layouts.app')

@section('title', 'Панель управления - Админ')
@section('page-title', 'Панель управления')

@section('content')
<div class="row g-4">
    <!-- Статистика системы -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Пользователи</h6>
                        <h4 class="mb-0">{{ \App\Models\User::count() }}</h4>
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
                        <i class="fas fa-database fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Клиенты</h6>
                        <h4 class="mb-0">{{ \App\Models\Client::count() }}</h4>
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
                        <i class="fas fa-box fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Товары</h6>
                        <h4 class="mb-0">{{ \App\Models\Product::count() }}</h4>
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
                        <i class="fas fa-calendar fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Записи</h6>
                        <h4 class="mb-0">{{ \App\Models\Appointment::count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
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
    
    <!-- Быстрые действия -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Быстрые действия</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>Управление пользователями
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cog me-2"></i>Настройки системы
                    </a>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-file-alt me-2"></i>Просмотр логов
                    </a>
                    <a href="{{ route('admin.security.index') }}" class="btn btn-outline-warning">
                        <i class="fas fa-lock me-2"></i>Безопасность
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Последние действия -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Последние действия</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">Показать все</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Пользователь</th>
                                <th>Действие</th>
                                <th>Объект</th>
                                <th>Время</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Администратор</td>
                                <td>Создал пользователя</td>
                                <td>user@example.com</td>
                                <td>{{ now()->subMinutes(5)->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Менеджер</td>
                                <td>Обновил настройки</td>
                                <td>Системные настройки</td>
                                <td>{{ now()->subMinutes(15)->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Администратор</td>
                                <td>Создал резервную копию</td>
                                <td>База данных</td>
                                <td>{{ now()->subMinutes(30)->format('d.m.Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
