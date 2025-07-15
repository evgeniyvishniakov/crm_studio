@extends('client.layouts.app')

@section('content')

<div class="dashboard-container">
    <div class="clients-header">
        <h1>Роли и Доступы</h1>
        <div id="notification"></div>
        <div class="header-actions">
            <button class="btn-add-client" id="btnAddRole">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Добавить роль
            </button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="clients-table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Доступы</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr id="role-{{ $role->id }}">
                        <td>{{ $role->label }}</td>
                        <td>
                            @php
                                // Получаем список permissions для роли
                                $rolePerms = isset($role->permissions) ? $role->permissions : (\DB::table('role_permission')->where('role_id', $role->id)->pluck('permission_id')->toArray());
                                $permNames = [];
                                if (!empty($rolePerms)) {
                                    $allPerms = isset($permissions) ? $permissions : \DB::table('permissions')->get();
                                    foreach ($allPerms as $perm) {
                                        if (is_object($perm) && in_array($perm->id, $rolePerms)) {
                                            $permNames[] = $perm->label ?? $perm->name;
                                        }
                                    }
                                }
                            @endphp
                            {{ $permNames ? implode(', ', $permNames) : '—' }}
                        </td>
                        <td class="actions-cell" style="vertical-align: middle;">
                            @if($role->name !== 'admin' && !$role->is_system)
                                <button class="btn-edit" data-id="{{ $role->id }}" title="Редактировать">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828.793-.793z" />
                                    </svg>
                                </button>
                                <button class="btn-delete" data-id="{{ $role->id }}" title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; color:#888; padding:40px 0;">Пока нет данных. Добавьте первую роль.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Модальное окно подтверждения удаления -->
    <div id="roleConfirmationModal" class="confirmation-modal" style="display:none;">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить эту роль?</p>
            <div class="confirmation-buttons">
                <button id="cancelDeleteRole" class="cancel-btn">Отмена</button>
                <button id="confirmDeleteRole" class="confirm-btn">Удалить</button>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для добавления/редактирования роли -->
<div id="roleModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="roleModalTitle">Добавить роль</h2>
            <span class="close" onclick="closeRoleModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="roleForm">
                @csrf
                <div class="form-group">
                    <label for="roleSelect">Роль</label>
                    <select id="roleSelect" name="name" required onchange="onRoleSelectChange()">
                        <option value="">Выберите роль...</option>
                        @foreach(config('roles') as $key => $label)
                            @if($key !== 'admin')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            
                <div class="form-group">
                    <label>Доступы</label>
                    <div class="permissions-list">
                        
                            <label><input type="checkbox" name="permissions[]" value="dashboard"> <span>Панель управления (дешборд)</span></label>
                            <label><input type="checkbox" name="permissions[]" value="clients"> <span>Клиенты</span></label>
                            <label><input type="checkbox" name="permissions[]" value="appointments"> <span>Записи</span></label>
                            <label><input type="checkbox" name="permissions[]" value="analytics"> <span>Аналитика</span></label>
                            <label><input type="checkbox" name="permissions[]" value="warehouse"> <span>Склад</span></label>
                            <label><input type="checkbox" name="permissions[]" value="purchases"> <span>Закупки</span></label>
                            <label><input type="checkbox" name="permissions[]" value="sales"> <span>Продажи</span></label>
                            <label><input type="checkbox" name="permissions[]" value="expenses"> <span>Расходы</span></label>
                            <label><input type="checkbox" name="permissions[]" value="inventory"> <span>Инвентаризация</span></label>
                            <label><input type="checkbox" name="permissions[]" value="reference"> <span>Справочник</span></label>
                            <label><input type="checkbox" name="permissions[]" value="settings"> <span>Настройки</span></label>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeRoleModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
window.permissions = @json($permissions->pluck('name'));
let editingRoleId = null;
let currentDeleteRoleRow = null;
let currentDeleteRoleId = null;

