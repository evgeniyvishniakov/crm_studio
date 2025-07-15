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
                <tr></tr>
                    <th>Имя</th>
                    <th>Почта/Логин</th>
                    <th>Роль</th>
                    <th>Статус</th>
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
                        <td>{{ $user->email ?: $user->username }}</td>
                        <td>{{ $roles[$user->role] ?? $user->role }}</td>
                        <td><span class="status-badge {{ $user->status === 'active' ? 'status-completed' : 'status-cancelled' }}">{{ $user->status === 'active' ? 'Активен' : 'Неактивен' }}</span></td>
                        <td>{{ $user->registered_at ? \Carbon\Carbon::parse($user->registered_at)->format('d.m.Y H:i') : '' }}</td>
                        <td class="actions-cell" style="vertical-align: middle;">
                            @if($user->username !== 'admin')
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
                            @endif
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
            <div id="addUserErrors" class="modal-errors" style="display:none;color:#d32f2f;margin-bottom:10px;"></div>
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
                        @foreach($roles as $key => $label)
                            @if($key !== 'admin')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
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

<!-- Модальное окно для редактирования пользователя -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Редактировать пользователя</h2>
            <span class="close" onclick="closeEditUserModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                <div class="form-group">
                    <label for="editUserName">Имя *</label>
                    <input type="text" id="editUserName" name="name" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="editUserUsername">Логин *</label>
                    <input type="text" id="editUserUsername" name="username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="editUserEmail">Email</label>
                    <input type="email" id="editUserEmail" name="email" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="editUserRole">Роль</label>
                    <select id="editUserRole" name="role" required>
                        @foreach($roles as $key => $label)
                            @if($key !== 'admin')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="editUserStatus">Статус</label>
                    <select id="editUserStatus" name="status" required>
                        <option value="active">Активен</option>
                        <option value="inactive">Неактивен</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditUserModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления пользователя -->
<div id="userConfirmationModal" class="confirmation-modal">
    <div class="confirmation-content">
        <h3>Подтверждение удаления</h3>
        <p>Вы уверены, что хотите удалить этого пользователя?</p>
        <div class="confirmation-buttons">
            <button id="cancelUserDelete" class="cancel-btn">Отмена</button>
            <button id="confirmUserDelete" class="confirm-btn">Удалить</button>
        </div>
    </div>
</div>

<script>
    window.roles = @json($roles);
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

function showErrors(errors, formId = 'addUserForm') {
    clearErrors(formId);
    let fallbackHtml = '';
    Object.entries(errors).forEach(([field, messages]) => {
        const input = document.querySelector(`#${formId} [name="${field}"]`);
        if (input) {
            const inputGroup = input.closest('.form-group');
            if (inputGroup) {
                inputGroup.classList.add('has-error');
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                errorElement.style.color = '#d32f2f';
                errorElement.style.marginTop = '5px';
                errorElement.style.fontSize = '0.85rem';
                inputGroup.appendChild(errorElement);
            } else {
                fallbackHtml += `<div>${Array.isArray(messages) ? messages[0] : messages}</div>`;
            }
        } else {
            fallbackHtml += `<div>${Array.isArray(messages) ? messages[0] : messages}</div>`;
        }
    });
    // Если не удалось найти input, выводим ошибку в верхний div
    if (fallbackHtml) {
        const addUserErrors = document.getElementById('addUserErrors');
        if (addUserErrors) {
            addUserErrors.innerHTML = fallbackHtml;
            addUserErrors.style.display = 'block';
        }
    }
}
function clearErrors(formId = 'addUserForm') {
    const form = document.getElementById(formId);
    if (form) {
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        form.querySelectorAll('.has-error').forEach(el => {
            el.classList.remove('has-error');
        });
    }
}

