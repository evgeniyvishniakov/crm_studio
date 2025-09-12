// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ ПОЛЬЗОВАТЕЛЕЙ =====

// Функция для получения перевода роли
function getRoleLabel(roleKey) {
    // Используем переводы из файлов переводов, переданные из PHP
    return window.roles && window.roles[roleKey] ? window.roles[roleKey] : roleKey;
}



// Функции для мобильной версии
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const usersCards = document.getElementById('usersCards');
    const mobilePagination = document.getElementById('mobileUsersPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (usersCards) usersCards.style.display = 'block';
        if (mobilePagination) mobilePagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (usersCards) usersCards.style.display = 'none';
        if (mobilePagination) mobilePagination.style.display = 'none';
    }
}

// Функция для открытия модального окна редактирования из карточки
function openEditUserModalFromCard(userId) {
    fetch(`/users/${userId}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        openEditUserModal(data.user);
    })
    .catch(() => window.showNotification('error', 'Ошибка при загрузке данных пользователя'));
}

// Функция для показа подтверждения удаления
function showDeleteConfirmation(userId) {
    currentDeleteUserId = userId;
    toggleModal('userConfirmationModal', true);
}

// Функция для закрытия модального окна подтверждения удаления
function closeUserConfirmationModal() {
    toggleModal('userConfirmationModal', false);
    currentDeleteUserId = null;
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

// Функция для удаления пользователя
function deleteUser(userId) {
    const userCard = document.getElementById(`user-card-${userId}`);
    const userRow = document.getElementById(`user-${userId}`);
    
    if (userCard) userCard.classList.add('row-deleting');
    if (userRow) userRow.classList.add('row-deleting');
    
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
                if (userCard) userCard.remove();
                if (userRow) userRow.remove();
                window.showNotification('success', window.translations?.user_successfully_deleted || 'Пользователь успешно удален');
            }, 300);
        } else {
            if (userCard) userCard.classList.remove('row-deleting');
            if (userRow) userRow.classList.remove('row-deleting');
            window.showNotification('error', data.message || 'Ошибка при удалении пользователя');
        }
    })
    .catch(error => {
        if (userCard) userCard.classList.remove('row-deleting');
        if (userRow) userRow.classList.remove('row-deleting');
        window.showNotification('error', 'Ошибка при удалении пользователя');
    });
}

// Функция для обновления карточки пользователя
function updateUserCard(user) {
    const userCard = document.getElementById(`user-card-${user.id}`);
    if (userCard) {
        const avatarHtml = user.avatar 
            ? `<img src="/storage/${user.avatar}" alt="${user.name}" class="user-avatar-img">`
            : `<div class="user-avatar-placeholder">${user.name.charAt(0)}</div>`;
        
        userCard.innerHTML = `
            <div class="user-card-header">
                <div class="user-main-info">
                    <div class="user-avatar">
                        ${avatarHtml}
                    </div>
                    <div class="user-name">${user.name}</div>
                </div>
                <div class="user-status">
                    <span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? (window.translations?.active || 'Активный') : (window.translations?.inactive || 'Неактивный')}</span>
                </div>
            </div>
            <div class="user-info">
                <div class="user-info-item">
                    <div class="user-info-label">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        Email/Логин:
                    </div>
                    <div class="user-info-value">${user.email ? user.email : user.username}</div>
                </div>
                <div class="user-info-item">
                    <div class="user-info-label">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Роль:
                    </div>
                    <div class="user-info-value">${getRoleLabel(user.role)}</div>
                </div>
                <div class="user-info-item">
                    <div class="user-info-label">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                        </svg>
                        Дата регистрации:
                    </div>
                    <div class="user-info-value">${user.registered_at ? formatDateTime(user.registered_at) : ''}</div>
                </div>
            </div>
            ${user.role !== 'admin' ? `
            <div class="user-actions">
                <button class="btn-edit" title="${window.translations?.edit || 'Редактировать'}" onclick="openEditUserModalFromCard(${user.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    ${window.translations?.edit || 'Редактировать'}
                </button>
                <button class="btn-delete" title="${window.translations?.delete || 'Удалить'}" onclick="showDeleteConfirmation(${user.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    ${window.translations?.delete || 'Удалить'}
                </button>
            </div>
            ` : ''}
        `;
    }
}

function openUserModal() {
    // Очищаем все поля формы перед открытием
    document.getElementById('addUserForm').reset();
    // Очищаем ошибки
    clearErrors('addUserForm');
    toggleModal('addUserModal', true);
}

function closeUserModal() {
    toggleModal('addUserModal', false);
    // Очищаем все поля формы
    document.getElementById('addUserForm').reset();
    // Очищаем ошибки
    clearErrors('addUserForm');
}

function generateUserPassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    let pass = '';
    for (let i = 0; i < 10; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('userPassword').value = pass;
}

// Функция для генерации пароля при редактировании
function generateEditUserPassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    let pass = '';
    for (let i = 0; i < 10; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('editUserPassword').value = pass;
}

// Функция для переключения видимости пароля
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const button = event.target.closest('button');
    
    if (field && button) {
        if (field.type === 'password') {
            field.type = 'text';
            // Меняем иконку на "скрыть"
            button.innerHTML = `
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor" style="width: 16px; height: 16px;">
                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                </svg>
            `;
            button.title = 'Скрыть пароль';
        } else {
            field.type = 'password';
            // Возвращаем иконку "показать"
            button.innerHTML = `
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor" style="width: 16px; height: 16px;">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                </svg>
            `;
            button.title = 'Показать пароль';
        }
    }
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
            fetch("/users", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
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
                    // Добавить пользователя в таблицу и создать карточку
                    const user = data.user;
                    const tbody = document.getElementById('usersTableBody');
                    const tr = document.createElement('tr');
                    tr.id = 'user-' + user.id;
                    
                    const avatarHtml = user.avatar 
                        ? `<img src="/storage/${user.avatar}" alt="${user.name}" class="user-avatar">`
                        : `<div class="user-avatar-placeholder">${user.name.charAt(0)}</div>`;
                    
                    tr.innerHTML = `
                        <td>
                            <div class="client-info">
                                <div class="client-avatar">
                                    ${avatarHtml}
                                </div>
                                <div class="client-details">
                                    <div class="client-name">${user.name}</div>
                                </div>
                            </div>
                        </td>
                        <td>${user.email ? user.email : user.username}</td>
                        <td>${getRoleLabel(user.role)}</td>
                        <td><span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? (window.translations?.active || 'Активный') : (window.translations?.inactive || 'Неактивный')}</span></td>
                        <td>${user.registered_at ? formatDateTime(user.registered_at) : ''}</td>
                        <td class="actions-cell">
                            <button class="btn-edit" title="${window.translations?.edit || 'Редактировать'}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button class="btn-delete" title="${window.translations?.delete || 'Удалить'}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);

                    // Создать мобильную карточку
                    const usersCards = document.getElementById('usersCards');
                    const userCard = document.createElement('div');
                    userCard.id = 'user-card-' + user.id;
                    userCard.className = 'user-card';
                    
                    const cardAvatarHtml = user.avatar 
                        ? `<img src="/storage/${user.avatar}" alt="${user.name}" class="user-avatar-img">`
                        : `<div class="user-avatar-placeholder">${user.name.charAt(0)}</div>`;
                    
                    userCard.innerHTML = `
                        <div class="user-card-header">
                            <div class="user-main-info">
                                <div class="user-avatar">
                                    ${cardAvatarHtml}
                                </div>
                                <div class="user-name">${user.name}</div>
                            </div>
                            <div class="user-status">
                                <span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? (window.translations?.active || 'Активный') : (window.translations?.inactive || 'Неактивный')}</span>
                            </div>
                        </div>
                        <div class="user-info">
                            <div class="user-info-item">
                                <div class="user-info-label">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    Email/Логин:
                                </div>
                                <div class="user-info-value">${user.email ? user.email : user.username}</div>
                            </div>
                            <div class="user-info-item">
                                <div class="user-info-label">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    Роль:
                                </div>
                                <div class="user-info-value">${getRoleLabel(user.role)}</div>
                            </div>
                            <div class="user-info-item">
                                <div class="user-info-label">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                    </svg>
                                    Дата регистрации:
                                </div>
                                <div class="user-info-value">${user.registered_at ? formatDateTime(user.registered_at) : ''}</div>
                            </div>
                        </div>
                        ${user.role !== 'admin' ? `
                        <div class="user-actions">
                            <button class="btn-edit" title="${window.translations?.edit || 'Редактировать'}" onclick="openEditUserModalFromCard(${user.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                ${window.translations?.edit || 'Редактировать'}
                            </button>
                            <button class="btn-delete" title="${window.translations?.delete || 'Удалить'}" onclick="showDeleteConfirmation(${user.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                ${window.translations?.delete || 'Удалить'}
                            </button>
                        </div>
                        ` : ''}
                    `;
                    usersCards.appendChild(userCard);

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
                    window.showNotification('success', window.translations?.user_successfully_added || 'Пользователь успешно добавлен');
                } else {
                    window.showNotification('error', data.message || 'Ошибка при добавлении пользователя');
                }
            })
            .catch(err => {
                if (err.errors) {
                    showErrors(err.errors, 'addUserForm');
                    window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    window.showNotification('error', 'Ошибка при добавлении пользователя');
                }
            });
        });
    }
});

