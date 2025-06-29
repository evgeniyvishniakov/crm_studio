@extends('admin.layouts.app')

@section('title', 'Настройки системы - Админ')
@section('page-title', 'Настройки системы')

@section('content')
<div class="row g-4">
    <!-- Основные настройки -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Основные настройки</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Название сайта</label>
                                <input type="text" class="form-control" id="site_name" value="CRM Studio">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="site_email" class="form-label">Email сайта</label>
                                <input type="email" class="form-control" id="site_email" value="info@crmstudio.ru">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Часовой пояс</label>
                                <select class="form-select" id="timezone">
                                    <option value="Europe/Moscow" selected>Москва (UTC+3)</option>
                                    <option value="Europe/London">Лондон (UTC+0)</option>
                                    <option value="America/New_York">Нью-Йорк (UTC-5)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="language" class="form-label">Язык</label>
                                <select class="form-select" id="language">
                                    <option value="ru" selected>Русский</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Описание сайта</label>
                        <textarea class="form-control" id="description" rows="3">CRM Studio - система управления салоном красоты</textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Дополнительные настройки -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Уведомления</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                    <label class="form-check-label" for="email_notifications">
                        Email уведомления
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="sms_notifications">
                    <label class="form-check-label" for="sms_notifications">
                        SMS уведомления
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="push_notifications" checked>
                    <label class="form-check-label" for="push_notifications">
                        Push уведомления
                    </label>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Безопасность</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="two_factor" checked>
                    <label class="form-check-label" for="two_factor">
                        Двухфакторная аутентификация
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="session_timeout" checked>
                    <label class="form-check-label" for="session_timeout">
                        Автоматический выход
                    </label>
                </div>
                <div class="mb-3">
                    <label for="session_duration" class="form-label">Время сессии (минуты)</label>
                    <input type="number" class="form-control" id="session_duration" value="120">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Резервное копирование -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Резервное копирование</h5>
                <button class="btn btn-success">
                    <i class="fas fa-download me-2"></i>Создать резервную копию
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Файл</th>
                                <th>Размер</th>
                                <th>Дата создания</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>backup_2024_01_15.sql</td>
                                <td>2.5 MB</td>
                                <td>15.01.2024 14:30</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>backup_2024_01_14.sql</td>
                                <td>2.3 MB</td>
                                <td>14.01.2024 14:30</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
