@extends('admin.layouts.app')

@section('title', 'Логи системы - Админ')
@section('page-title', 'Логи системы')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Логи системы</h5>
        <div>
            <button class="btn btn-outline-secondary me-2">
                <i class="fas fa-download me-2"></i>Экспорт
            </button>
            <button class="btn btn-outline-danger">
                <i class="fas fa-trash me-2"></i>Очистить
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select" id="logLevel">
                    <option value="">Все уровни</option>
                    <option value="error">Ошибки</option>
                    <option value="warning">Предупреждения</option>
                    <option value="info">Информация</option>
                    <option value="debug">Отладка</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" id="logDate" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="logSearch" placeholder="Поиск в логах...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Фильтр</button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Время</th>
                        <th>Уровень</th>
                        <th>Сообщение</th>
                        <th>Пользователь</th>
                        <th>IP</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ now()->format('d.m.Y H:i:s') }}</td>
                        <td><span class="badge bg-info">INFO</span></td>
                        <td>Пользователь вошел в систему</td>
                        <td>admin@example.com</td>
                        <td>127.0.0.1</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ now()->subMinutes(5)->format('d.m.Y H:i:s') }}</td>
                        <td><span class="badge bg-warning">WARNING</span></td>
                        <td>Неудачная попытка входа</td>
                        <td>user@example.com</td>
                        <td>192.168.1.100</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ now()->subMinutes(10)->format('d.m.Y H:i:s') }}</td>
                        <td><span class="badge bg-success">SUCCESS</span></td>
                        <td>Создана новая запись</td>
                        <td>manager@example.com</td>
                        <td>192.168.1.101</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ now()->subMinutes(15)->format('d.m.Y H:i:s') }}</td>
                        <td><span class="badge bg-danger">ERROR</span></td>
                        <td>Ошибка подключения к базе данных</td>
                        <td>system</td>
                        <td>127.0.0.1</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <nav aria-label="Навигация по логам">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Предыдущая</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Следующая</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Модальное окно просмотра лога -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Детали лога</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Время:</strong> {{ now()->format('d.m.Y H:i:s') }}<br>
                        <strong>Уровень:</strong> <span class="badge bg-info">INFO</span><br>
                        <strong>Пользователь:</strong> admin@example.com<br>
                        <strong>IP адрес:</strong> 127.0.0.1
                    </div>
                    <div class="col-md-6">
                        <strong>User Agent:</strong><br>
                        <small>Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36</small>
                    </div>
                </div>
                <hr>
                <div>
                    <strong>Сообщение:</strong><br>
                    <pre class="bg-light p-3 mt-2">Пользователь успешно вошел в систему</pre>
                </div>
                <div>
                    <strong>Контекст:</strong><br>
                    <pre class="bg-light p-3 mt-2">{"user_id": 1, "session_id": "abc123"}</pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
@endsection 