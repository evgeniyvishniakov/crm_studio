// Основные функции управления модальными окнами
function openModal(modalId = 'addClientModal') {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId = 'addClientModal') {
    document.getElementById(modalId).style.display = 'none';
    clearErrors(modalId === 'addClientModal' ? 'addClientForm' : 'addProductForm');
}

function closeEditModal(modalId = 'editClientModal') {
    document.getElementById(modalId).style.display = 'none';
}

// Закрытие модального окна при клике вне его
window.onclick = function (event) {
    const modals = ['addClientModal', 'editClientModal', 'addProductModal', 'editProductModal', 'confirmationModal'];
    modals.forEach(modalId => {
        if (event.target == document.getElementById(modalId)) {
            if (modalId === 'confirmationModal') {
                document.getElementById(modalId).style.display = 'none';
                currentDeleteRow = null;
                currentDeleteId = null;
            } else if (modalId.includes('Client')) {
                closeModal(modalId);
            } else {
                closeModal(modalId);
            }
        }
    });
}

// Функция для генерации цвета аватара (для клиентов)
function getAvatarColor(name) {
    const colors = ['#dbeafe', '#f3e8ff', '#fce7f3', '#fef3c7', '#dcfce7', '#e0f2fe'];
    const hash = name.split('').reduce((acc, char) => char.charCodeAt(0) + acc, 0);
    return colors[hash % colors.length];
}

// Функция для генерации цвета текста аватара (для клиентов)
function getAvatarTextColor(name) {
    const colors = ['#2563eb', '#7c3aed', '#db2777', '#d97706', '#059669', '#0369a1'];
    const hash = name.split('').reduce((acc, char) => char.charCodeAt(0) + acc, 0);
    return colors[hash % colors.length];
}

// Функция для получения инициалов (для клиентов)
function getInitials(name) {
    const parts = name.split(' ');
    return parts.length >= 2
        ? `${parts[0][0]}${parts[1][0]}`.toUpperCase()
        : name.substring(0, 2).toUpperCase();
}

// Функция для показа уведомлений
function showNotification(type, message) {
    const notification = document.getElementById('notification');
    if (!notification) return;

    notification.className = `notification ${type} show`;

    const icon = type === 'success' ?
        '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>' :
        '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';

    notification.innerHTML = `
            <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                ${icon}
            </svg>
            <span class="notification-message">${message}</span>
        `;

    setTimeout(() => {
        notification.className = `notification ${type}`;
    }, 3000);
}

// Функция для очистки ошибок
function clearErrors(formId = 'addClientForm') {
    const form = document.getElementById(formId);
    if (form) {
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        form.querySelectorAll('.has-error').forEach(el => {
            el.classList.remove('has-error');
        });
    }
}

// Функция для отображения ошибок
function showErrors(errors, formId = 'addClientForm') {
    clearErrors(formId);

    Object.entries(errors).forEach(([field, messages]) => {
        const input = document.querySelector(`#${formId} [name="${field}"]`);
        if (input) {
            const inputGroup = input.closest('.form-group');
            inputGroup.classList.add('has-error');

            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;

            inputGroup.appendChild(errorElement);
        }
    });
}