// Открытие/закрытие модального окна редактирования
function openEditUserModal(user) {
    document.getElementById('editUserId').value = user.id || '';
    document.getElementById('editUserName').value = user.name || '';
    document.getElementById('editUserUsername').value = user.username || '';
    document.getElementById('editUserEmail').value = user.email || '';
    document.getElementById('editUserRole').value = user.role || '';
    document.getElementById('editUserStatus').value = user.status || '';
    document.getElementById('editUserPassword').value = ''; // Очищаем поле пароля

    // Отображение текущей аватарки
    const currentAvatarDiv = document.getElementById('currentAvatar');
    const currentAvatarImg = document.getElementById('currentAvatarImg');
    
    if (user.avatar) {
        currentAvatarImg.src = `/storage/${user.avatar}`;
        currentAvatarDiv.style.display = 'block';
    } else {
        currentAvatarDiv.style.display = 'none';
    }

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
            note.textContent = 'Роль и логин главного администратора не могут быть изменены';
            document.getElementById('editUserForm').appendChild(note);
        }
    } else {
        document.getElementById('editUserUsername').removeAttribute('readonly');
        document.getElementById('editUserRole').removeAttribute('disabled');
        document.querySelector('#editUserForm .btn-submit').removeAttribute('disabled');
        let note = document.getElementById('adminEditNote');
        if (note) note.remove();
    }

    toggleModal('editUserModal', true);
}

