@extends('admin.layouts.app')

@section('title', 'Настройки системы - Админ')
@section('page-title', 'Настройки системы')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Основные настройки</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Название сайта</label>
                        <input type="text" class="form-control" id="site_name" value="CRM Studio">
                    </div>
                    <div class="mb-3">
                        <label for="site_description" class="form-label">Описание сайта</label>
                        <textarea class="form-control" id="site_description" rows="3">Система управления клиентами и записями</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Email администратора</label>
                        <input type="email" class="form-control" id="admin_email" value="admin@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="timezone" class="form-label">Часовой пояс</label>
                        <select class="form-select" id="timezone">
                            <option value="Europe/Moscow" selected>Москва (UTC+3)</option>
                            <option value="Europe/London">Лондон (UTC+0)</option>
                            <option value="America/New_York">Нью-Йорк (UTC-5)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Системная информация</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Версия PHP:</strong> {{ phpversion() }}
                </div>
                <div class="mb-3">
                    <strong>Версия Laravel:</strong> {{ app()->version() }}
                </div>
                <div class="mb-3">
                    <strong>База данных:</strong> {{ config('database.default') }}
                </div>
                <div class="mb-3">
                    <strong>Режим:</strong> 
                    <span class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                        {{ app()->environment() }}
                    </span>
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
