// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ РОЛЕЙ =====

// Используем переводы из Laravel вместо хардкода
// Переводы передаются из Blade template через window.roleTranslations и window.permissionTranslations

let editingRoleId = null;
let currentDeleteRoleRow = null;
let currentDeleteRoleId = null;

// Функция для исправления названий ролей в мобильных карточках
function fixRoleNames() {
    const roleNames = document.querySelectorAll('.role-name');
    roleNames.forEach(nameElement => {
        const roleKey = nameElement.getAttribute('data-role');
        if (roleKey && window.roleLabels[roleKey]) {
            nameElement.textContent = window.roleLabels[roleKey];
        }
    });
}

// Функции для мобильной версии
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const rolesCards = document.querySelector('.roles-cards');
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
    fetch(`/roles/${roleId}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(role => {
        openEditRoleModal(role);
    })
    .catch(() => window.showNotification('error', 'Ошибка при загрузке данных роли'));
}

// Функция для показа подтверждения удаления
function showDeleteConfirmation(roleId) {
    currentDeleteRoleId = roleId;
    toggleModal('roleConfirmationModal', true);
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
        const roleLabel = window.roleTranslations && window.roleTranslations[role.name] ? window.roleTranslations[role.name] : role.name;
        const permissionsText = translatePermissions(role.permissions);
        
        roleCard.innerHTML = `
            <div class="role-card-header">
                <div class="role-main-info">
                    <div class="role-icon">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="role-name" data-role="${role.name}">${roleLabel}</div>
                </div>
                <div class="role-type">
                    <span class="role-badge ${role.name === 'admin' ? 'admin' : 'custom'}">${role.name === 'admin' ? 'Системная' : 'Пользовательская'}</span>
                </div>
            </div>
            <div class="role-info">
                <div class="role-info-item">
                    <div class="role-info-label">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                        </svg>
                        Разрешения:
                    </div>
                    <div class="role-info-value">${permissionsText}</div>
                </div>
            </div>
            ${role.name !== 'admin' ? `
            <div class="role-actions">
                <button class="btn-edit" title="Редактировать" onclick="openEditRoleModalFromCard(${role.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
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

function openRoleModal() {
    toggleModal('roleModal', true);
    document.getElementById('roleModalTitle').textContent = 'Добавить роль';
    document.getElementById('roleForm').reset();
    editingRoleId = null;
}

function closeRoleModal() {
    toggleModal('roleModal', false);
}

function openEditRoleModal(role) {
    editingRoleId = role.id;
    document.getElementById('roleModalTitle').textContent = 'Редактировать роль';
    document.getElementById('roleSelect').value = role.name;

    
    // Сбрасываем все чекбоксы
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Отмечаем разрешения роли
    if (role.permissions && Array.isArray(role.permissions)) {

        role.permissions.forEach(permissionName => {
            const checkbox = document.querySelector(`input[name="permissions[]"][value="${permissionName}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }
    
    toggleModal('roleModal', true);
}

function closeEditRoleModal() {
    toggleModal('roleModal', false);
}

// Функция для обработки изменения выбора роли
function onRoleSelectChange() {
    const roleSelect = document.getElementById('roleSelect');
    const selectedRole = roleSelect.value;
    
    // Сбрасываем все чекбоксы при смене роли
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}



// Функция для перевода названий разрешений
function translatePermission(permissionName) {
    // Сначала проверяем, есть ли прямой перевод в window.permissionTranslations
    if (window.permissionTranslations && window.permissionTranslations[permissionName]) {
        return window.permissionTranslations[permissionName];
    }
    
    // Если нет, возвращаем оригинальное название
    return permissionName;
}

// Функция для перевода массива разрешений
function translatePermissions(permissions) {
    if (!permissions || !Array.isArray(permissions)) return 'Нет разрешений';
    return permissions.map(translatePermission).join(', ');
}

// Функция для переключения групп разрешений
function toggleGroup(groupName) {
    const checkboxes = document.querySelectorAll(`input[name="permissions[]"][data-group="${groupName}"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}

// Функция для показа уведомлений


// Функция инициализации поиска
function initSearch() {
    const searchInput = document.getElementById('roleSearchInput');
    const searchInputMobile = document.getElementById('roleSearchInputMobile');
    
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

// --- AJAX добавление/редактирование роли ---
document.addEventListener('DOMContentLoaded', function() {
    const roleForm = document.getElementById('roleForm');
    if (roleForm) {
        roleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(roleForm);
            
            // Отладка: выводим данные формы
    
            for (let [key, value] of formData.entries()) {

            }
            
            // Для PUT запроса добавляем _method
            if (editingRoleId) {
                formData.append('_method', 'PUT');
            }
            

            
            const url = editingRoleId ? `/roles/${editingRoleId}` : '/roles';
            const method = editingRoleId ? 'POST' : 'POST'; // Всегда используем POST с _method
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {

                if (!response.ok) {
                    return response.json().then(err => {

                        return Promise.reject(err);
                    });
                }
                return response.json().then(data => {

                    return data;
                });
            })
            .then(data => {

                if (data.success) {
                    const role = data.role;

                    
                    if (editingRoleId) {
                        // Обновляем существующую роль
                        updateRoleCard(role);
                        

                        
                        // Обновляем строку в таблице
                        const tr = document.getElementById(`role-${role.id}`);
                        if (tr) {
                            const roleLabel = window.roleTranslations && window.roleTranslations[role.name] ? window.roleTranslations[role.name] : role.name;
                            const permissionsText = translatePermissions(role.permissions);
                            
                            tr.innerHTML = `
                                <td>${roleLabel}</td>
                                <td>${permissionsText}</td>
                                <td class="actions-cell">
                                    ${role.name !== 'admin' ? `
                                    <button class="btn-edit" data-id="${role.id}" title="Редактировать">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <button class="btn-delete" data-id="${role.id}" title="Удалить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    ` : ''}
                                </td>
                            `;
                        }
                        
                        window.showNotification('success', 'Роль успешно обновлена');
                    } else {
                        // Добавляем новую роль
                        const tbody = document.getElementById('rolesTableBody');
                        const tr = document.createElement('tr');
                        tr.id = `role-${role.id}`;
                        
                        const roleLabel = window.roleTranslations && window.roleTranslations[role.name] ? window.roleTranslations[role.name] : role.name;
                        const permissionsText = translatePermissions(role.permissions);
                        
                        tr.innerHTML = `
                            <td>${roleLabel}</td>
                            <td>${permissionsText}</td>
                            <td class="actions-cell">
                                ${role.name !== 'admin' ? `
                                <button class="btn-edit" data-id="${role.id}" title="Редактировать">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <button class="btn-delete" data-id="${role.id}" title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                ` : ''}
                            </td>
                        `;
                        tbody.appendChild(tr);

                        // Создаем мобильную карточку
                        const rolesCards = document.querySelector('.roles-cards');
                        const roleCard = document.createElement('div');
                        roleCard.id = `role-card-${role.id}`;
                        roleCard.className = 'role-card';
                        
                        roleCard.innerHTML = `
                            <div class="role-card-header">
                                <div class="role-main-info">
                                    <div class="role-icon">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="role-name" data-role="${role.name}">${roleLabel}</div>
                                </div>
                                <div class="role-type">
                                    <span class="role-badge ${role.name === 'admin' ? 'admin' : 'custom'}">${role.name === 'admin' ? 'Системная' : 'Пользовательская'}</span>
                                </div>
                            </div>
                            <div class="role-info">
                                <div class="role-info-item">
                                    <div class="role-info-label">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                        </svg>
                                        Разрешения:
                                    </div>
                                    <div class="role-info-value">${permissionsText}</div>
                                </div>
                            </div>
                            ${role.name !== 'admin' ? `
                            <div class="role-actions">
                                <button class="btn-edit" title="Редактировать" onclick="openEditRoleModalFromCard(${role.id})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
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
                        rolesCards.appendChild(roleCard);
                        
                        window.showNotification('success', 'Роль успешно добавлена');
                    }
                    
                    closeRoleModal();
                } else {
                    window.showNotification('error', data.message || 'Ошибка при сохранении роли');
                }
            })
            .catch(err => {
                window.showNotification('error', 'Ошибка при сохранении роли');
            });
        });
    }
});

// Обработчики для кнопок удаления
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-delete')) {
        const tr = e.target.closest('tr');
        if (tr) {
            const roleId = tr.id.split('-')[1];
            currentDeleteRoleId = roleId;
            toggleModal('roleConfirmationModal', true);
        }
    }
    
    if (e.target.closest('.btn-edit')) {
        const tr = e.target.closest('tr');
        if (tr) {
            const roleId = tr.id.split('-')[1];
            fetch(`/roles/${roleId}/edit`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(role => {
                openEditRoleModal(role);
            })
            .catch(() => window.showNotification('error', 'Ошибка при загрузке данных роли'));
        }
    }
});

// Обработчики для модального окна подтверждения удаления
document.addEventListener('DOMContentLoaded', function() {
    const cancelRoleDelete = document.getElementById('cancelDeleteRole');
    const confirmRoleDelete = document.getElementById('confirmDeleteRole');
    
    if (cancelRoleDelete) {
        cancelRoleDelete.addEventListener('click', function() {
            closeRoleConfirmationModal();
        });
    }
    
    if (confirmRoleDelete) {
        confirmRoleDelete.addEventListener('click', function() {
            if (currentDeleteRoleId) {
                deleteRole(currentDeleteRoleId);
            }
            closeRoleConfirmationModal();
        });
    }
});

// Функция для закрытия модального окна подтверждения удаления
function closeRoleConfirmationModal() {
    toggleModal('roleConfirmationModal', false);
    currentDeleteRoleId = null;
}

// Функция для переключения модальных окон
function toggleModal(modalId, show = true) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    if (show) {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    } else {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Исправляем названия ролей в мобильных карточках
    fixRoleNames();
    
    // Инициализация мобильной версии
    toggleMobileView();
    
    // Обработчик изменения размера окна
    window.addEventListener('resize', toggleMobileView);
    

    
    // Инициализация поиска
    initSearch();
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