// Добавление новой сущности (клиент или товар)
function handleAddFormSubmit(formId, route, entityType) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        const tableBody = document.getElementById(`${entityType}TableBody`);

        clearErrors(formId);

        submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
        submitBtn.disabled = true;

        fetch(route, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data[entityType]) {
                    // Создаем новую строку для таблицы
                    const newRow = document.createElement('tr');
                    newRow.id = `${entityType}-${data[entityType].id}`;

                    if (entityType === 'client') {
                        // Форматируем статус
                        let statusText = 'Новый клиент';
                        if (data.client.status === 'regular') statusText = 'Постоянный клиент';
                        if (data.client.status === 'vip') statusText = 'VIP клиент';

                        // Форматируем Instagram ссылку
                        let instagramLink = '';
                        if (data.client.instagram) {
                            instagramLink = `
                                    <a href="https://instagram.com/${data.client.instagram}" target="_blank" class="instagram-link">
                                        <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                                        </svg>
                                        @${data.client.instagram}
                                    </a>
                                `;
                        }

                        newRow.innerHTML = `
                                <td>
                                    <div class="client-info">
                                        <div class="client-avatar" style="background-color: ${getAvatarColor(data.client.name)};">
                                            <span style="color: ${getAvatarTextColor(data.client.name)};">${getInitials(data.client.name)}</span>
                                        </div>
                                        <div class="client-details">
                                            <div class="client-name">${data.client.name}</div>
                                            <div class="client-status">${statusText}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>${instagramLink}</td>
                                <td>${data.client.phone || ''}</td>
                                <td>${data.client.email || ''}</td>
                                <td class="actions-cell" style="vertical-align: middle;">
                                    <button class="btn-view">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Просмотр
                                    </button>
                                    <button class="btn-edit">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Ред.
                                    </button>
                                    <button class="btn-delete">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Удалить
                                    </button>
                                </td>
                            `;
                    } else if (entityType === 'product') {
                        // Создаем HTML для фото
                        let photoHtml = '<div class="no-photo">Нет фото</div>';
                        if (data.product.photo) {
                            photoHtml = `<img src="/storage/${data.product.photo}" alt="${data.product.name}" class="product-photo">`;
                        }

                        newRow.innerHTML = `
                                <td>${photoHtml}</td>
                                <td>${data.product.name}</td>
                                <td>${data.product.category}</td>
                                <td>${data.product.brand}</td>
                                <td class="actions-cell">
                                    <button class="btn-edit">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Ред.
                                    </button>
                                    <button class="btn-delete">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Удалить
                                    </button>
                                </td>
                            `;
                    }

                    // Добавляем новую строку в начало таблицы
                    tableBody.insertBefore(newRow, tableBody.firstChild);

                    // Показываем уведомление
                    showNotification('success', `${entityType === 'client' ? 'Клиент' : 'Товар'} ${data[entityType].name} успешно добавлен`);

                    // Закрываем модальное окно и очищаем форму
                    closeModal(formId === 'addClientForm' ? 'addClientModal' : 'addProductModal');
                    this.reset();
                } else {
                    throw new Error(`Сервер не вернул данные ${entityType === 'client' ? 'клиента' : 'товара'}`);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);

                if (error.errors) {
                    showErrors(error.errors, formId);
                    showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    showNotification('error', error.message || `Произошла ошибка при добавлении ${entityType === 'client' ? 'клиента' : 'товара'}`);
                }
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
    });
}

// Инициализация аватаров клиентов
document.querySelectorAll('.client-avatar').forEach(avatar => {
    const name = avatar.dataset.name;
    avatar.style.backgroundColor = getAvatarColor(name);
    avatar.querySelector('span').style.color = getAvatarTextColor(name);
    avatar.querySelector('span').textContent = getInitials(name);
});