function closeEditUserModal() {
    toggleModal('editUserModal', false);
    // Очищаем все поля формы
    document.getElementById('editUserForm').reset();
    // Очищаем ошибки
    clearErrors('editUserForm');
    // Скрываем текущую аватарку
    const currentAvatarDiv = document.getElementById('currentAvatar');
    if (currentAvatarDiv) {
        currentAvatarDiv.style.display = 'none';
    }
    // Убираем блокировку для admin
    const usernameField = document.getElementById('editUserUsername');
    const roleField = document.getElementById('editUserRole');
    const submitButton = document.querySelector('#editUserForm .btn-submit');
    if (usernameField) usernameField.removeAttribute('readonly');
    if (roleField) roleField.removeAttribute('disabled');
    if (submitButton) submitButton.removeAttribute('disabled');
    // Убираем поясняющее сообщение
    const adminEditNote = document.getElementById('adminEditNote');
    if (adminEditNote) adminEditNote.remove();
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
                window.showNotification('error', 'Главный администратор не может быть отредактирован');
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
            .then(data => {
                openEditUserModal(data.user);
            })
            .catch(() => window.showNotification('error', 'Ошибка при загрузке данных пользователя'));
        }
    }
    // Удаление пользователя (открытие модалки)
    if (e.target.closest('.btn-delete')) {
        const tr = e.target.closest('tr');
        if (tr) {
            const userId = tr.id.split('-')[1];
            currentDeleteUserId = userId;
            toggleModal('userConfirmationModal', true);
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Инициализация мобильной версии
    toggleMobileView();
    
    // Обработчик изменения размера окна
    window.addEventListener('resize', toggleMobileView);
    
    // Обработка сохранения редактирования пользователя
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const userId = document.getElementById('editUserId').value;
            const formData = new FormData(editUserForm);
            
            // Проверяем, есть ли файл аватарки
            const avatarFile = document.getElementById('editUserAvatar').files[0];
            
            if (avatarFile) {
                // Если есть файл, сначала загружаем аватарку
                const avatarFormData = new FormData();
                avatarFormData.append('avatar', avatarFile);
                avatarFormData.append('_token', document.querySelector('input[name="_token"]').value);
                
                fetch(`/users/${userId}/avatar`, {
                    method: 'POST',
                    body: avatarFormData
                })
                .then(res => res.json())
                .then(avatarData => {
                    if (avatarData.success) {
                        // После успешной загрузки аватарки обновляем остальные данные
                        updateUserData(userId, formData);
                    } else {
                        window.showNotification('error', avatarData.message || 'Ошибка при загрузке аватарки');
                    }
                })
                .catch(() => window.showNotification('error', 'Ошибка при загрузке аватарки'));
            } else {
                // Если нет файла, просто обновляем данные
                updateUserData(userId, formData);
            }
        });
    }
    
                    // Функция для обновления данных пользователя
                function updateUserData(userId, formData) {
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
                            // Обновить строку в таблице и карточку
                            const user = data.user;
                            const tr = document.getElementById('user-' + user.id);
                            if (tr) {
                                const avatarHtml = user.avatar 
                                    ? `<img src="/storage/${user.avatar}" alt="${user.name}" class="user-avatar">`
                                    : `<div class="user-avatar-placeholder">${user.name.charAt(0)}</div>`;
                                
                                tr.innerHTML = `
                                    <td>
                                        <div class="client-info">
                                            <div class="client-avatar">
                                                ${avatarHtml}
                                            </div>
                                            <div class="client-details">
                                                <div class="client-name">${user.name}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${user.email ? user.email : user.username}</td>
                                    <td>${getRoleLabel(user.role)}</td>
                                    <td><span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? (window.translations?.active || 'Активный') : (window.translations?.inactive || 'Неактивный')}</span></td>
                                    <td>${user.registered_at ? formatDateTime(user.registered_at) : ''}</td>
                        <td class="actions-cell">
                            <button class="btn-edit" title="${window.translations?.edit || 'Редактировать'}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button class="btn-delete" title="${window.translations?.delete || 'Удалить'}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    `;
                }
                
                // Обновить мобильную карточку
                updateUserCard(user);
                closeEditUserModal();
                
                // Проверяем, был ли изменен пароль
                const passwordField = document.getElementById('editUserPassword');
                const passwordChanged = passwordField && passwordField.value.trim() !== '';
                
                let message = window.translations?.user_successfully_updated || 'Пользователь успешно обновлен';
                if (passwordChanged) {
                    message += ' (пароль изменен)';
                }
                
                window.showNotification('success', message);
            } else {
                window.showNotification('error', data.message || 'Ошибка при обновлении пользователя');
            }
        })
        .catch(() => window.showNotification('error', 'Ошибка при обновлении пользователя'));
    }
});