// --- AJAX добавление пользователя ---
document.addEventListener('DOMContentLoaded', function() {
    const addUserForm = document.getElementById('addUserForm');
    const addUserErrors = document.getElementById('addUserErrors');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors('addUserForm');
            const formData = new FormData(addUserForm);
            fetch("{{ route('client.users.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=\"_token\"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) return response.json().then(err => Promise.reject(err));
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Добавить пользователя в таблицу
                    const user = data.user;
                    const tbody = document.getElementById('usersTableBody');
                    const tr = document.createElement('tr');
                    tr.id = 'user-' + user.id;
                    tr.innerHTML = `
                        <td>
                            <div class="client-info">
                                <div class="client-details">
                                    <div class="client-name">${user.name}</div>
                                </div>
                            </div>
                        </td>
                        <td>${user.email ? user.email : user.username}</td>
                        <td>${window.roles && window.roles[user.role] ? window.roles[user.role] : user.role}</td>
                        <td><span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? 'Активен' : 'Неактивен'}</span></td>
                        <td>${user.registered_at ? formatDateTime(user.registered_at) : ''}</td>
                        <td class="actions-cell" style="vertical-align: middle;">
                            @if($user->username !== 'admin')
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
                            @endif
                        </td>
                    `;
                    tbody.appendChild(tr);

                    // Сортировка: admin всегда первый
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    rows.sort((a, b) => {
                        const roleA = a.children[2].textContent.trim();
                        const roleB = b.children[2].textContent.trim();
                        if (roleA === 'admin') return -1;
                        if (roleB === 'admin') return 1;
                        return 0;
                    });
                    rows.forEach(row => tbody.appendChild(row));

                    addUserForm.reset();
                    closeUserModal();
                    showNotification('success', 'Пользователь успешно добавлен');
                } else {
                    showNotification('error', data.message || 'Ошибка при добавлении пользователя');
                }
            })
            .catch(err => {
                if (err.errors) {
                    showErrors(err.errors, 'addUserForm');
                    showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    showNotification('error', 'Ошибка при добавлении пользователя');
                }
            });
        });
    }
});

// Открытие/закрытие модального окна редактирования
function openEditUserModal(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserName').value = user.name;
    document.getElementById('editUserUsername').value = user.username;
    document.getElementById('editUserEmail').value = user.email || '';
    document.getElementById('editUserRole').value = user.role;
    document.getElementById('editUserStatus').value = user.status;

    // Блокируем поля для admin
    if (user.username === 'admin') {
        document.getElementById('editUserUsername').setAttribute('readonly', true);
        document.getElementById('editUserRole').setAttribute('disabled', true);
        document.querySelector('#editUserForm .btn-submit').setAttribute('disabled', true);
        // Поясняющее сообщение
        if (!document.getElementById('adminEditNote')) {
            let note = document.createElement('div');
            note.id = 'adminEditNote';
            note.style.color = '#d32f2f';
            note.style.marginTop = '10px';
            note.textContent = 'Роль и логин главного администратора нельзя изменить.';
            document.getElementById('editUserForm').appendChild(note);
        }
    } else {
        document.getElementById('editUserUsername').removeAttribute('readonly');
        document.getElementById('editUserRole').removeAttribute('disabled');
        document.querySelector('#editUserForm .btn-submit').removeAttribute('disabled');
        let note = document.getElementById('adminEditNote');
        if (note) note.remove();
    }

    document.getElementById('editUserModal').style.display = 'block';
}
function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

// Удаление пользователя
function attachDeleteUserHandlers() {
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.onclick = function() {
            const tr = btn.closest('tr');
            const userId = tr.id.replace('user-', '');
            document.getElementById('editUserId').value = userId; // Передаем ID для удаления
            document.getElementById('userConfirmationModal').style.display = 'block';
        };
    });
}

