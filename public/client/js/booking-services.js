// ===== ФУНКЦИИ ДЛЯ УПРАВЛЕНИЯ УСЛУГАМИ МАСТЕРОВ =====

// Функция для форматирования валюты
function formatCurrency(amount) {
    if (window.CurrencyManager) {
        return window.CurrencyManager.formatAmount(amount);
    } else {
        // Fallback форматирование - убираем .00 если нет копеек
        const num = parseFloat(amount);
        if (isNaN(num)) return amount;
        
        // Если число целое, не показываем .00
        if (Number.isInteger(num)) {
            return num.toLocaleString('ru-RU') + ' ₴';
        } else {
            return num.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₴';
        }
    }
}

// Функции для управления услугами мастеров
function addUserService() {
    // Очищаем форму
    document.getElementById('user-service-form').reset();
    document.getElementById('user-service-id').value = '';
    document.getElementById('modal-is-active').checked = true;
    
    // Обновляем заголовок модального окна
    document.getElementById('userServiceModalTitle').textContent = translations.add_master_service;
    
    // Открываем модальное окно
    const modal = document.getElementById('userServiceModal');
    modal.style.display = 'block';
}

function editUserService(userServiceId) {
    // Загружаем данные услуги мастера
    fetch(`/booking/user-services/${userServiceId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || 'Ошибка при загрузке данных');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.userServices.length > 0) {
            const userService = data.userServices[0];
            
        
            
            // Заполняем форму данными для редактирования
            document.getElementById('user-service-id').value = userService.id;
            document.getElementById('modal-user-id').value = userService.user_id;
            document.getElementById('modal-service-id').value = userService.service_id;
            document.getElementById('modal-is-active').checked = userService.is_active_for_booking;
            document.getElementById('modal-price').value = userService.price || '';
            document.getElementById('modal-duration').value = userService.duration || '';
            document.getElementById('modal-description').value = userService.description || '';
            
            // Обновляем заголовок модального окна
            document.getElementById('userServiceModalTitle').textContent = translations.edit_service_to_master;
            
            // Открываем модальное окно
            const modal = document.getElementById('userServiceModal');
            modal.style.display = 'block';
        } else {
            window.showNotification('error', data.message || translations.error_loading_service_data);
        }
    })
    .catch(error => {
        console.error('Ошибка при загрузке данных:', error);
        window.showNotification('error', error.message || translations.error_loading_data);
    });
}

function deleteUserService(userServiceId) {
    // Проверяем, не удаляется ли уже эта запись
    if (window.currentDeleteId === userServiceId) {

        return;
    }
    
    // Находим элементы для удаления
    const row = document.querySelector(`tr[data-user-service-id="${userServiceId}"]`);
    const card = document.querySelector(`.user-service-card[data-user-service-id="${userServiceId}"]`);
    
    // Проверяем, что элементы существуют
    if (!row && !card) {
        console.error('Элементы для удаления не найдены:', userServiceId);
        window.showNotification('error', 'Элемент не найден');
        return;
    }
    
    // Сохраняем ссылки на элементы для удаления
    window.currentDeleteRow = row;
    window.currentDeleteCard = card;
    window.currentDeleteId = userServiceId;
    
    // Показываем модальное окно подтверждения
    document.getElementById('confirmationModal').style.display = 'block';
}

function confirmDeleteUserService() {
    if (!window.currentDeleteId) return;
    
    // Проверяем, не выполняется ли уже удаление
    if (window.isDeleting) {

        return;
    }
    
    // Устанавливаем флаг выполнения удаления
    window.isDeleting = true;

    // Добавляем класс для анимации удаления
    if (window.currentDeleteRow) window.currentDeleteRow.classList.add('row-deleting');
    if (window.currentDeleteCard) window.currentDeleteCard.classList.add('row-deleting');

    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    
    fetch(`/booking/user-services/${window.currentDeleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {

        
        if (!response.ok) {
            return response.json().then(errorData => {
                console.error('Error response data:', errorData);
                throw new Error(errorData.message || 'Ошибка при удалении');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Немедленно удаляем элементы из DOM
            if (window.currentDeleteRow) {

                window.currentDeleteRow.remove();
            }
            if (window.currentDeleteCard) {

                window.currentDeleteCard.remove();
            }
            
            // Показываем уведомление об успехе
            window.showNotification('success', translations.service_deleted_successfully || 'Услуга успешно удалена');
            
            // Обновляем статистику
            updateStatistics();
            
            // Проверяем, есть ли еще услуги
            const tbody = document.getElementById('user-services-tbody');
            const cards = document.getElementById('userServicesCards');
            
            if (tbody && tbody.children.length === 0) {
                // Если нет услуг, показываем сообщение
                const noServicesMessage = document.querySelector('#tab-user-services .text-center');
                if (noServicesMessage) {
                    noServicesMessage.style.display = 'block';
                }
            }
            
            if (cards && cards.children.length === 0) {
                // Если нет карточек, показываем сообщение
                const noServicesMessage = document.querySelector('#tab-user-services .text-center');
                if (noServicesMessage) {
                    noServicesMessage.style.display = 'block';
                }
            }
        } else {
            window.showNotification('error', data.message || 'Ошибка при удалении услуги');
        }
    })
    .catch(error => {
        console.error('Ошибка при удалении:', error);
        window.showNotification('error', error.message || translations.error_deleting || 'Произошла ошибка при удалении');
        
        // Убираем класс анимации при ошибке
        if (window.currentDeleteRow) {
            window.currentDeleteRow.classList.remove('row-deleting');
            
        }
        if (window.currentDeleteCard) {
            window.currentDeleteCard.classList.remove('row-deleting');
            
        }
    })
    .finally(() => {
        // Сбрасываем флаг выполнения удаления
        window.isDeleting = false;
        
        // Закрываем модальное окно
        closeConfirmationModal();
    });
}

function saveUserService() {
    const form = document.getElementById('user-service-form');
    const formData = new FormData(form);
    const userServiceId = document.getElementById('user-service-id').value;
    
    // Создаем объект данных, правильно обрабатывая чекбокс
    const data = {
        user_id: formData.get('user_id'),
        service_id: formData.get('service_id'),
        is_active_for_booking: document.getElementById('modal-is-active').checked, // boolean
        price: formData.get('price') || null,
        duration: formData.get('duration') || null,
        description: formData.get('description') || null
    };

    
    // Дополнительная отладка - проверяем значения полей формы

    
    const url = userServiceId ? 
        `/booking/user-services/${userServiceId}` : 
        '/booking/user-services';
    
    const method = userServiceId ? 'PUT' : 'POST';
    

    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || 'Произошла ошибка при сохранении');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message);
            closeUserServiceModal();
            

            
            if (!userServiceId) {
                // Если это новая запись, добавляем её в таблицу
                if (data.userService) {

                    addUserServiceToTable(data.userService);
                } else {
                    console.error('userService не найден в ответе сервера');
                }
            } else {
                // Если это редактирование, обновляем существующую строку

                updateUserServiceInTable(data.userService);
            }
            
            // Обновляем статистику в первой вкладке
            updateStatistics();
            
            // Очищаем форму
            document.getElementById('user-service-form').reset();
            document.getElementById('modal-is-active').checked = true;
        } else {
            window.showNotification('error', data.message || translations.error_saving);
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        window.showNotification('error', error.message || translations.error_saving);
    });
}

