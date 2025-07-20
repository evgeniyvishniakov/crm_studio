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
            <form method="GET" class="row mb-3">
                <div class="col-md-2">
                    <select class="form-select" name="project_id" onchange="this.form.submit()">
                        <option value="">Все проекты</option>
                        @foreach($projects as $id => $name)
                            <option value="{{ $id }}" {{ request('project_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="level" onchange="this.form.submit()">
                        <option value="">Все уровни</option>
                        <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Ошибки</option>
                        <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Предупреждения</option>
                        <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Информация</option>
                        <option value="debug" {{ request('level') == 'debug' ? 'selected' : '' }}>Отладка</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date" value="{{ request('date', date('Y-m-d')) }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Поиск в логах...">
                </div>
                <div class="col-md-2 mt-2 mt-md-0">
                    <button class="btn btn-primary w-100" type="submit">Фильтр</button>
                </div>
            </form>
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
                    @foreach($logs as $log)
                        <tr id="log-row-{{ $log->id }}">
                            <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            <td><span class="badge bg-{{ $log->level === 'error' ? 'danger' : ($log->level === 'warning' ? 'warning' : ($log->level === 'info' ? 'info' : 'secondary')) }}">{{ strtoupper($log->level) }}</span></td>
                            <td>{{ Str::limit($log->message, 60) }}</td>
                            <td>{{ $log->user_email ?? 'system' }}</td>
                            <td>{{ $log->ip }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" data-id="{{ $log->id }}" onclick="showLogDetail({{ $log->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($log->level === 'error' && $log->status === 'new')
                                    <button class="btn btn-sm btn-success ms-1" onclick="markLogFixed({{ $log->id }})">
                                        <i class="fas fa-check"></i> Исправлено
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <nav aria-label="Навигация по логам">
            {{ $logs->links('pagination::bootstrap-4') }}
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

<script>
function showLogDetail(id) {
    fetch(`/panel/logs/${id}`)
        .then(res => res.json())
        .then(log => {
            let modal = document.getElementById('logDetailModal');
            modal.querySelector('.modal-title').textContent = 'Детали лога';
            let body = modal.querySelector('.modal-body');
            body.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Время:</strong> ${log.created_at ? new Date(log.created_at).toLocaleString() : ''}<br>
                        <strong>Уровень:</strong> <span class="badge bg-${log.level === 'error' ? 'danger' : (log.level === 'warning' ? 'warning' : (log.level === 'info' ? 'info' : 'secondary'))}">${log.level.toUpperCase()}</span><br>
                        <strong>Пользователь:</strong> ${log.user_email ?? 'system'}<br>
                        <strong>IP адрес:</strong> ${log.ip ?? ''}<br>
                        <strong>Модуль:</strong> ${log.module ?? ''}<br>
                        <strong>Действие:</strong> ${log.action ?? ''}<br>
                    </div>
                    <div class="col-md-6">
                        <strong>ID пользователя:</strong> ${log.user_id ?? ''}<br>
                    </div>
                </div>
                <hr>
                <div>
                    <strong>Сообщение:</strong><br>
                    <pre class="bg-light p-3 mt-2">${log.message}</pre>
                </div>
                <div>
                    <strong>Контекст:</strong><br>
                    <pre class="bg-light p-3 mt-2">${log.context ? JSON.stringify(JSON.parse(log.context), null, 2) : ''}</pre>
                </div>
            `;
            let bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        });
}

function markLogFixed(id) {
    // Убираю confirm, теперь действие выполняется сразу
    fetch(`/panel/logs/${id}/fix`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('log-row-' + id).remove();
        } else {
            alert(data.message || 'Ошибка при изменении статуса');
        }
    });
}
</script> 