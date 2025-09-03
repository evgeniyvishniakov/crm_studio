@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Уведомления</h1>
    <div class="row g-2 mb-3">
        <div class="col">
            <form method="get" class="row g-2">
                <div class="col-auto">
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="">Все типы</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" @if(request('type') == $type) selected @endif>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Все статусы</option>
                        <option value="unread" @if(request('status') == 'unread') selected @endif>Непрочитанные</option>
                        <option value="read" @if(request('status') == 'read') selected @endif>Прочитанные</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="col-auto">
            <form method="POST" action="{{ route('admin.notifications.check-subscriptions') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-info me-2">
                    <i class="fas fa-bell"></i> Проверить подписки
                </button>
            </form>
            @if($notifications->count() > 0)
                <button type="button" class="btn btn-danger" onclick="deleteAllNotifications()">
                    <i class="fas fa-trash"></i> Удалить все
                </button>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Тип</th>
                        <th>Заголовок</th>
                        <th>Дата</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($notifications as $notification)
                    <tr @if(!$notification->is_read) style="font-weight:bold;" @endif>
                        <td>{{ ucfirst($notification->type) }}</td>
                        <td>{{ $notification->title }}</td>
                        <td>{{ $notification->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($notification->is_read)
                                <span class="badge bg-success">{{ __('messages.read') }}</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ __('messages.unread') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($notification->url)
                                    <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Открыть</button>
                                    </form>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteNotification({{ $notification->id }})" title="Удалить">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Нет уведомлений</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $notifications->withQueryString()->links() }}</div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтвердите удаление</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="deleteModalText">Вы уверены, что хотите удалить это уведомление?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let notificationIdToDelete = null;
let deleteAllMode = false;

function deleteNotification(id) {
    notificationIdToDelete = id;
    deleteAllMode = false;
    document.getElementById('deleteModalText').textContent = 'Вы уверены, что хотите удалить это уведомление?';
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
}

function deleteAllNotifications() {
    notificationIdToDelete = null;
    deleteAllMode = true;
    document.getElementById('deleteModalText').textContent = 'Вы уверены, что хотите удалить ВСЕ уведомления? Это действие нельзя отменить.';
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteAllMode) {
        // Удаляем все уведомления
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.notifications.destroy-all") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    } else if (notificationIdToDelete) {
        // Удаляем одно уведомление
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.notifications.destroy", ":id") }}'.replace(':id', notificationIdToDelete);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
    
    bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
});
</script>
@endpush
@endsection 