function closeUserServiceModal() {
    const modal = document.getElementById('userServiceModal');
    modal.style.display = 'none';
}

// Функция для обновления услуги в таблице
function updateUserServiceInTable(userService) {
    // Проверяем, что userService и его свойства существуют
    if (!userService || !userService.id) {
        console.error('updateUserServiceInTable: userService или userService.id не определены', userService);
        return;
    }
    
    const tbody = document.getElementById('user-services-tbody');
    const existingRow = tbody.querySelector(`tr[data-user-service-id="${userService.id}"]`);
    
    if (existingRow) {
        // Используем данные, которые возвращает сервер
        // Проверяем разные возможные форматы данных
        const userName = userService.user_name || userService.user?.name || 'Неизвестный мастер';
        const userEmail = userService.user_email || userService.user?.email || '';
        const serviceName = userService.service_name || userService.service?.name || 'Неизвестная услуга';
        const serviceDescription = userService.service_description || userService.service?.description || '';
        
        // Используем цену и длительность из userService, если они есть, иначе из базовой услуги
        const price = userService.price !== null && userService.price !== undefined ? userService.price : (userService.service_price || 0);
        const duration = userService.duration !== null && userService.duration !== undefined ? userService.duration : (userService.service_duration || 0);
        
        // Обновляем существующую строку
        existingRow.innerHTML = `
            <td>
                <div class="master-info">
                    <div class="master-name">${userName}</div>
                    <div class="master-details">${userEmail}</div>
                </div>
            </td>
            <td>
                <div class="service-info">
                    <div class="service-name">${serviceName}</div>
                    <div class="service-details">${serviceDescription}</div>
                </div>
            </td>
            <td>${formatCurrency(price)}</td>
            <td>${formatDuration(duration, !userService.is_custom_duration)}</td>
            <td>
                <span class="status-badge ${userService.is_active_for_booking ? 'active' : 'inactive'}">
                    ${userService.is_active_for_booking ? 'Активна' : 'Неактивна'}
                </span>
            </td>
            <td class="actions-cell">
                <button type="button" class="btn-edit" onclick="editUserService(${userService.id})" title="Редактировать">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                </button>
                <button type="button" class="btn-delete" onclick="deleteUserService(${userService.id})" title="Удалить">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                </button>
            </td>
        `;
    }
    
    // Также обновляем карточку в мобильной версии
    const existingCard = document.querySelector(`.user-service-card[data-user-service-id="${userService.id}"]`);
    if (existingCard) {
        // Используем данные, которые возвращает сервер
        // Проверяем разные возможные форматы данных
        const userName = userService.user_name || userService.user?.name || 'Неизвестный мастер';
        const userEmail = userService.user_email || userService.user?.email || '';
        const serviceName = userService.service_name || userService.service?.name || 'Неизвестная услуга';
        
        // Используем цену и длительность из userService, если они есть, иначе из базовой услуги
        const price = userService.price !== null && userService.price !== undefined ? userService.price : (userService.service_price || 0);
        const duration = userService.duration !== null && userService.duration !== undefined ? userService.duration : (userService.service_duration || 0);
        
        existingCard.innerHTML = `
            <div class="user-service-card-header">
                <div class="user-service-main-info">
                    <h3 class="user-service-name">${userName} - ${serviceName}</h3>
                    <div class="user-service-status">
                        <span class="status-badge ${userService.is_active_for_booking ? 'active' : 'inactive'}">
                            ${userService.is_active_for_booking ? 'Активна' : 'Неактивна'}
                        </span>
                    </div>
                </div>
            </div>
            <div class="user-service-info">
                <div class="user-service-info-item">
                    <div class="user-service-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Мастер
                    </div>
                    <div class="user-service-info-value">${userEmail}</div>
                </div>
                <div class="user-service-info-item">
                    <div class="user-service-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Цена
                    </div>
                    <div class="user-service-info-value">${formatCurrency(userService.price || 0)}</div>
                </div>
                <div class="user-service-info-item">
                    <div class="user-service-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Длительность
                    </div>
                    <div class="user-service-info-value">${formatDuration(userService.duration || 0, !userService.is_custom_duration)}</div>
                </div>
            </div>
            <div class="user-service-actions">
                <button type="button" class="btn-edit" onclick="editUserService(${userService.id})" title="Редактировать">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Редактировать
                </button>
                <button type="button" class="btn-delete" onclick="deleteUserService(${userService.id})" title="Удалить">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                    Удалить
                </button>
            </div>
        `;
    }
}

