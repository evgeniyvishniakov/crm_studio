@extends('client.layouts.app')

@section('content')
    <div class="dashboard-container">
        <div class="clients-header">
            <h1>Пользователи</h1>
            <div id="notification"></div>
            <div class="header-actions">
                <button class="btn-add-client" onclick="openUserModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Добавить пользователя
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" placeholder="Поиск..." autocomplete="off" id="userSearchInput">
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table-striped clients-table">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Дата регистрации</th>
                    <th class="actions-column">Действия</th>
                </tr>
                </thead>
                <tbody id="usersTableBody">
                @foreach($users as $user)
                    <tr id="user-{{ $user->id }}">
                        <td>
                            <div class="client-info">
                                <div class="client-details">
                                    <div class="client-name">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->registered_at ? \Carbon\Carbon::parse($user->registered_at)->format('d.m.Y H:i') : '' }}</td>
                        <td class="actions-cell" style="vertical-align: middle;">
                            <button class="btn-edit" title="Редактировать">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button class="btn-delete" title="Удалить">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

<!-- Модальное окно для добавления пользователя -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Добавить пользователя</h2>
            <span class="close" onclick="closeUserModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="addUserForm">
                @csrf
                <div class="form-group">
                    <label for="userName">Имя *</label>
                    <input type="text" id="userName" name="name" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="userUsername">Логин *</label>
                    <input type="text" id="userUsername" name="username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="userEmail">Email</label>
                    <input type="email" id="userEmail" name="email" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="userPassword">Пароль *</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="text" id="userPassword" name="password" required autocomplete="off" style="flex:1;">
                        <button type="button" class="btn-cancel" onclick="generateUserPassword()">Сгенерировать</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="userRole">Роль</label>
                    <select id="userRole" name="role" required>
                        <option value="admin">Администратор</option>
                        <option value="manager">Менеджер</option>
                        <option value="master">Мастер</option>
                        <option value="user">Пользователь</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeUserModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
}
function closeUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}
function generateUserPassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    let pass = '';
    for (let i = 0; i < 10; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('userPassword').value = pass;
}
// Закрытие по клику вне окна
window.onclick = function(event) {
    const modal = document.getElementById('addUserModal');
    if (event.target === modal) closeUserModal();
}
</script>
@endsection 