// Открытие модалки для редактирования роли (AJAX)
function openEditRoleModal(id) {
    fetch(`/roles/${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.role) {
                window.showNotification('error', data.message || 'Ошибка загрузки роли');
                return;
            }
            const role = data.role;
            editingRoleId = id;
            document.getElementById('roleModalTitle').textContent = 'Редактировать роль';
            document.getElementById('roleSelect').value = role.name;
            document.getElementById('roleSelect').disabled = true;
            document.querySelectorAll('.permissions-list input[type="checkbox"]').forEach(cb => {
                cb.checked = role.permissions.includes(cb.value);
            });
            document.getElementById('roleModal').style.display = 'block';
        })
        .catch(() => {
            window.showNotification('error', 'Ошибка загрузки роли');
        });
}

// Открытие модалки для добавления роли
function openRoleModal() {
    editingRoleId = null;
    document.getElementById('roleModalTitle').textContent = 'Добавить роль';
    document.getElementById('roleSelect').value = '';
    document.getElementById('roleSelect').disabled = false; // Разрешаем выбор роли при добавлении
    document.querySelectorAll('.permissions-list input[type="checkbox"]').forEach(cb => cb.checked = false);
    document.getElementById('roleModal').style.display = 'block';
}

// Закрытие модалки
function closeRoleModal() {
    document.getElementById('roleModal').style.display = 'none';
}

// Сохранение роли (создание/редактирование)
document.getElementById('roleForm').onsubmit = function(e) {
    e.preventDefault();
    const name = document.getElementById('roleSelect').value;
    const label = document.getElementById('roleSelect').selectedOptions[0].text; // label из option
    const perms = Array.from(document.querySelectorAll('.permissions-list input[type="checkbox"]:checked')).map(cb => cb.value);
    const url = editingRoleId ? `/roles/${editingRoleId}` : '/roles';
    const method = editingRoleId ? 'PUT' : 'POST';
    const submitBtn = this.querySelector('.btn-submit');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
    submitBtn.disabled = true;
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            label: label,
            permissions: perms
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.role) {
            if (editingRoleId) {
                // Отладочный вывод
                console.log('Обновляем роль:', data.role);
                updateRoleRow(data.role);
                // Форсируем обновление: повторно получаем роль с сервера и обновляем строку
                fetch(`/roles/${editingRoleId}`)
                    .then(res => res.json())
                    .then(fresh => {
                        if (fresh.success && fresh.role) {
                            console.log('Форсированное обновление:', fresh.role);
                            updateRoleRow(fresh.role);
                        }
                    });
                window.showNotification('success', 'Роль успешно обновлена');
            } else {
                addRoleRow(data.role, perms, label);
                window.showNotification('success', 'Роль успешно добавлена');
            }
            closeRoleModal();
            this.reset();
        } else {
            window.showNotification('error', data.message || 'Ошибка');
        }
    })
    .finally(() => {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
};

function addRoleRow(role, perms, label) {
    const tbody = document.querySelector('.clients-table tbody');
    const tr = document.createElement('tr');
    tr.id = 'role-' + String(role.id);
    tr.setAttribute('data-name', role.name);
    tr.setAttribute('data-perms', perms.join(','));
    tr.innerHTML = `
        <td>${label}</td>
        <td>${perms.length ? perms.map(p => getPermissionLabel(p)).join(', ') : '—'}</td>
        <td class="actions-cell" style="vertical-align: middle;">
            <button class="btn-edit" data-id="${role.id}" title="Редактировать">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828.793-.793z" />
                </svg>
            </button>
            <button class="btn-delete" data-id="${role.id}" title="Удалить">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </td>
    `;
    tbody.insertBefore(tr, tbody.firstChild);
}

function updateRoleRow(role) {
    const tr = document.getElementById('role-' + String(role.id));
    if (!tr) {
        console.warn('Не найдена строка для роли:', role.id, role);
        return;
    }
    console.log('Обновляем строку для роли:', role.id, role);
    const perms = role.permissions || [];
    tr.setAttribute('data-name', role.name);
    tr.setAttribute('data-perms', perms.join(','));
    tr.children[0].textContent = role.label;
    tr.children[1].textContent = perms.length ? perms.map(p => getPermissionLabel(p)).join(', ') : '—';
}

function getPermissionLabel(name) {
    // Можно доработать для красивых названий
    switch(name) {
        case 'dashboard': return 'Панель управления (дешборд)';
        case 'clients': return 'Клиенты';
        case 'appointments': return 'Записи';
        case 'analytics': return 'Аналитика';
        case 'warehouse': return 'Склад';
        case 'purchases': return 'Закупки';
        case 'sales': return 'Продажи';
        case 'expenses': return 'Расходы';
        case 'inventory': return 'Инвентаризация';
        case 'reference': return 'Справочник';
        case 'settings': return 'Настройки';
        default: return name;
    }
}

// --- Удаление роли с подтверждением ---
function openDeleteRoleModal(btn, id) {
    currentDeleteRoleRow = btn.closest('tr');
    currentDeleteRoleId = id;
    document.getElementById('roleConfirmationModal').style.display = 'block';
}

// --- Делегирование событий для редактирования и удаления ролей ---
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-edit')) {
        const btn = e.target.closest('.btn-edit');
        const id = String(btn.dataset.id);
        openEditRoleModal(id);
    }
    if (e.target.closest('.btn-delete')) {
        const btn = e.target.closest('.btn-delete');
        const row = btn.closest('tr');
        const id = String(btn.dataset.id || (row && row.id ? row.id.split('-')[1] : ''));
        openDeleteRoleModal(btn, id);
    }
});

document.getElementById('cancelDeleteRole').onclick = function() {
    document.getElementById('roleConfirmationModal').style.display = 'none';
    currentDeleteRoleRow = null;
    currentDeleteRoleId = null;
};
document.getElementById('confirmDeleteRole').onclick = function() {
    if (!currentDeleteRoleRow || !currentDeleteRoleId) return;
    fetch(`/roles/${String(currentDeleteRoleId)}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentDeleteRoleRow.remove();
            window.showNotification('success', 'Роль успешно удалена');
        } else {
            window.showNotification('error', data.message || 'Ошибка удаления');
        }
        document.getElementById('roleConfirmationModal').style.display = 'none';
        currentDeleteRoleRow = null;
        currentDeleteRoleId = null;
    });
};

