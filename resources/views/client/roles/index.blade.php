@extends('client.layouts.app')

@section('content')

<div class="dashboard-container">
    <div class="clients-header">
        <h1>{{ __('messages.roles_and_permissions') }}</h1>
        <div id="notification"></div>
        <div class="header-actions">
            <button class="btn-add-client" id="btnAddRole">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('messages.add_role') }}
            </button>
        </div>
    </div>
    <!-- Десктопная таблица -->
    <div class="table-wrapper">
        <table class=" table-striped clients-table">
            <thead>
                <tr>
                    <th>{{ __('messages.table_name') }}</th>
                    <th>{{ __('messages.table_permissions') }}</th>
                    <th>{{ __('messages.table_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr id="role-{{ $role->id }}">
                        <td>{{ __('messages.role_' . $role->name) }}</td>
                        <td>
                            @php
                                // Получаем список permissions для роли
                                $rolePerms = isset($role->permissions) ? $role->permissions : (\DB::table('role_permission')->where('role_id', $role->id)->pluck('permission_id')->toArray());
                                $permNames = [];
                                if (!empty($rolePerms)) {
                                    $allPerms = isset($permissions) ? $permissions : \DB::table('permissions')->get();
                                    foreach ($allPerms as $perm) {
                                        if (is_object($perm) && in_array($perm->id, $rolePerms)) {
                                            $permKey = str_replace(['-', '.'], '_', $perm->name);
                                            $permNames[] = __('messages.permission_' . $permKey);
                                        }
                                    }
                                }
                            @endphp
                            {{ $permNames ? implode(', ', $permNames) : '—' }}
                        </td>
                        <td class="actions-cell" style="vertical-align: middle;">
                            @if($role->name !== 'admin' && !$role->is_system)
                                                            <button class="btn-edit" data-id="{{ $role->id }}" title="{{ __('messages.edit') }}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828.793-.793z" />
                                </svg>
                            </button>
                            <button class="btn-delete" data-id="{{ $role->id }}" title="{{ __('messages.delete') }}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; color:#888; padding:40px 0;">{{ __('messages.no_data_yet_add_first_role') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Мобильные карточки ролей -->
    <div class="roles-cards" id="rolesCards" style="display: none;">
        @forelse($roles as $role)
            @php
                // Получаем список permissions для роли
                $rolePerms = isset($role->permissions) ? $role->permissions : (\DB::table('role_permission')->where('role_id', $role->id)->pluck('permission_id')->toArray());
                $permNames = [];
                if (!empty($rolePerms)) {
                    $allPerms = isset($permissions) ? $permissions : \DB::table('permissions')->get();
                    foreach ($allPerms as $perm) {
                        if (is_object($perm) && in_array($perm->id, $rolePerms)) {
                            $permKey = str_replace(['-', '.'], '_', $perm->name);
                            $permNames[] = __('messages.permission_' . $permKey);
                        }
                    }
                }
            @endphp
            <div class="role-card" id="role-card-{{ $role->id }}">
                <div class="role-card-header">
                    <div class="role-main-info">
                        <div class="role-icon">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="role-name" data-role-name="{{ $role->name }}">{{ __('messages.role_' . $role->name) }}</div>
                    </div>
                    <div class="role-type">
                        @if($role->name === 'admin')
                            <span class="role-badge admin">{{ __('messages.system_role') }}</span>
                        @elseif($role->is_system)
                            <span class="role-badge system">{{ __('messages.system_role') }}</span>
                        @else
                            <span class="role-badge custom">{{ __('messages.custom_role') }}</span>
                        @endif
                    </div>
                </div>
                <div class="role-info">
                    <div class="role-info-item">
                        <div class="role-info-label">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.table_permissions') }}:
                        </div>
                        <div class="role-info-value">
                            @if($permNames)
                                <div class="permissions-tags">
                                    @foreach($permNames as $permName)
                                        <span class="permission-tag">{{ $permName }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="no-permissions">{{ __('messages.no_permissions') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($role->name !== 'admin' && !$role->is_system)
                <div class="role-actions">
                    <button class="btn-edit" title="{{ __('messages.edit') }}" onclick="openEditRoleModalFromCard({{ $role->id }})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        {{ __('messages.edit') }}
                    </button>
                    <button class="btn-delete" title="{{ __('messages.delete') }}" onclick="showDeleteConfirmation({{ $role->id }})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('messages.delete') }}
                    </button>
                </div>
                @endif
            </div>
        @empty
            <div class="no-roles-message">
                <div class="no-roles-icon">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="no-roles-text">{{ __('messages.no_data_yet_add_first_role') }}</div>
            </div>
        @endforelse
    </div>

    <!-- Мобильная пагинация -->
    <div id="mobileRolesPagination" class="mobile-pagination" style="display: none;">
        <!-- Пагинация будет добавлена через JavaScript -->
    </div>
    <!-- Модальное окно подтверждения удаления -->
    <div id="roleConfirmationModal" class="confirmation-modal" style="display:none;">
        <div class="confirmation-content">
            <h3>{{ __('messages.confirmation_delete') }}</h3>
            <p>{{ __('messages.are_you_sure_you_want_to_delete_this_role') }}</p>
            <div class="confirmation-buttons">
                <button id="cancelDeleteRole" class="cancel-btn">{{ __('messages.cancel') }}</button>
                <button id="confirmDeleteRole" class="confirm-btn">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для добавления/редактирования роли -->
<div id="roleModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="roleModalTitle">{{ __('messages.add_role') }}</h2>
            <span class="close" onclick="closeRoleModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="roleForm">
                @csrf
                <div class="form-group">
                    <label for="roleSelect">{{ __('messages.role') }}</label>
                    <select id="roleSelect" name="name" required onchange="onRoleSelectChange()">
                        <option value="">{{ __('messages.select_role') }}</option>
                        @foreach(config('roles') as $key => $label)
                            @if($key !== 'admin')
                                <option value="{{ $key }}">{{ __('messages.role_' . $key) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            
                <div class="form-group">
                    <label>{{ __('messages.permissions') }}</label>
                    <div class="permissions-list">
                        @foreach($permissions as $perm)
                            <label>
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}">
                                <span>{{ __('messages.permission_' . str_replace(['-', '.'], '_', $perm->name)) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeRoleModal()">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
// Создаем объект с переводами ролей
window.roleLabels = {
    'admin': '{{ __('messages.role_admin') }}',
    'manager': '{{ __('messages.role_manager') }}',
    'master': '{{ __('messages.role_master') }}',
    'hairdresser': '{{ __('messages.role_hairdresser') }}',
    'cosmetologist': '{{ __('messages.role_cosmetologist') }}',
    'nailmaster': '{{ __('messages.role_nailmaster') }}',
    'makeup': '{{ __('messages.role_makeup') }}',
    'browlash': '{{ __('messages.role_browlash') }}',
    'massage': '{{ __('messages.role_massage') }}',
    'stylist': '{{ __('messages.role_stylist') }}',
    'barber': '{{ __('messages.role_barber') }}',
    'senior_barber': '{{ __('messages.role_senior_barber') }}',
    'shaving': '{{ __('messages.role_shaving') }}',
    'intern': '{{ __('messages.role_intern') }}',
    'seller': '{{ __('messages.role_seller') }}',
    'storekeeper': '{{ __('messages.role_storekeeper') }}'
};

// Функция для исправления названий ролей в мобильных карточках
function fixRoleNames() {
    const roleNameElements = document.querySelectorAll('.role-name[data-role-name]');
    roleNameElements.forEach(element => {
        const roleName = element.getAttribute('data-role-name');
        const translatedName = getRoleLabel(roleName);
        if (translatedName && translatedName !== roleName) {
            element.textContent = translatedName;
        }
    });
}

// Функции для мобильной версии
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const rolesCards = document.getElementById('rolesCards');
    const mobilePagination = document.getElementById('mobileRolesPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (rolesCards) rolesCards.style.display = 'block';
        if (mobilePagination) mobilePagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (rolesCards) rolesCards.style.display = 'none';
        if (mobilePagination) mobilePagination.style.display = 'none';
    }
}

// Функция для открытия модального окна редактирования из карточки
function openEditRoleModalFromCard(roleId) {
    openEditRoleModal(roleId);
}

// Функция для показа подтверждения удаления
function showDeleteConfirmation(roleId) {
    currentDeleteRoleId = roleId;
    document.getElementById('roleConfirmationModal').style.display = 'block';
}

// Функция для удаления роли
function deleteRole(roleId) {
    const roleCard = document.getElementById(`role-card-${roleId}`);
    const roleRow = document.getElementById(`role-${roleId}`);
    
    if (roleCard) roleCard.classList.add('row-deleting');
    if (roleRow) roleRow.classList.add('row-deleting');
    
    fetch(`/roles/${roleId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            setTimeout(() => {
                if (roleCard) roleCard.remove();
                if (roleRow) roleRow.remove();
                window.showNotification('success', '{{ __('messages.role_successfully_deleted') }}');
            }, 300);
        } else {
            if (roleCard) roleCard.classList.remove('row-deleting');
            if (roleRow) roleRow.classList.remove('row-deleting');
            window.showNotification('error', data.message || '{{ __('messages.error_deleting_role') }}');
        }
    })
    .catch(error => {
        if (roleCard) roleCard.classList.remove('row-deleting');
        if (roleRow) roleRow.classList.remove('row-deleting');
        window.showNotification('error', '{{ __('messages.error_deleting_role') }}');
    });
}

// Функция для обновления карточки роли
function updateRoleCard(role) {
    const roleCard = document.getElementById(`role-card-${role.id}`);
    if (roleCard) {
        const perms = role.permissions || [];
        const permNames = perms.map(p => getPermissionLabel(p));
        
        const roleType = role.name === 'admin' ? 'admin' : (role.is_system ? 'system' : 'custom');
        const roleTypeText = role.name === 'admin' || role.is_system ? '{{ __('messages.system_role') }}' : '{{ __('messages.custom_role') }}';
        
        roleCard.innerHTML = `
            <div class="role-card-header">
                <div class="role-main-info">
                    <div class="role-icon">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="role-name">${getRoleLabel(role.name)}</div>
                </div>
                <div class="role-type">
                    <span class="role-badge ${roleType}">${roleTypeText}</span>
                </div>
            </div>
            <div class="role-info">
                <div class="role-info-item">
                    <div class="role-info-label">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                        </svg>
                        {{ __('messages.table_permissions') }}:
                    </div>
                    <div class="role-info-value">
                        ${permNames.length ? `
                            <div class="permissions-tags">
                                ${permNames.map(permName => `<span class="permission-tag">${permName}</span>`).join('')}
                            </div>
                        ` : `
                            <span class="no-permissions">{{ __('messages.no_permissions') }}</span>
                        `}
                    </div>
                </div>
            </div>
            ${role.name !== 'admin' && !role.is_system ? `
            <div class="role-actions">
                <button class="btn-edit" title="{{ __('messages.edit') }}" onclick="openEditRoleModalFromCard(${role.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    {{ __('messages.edit') }}
                </button>
                <button class="btn-delete" title="{{ __('messages.delete') }}" onclick="showDeleteConfirmation(${role.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.delete') }}
                </button>
            </div>
            ` : ''}
        `;
    }
}

// Создаем объект с переводами разрешений
window.permissionsLabels = {
    'dashboard': '{{ __('messages.permission_dashboard') }}',
    'clients': '{{ __('messages.permission_clients') }}',
    'appointments': '{{ __('messages.permission_appointments') }}',
    'analytics': '{{ __('messages.permission_analytics') }}',
    'warehouse': '{{ __('messages.permission_warehouse') }}',
    'purchases': '{{ __('messages.permission_purchases') }}',
    'sales': '{{ __('messages.permission_sales') }}',
    'expenses': '{{ __('messages.permission_expenses') }}',
    'inventory': '{{ __('messages.permission_inventory') }}',
    'services': '{{ __('messages.permission_services') }}',
    'products': '{{ __('messages.permission_products') }}',
    'product-categories': '{{ __('messages.permission_product_categories') }}',
    'product-brands': '{{ __('messages.permission_product_brands') }}',
    'suppliers': '{{ __('messages.permission_suppliers') }}',
    'client-types': '{{ __('messages.permission_client_types') }}',
    'client.users': '{{ __('messages.permission_client_users') }}',
    'roles': '{{ __('messages.permission_roles') }}',
    'settings': '{{ __('messages.permission_settings') }}',
    'email-templates': '{{ __('messages.permission_email_templates') }}',
    'security': '{{ __('messages.permission_security') }}',
    'analytics.clients': '{{ __('messages.permission_analytics_clients') }}',
    'analytics.turnover': '{{ __('messages.permission_analytics_turnover') }}',
    'settings.manage': '{{ __('messages.permission_settings_manage') }}',
    'support.manage': '{{ __('messages.permission_support_manage') }}',
    'notifications.manage': '{{ __('messages.permission_notifications_manage') }}'
};
let editingRoleId = null;
let currentDeleteRoleRow = null;
let currentDeleteRoleId = null;

// Открытие модалки для редактирования роли (AJAX)
function openEditRoleModal(id) {
    fetch(`/roles/${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.role) {
                window.showNotification('error', data.message || '{{ __('messages.error_loading_role') }}');
                return;
            }
            const role = data.role;
            editingRoleId = id;
            document.getElementById('roleModalTitle').textContent = '{{ __('messages.edit_role') }}';
            document.getElementById('roleSelect').value = role.name;
            document.getElementById('roleSelect').disabled = true;
            document.querySelectorAll('.permissions-list input[type="checkbox"]').forEach(cb => {
                cb.checked = role.permissions.includes(cb.value);
            });
            document.getElementById('roleModal').style.display = 'block';
        })
        .catch(() => {
            window.showNotification('error', '{{ __('messages.error_loading_role') }}');
        });
}

// Открытие модалки для добавления роли
function openRoleModal() {
    editingRoleId = null;
    document.getElementById('roleModalTitle').textContent = '{{ __('messages.add_role') }}';
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
    submitBtn.innerHTML = '<span class="loader"></span> {{ __('messages.saving') }}...';
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
                updateRoleRow(data.role);
                // Форсируем обновление: повторно получаем роль с сервера и обновляем строку
                fetch(`/roles/${editingRoleId}`)
                    .then(res => res.json())
                    .then(fresh => {
                        if (fresh.success && fresh.role) {
                            updateRoleRow(fresh.role);
                        }
                    });
                window.showNotification('success', '{{ __('messages.role_successfully_updated') }}');
            } else {
                addRoleRow(data.role, perms, label);
                window.showNotification('success', '{{ __('messages.role_successfully_added') }}');
            }
            closeRoleModal();
            this.reset();
        } else {
            window.showNotification('error', data.message || '{{ __('messages.error') }}');
        }
    })
    .finally(() => {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
};

function addRoleRow(role, perms, label) {
    const tbody = document.querySelector('.clients-table tbody');
    // Удаляем строку 'Пока нет данных...'
    const emptyRow = tbody.querySelector('tr td[colspan]');
    if (emptyRow && emptyRow.textContent.includes('{{ __('messages.no_data_yet_add_first_role') }}')) {
        emptyRow.parentElement.remove();
    }
    const tr = document.createElement('tr');
    tr.id = 'role-' + String(role.id);
    tr.setAttribute('data-name', role.name);
    tr.setAttribute('data-perms', perms.join(','));
    tr.innerHTML = `
        <td>${getRoleLabel(role.name)}</td>
        <td>${perms.length ? perms.map(p => getPermissionLabel(p)).join(', ') : '—'}</td>
        <td class="actions-cell" style="vertical-align: middle;">
            <button class="btn-edit" data-id="${role.id}" title="{{ __('messages.edit') }}">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828.793-.793z" />
                </svg>
            </button>
            <button class="btn-delete" data-id="${role.id}" title="{{ __('messages.delete') }}">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </td>
    `;
    tbody.insertBefore(tr, tbody.firstChild);

    // Создать мобильную карточку
    const rolesCards = document.getElementById('rolesCards');
    const noRolesMessage = rolesCards.querySelector('.no-roles-message');
    if (noRolesMessage) {
        noRolesMessage.remove();
    }
    
    const roleCard = document.createElement('div');
    roleCard.id = 'role-card-' + role.id;
    roleCard.className = 'role-card';
    
    const permNames = perms.map(p => getPermissionLabel(p));
    const roleType = role.name === 'admin' ? 'admin' : (role.is_system ? 'system' : 'custom');
    const roleTypeText = role.name === 'admin' || role.is_system ? '{{ __('messages.system_role') }}' : '{{ __('messages.custom_role') }}';
    
    roleCard.innerHTML = `
        <div class="role-card-header">
            <div class="role-main-info">
                <div class="role-icon">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="role-name">${getRoleLabel(role.name)}</div>
            </div>
            <div class="role-type">
                <span class="role-badge ${roleType}">${roleTypeText}</span>
            </div>
        </div>
        <div class="role-info">
            <div class="role-info-item">
                <div class="role-info-label">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.table_permissions') }}:
                </div>
                <div class="role-info-value">
                    ${permNames.length ? `
                        <div class="permissions-tags">
                            ${permNames.map(permName => `<span class="permission-tag">${permName}</span>`).join('')}
                        </div>
                    ` : `
                        <span class="no-permissions">{{ __('messages.no_permissions') }}</span>
                    `}
                </div>
            </div>
        </div>
        ${role.name !== 'admin' && !role.is_system ? `
        <div class="role-actions">
            <button class="btn-edit" title="{{ __('messages.edit') }}" onclick="openEditRoleModalFromCard(${role.id})">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                {{ __('messages.edit') }}
            </button>
            <button class="btn-delete" title="{{ __('messages.delete') }}" onclick="showDeleteConfirmation(${role.id})">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ __('messages.delete') }}
            </button>
        </div>
        ` : ''}
    `;
    rolesCards.insertBefore(roleCard, rolesCards.firstChild);
}

function updateRoleRow(role) {
    const tr = document.getElementById('role-' + String(role.id));
    if (!tr) {
        return;
    }
    const perms = role.permissions || [];
    tr.setAttribute('data-name', role.name);
    tr.setAttribute('data-perms', perms.join(','));
    tr.children[0].textContent = getRoleLabel(role.name);
    tr.children[1].textContent = perms.length ? perms.map(p => getPermissionLabel(p)).join(', ') : '—';
    
    // Обновить мобильную карточку
    updateRoleCard(role);
}

function getRoleLabel(name) {
    if (window.roleLabels && window.roleLabels[name]) {
        return window.roleLabels[name];
    }
    return name;
}

function getPermissionLabel(name) {
    if (window.permissionsLabels && window.permissionsLabels[name]) {
        return window.permissionsLabels[name];
    }
    return name;
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
    if (!currentDeleteRoleId) return;
    deleteRole(currentDeleteRoleId);
    document.getElementById('roleConfirmationModal').style.display = 'none';
    currentDeleteRoleRow = null;
    currentDeleteRoleId = null;
};

function onRoleSelectChange() {
    // label теперь не нужен, функция оставлена пустой
}

function toggleGroup(group) {
    const groupCheckbox = document.getElementById('group-' + group);
    const permsDiv = document.getElementById(group + '-permissions');
    if (groupCheckbox.checked) {
        permsDiv.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true);
    } else {
        permsDiv.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Исправляем названия ролей в мобильных карточках
    fixRoleNames();
    
    // Инициализация мобильной версии
    toggleMobileView();
    
    // Обработчик изменения размера окна
    window.addEventListener('resize', toggleMobileView);
    
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
        align-items: flex-start;
        margin-left: 0;
        padding-left: 0;
    }
    .permissions-list label {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
        font-weight: 400;
        white-space: nowrap;
        justify-content: flex-start;
    }
    .permissions-list input[type="checkbox"] {
        margin-right: 8px;
        margin-left: 0;
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