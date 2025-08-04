// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ РОЛЕЙ =====

// Создаем объект с переводами ролей
window.roleLabels = {
    'admin': 'Администратор',
    'manager': 'Менеджер',
    'master': 'Мастер',
    'hairdresser': 'Парикмахер',
    'cosmetologist': 'Косметолог',
    'nailmaster': 'Мастер маникюра',
    'makeup': 'Визажист',
    'browlash': 'Мастер бровей и ресниц',
    'massage': 'Массажист',
    'stylist': 'Стилист',
    'barber': 'Барбер',
    'senior_barber': 'Старший барбер',
    'shaving': 'Мастер бритья',
    'intern': 'Стажер',
    'seller': 'Продавец',
    'storekeeper': 'Кладовщик'
};

// Создаем объект с переводами разрешений
window.permissionsLabels = {
    'dashboard': 'Панель управления',
    'clients': 'Клиенты',
    'appointments': 'Записи',
    'analytics': 'Аналитика',
    'warehouse': 'Склад',
    'purchases': 'Закупки',
    'sales': 'Продажи',
    'expenses': 'Расходы',
    'inventory': 'Инвентаризация',
    'services': 'Услуги',
    'products': 'Товары',
    'product-categories': 'Категории товаров',
    'product-brands': 'Бренды товаров',
    'suppliers': 'Поставщики',
    'client-types': 'Типы клиентов',
    'client.users': 'Пользователи клиента',
    'roles': 'Роли',
    'settings': 'Настройки',
    'email-templates': 'Шаблоны email',
    'security': 'Безопасность',
    'analytics.clients': 'Аналитика клиентов',
    'analytics.turnover': 'Аналитика оборота',
    'settings.manage': 'Управление настройками',
    'support.manage': 'Управление поддержкой',
    'notifications.manage': 'Управление уведомлениями'
};

let editingRoleId = null;
let currentDeleteRoleRow = null;
let currentDeleteRoleId = null;

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
                window.showNotification('success', 'Роль успешно удалена');
            }, 300);
        } else {
            if (roleCard) roleCard.classList.remove('row-deleting');
            if (roleRow) roleRow.classList.remove('row-deleting');
            window.showNotification('error', data.message || 'Ошибка при удалении роли');
        }
    })
    .catch(error => {
        if (roleCard) roleCard.classList.remove('row-deleting');
        if (roleRow) roleRow.classList.remove('row-deleting');
        window.showNotification('error', 'Ошибка при удалении роли');
    });
}