function onRoleSelectChange() {
    // label теперь не нужен, функция оставлена пустой
}

document.addEventListener('DOMContentLoaded', function() {
    // Назначаем обработчик на кнопку только после загрузки DOM
    document.getElementById('btnAddRole').onclick = openRoleModal;
    // Загружаем роли только после загрузки DOM
    // loadRoles(); // Удаляю JS-функцию loadRoles и все её вызовы, так как таблица теперь серверная
});

window.closeRoleModal = closeRoleModal;
window.openEditRoleModal = openEditRoleModal;
window.openRoleModal = openRoleModal;

</script>
<style>
    .permissions-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }
    .permissions-list label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 0;
        white-space: nowrap;
        font-size: 16px;
    }
    .permissions-list span {
        white-space: nowrap;
    }
.confirmation-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0; top: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.2);
    align-items: center;
    justify-content: center;
}
.confirmation-modal .confirmation-content {
    background: #fff;
    border-radius: 8px;
    padding: 32px 24px;
    max-width: 350px;
    margin: 100px auto;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    text-align: center;
}
.confirmation-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 24px;
}
.confirm-btn {
    background: #dc3545;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 8px 18px;
    font-weight: 600;
    cursor: pointer;
}
.cancel-btn {
    background: #e5e7eb;
    color: #222;
    border: none;
    border-radius: 6px;
    padding: 8px 18px;
    font-weight: 600;
    cursor: pointer;
}
.notification {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 9999;
    padding: 16px 28px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    color: #fff;
    background: #3b82f6;
    box-shadow: 0 2px 8px rgba(59,130,246,0.08);
    display: none;
}
.notification.success { background: #22c55e; }
.notification.error { background: #ef4444; }
</style>
@endsection 