// ===== ФУНКЦИИ ДЛЯ УПРАВЛЕНИЯ УСЛУГАМИ МАСТЕРОВ =====

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
    .then(response => response.json())
    .then(data => {
        if (data.success && data.userServices.length > 0) {
            const userService = data.userServices[0];
            
            console.log('editUserService - Загруженные данные:', userService);
            
            // Заполняем форму данными для редактирования
            document.getElementById('user-service-id').value = userService.id;
            document.getElementById('modal-user-id').value = userService.user_id;
            document.getElementById('modal-service-id').value = userService.service_id;
            document.getElementById('modal-is-active').checked = userService.is_active_for_booking;
            document.getElementById('modal-price').value = userService.price || '';
            document.getElementById('modal-duration').value = userService.duration || '';
            document.getElementById('modal-description').value = userService.description || '';
            
            console.log('editUserService - Заполненные поля формы:', {
                price: document.getElementById('modal-price').value,
                duration: document.getElementById('modal-duration').value,
                description: document.getElementById('modal-description').value
            });
            
            // Обновляем заголовок модального окна
            document.getElementById('userServiceModalTitle').textContent = translations.edit_service_to_master;
            
            // Открываем модальное окно
            const modal = document.getElementById('userServiceModal');
            modal.style.display = 'block';
        } else {
            window.showNotification('error', translations.error_loading_service_data);
        }
    })
    .catch(error => {
        console.error('Ошибка при загрузке данных:', error);
        window.showNotification('error', translations.error_loading_data);
    });
}

function deleteUserService(userServiceId) {
    currentDeleteUserServiceId = userServiceId;
    const confirmationModal = document.getElementById('confirmationModal');
    confirmationModal.style.display = 'block';
}

function confirmDeleteUserService() {
    const userServiceId = currentDeleteUserServiceId;
    if (!userServiceId) {
        return;
    }

    fetch(`/booking/user-services/${userServiceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message);
            
            // Удаляем строку из таблицы
            const row = document.querySelector(`tr[data-user-service-id="${userServiceId}"]`);
            if (row) {
                row.remove();
            }
            
            // Удаляем мобильную карточку
            const card = document.querySelector(`.user-service-card[data-user-service-id="${userServiceId}"]`);
            if (card) {
                card.remove();
            }
            
            // Если таблица и карточки пустые, показываем сообщение
            const tbody = document.getElementById('user-services-tbody');
            const userServicesCards = document.getElementById('userServicesCards');
            const hasTableRows = tbody && tbody.children.length > 0;
            const hasCards = userServicesCards && userServicesCards.children.length > 0;
            
            if (!hasTableRows && !hasCards) {
                const noServicesMessage = document.querySelector('#tab-user-services .text-center');
                if (noServicesMessage) {
                    noServicesMessage.style.display = 'block';
                }
            }
            
            // Обновляем статистику в первой вкладке
            updateStatistics();
        } else {
            window.showNotification('error', 'Ошибка: ' + data.message);
        }
        
        // Закрываем модальное окно подтверждения в любом случае
        closeConfirmationModal();
    })
    .catch(error => {
        console.error('Ошибка при удалении:', error);
        window.showNotification('error', translations.error_deleting);
        // Закрываем модальное окно подтверждения даже при ошибке
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
    
    console.log('saveUserService - Отправляемые данные:', {
        userServiceId: userServiceId,
        data: data,
        isEdit: !!userServiceId
    });
    
    // Дополнительная отладка - проверяем значения полей формы
    console.log('saveUserService - Значения полей формы:', {
        price: document.getElementById('modal-price').value,
        duration: document.getElementById('modal-duration').value,
        description: document.getElementById('modal-description').value,
        isActive: document.getElementById('modal-is-active').checked
    });
    
    const url = userServiceId ? 
        `/booking/user-services/${userServiceId}` : 
        '/booking/user-services';
    
    const method = userServiceId ? 'PUT' : 'POST';
    
    console.log('saveUserService - URL и метод:', {
        url: url,
        method: method
    });
    
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
            
            console.log('Данные с сервера:', data);
            console.log('userServiceId:', userServiceId);
            
            if (!userServiceId) {
                // Если это новая запись, добавляем её в таблицу
                if (data.userService) {
                    console.log('Добавляем новую услугу:', data.userService);
                    addUserServiceToTable(data.userService);
                }
            } else {
                // Если это редактирование, обновляем существующую строку
                console.log('Обновляем существующую услугу:', data.userService);
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

function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'none';
    currentDeleteUserServiceId = null; // Сбрасываем ID при закрытии модального окна
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
    
    console.log('toggleUserServicesView вызвана. Ширина окна:', window.innerWidth);
    console.log('Найденные элементы:', {
        tableWrapper: !!tableWrapper,
        userServicesCards: !!userServicesCards
    });
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (userServicesCards) userServicesCards.style.display = 'grid';
        console.log('Переключено на мобильную версию');
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (userServicesCards) userServicesCards.style.display = 'none';
        console.log('Переключено на десктопную версию');
    }
}