// Функция для обновления карточки роли
function updateRoleCard(role) {
    const roleCard = document.getElementById(`role-card-${role.id}`);
    if (roleCard) {
        const perms = role.permissions || [];
        const permNames = perms.map(p => getPermissionLabel(p));
        
        const roleType = role.name === 'admin' ? 'admin' : (role.is_system ? 'system' : 'custom');
        const roleTypeText = role.name === 'admin' || role.is_system ? 'Системная роль' : 'Пользовательская роль';
        
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
                        Разрешения:
                    </div>
                    <div class="role-info-value">
                        ${permNames.length ? `
                            <div class="permissions-tags">
                                ${permNames.map(permName => `<span class="permission-tag">${permName}</span>`).join('')}
                            </div>
                        ` : `
                            <span class="no-permissions">Нет разрешений</span>
                        `}
                    </div>
                </div>
            </div>
            ${role.name !== 'admin' && !role.is_system ? `
            <div class="role-actions">
                <button class="btn-edit" title="Редактировать" onclick="openEditRoleModalFromCard(${role.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828.793-.793z" />
                    </svg>
                    Редактировать
                </button>
                <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${role.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Удалить
                </button>
            </div>
            ` : ''}
        `;
    }
}

// Открытие модалки для редактирования роли (AJAX)
function openEditRoleModal(id) {
    fetch(`/roles/${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.role) {
                window.showNotification('error', data.message || 'Ошибка при загрузке роли');
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
            window.showNotification('error', 'Ошибка при загрузке роли');
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
function initRoleForm() {
    const roleForm = document.getElementById('roleForm');
    if (roleForm) {
        roleForm.onsubmit = function(e) {
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
                        updateRoleRow(data.role);
                        // Форсируем обновление: повторно получаем роль с сервера и обновляем строку
                        fetch(`/roles/${editingRoleId}`)
                            .then(res => res.json())
                            .then(fresh => {
                                if (fresh.success && fresh.role) {
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
    }
}

function addRoleRow(role, perms, label) {
    const tbody = document.querySelector('.clients-table tbody');
    // Удаляем строку 'Пока нет данных...'
    const emptyRow = tbody.querySelector('tr td[colspan]');
    if (emptyRow && emptyRow.textContent.includes('Пока нет данных, добавьте первую роль')) {
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
    const roleTypeText = role.name === 'admin' || role.is_system ? 'Системная роль' : 'Пользовательская роль';
    
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
                    Разрешения:
                </div>
                <div class="role-info-value">
                    ${permNames.length ? `
                        <div class="permissions-tags">
                            ${permNames.map(permName => `<span class="permission-tag">${permName}</span>`).join('')}
                        </div>
                    ` : `
                        <span class="no-permissions">Нет разрешений</span>
                    `}
                </div>
            </div>
        </div>
        ${role.name !== 'admin' && !role.is_system ? `
        <div class="role-actions">
            <button class="btn-edit" title="Редактировать" onclick="openEditRoleModalFromCard(${role.id})">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828.793-.793z" />
                </svg>
                Редактировать
            </button>
            <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${role.id})">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Удалить
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
function initRoleEventListeners() {
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

    // Обработчики для модального окна подтверждения удаления
    const cancelDeleteBtn = document.getElementById('cancelDeleteRole');
    const confirmDeleteBtn = document.getElementById('confirmDeleteRole');
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.onclick = function() {
            document.getElementById('roleConfirmationModal').style.display = 'none';
            currentDeleteRoleRow = null;
            currentDeleteRoleId = null;
        };
    }
    
    if (confirmDeleteBtn) {
        confirmDeleteBtn.onclick = function() {
            if (!currentDeleteRoleId) return;
            deleteRole(currentDeleteRoleId);
            document.getElementById('roleConfirmationModal').style.display = 'none';
            currentDeleteRoleRow = null;
            currentDeleteRoleId = null;
        };
    }
}

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

// Функция инициализации поиска
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchInputMobile = document.getElementById('searchInputMobile');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            handleSearch(this.value);
        });
    }
    
    if (searchInputMobile) {
        searchInputMobile.addEventListener('input', function() {
            handleSearch(this.value);
        });
    }
}

// Функция обработки поиска
function handleSearch(searchTerm) {
    const tableRows = document.querySelectorAll('.clients-table tbody tr');
    const roleCards = document.querySelectorAll('.role-card');
    
    searchTerm = searchTerm.toLowerCase().trim();
    
    // Поиск в таблице
    tableRows.forEach(row => {
        const roleName = row.querySelector('td:first-child').textContent.toLowerCase();
        const permissions = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        if (roleName.includes(searchTerm) || permissions.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Поиск в мобильных карточках
    roleCards.forEach(card => {
        const roleName = card.querySelector('.role-name').textContent.toLowerCase();
        const permissions = card.querySelector('.role-info-value').textContent.toLowerCase();
        
        if (roleName.includes(searchTerm) || permissions.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Исправляем названия ролей в мобильных карточках
    fixRoleNames();
    
    // Инициализация мобильной версии
    toggleMobileView();
    
    // Обработчик изменения размера окна
    window.addEventListener('resize', toggleMobileView);
    
    // Назначаем обработчики на кнопки добавления роли
    const btnAddRole = document.getElementById('btnAddRole');
    const btnAddRoleMobile = document.getElementById('btnAddRoleMobile');
    
    if (btnAddRole) {
        btnAddRole.onclick = openRoleModal;
    }
    
    if (btnAddRoleMobile) {
        btnAddRoleMobile.onclick = openRoleModal;
    }
    
    // Инициализация поиска
    initSearch();
    
    // Инициализация формы ролей
    initRoleForm();
    
    // Инициализация обработчиков событий
    initRoleEventListeners();
});

// Экспорт функций для глобального использования
window.closeRoleModal = closeRoleModal;
window.openEditRoleModal = openEditRoleModal;
window.openRoleModal = openRoleModal;
window.openEditRoleModalFromCard = openEditRoleModalFromCard;
window.showDeleteConfirmation = showDeleteConfirmation;
window.deleteRole = deleteRole;
window.onRoleSelectChange = onRoleSelectChange;
window.toggleGroup = toggleGroup; 