// Функция для добавления новой услуги в таблицу
function addUserServiceToTable(userService) {
    // Проверяем, что userService и его свойства существуют
    if (!userService || !userService.id) {
        console.error('addUserServiceToTable: userService или userService.id не определены', userService);
        return;
    }
    

    
    const tbody = document.getElementById('user-services-tbody');
    const userServicesCards = document.getElementById('userServicesCards');
    
    // Используем данные, которые возвращает сервер
    // Проверяем разные возможные форматы данных
    const userName = userService.user_name || userService.user?.name || 'Неизвестный мастер';
    const userEmail = userService.user_email || userService.user?.email || '';
    const serviceName = userService.service_name || userService.service?.name || 'Неизвестная услуга';
    const serviceDescription = userService.service_description || userService.service?.description || '';
    
    // Используем цену и длительность из userService, если они есть, иначе из базовой услуги
    const price = userService.price !== null && userService.price !== undefined ? userService.price : (userService.service_price || 0);
    const duration = userService.duration !== null && userService.duration !== undefined ? userService.duration : (userService.service_duration || 0);
    


    
    // Добавляем строку в таблицу
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-user-service-id', userService.id);
    newRow.innerHTML = `
        <td>
            <div class="master-info">
                <div class="master-name">${userName}</div>
                <div class="master-details">${userEmail}</div>
            </div>
        </td>
        <td>
            <div class="service-info">
                <div class="service-name">${serviceName}</div>
                <div class="service-details">${serviceDescription}</div>
            </div>
        </td>
        <td>${formatCurrency(price)}</td>
        <td>${formatDuration(duration, !userService.is_custom_duration)}</td>
        <td>
            <span class="status-badge ${userService.is_active_for_booking ? 'active' : 'inactive'}">
                ${userService.is_active_for_booking ? 'Активна' : 'Неактивна'}
            </span>
        </td>
        <td class="actions-cell">
            <button type="button" class="btn-edit" onclick="editUserService(${userService.id})" title="Редактировать">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
            </button>
            <button type="button" class="btn-delete" onclick="deleteUserService(${userService.id})" title="Удалить">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    
    // Добавляем карточку в мобильную версию
    if (userServicesCards) {
        const newCard = document.createElement('div');
        newCard.className = 'user-service-card';
        newCard.setAttribute('data-user-service-id', userService.id);
        
        // Используем те же данные для мобильной версии
        const mobileUserName = userService.user_name || userService.user?.name || 'Неизвестный мастер';
        const mobileUserEmail = userService.user_email || userService.user?.email || '';
        const mobileServiceName = userService.service_name || userService.service?.name || 'Неизвестная услуга';
        // Используем те же переменные price и duration для мобильной версии
        newCard.innerHTML = `
            <div class="user-service-card-header">
                <div class="user-service-main-info">
                    <h3 class="user-service-name">${mobileUserName} - ${mobileServiceName}</h3>
                    <div class="user-service-status">
                        <span class="status-badge ${userService.is_active_for_booking ? 'active' : 'inactive'}">
                            ${userService.is_active_for_booking ? 'Активна' : 'Неактивна'}
                        </span>
                    </div>
                </div>
            </div>
            <div class="user-service-info">
                <div class="user-service-info-item">
                    <div class="user-service-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Мастер
                    </div>
                    <div class="user-service-info-value">${mobileUserEmail}</div>
                </div>
                <div class="user-service-info-item">
                    <div class="user-service-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Цена
                    </div>
                    <div class="user-service-info-value">${formatCurrency(price)}</div>
                </div>
                <div class="user-service-info-item">
                    <div class="user-service-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Длительность
                    </div>
                    <div class="user-service-info-value">${formatDuration(duration, !userService.is_custom_duration)}</div>
                </div>
            </div>
            <div class="user-service-actions">
                <button type="button" class="btn-edit" onclick="editUserService(${userService.id})" title="Редактировать">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Редактировать
                </button>
                <button type="button" class="btn-delete" onclick="deleteUserService(${userService.id})" title="Удалить">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                    Удалить
                </button>
            </div>
        `;
        userServicesCards.appendChild(newCard);
    }
    
    // Скрываем сообщение "нет услуг" если оно есть
    const noServicesMessage = document.querySelector('#tab-user-services .text-center');
    if (noServicesMessage) {
        noServicesMessage.style.display = 'none';
    }
}

function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    if (modal) {
        modal.style.display = 'none';
    }
    // Сбрасываем все переменные при закрытии модального окна
    window.currentDeleteId = null;
    window.currentDeleteRow = null;
    window.currentDeleteCard = null;
    window.isDeleting = false; // Сбрасываем флаг выполнения удаления
}

// Функция поиска в таблице услуг мастеров
function searchUserServices(searchTerm) {
    const tbody = document.getElementById('user-services-tbody');
    const rows = tbody.querySelectorAll('tr');
    
    searchTerm = searchTerm.toLowerCase().trim();
    
    rows.forEach(row => {
        const masterName = row.cells[0].textContent.toLowerCase();
        const serviceName = row.cells[1].textContent.toLowerCase();
        const price = row.cells[2].textContent.toLowerCase();
        const duration = row.cells[3].textContent.toLowerCase();
        
        const matches = masterName.includes(searchTerm) || 
                       serviceName.includes(searchTerm) || 
                       price.includes(searchTerm) || 
                       duration.includes(searchTerm);
        
        row.style.display = matches ? '' : 'none';
    });
    
    // Показываем сообщение если ничего не найдено
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    const noResultsMessage = document.getElementById('no-search-results');
    
    if (visibleRows.length === 0 && searchTerm !== '') {
        if (!noResultsMessage) {
            const message = document.createElement('tr');
            message.id = 'no-search-results';
            message.innerHTML = `
                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">
                    <div style="margin-bottom: 10px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" style="color: #d1d5db;">
                            <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                    </div>
                    <div style="font-size: 16px; font-weight: 500; margin-bottom: 5px;">Ничего не найдено</div>
                    <div style="font-size: 14px;">Попробуйте изменить поисковый запрос</div>
                </td>
            `;
            tbody.appendChild(message);
        }
    } else {
        if (noResultsMessage) {
            noResultsMessage.remove();
        }
    }
}

// Функция для переключения между десктопной и мобильной версией услуг мастеров
function toggleUserServicesView() {
    const tableWrapper = document.querySelector('#tab-user-services .table-wrapper');
    const userServicesCards = document.getElementById('userServicesCards');

    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (userServicesCards) userServicesCards.style.display = 'grid';

    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (userServicesCards) userServicesCards.style.display = 'none';

    }
}

// Функция для обновления статистики
function updateStatistics() {
    // Здесь можно добавить логику обновления статистики
    // Например, пересчет количества активных/неактивных услуг

}

// Инициализация обработчиков при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Переключаем на правильную версию при загрузке
    toggleUserServicesView();
    
    // Обработчики для модального окна подтверждения удаления
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            closeConfirmationModal();
        });
    }
    
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            confirmDeleteUserService();
        });
    }
    
    // Обработчик для закрытия модального окна по клику на крестик
    const closeBtn = document.querySelector('#confirmationModal .close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closeConfirmationModal();
        });
    }
    
    // Обработчик для закрытия модального окна по клику вне его
    const confirmationModal = document.getElementById('confirmationModal');
    if (confirmationModal) {
        confirmationModal.addEventListener('click', function(e) {
            if (e.target === confirmationModal) {
                closeConfirmationModal();
            }
        });
    }
});

// Обработчик изменения размера окна
window.addEventListener('resize', function() {
    toggleUserServicesView();
});