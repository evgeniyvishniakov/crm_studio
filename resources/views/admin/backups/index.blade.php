@extends('admin.layouts.app')

@section('title', 'Резервные копии - Админ панель')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Резервные копии</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="createDatabaseBackup()">
                        <i class="fas fa-database me-2"></i>
                        Создать бэкап БД
                    </button>
                    <button class="btn btn-success" onclick="createFilesBackup()">
                        <i class="fas fa-file-archive me-2"></i>
                        Создать бэкап файлов
                    </button>
                </div>
            </div>

            <!-- Статистика -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Общий размер бэкапов</h6>
                                    <h3 class="mb-0">{{ $diskUsage['total'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-hdd fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Последний бэкап</h6>
                                    <h3 class="mb-0">
                                        @if($lastBackup)
                                            {{ $lastBackup['date'] }}
                                        @else
                                            Нет данных
                                        @endif
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Всего файлов</h6>
                                    <h3 class="mb-0">
                                        {{ (isset($backups['database']) ? count($backups['database']) : 0) + (isset($backups['files']) ? count($backups['files']) : 0) }}
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-files-o fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблицы с бэкапами -->
            <div class="row">
                <!-- Базы данных -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-database me-2"></i>
                                Резервные копии баз данных
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($backups['database']) && count($backups['database']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Файл</th>
                                                <th>Размер</th>
                                                <th>Дата</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($backups['database'] as $backup)
                                            <tr>
                                                <td>
                                                    <small class="text-muted">{{ $backup['name'] }}</small>
                                                </td>
                                                <td>{{ $backup['size'] }}</td>
                                                <td>{{ $backup['date'] }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" onclick="downloadBackup('database', '{{ $backup['name'] }}')" title="Скачать">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning" onclick="restoreDatabase('{{ $backup['name'] }}')" title="Восстановить">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" onclick="deleteBackup('database', '{{ $backup['name'] }}')" title="Удалить">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Резервные копии баз данных отсутствуют</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Файлы -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-file-archive me-2"></i>
                                Резервные копии файлов
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($backups['files']) && count($backups['files']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Файл</th>
                                                <th>Размер</th>
                                                <th>Дата</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($backups['files'] as $backup)
                                            <tr>
                                                <td>
                                                    <small class="text-muted">{{ $backup['name'] }}</small>
                                                </td>
                                                <td>{{ $backup['size'] }}</td>
                                                <td>{{ $backup['date'] }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" onclick="downloadBackup('files', '{{ $backup['name'] }}')" title="Скачать">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" onclick="deleteBackup('files', '{{ $backup['name'] }}')" title="Удалить">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-archive fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Резервные копии файлов отсутствуют</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Информация -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Информация о резервном копировании
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Рекомендации:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Создавайте бэкапы перед обновлениями</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Храните копии в разных местах</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Регулярно тестируйте восстановление</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Удаляйте старые копии для экономии места</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Автоматизация:</h6>
                                    <p class="text-muted">
                                        Для автоматического создания резервных копий настройте cron задачи:
                                    </p>
                                    <code class="d-block bg-light p-2 rounded">
                                        0 2 * * * php /path/to/your/project/artisan backup:create
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для восстановления -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Восстановление базы данных</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Внимание!</strong> Восстановление базы данных перезапишет все текущие данные. 
                    Убедитесь, что у вас есть резервная копия текущего состояния.
                </div>
                <p>Вы действительно хотите восстановить базу данных из файла <strong id="restoreFileName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-warning" onclick="confirmRestore()">Восстановить</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentRestoreFile = '';

function createDatabaseBackup() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Создание...';
    
    fetch('{{ route("admin.backups.database.create") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        showNotification('error', 'Ошибка при создании резервной копии');
        console.error('Error:', error);
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function createFilesBackup() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Создание...';
    
    fetch('{{ route("admin.backups.files.create") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        showNotification('error', 'Ошибка при создании резервной копии');
        console.error('Error:', error);
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function downloadBackup(type, filename) {
    window.location.href = `{{ route('admin.backups.download', ['type' => ':type', 'filename' => ':filename']) }}`
        .replace(':type', type)
        .replace(':filename', filename);
}

function deleteBackup(type, filename) {
    if (!confirm('Вы действительно хотите удалить эту резервную копию?')) {
        return;
    }
    
    fetch(`{{ route('admin.backups.delete', ['type' => ':type', 'filename' => ':filename']) }}`
        .replace(':type', type)
        .replace(':filename', filename), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        showNotification('error', 'Ошибка при удалении файла');
        console.error('Error:', error);
    });
}

function restoreDatabase(filename) {
    currentRestoreFile = filename;
    document.getElementById('restoreFileName').textContent = filename;
    new bootstrap.Modal(document.getElementById('restoreModal')).show();
}

function confirmRestore() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('restoreModal'));
    modal.hide();
    
    fetch('{{ route("admin.backups.database.restore") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            filename: currentRestoreFile
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        showNotification('error', 'Ошибка при восстановлении базы данных');
        console.error('Error:', error);
    });
}

function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endpush 