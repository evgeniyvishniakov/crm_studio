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
    .then(user => {
        openEditUserModal(user);
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
                window.showNotification('success', 'Пользователь успешно удален');
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
                    <span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? 'Активный' : 'Неактивный'}</span>
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
                <button class="btn-edit" title="Редактировать" onclick="openEditUserModalFromCard(${user.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Редактировать
                </button>
                <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${user.id})">
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

function openUserModal() {
    toggleModal('addUserModal', true);
}

function closeUserModal() {
    toggleModal('addUserModal', false);
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
                        <td><span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? 'Активный' : 'Неактивный'}</span></td>
                        <td>${user.registered_at ? formatDateTime(user.registered_at) : ''}</td>
                        <td class="actions-cell">
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
                                <span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? 'Активный' : 'Неактивный'}</span>
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
                            <button class="btn-edit" title="Редактировать" onclick="openEditUserModalFromCard(${user.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Редактировать
                            </button>
                            <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${user.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Удалить
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
                    window.showNotification('success', 'Пользователь успешно добавлен');
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
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserName').value = user.name;
    document.getElementById('editUserUsername').value = user.username;
    document.getElementById('editUserEmail').value = user.email || '';
    document.getElementById('editUserRole').value = user.role;
    document.getElementById('editUserStatus').value = user.status;

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
            .then(user => {
                openEditUserModal(user);
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
                                    <td><span class="status-badge ${user.status === 'active' ? 'status-completed' : 'status-cancelled'}">${user.status === 'active' ? 'Активный' : 'Неактивный'}</span></td>
                                    <td>${user.registered_at ? formatDateTime(user.registered_at) : ''}</td>
                        <td class="actions-cell">
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
                    `;
                }
                
                // Обновить мобильную карточку
                updateUserCard(user);
                closeEditUserModal();
                window.showNotification('success', 'Пользователь успешно обновлен');
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