let currentDeleteUserId = null;

document.getElementById('cancelUserDelete').addEventListener('click', function() {
    closeUserConfirmationModal();
});

document.getElementById('confirmUserDelete').addEventListener('click', function() {
    if (currentDeleteUserId) {
        deleteUser(currentDeleteUserId);
    }
    closeUserConfirmationModal();
});

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

// ===== ФУНКЦИИ ДЛЯ ЗАГРУЗКИ АВАТАРКИ АДМИНИСТРАТОРА =====

// Открытие модального окна для загрузки аватарки администратора
function openAdminAvatarModal(userId) {
    document.getElementById('adminUserId').value = userId;
    
    // Получаем текущую аватарку пользователя
    const userRow = document.getElementById(`user-${userId}`);
    const userCard = document.getElementById(`user-card-${userId}`);
    let currentAvatarSrc = null;
    
    if (userRow) {
        // Ищем аватарку в таблице
        const avatarContainer = userRow.querySelector('.client-avatar');
        
        if (avatarContainer) {
            const avatarImg = avatarContainer.querySelector('.user-avatar');
            
            if (avatarImg && avatarImg.tagName === 'IMG' && avatarImg.src) {
                currentAvatarSrc = avatarImg.src;
            }
        }
    } else if (userCard) {
        // Ищем аватарку в мобильной карточке
        const avatarContainer = userCard.querySelector('.user-avatar');
        
        if (avatarContainer) {
            const avatarImg = avatarContainer.querySelector('.user-avatar-img');
            
            if (avatarImg && avatarImg.src) {
                currentAvatarSrc = avatarImg.src;
            }
        }
    }
    
    // Показываем текущую аватарку если она есть
    if (currentAvatarSrc && !currentAvatarSrc.includes('data:image/svg') && !currentAvatarSrc.includes('placeholder')) {
        const currentAvatarImg = document.getElementById('currentAvatarImg');
        const currentAvatarPreview = document.getElementById('currentAvatarPreview');
        const noAvatarPreview = document.getElementById('noAvatarPreview');
        
        if (currentAvatarImg) {
            currentAvatarImg.src = currentAvatarSrc;
            
            // Проверяем через небольшую задержку, не сбросился ли src
            setTimeout(() => {
                if (currentAvatarImg.src !== currentAvatarSrc) {
                    currentAvatarImg.src = currentAvatarSrc;
                }
            }, 50);
        }
        
        if (currentAvatarPreview) {
            currentAvatarPreview.style.display = 'block';
            currentAvatarPreview.style.visibility = 'visible';
        }
        
        if (noAvatarPreview) {
            noAvatarPreview.style.display = 'none';
            noAvatarPreview.style.visibility = 'hidden';
        }
    } else {
        const currentAvatarPreview = document.getElementById('currentAvatarPreview');
        const noAvatarPreview = document.getElementById('noAvatarPreview');
        
        if (currentAvatarPreview) {
            currentAvatarPreview.style.display = 'none';
        }
        
        if (noAvatarPreview) {
            noAvatarPreview.style.display = 'block';
        }
    }
    
    // Скрываем предварительный просмотр новой аватарки
    document.getElementById('newAvatarPreview').style.display = 'none';
    
    toggleModal('adminAvatarModal', true);
    
    // Принудительно обновляем отображение после открытия модального окна
    setTimeout(() => {
        if (currentAvatarSrc && !currentAvatarSrc.includes('data:image/svg') && !currentAvatarSrc.includes('placeholder')) {
            const currentAvatarPreview = document.getElementById('currentAvatarPreview');
            const noAvatarPreview = document.getElementById('noAvatarPreview');
            const currentAvatarImg = document.getElementById('currentAvatarImg');
            
            if (currentAvatarPreview) {
                currentAvatarPreview.style.display = 'block';
                currentAvatarPreview.style.visibility = 'visible';
                currentAvatarPreview.style.opacity = '1';
                currentAvatarPreview.style.height = 'auto';
                currentAvatarPreview.style.width = 'auto';
            }
            
            if (currentAvatarImg) {
                currentAvatarImg.style.display = 'block';
                currentAvatarImg.style.visibility = 'visible';
                currentAvatarImg.style.opacity = '1';
                
                // Попробуем создать новый элемент изображения
                const newImg = document.createElement('img');
                newImg.src = currentAvatarSrc;
                newImg.alt = 'Current Avatar';
                newImg.style.width = '120px';
                newImg.style.height = '120px';
                newImg.style.borderRadius = '50%';
                newImg.style.objectFit = 'cover';
                newImg.style.border = '3px solid #ddd';
                newImg.style.marginBottom = '10px';
                
                // Очищаем контейнер и добавляем новое изображение
                const title = window.translations ? window.translations.current_avatar : 'Текущая аватарка';
                currentAvatarPreview.innerHTML = '<h4 style="margin-bottom: 10px; color: #666;">' + title + '</h4>';
                currentAvatarPreview.appendChild(newImg);
            }
            
            if (noAvatarPreview) {
                noAvatarPreview.style.display = 'none';
                noAvatarPreview.style.visibility = 'hidden';
                noAvatarPreview.style.opacity = '0';
            }
        }
    }, 100);
}