// Проверка существующих данных при вводе (on blur) - для клиентов
document.querySelectorAll('#addClientForm input').forEach(input => {
    input.addEventListener('blur', function () {
        const fieldName = this.name;
        const fieldValue = this.value.trim();

        // Очищаем предыдущую ошибку для этого поля
        const inputGroup = this.closest('.form-group');
        inputGroup.classList.remove('has-error');
        const existingError = inputGroup.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        // Проверяем только заполненные поля
        if (fieldValue === '') return;

        // Проверяем только определенные поля
        if (!['instagram', 'phone', 'email'].includes(fieldName)) return;

        fetch(`/clients/check?field=${fieldName}&value=${encodeURIComponent(fieldValue)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    inputGroup.classList.add('has-error');

                    const errorElement = document.createElement('div');
                    errorElement.className = 'error-message';

                    // Русские сообщения об ошибках
                    const errorMessages = {
                        'instagram': 'Клиент с таким Instagram уже существует',
                        'phone': 'Клиент с таким номером телефона уже существует',
                        'email': 'Клиент с такой почтой уже существует'
                    };

                    errorElement.textContent = errorMessages[fieldName] || 'Это значение уже используется';
                    errorElement.style.color = '#f44336';
                    errorElement.style.marginTop = '5px';
                    errorElement.style.fontSize = '0.85rem';

                    inputGroup.appendChild(errorElement);
                }
            })
            .catch(error => {
                console.error('Ошибка при проверке:', error);
            });
    });
});

// Глобальные переменные для удаления
let currentDeleteRow = null;
let currentDeleteId = null;
let currentDeleteType = null; // 'client' или 'product'

// Обработчик клика по кнопке удаления
document.addEventListener('click', function (e) {
    const deleteBtn = e.target.closest('.btn-delete');
    if (deleteBtn) {
        const row = deleteBtn.closest('tr');
        if (row) {
            const idParts = row.id.split('-');
            if (idParts.length === 2) {
                currentDeleteType = idParts[0];
                currentDeleteId = idParts[1];
                currentDeleteRow = row;

                // Показываем модальное окно подтверждения
                document.getElementById('confirmationModal').style.display = 'block';
            }
        }
    }
});

// Обработчики для модального окна подтверждения
document.getElementById('cancelDelete').addEventListener('click', function () {
    document.getElementById('confirmationModal').style.display = 'none';
    currentDeleteRow = null;
    currentDeleteId = null;
    currentDeleteType = null;
});

document.getElementById('confirmDelete').addEventListener('click', function () {
    if (currentDeleteRow && currentDeleteId && currentDeleteType) {
        deleteEntity(currentDeleteRow, currentDeleteId, currentDeleteType);
    }
    document.getElementById('confirmationModal').style.display = 'none';
});

// Функция для удаления сущности
function deleteEntity(row, entityId, entityType) {
    // Добавляем класс для анимации
    row.classList.add('row-deleting');

    // Отправляем запрос на удаление
    fetch(`/${entityType}s/${entityId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                // Удаляем строку после завершения анимации
                setTimeout(() => {
                    row.remove();
                    showNotification('success', `${entityType === 'client' ? 'Клиент' : 'Товар'} успешно удален`);
                }, 300);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            row.classList.remove('row-deleting');
            showNotification('error', `Не удалось удалить ${entityType === 'client' ? 'клиента' : 'товар'}`);
        });
}

// Функции для работы с модальным окном редактирования
function openEditModal(entityId, entityType = 'client') {
    const modalId = `${entityType === 'client' ? 'editClient' : 'editProduct'}Modal`;
    const modal = document.getElementById(modalId);
    const modalBody = modal.querySelector('.modal-body');

    // Показываем модальное окно и лоадер
    modal.style.display = 'block';
    modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

    fetch(`/${entityType}s/${entityId}/edit`, {
        headers: {'Accept': 'application/json'}
    })
        .then(response => {
            if (!response.ok) throw new Error('Ошибка сети');
            return response.json();
        })
        .then(data => {
            if (entityType === 'client') {
                const client = data;
                modalBody.innerHTML = `
                        <form id="editClientForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editClientId" name="id" value="${client.id}">
                            <div class="form-group">
                                <label for="editClientName">Имя *</label>
                                <input type="text" id="editClientName" name="name" value="${(client.name || '').replace(/"/g, '&quot;')}" required>
                            </div>
                            <div class="form-group">
                                <label for="editClientInstagram">Инстаграм</label>
                                <input type="text" id="editClientInstagram" name="instagram" value="${client.instagram || ''}">
                            </div>
                            <div class="form-group">
                                <label for="editClientPhone">Телефон</label>
                                <input type="tel" id="editClientPhone" name="phone" value="${client.phone || ''}">
                            </div>
                            <div class="form-group">
                                <label for="editClientEmail">Почта</label>
                                <input type="email" id="editClientEmail" name="email" value="${client.email || ''}">
                            </div>
                            <div class="form-group">
                                <label for="editClientStatus">Статус</label>
                                <select id="editClientStatus" name="status" class="form-control">
                                    <option value="new" ${client.status === 'new' ? 'selected' : ''}>Новый клиент</option>
                                    <option value="regular" ${client.status === 'regular' ? 'selected' : ''}>Постоянный клиент</option>
                                    <option value="vip" ${client.status === 'vip' ? 'selected' : ''}>VIP клиент</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-cancel" onclick="closeEditModal('${modalId}')">Отмена</button>
                                <button type="submit" class="btn-submit">Сохранить</button>
                            </div>
                        </form>
                    `;

                document.getElementById('editClientForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    submitEditForm(this, 'client');
                });
            } else {
                const product = data;
                modalBody.innerHTML = `
                        <form id="editProductForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editProductId" name="id" value="${product.id}">
                            <div class="form-group">
                                <label for="editProductName">Название *</label>
                                <input type="text" id="editProductName" name="name" value="${(product.name || '').replace(/"/g, '&quot;')}" required>
                            </div>
                            <div class="form-group">
                                <label for="editProductCategory">Категория *</label>
                                <input type="text" id="editProductCategory" name="category" value="${product.category || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editProductBrand">Бренд *</label>
                                <input type="text" id="editProductBrand" name="brand" value="${product.brand || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editProductPhoto">Фото</label>
                                <input type="file" id="editProductPhoto" name="photo" accept="image/jpeg,image/png,image/jpg">
                                <small class="form-text text-muted">Максимальный размер: 2MB. Допустимые форматы: JPEG, PNG, JPG</small>
                                <div id="currentPhotoContainer" class="mt-2">
                                    ${product.photo ? `
                                        <div>
                                            <p>Текущее фото:</p>
                                            <img src="/storage/${product.photo}" alt="${product.name}" class="current-photo">
                                            <button type="button" class="remove-photo-btn" onclick="removePhoto(${product.id}, 'product')">Удалить фото</button>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-cancel" onclick="closeEditModal('${modalId}')">Отмена</button>
                                <button type="submit" class="btn-submit">Сохранить</button>
                            </div>
                        </form>
                    `;

                document.getElementById('editProductForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    submitEditForm(this, 'product');
                });
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки данных:', error);
            modalBody.innerHTML = `<p class="text-danger">Ошибка загрузки данных ${entityType === 'client' ? 'клиента' : 'товара'}</p>`;
        });
}

// Функция для удаления фото товара
function removePhoto(entityId, entityType = 'product') {
    if (confirm(`Вы уверены, что хотите удалить фото ${entityType === 'client' ? 'клиента' : 'товара'}?`)) {
        fetch(`/${entityType}s/${entityId}/remove-photo`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('currentPhotoContainer').innerHTML = '';
                    showNotification('success', `Фото ${entityType === 'client' ? 'клиента' : 'товара'} успешно удалено`);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showNotification('error', `Не удалось удалить фото ${entityType === 'client' ? 'клиента' : 'товара'}`);
            });
    }
}

// Обработчик клика по кнопке редактирования
document.addEventListener('click', function (e) {
    const editBtn = e.target.closest('.btn-edit');
    if (editBtn) {
        const row = editBtn.closest('tr');
        if (row) {
            const idParts = row.id.split('-');
            if (idParts.length === 2) {
                openEditModal(idParts[1], idParts[0]);
            }
        }
    }
});

// Обработчик отправки формы редактирования
async function submitEditForm(form, entityType) {
    const formData = new FormData(form);
    const entityId = formData.get('id');
    const submitBtn = form.querySelector('.btn-submit');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
    submitBtn.disabled = true;

    try {
        const response = await fetch(`/${entityType}s/${entityId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT',
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(
                errorData?.message ||
                `Ошибка сервера: ${response.status} ${response.statusText}`
            );
        }

        const data = await response.json();

        if (data.success) {
            updateEntityRow(data[entityType], entityType);
            showNotification('success', '✅ Изменения сохранены');
            closeEditModal(`${entityType === 'client' ? 'editClient' : 'editProduct'}Modal`);
        } else {
            throw new Error(data.message || 'Неизвестная ошибка сервера');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        showNotification('error', `❌ ${error.message || 'Ошибка сети или сервера'}`);
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Функция для обновления строки сущности в таблице
function updateEntityRow(entity, entityType) {
    const row = document.getElementById(`${entityType}-${entity.id}`);
    if (!row) return;

    if (entityType === 'client') {
        // Обновляем аватар
        const avatar = row.querySelector('.client-avatar');
        if (avatar) {
            avatar.style.backgroundColor = getAvatarColor(entity.name);
            const initials = avatar.querySelector('span');
            initials.style.color = getAvatarTextColor(entity.name);
            initials.textContent = getInitials(entity.name);
        }

        // Обновляем имя и статус
        const nameElement = row.querySelector('.client-name');
        if (nameElement) nameElement.textContent = entity.name;

        const statusElement = row.querySelector('.client-status');
        if (statusElement) {
            statusElement.textContent =
                entity.status === 'new' ? 'Новый клиент' :
                    entity.status === 'regular' ? 'Постоянный клиент' : 'VIP клиент';
        }

        // Обновляем Instagram
        const instagramCell = row.querySelector('td:nth-child(2)');
        if (instagramCell) {
            instagramCell.innerHTML = client.instagram ? `
            <a href="https://instagram.com/${client.instagram}" target="_blank" class="instagram-link">
                <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                </svg>
                @${client.instagram}
            </a>
        ` : '';
        }

        // Обновляем телефон и email
        const phoneCell = row.querySelector('td:nth-child(3)');
        if (phoneCell) phoneCell.textContent = client.phone || '';

        const emailCell = row.querySelector('td:nth-child(4)');
        if (emailCell) emailCell.textContent = client.email || '';
    } else if (entityType === 'product') {
        // Обновляем фото
        const photoCell = row.querySelector('td:first-child');
        if (photoCell) {
            photoCell.innerHTML = product.photo
                ? `<img src="/storage/${product.photo}" alt="${product.name}" class="product-photo">`
                : '<div class="no-photo">Нет фото</div>';
        }

        // Обновляем название
        const nameCell = row.querySelector('td:nth-child(2)');
        if (nameCell) nameCell.textContent = entity.name;

        // Обновляем категорию
        const categoryCell = row.querySelector('td:nth-child(3)');
        if (categoryCell) categoryCell.textContent = entity.category;

        // Обновляем бренд
        const brandCell = row.querySelector('td:nth-child(4)');
        if (brandCell) brandCell.textContent = entity.brand;
    }
}

// Обработчик для кнопки просмотра (можно добавить функционал по необходимости)
document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-view')) {
        const row = e.target.closest('tr');
        const entityId = row.id.split('-')[1];
        // Здесь можно реализовать просмотр деталей сущности
        alert('Просмотр сущности с ID: ' + entityId);
    }
});

// Поиск сущностей
document.querySelectorAll('.search-box input').forEach(searchInput => {
    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const tableId = this.closest('.header-actions').previousElementSibling.textContent.trim() === 'Клиенты'
            ? 'clientsTableBody'
            : 'productsTableBody';
        const rows = document.querySelectorAll(`#${tableId} tr`);

        rows.forEach(row => {
            let cellsToSearch = [];
            if (tableId === 'clientsTableBody') {
                // Для клиентов ищем по имени, инстаграму, телефону и email
                cellsToSearch = [
                    row.querySelector('.client-name')?.textContent.toLowerCase() || '',
                    row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '',
                    row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '',
                    row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || ''
                ];
            } else {
                // Для товаров ищем по названию, категории и бренду
                cellsToSearch = [
                    row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '',
                    row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '',
                    row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || ''
                ];
            }

            const match = cellsToSearch.some(cell => cell.includes(searchTerm));
            row.style.display = match ? '' : 'none';
        });
    });
});

// Инициализация форм добавления
document.addEventListener('DOMContentLoaded', function () {
    // Для клиентов
    if (document.getElementById('addClientForm')) {
        handleAddFormSubmit('addClientForm', "{{ route('clients.store') }}", 'client');
    }

    // Для товаров
    if (document.getElementById('addProductForm')) {
        handleAddFormSubmit('addProductForm', "{{ route('products.store') }}", 'product');
    }
});