// --- Удаляю attachEditUserHandlers и attachDeleteUserHandlers, заменяю на делегирование ---
document.addEventListener('click', function(e) {
    // Редактирование пользователя
    if (e.target.closest('.btn-edit')) {
        const tr = e.target.closest('tr');
        if (tr) {
            // Проверка: если это admin, не открывать окно
            const tds = tr.getElementsByTagName('td');
            if (tds.length > 1 && (tds[1].innerText.trim() === 'admin' || tr.id === 'user-admin')) {
                showNotification('error', 'Главного администратора нельзя редактировать.');
                return;
            }
            const userId = tr.id.split('-')[1];
            fetch(`/users/${userId}/edit`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(user => {
                openEditUserModal(user);
            })
            .catch(() => showNotification('error', 'Ошибка при получении данных пользователя'));
        }
    }
    // Удаление пользователя (открытие модалки)
    if (e.target.closest('.btn-delete')) {
        const tr = e.target.closest('tr');
        if (tr) {
            const userId = tr.id.split('-')[1];
            currentDeleteUserRow = tr;
            currentDeleteUserId = userId;
            document.getElementById('userConfirmationModal').style.display = 'block';
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Обработка сохранения редактирования пользователя
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const userId = document.getElementById('editUserId').value;
            const formData = new FormData(editUserForm);
            fetch(`/users/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Обновить строку в таблице
                    const user = data.user;
                    const tr = document.getElementById('user-' + user.id);
                    if (tr) {
                        tr.innerHTML = `
                            <td>
                                <div class="client-info">
                                    <div class="client-details">
                                        <div class="client-name">${user.name}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${user.email ? user.email : user.username}</td>
                            <td>${window.roles && window.roles[user.role] ? window.roles[user.role] : user.role}</td>
                            <td><span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? 'Активен' : 'Неактивен'}</span></td>
                            <td>${user.registered_at ? formatDateTime(user.registered_at) : ''}</td>
                            <td class="actions-cell" style="vertical-align: middle;">
                                @if($user->username !== 'admin')
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
                                @endif
                            </td>
                        `;
                        // Re-attach handlers after updating the row
                        document.addEventListener('click', function(e) {
                            if (e.target.closest('.btn-edit')) {
                                const tr = e.target.closest('tr');
                                if (tr) {
                                    const userId = tr.id.split('-')[1];
                                    fetch(`/users/${userId}/edit`, {
                                        method: 'GET',
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(res => res.json())
                                    .then(user => {
                                        openEditUserModal(user);
                                    })
                                    .catch(() => showNotification('error', 'Ошибка при получении данных пользователя'));
                                }
                            }
                            if (e.target.closest('.btn-delete')) {
                                const tr = e.target.closest('tr');
                                if (tr) {
                                    const userId = tr.id.split('-')[1];
                                    currentDeleteUserRow = tr;
                                    currentDeleteUserId = userId;
                                    document.getElementById('userConfirmationModal').style.display = 'block';
                                }
                            }
                        });
                    }
                    closeEditUserModal();
                    showNotification('success', 'Пользователь успешно обновлён');
                } else {
                    showNotification('error', data.message || 'Ошибка при обновлении пользователя');
                }
            })
            .catch(() => showNotification('error', 'Ошибка при обновлении пользователя'));
        });
    }
});

let currentDeleteUserRow = null;
let currentDeleteUserId = null;

document.getElementById('cancelUserDelete').addEventListener('click', function() {
    document.getElementById('userConfirmationModal').style.display = 'none';
    currentDeleteUserRow = null;
    currentDeleteUserId = null;
});

document.getElementById('confirmUserDelete').addEventListener('click', function() {
    if (currentDeleteUserRow && currentDeleteUserId) {
        deleteUser(currentDeleteUserRow, currentDeleteUserId);
    }
    document.getElementById('userConfirmationModal').style.display = 'none';
});

function deleteUser(row, userId) {
    row.classList.add('row-deleting');
    fetch(`/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Ошибка при удалении');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            setTimeout(() => {
                row.remove();
                showNotification('success', 'Пользователь успешно удалён');
            }, 300);
        } else {
            row.classList.remove('row-deleting');
            showNotification('error', data.message || 'Не удалось удалить пользователя');
        }
    })
    .catch(error => {
        row.classList.remove('row-deleting');
        showNotification('error', 'Не удалось удалить пользователя');
    });
}

// Добавить функцию форматирования даты без секунд
function formatDateTime(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${day}.${month}.${year} ${hours}:${minutes}`;
}
</script>
@endsection 