// Закрытие модального окна для загрузки аватарки администратора
function closeAdminAvatarModal() {
    toggleModal('adminAvatarModal', false);
    document.getElementById('adminAvatarForm').reset();
    document.getElementById('adminAvatarErrors').style.display = 'none';
    
    // Сбрасываем предварительный просмотр
    document.getElementById('currentAvatarPreview').style.display = 'none';
    document.getElementById('newAvatarPreview').style.display = 'none';
    document.getElementById('noAvatarPreview').style.display = 'block';
    document.getElementById('newAvatarImg').src = '';
    
    // Восстанавливаем исходную структуру currentAvatarPreview
    const currentAvatarPreview = document.getElementById('currentAvatarPreview');
    if (currentAvatarPreview) {
        const title = window.translations ? window.translations.current_avatar : 'Текущая аватарка';
        currentAvatarPreview.innerHTML = '<h4 style="margin-bottom: 10px; color: #666;">' + title + '</h4><img id="currentAvatarImg" src="" alt="Current Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #ddd; margin-bottom: 10px;">';
    }
}

// Обработка отправки формы загрузки аватарки администратора
document.getElementById('adminAvatarForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const userId = formData.get('user_id');
    const uploadBtn = document.getElementById('uploadAvatarBtn');
    
    // Показываем индикатор загрузки
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Загрузка...';
    
    fetch(`/users/${userId}/avatar`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', window.translations?.avatar_uploaded || 'Аватарка успешно загружена');
            closeAdminAvatarModal();
            // Обновляем аватарку в таблице
            // Извлекаем путь из URL
            const avatarUrl = data.avatar_url;
            let avatarPath = null;
            if (avatarUrl) {
                // Извлекаем только путь после /storage/
                const match = avatarUrl.match(/\/storage\/(.+)$/);
                avatarPath = match ? match[1] : null;
            }
            updateUserAvatar(userId, avatarPath);
        } else {
            showAdminAvatarErrors(data.errors || {});
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.showNotification('error', 'Ошибка при загрузке аватарки');
    })
    .finally(() => {
        // Восстанавливаем кнопку
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = 'Загрузить';
    });
});

