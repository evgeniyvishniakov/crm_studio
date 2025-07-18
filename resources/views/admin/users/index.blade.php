@extends('admin.layouts.app')

@section('title', 'Управление пользователями - Админ')
@section('page-title', 'Управление пользователями')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Список пользователей</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-2"></i>Добавить пользователя
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Проект</th>
                        <th>Роль</th>
                        <th>Статус</th>
                        <th>Дата регистрации</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->project ? $user->project->project_name : '—' }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $user->status === 'active' ? 'Активен' : 'Неактивен' }}
                            </span>
                        </td>
                        <td>{{ $user->registered_at ? \Carbon\Carbon::parse($user->registered_at)->format('d.m.Y H:i') : '' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" title="Редактировать" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Удалить" onclick="if(confirm('Удалить пользователя?')){ this.form.submit(); }"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Модальное окно редактирования пользователя -->
                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Редактировать пользователя: {{ $user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="edit-name-{{ $user->id }}" class="form-label">Имя</label>
                                            <input type="text" class="form-control" id="edit-name-{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit-email-{{ $user->id }}" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="edit-email-{{ $user->id }}" name="email" value="{{ $user->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit-role-{{ $user->id }}" class="form-label">Роль</label>
                                            <select class="form-select" id="edit-role-{{ $user->id }}" name="role" required>
                                                <option value="admin" @if($user->role=='admin') selected @endif>Администратор</option>
                                                <option value="manager" @if($user->role=='manager') selected @endif>Менеджер</option>
                                                <option value="user" @if($user->role=='user') selected @endif>Пользователь</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit-status-{{ $user->id }}" class="form-label">Статус</label>
                                            <select class="form-select" id="edit-status-{{ $user->id }}" name="status" required>
                                                <option value="active" @if($user->status=='active') selected @endif>Активен</option>
                                                <option value="inactive" @if($user->status=='inactive') selected @endif>Неактивен</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                        <button type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Нет данных для отображения</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Модальное окно создания пользователя -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить пользователя</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div id="createUserErrors" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label for="user-name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="user-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="user-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="user-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="user-password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="user-password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="user-password_confirmation" class="form-label">Подтверждение пароля</label>
                        <input type="password" class="form-control" id="user-password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="user-role" class="form-label">Роль</label>
                        <select class="form-select" id="user-role" name="role" required>
                            <option value="">Выберите роль</option>
                            <option value="admin">Администратор</option>
                            <option value="manager">Менеджер</option>
                            <option value="user">Пользователь</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="user-status" class="form-label">Статус</label>
                        <select class="form-select" id="user-status" name="status" required>
                            <option value="active">Активен</option>
                            <option value="inactive">Неактивен</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="user-project_id" class="form-label">Проект</label>
                        <select class="form-select" id="user-project_id" name="project_id" required>
                            <option value="">Выберите проект</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch("{{ route('admin.users.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                if (response.ok) return response.json();
                const data = await response.json();
                throw data;
            })
            .then(data => {
                if (data.success) {
                    form.reset();
                    document.getElementById('createUserErrors').classList.add('d-none');
                    var modal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    throw data;
                }
            })
            .catch(error => {
                let msg = 'Ошибка при создании пользователя.';
                if (error.errors) {
                    msg = Object.values(error.errors).join('<br>');
                } else if (error.message) {
                    msg = error.message;
                }
                const errorDiv = document.getElementById('createUserErrors');
                if (errorDiv) {
                    errorDiv.innerHTML = msg;
                    errorDiv.classList.remove('d-none');
                }
            });
        });
    }
});
</script>
@endpush 
