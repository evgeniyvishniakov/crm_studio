@extends('admin.layouts.app')

@section('title', 'Безопасность - Админ')
@section('page-title', 'Безопасность')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Настройки безопасности</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="two_factor" checked>
                            <label class="form-check-label" for="two_factor">
                                Двухфакторная аутентификация
                            </label>
                        </div>
                        <small class="form-text text-muted">Требовать двухфакторную аутентификацию для всех пользователей</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="session_timeout" checked>
                            <label class="form-check-label" for="session_timeout">
                                Автоматический выход из системы
                            </label>
                        </div>
                        <small class="form-text text-muted">Автоматически выходить из системы после периода неактивности</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="session_duration" class="form-label">Время сессии (минуты)</label>
                        <input type="number" class="form-control" id="session_duration" value="120" min="30" max="1440">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="password_policy" checked>
                            <label class="form-check-label" for="password_policy">
                                Политика паролей
                            </label>
                        </div>
                        <small class="form-text text-muted">Требовать сложные пароли</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="min_password_length" class="form-label">Минимальная длина пароля</label>
                        <input type="number" class="form-control" id="min_password_length" value="8" min="6" max="50">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="login_attempts" checked>
                            <label class="form-check-label" for="login_attempts">
                                Ограничение попыток входа
                            </label>
                        </div>
                        <small class="form-text text-muted">Блокировать аккаунт после неудачных попыток входа</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_login_attempts" class="form-label">Максимальное количество попыток</label>
                        <input type="number" class="form-control" id="max_login_attempts" value="5" min="3" max="10">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Активные сессии</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Администратор</strong><br>
                            <small class="text-muted">127.0.0.1 • {{ now()->format('d.m.Y H:i') }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Менеджер</strong><br>
                            <small class="text-muted">192.168.1.100 • {{ now()->subMinutes(30)->format('d.m.Y H:i') }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Последние действия</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">{{ now()->subMinutes(5)->format('d.m.Y H:i') }}</small><br>
                    <strong>Вход в систему</strong> - Администратор
                </div>
                <div class="mb-2">
                    <small class="text-muted">{{ now()->subMinutes(15)->format('d.m.Y H:i') }}</small><br>
                    <strong>Изменение настроек</strong> - Менеджер
                </div>
                <div class="mb-2">
                    <small class="text-muted">{{ now()->subMinutes(30)->format('d.m.Y H:i') }}</small><br>
                    <strong>Выход из системы</strong> - Пользователь
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 