// Обработчик для предварительного просмотра выбранного файла
document.getElementById('adminAvatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Проверяем тип файла
        if (!file.type.startsWith('image/')) {
            window.showNotification('error', 'Пожалуйста, выберите изображение');
            this.value = '';
            return;
        }
        
        // Проверяем размер файла (максимум 5MB)
        if (file.size > 5 * 1024 * 1024) {
            window.showNotification('error', 'Размер файла не должен превышать 5MB');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('newAvatarImg').src = e.target.result;
            document.getElementById('newAvatarPreview').style.display = 'block';
            document.getElementById('noAvatarPreview').style.display = 'none';
            
            // Если есть текущая аватарка, скрываем её
            if (document.getElementById('currentAvatarPreview').style.display === 'block') {
                document.getElementById('currentAvatarPreview').style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    } else {
        // Если файл не выбран, возвращаем к исходному состоянию
        document.getElementById('newAvatarPreview').style.display = 'none';
        document.getElementById('newAvatarImg').src = '';
        
        // Показываем текущую аватарку или заглушку
        const currentAvatarSrc = document.getElementById('currentAvatarImg').src;
        if (currentAvatarSrc && !currentAvatarSrc.includes('data:image/svg')) {
            document.getElementById('currentAvatarPreview').style.display = 'block';
        } else {
            document.getElementById('noAvatarPreview').style.display = 'block';
        }
    }
});

// Показать ошибки валидации для формы аватарки
function showAdminAvatarErrors(errors) {
    const errorsDiv = document.getElementById('adminAvatarErrors');
    let errorHtml = '';
    
    for (const field in errors) {
        errorHtml += errors[field].join('<br>') + '<br>';
    }
    
    errorsDiv.innerHTML = errorHtml;
    errorsDiv.style.display = 'block';
}

// Обновить аватарку пользователя в таблице
function updateUserAvatar(userId, avatarPath) {
    const avatarUrl = avatarPath ? `/storage/${avatarPath}` : null;
    
    // Обновляем в десктопной таблице
    const userRow = document.getElementById(`user-${userId}`);
    if (userRow) {
        const avatarContainer = userRow.querySelector('.client-avatar');
        
        if (avatarContainer) {
            // Очищаем контейнер
            avatarContainer.innerHTML = '';
            
            if (avatarUrl) {
                // Создаем новое изображение
                const newImg = document.createElement('img');
                newImg.src = avatarUrl;
                newImg.alt = 'User Avatar';
                newImg.className = 'user-avatar';
                newImg.style.width = '40px';
                newImg.style.height = '40px';
                newImg.style.borderRadius = '50%';
                newImg.style.objectFit = 'cover';
                avatarContainer.appendChild(newImg);
            } else {
                // Создаем placeholder
                const placeholder = document.createElement('div');
                placeholder.className = 'user-avatar-placeholder';
                // Получаем первую букву имени из таблицы
                const userNameElement = userRow.querySelector('.client-name');
                const firstLetter = userNameElement ? userNameElement.textContent.charAt(0).toUpperCase() : '?';
                placeholder.textContent = firstLetter;
                avatarContainer.appendChild(placeholder);
            }
        }
    }
    
    // Обновляем в мобильных карточках
    const userCard = document.getElementById(`user-card-${userId}`);
    if (userCard) {
        const avatarContainer = userCard.querySelector('.user-avatar');
        
        if (avatarContainer) {
            // Очищаем контейнер
            avatarContainer.innerHTML = '';
            
            if (avatarUrl) {
                // Создаем новое изображение
                const newImg = document.createElement('img');
                newImg.src = avatarUrl;
                newImg.alt = 'User Avatar';
                newImg.className = 'user-avatar-img';
                newImg.style.width = '60px';
                newImg.style.height = '60px';
                newImg.style.borderRadius = '50%';
                newImg.style.objectFit = 'cover';
                avatarContainer.appendChild(newImg);
            } else {
                // Создаем placeholder
                const placeholder = document.createElement('div');
                placeholder.className = 'user-avatar-placeholder';
                // Получаем первую букву имени из мобильной карточки
                const userNameElement = userCard.querySelector('.user-name');
                const firstLetter = userNameElement ? userNameElement.textContent.charAt(0).toUpperCase() : '?';
                placeholder.textContent = firstLetter;
                avatarContainer.appendChild(placeholder);
            }
        }
    }
} 