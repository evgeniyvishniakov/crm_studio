// ===== СКРИПТЫ ДЛЯ BOOKING INDEX =====

// Глобальные переменные
let currentUserId = null;
let scheduleData = {};
let currentDeleteUserServiceId = null;

// Переводы для JavaScript
const translations = {
    'add_master_service': 'Добавить услугу мастеру',
    'edit_service_to_master': 'Редактировать услугу мастера',
    'error_loading_service_data': 'Ошибка загрузки данных услуги',
    'error_loading_data': 'Ошибка загрузки данных',
    'error_saving': 'Ошибка сохранения',
    'error_loading_schedule': 'Ошибка загрузки расписания',
    'error_deleting': 'Ошибка удаления',
    'saving': 'Сохранение...'
};

// Функция для форматирования длительности (аналог PHP TimeHelper::formatDuration)
function formatDuration(minutes) {
    if (!minutes || minutes < 60) {
        return '~' + minutes + ' мин';
    } else {
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        
        if (remainingMinutes == 0) {
            return '~' + hours + ' час';
        } else {
            const hourText = hours + ' час';
            const minuteText = remainingMinutes + ' мин';
            return '~' + hourText + ' ' + minuteText;
        }
    }
}

// Функция для форматирования валюты
function formatCurrency(amount) {
    if (window.CurrencyManager) {
        return window.CurrencyManager.formatAmount(amount);
    } else {
        // Fallback форматирование
        const num = parseFloat(amount);
        if (isNaN(num)) return amount;
        return num.toLocaleString('ru-RU') + ' ₽';
    }
}

// Функция для копирования URL записи
function copyBookingUrl() {
    const urlInput = document.getElementById('booking-url');
    urlInput.select();
    document.execCommand('copy');
    
    window.showNotification('success', 'Ссылка скопирована в буфер обмена');
}

// Функция для очистки ошибок
function clearErrors(formId = 'addServiceForm') {
    const form = document.getElementById(formId);
    if (form) {
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        form.querySelectorAll('.has-error').forEach(el => {
            el.classList.remove('has-error');
        });
    }
}

// Функция для отображения ошибок
function showErrors(errors, formId = 'addServiceForm') {
    clearErrors(formId);

    Object.entries(errors).forEach(([field, messages]) => {
        const input = document.querySelector(`#${formId} [name="${field}"]`);
        if (input) {
            const inputGroup = input.closest('.form-group');
            inputGroup.classList.add('has-error');

            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
            errorElement.style.color = '#f44336';
            errorElement.style.marginTop = '5px';
            errorElement.style.fontSize = '0.85rem';

            inputGroup.appendChild(errorElement);
        }
    });
}

// ===== ФУНКЦИИ ДЛЯ РАБОТЫ С ВКЛАДКАМИ И ФОРМАМИ =====

// Обработка вкладок
function initializeTabs() {
    // Получаем сохраненную активную вкладку из localStorage
    let activeTab = localStorage.getItem('bookingActiveTab');
    // Если в localStorage что-то не то, или вкладка не существует — по умолчанию 'booking-settings'
    if (!activeTab || !document.querySelector(`[data-tab="${activeTab}"]`)) {
        activeTab = 'booking-settings';
        localStorage.setItem('bookingActiveTab', activeTab);
    }
    // Показываем активную вкладку
    showTab(activeTab);
    
    // Добавляем обработчики для кнопок вкладок
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
            
            // Сохраняем активную вкладку в localStorage
            localStorage.setItem('bookingActiveTab', tabName);
        });
    });
}

// Функция для показа вкладки
function showTab(tabName) {
    // Скрываем все вкладки
    document.querySelectorAll('.settings-pane').forEach(pane => {
        pane.style.display = 'none';
    });
    
    // Убираем активный класс со всех кнопок
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Показываем нужную вкладку
    const targetPane = document.getElementById('tab-' + tabName);
    if (targetPane) {
        targetPane.style.display = 'block';
    }
    
    // Добавляем активный класс к нужной кнопке
    const targetButton = document.querySelector(`[data-tab="${tabName}"]`);
    if (targetButton) {
        targetButton.classList.add('active');
    }
}

// Обработка формы настроек записи
function initializeBookingForm() {
    const form = document.getElementById('booking-settings-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + translations.saving;
            
            // Правильно обрабатываем boolean значения
            const formDataObj = {};
            for (let [key, value] of formData.entries()) {
                if (key === 'booking_enabled' || key === 'allow_same_day_booking') {
                    formDataObj[key] = value === 'on' || value === 'true' || value === true;
                } else {
                    formDataObj[key] = value;
                }
            }
            
            fetch('/booking/settings', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formDataObj)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showNotification('success', data.message);
                    
                    // Обновляем URL если он изменился
                    if (data.booking_url) {
                        document.getElementById('booking-url').value = data.booking_url;
                    }
                    
                    // Показываем/скрываем блок с URL
                    const urlBlock = document.getElementById('booking-url-block');
                    if (formDataObj.booking_enabled) {
                        urlBlock.style.display = 'block';
                    } else {
                        urlBlock.style.display = 'none';
                    }
                } else {
                    window.showNotification('error', 'Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                window.showNotification('error', translations.error_saving);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
}

// Обработка выбора мастера для расписания
function initializeUserSelect() {
    const userSelect = document.getElementById('user-select');
    
    if (userSelect) {
        userSelect.addEventListener('change', function() {
            const userId = this.value;
            if (userId) {
                currentUserId = userId;
                loadUserSchedule(userId);
            } else {
                hideSchedule();
            }
        });
        
        // Автоматически загружаем расписание выбранного мастера при инициализации
        if (userSelect.value) {
            currentUserId = userSelect.value;
            loadUserSchedule(userSelect.value);
        }
    }
}

// Обработка чекбоксов
function initializeCheckboxes() {
    const editIsWorking = document.getElementById('edit-is-working');
    
    // Добавляем обработчик для чекбокса
    if (editIsWorking) {
        editIsWorking.addEventListener('change', toggleWorkingHoursFields);
    }
    
    // Обработчик для чекбокса веб-записи
    const bookingEnabledCheckbox = document.getElementById('booking_enabled');
    if (bookingEnabledCheckbox) {
        bookingEnabledCheckbox.addEventListener('change', function() {
            const urlBlock = document.getElementById('booking-url-block');
            if (this.checked) {
                urlBlock.style.display = 'block';
            } else {
                urlBlock.style.display = 'none';
            }
        });
    }
}

// Функция для переключения полей времени работы
function toggleWorkingHoursFields() {
    const isWorking = document.getElementById('edit-is-working').checked;
    const fields = document.getElementById('working-hours-fields');
    
    if (isWorking) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

// ===== ФУНКЦИИ ДЛЯ РАБОТЫ С РАСПИСАНИЕМ =====

function loadUserSchedule(userId) {
    // Очищаем предыдущие данные
    scheduleData = {};
    
    fetch(`/booking/schedules/user?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                scheduleData = data.schedule;
                showSchedule();
                renderScheduleTable();
            } else {
                console.error('Ошибка загрузки расписания:', data.message);
                window.showNotification('error', 'Ошибка загрузки расписания: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке расписания:', error);
            window.showNotification('error', translations.error_loading_schedule);
        });
}

function showSchedule() {
    document.getElementById('schedule-container').style.display = 'block';
    document.getElementById('select-user-message').style.display = 'none';
    toggleScheduleView(); // Переключаем на правильную версию
}

function hideSchedule() {
    document.getElementById('schedule-container').style.display = 'none';
    document.getElementById('select-user-message').style.display = 'block';
}

// Функция для переключения между десктопной и мобильной версией расписания
function toggleScheduleView() {
    const tableWrapper = document.querySelector('#schedule-container .table-wrapper');
    const scheduleCards = document.getElementById('scheduleCards');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (scheduleCards) scheduleCards.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (scheduleCards) scheduleCards.style.display = 'none';
    }
}

function renderScheduleTable() {
    const tbody = document.getElementById('schedule-tbody');
    const scheduleCards = document.getElementById('scheduleCards');
    
    // Очищаем содержимое
    if (tbody) tbody.innerHTML = '';
    if (scheduleCards) scheduleCards.innerHTML = '';
    
    // Проверяем, что данные есть
    if (!scheduleData) {
        return;
    }
    
    const days = [
        { id: 1, name: 'Понедельник' },
        { id: 2, name: 'Вторник' },
        { id: 3, name: 'Среда' },
        { id: 4, name: 'Четверг' },
        { id: 5, name: 'Пятница' },
        { id: 6, name: 'Суббота' },
        { id: 0, name: 'Воскресенье' }
    ];
    
    days.forEach(day => {
        const dayData = scheduleData[day.id] || {
            is_working: false,
            start_time: '09:00',
            end_time: '18:00',
            notes: '',
            booking_interval: null
        };
        
        // Создаем строку для десктопной таблицы
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${day.name}</strong></td>
            <td>
                ${dayData.is_working ? 
                    `${dayData.start_time} - ${dayData.end_time}` : 
                    '<span style="color: #6b7280;">Выходной</span>'
                }
                ${dayData.is_working && dayData.booking_interval ? 
                    `<br><small style="color: #3b82f6;">Интервал: ${dayData.booking_interval} мин</small>` : 
                    ''
                }
            </td>
            <td>
                ${dayData.is_working ? 
                    '<span class="badge badge-success">Рабочий</span>' : 
                    '<span class="badge badge-secondary">Выходной</span>'
                }
            </td>
            <td>
                ${dayData.notes ? 
                    `<span style="color: #6b7280; font-size: 13px;">${dayData.notes}</span>` : 
                    '<span style="color: #9ca3af; font-style: italic;">Нет заметок</span>'
                }
            </td>
            <td class="actions-cell">
                <button type="button" class="btn-view" onclick="editDay(${day.id})" title="Редактировать" style="display: flex; align-items: center; gap: 6px;">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Редактировать
                </button>
            </td>
        `;
        tbody.appendChild(row);
        
        // Создаем карточку для мобильной версии
        const card = document.createElement('div');
        card.className = 'schedule-card';
        card.id = `schedule-card-${day.id}`;
        card.innerHTML = `
            <div class="schedule-card-header">
                <div class="schedule-main-info">
                    <h3 class="schedule-day-name">${day.name}</h3>
                    <div class="schedule-status">
                        ${dayData.is_working ? 
                            '<span class="status-badge working">Рабочий</span>' : 
                            '<span class="status-badge day-off">Выходной</span>'
                        }
                    </div>
                </div>
            </div>
            <div class="schedule-info">
                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Рабочие часы
                    </div>
                    <div class="schedule-info-value">
                        ${dayData.is_working ? 
                            `${dayData.start_time} - ${dayData.end_time}` : 
                            'Выходной'
                        }
                    </div>
                </div>
                ${dayData.is_working && dayData.booking_interval ? `
                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Интервал
                    </div>
                    <div class="schedule-info-value">
                        ${dayData.booking_interval} мин
                    </div>
                </div>
                ` : ''}
                ${dayData.notes ? `
                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                        Заметки
                    </div>
                    <div class="schedule-info-value">
                        ${dayData.notes}
                    </div>
                </div>
                ` : ''}
            </div>
            <div class="schedule-actions">
                <button type="button" class="btn-edit" onclick="editDay(${day.id})" title="Редактировать">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Редактировать
                </button>
            </div>
        `;
        scheduleCards.appendChild(card);
    });
}

function editDay(dayOfWeek) {
    const dayData = scheduleData[dayOfWeek] || {
        is_working: false,
        start_time: '09:00',
        end_time: '18:00',
        notes: '',
        booking_interval: null
    };
    
    document.getElementById('edit-day-of-week').value = dayOfWeek;
    document.getElementById('edit-user-id').value = currentUserId;
    document.getElementById('edit-is-working').checked = dayData.is_working;
    document.getElementById('edit-start-time').value = dayData.start_time;
    document.getElementById('edit-end-time').value = dayData.end_time;
    document.getElementById('edit-notes').value = dayData.notes;
    document.getElementById('edit-booking-interval').value = dayData.booking_interval || '30';
    
    toggleWorkingHoursFields();
    
    // Простое отображение модального окна
    const modal = document.getElementById('editDayModal');
    modal.style.display = 'block';
}

function closeModal() {
    const modal = document.getElementById('editDayModal');
    modal.style.display = 'none';
    
    // Очищаем форму
    document.getElementById('edit-day-of-week').value = '';
    document.getElementById('edit-user-id').value = '';
    document.getElementById('edit-is-working').checked = false;
    document.getElementById('edit-start-time').value = '';
    document.getElementById('edit-end-time').value = '';
    document.getElementById('edit-notes').value = '';
    document.getElementById('edit-booking-interval').value = '';
}

function saveDaySchedule() {
    const dayOfWeek = document.getElementById('edit-day-of-week').value;
    const userId = document.getElementById('edit-user-id').value;
    const isWorking = document.getElementById('edit-is-working').checked;
    const startTime = document.getElementById('edit-start-time').value;
    const endTime = document.getElementById('edit-end-time').value;
    const notes = document.getElementById('edit-notes').value;
    const bookingInterval = document.getElementById('edit-booking-interval').value;
    
    // Валидация
    if (isWorking && (!startTime || !endTime)) {
        console.error('Не указано время работы');
        window.showNotification('error', 'Укажите время начала и окончания работы');
        return;
    }
    
    if (isWorking && startTime >= endTime) {
        console.error('Неправильное время работы');
        window.showNotification('error', 'Время окончания должно быть позже времени начала');
        return;
    }
    
    // Валидация интервала
    if (!bookingInterval || bookingInterval < 15 || bookingInterval > 120) {
        console.error('Неправильный интервал');
        window.showNotification('error', 'Интервал должен быть от 15 до 120 минут');
        return;
    }
    
    // Обновляем данные в локальном объекте
    scheduleData[dayOfWeek] = {
        is_working: isWorking,
        start_time: startTime,
        end_time: endTime,
        notes: notes,
        booking_interval: parseInt(bookingInterval)
    };
    
    // Закрываем модальное окно
    closeModal();
    
    // Обновляем таблицу и карточки
    renderScheduleTable();
    
    // Сразу сохраняем в базу данных
    fetch('/booking/schedules/save', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: currentUserId,
            schedule: scheduleData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', 'Расписание успешно сохранено');
        } else {
            console.error('Ошибка сохранения:', data.message);
            window.showNotification('error', 'Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        window.showNotification('error', translations.error_saving);
    });
}

// ===== ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ СТРАНИЦЫ =====

document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем все компоненты
    initializeTabs();
    initializeBookingForm();
    initializeUserSelect();
    initializeCheckboxes();
    
    // Добавляем обработчики для модальных окон
    initializeModals();
    
    // Добавляем обработчики для клавиши Escape
    initializeKeyboardHandlers();
    
    // Добавляем обработчики изменения размера окна
    initializeResizeHandlers();
    
    // Обработчики для модального окна подтверждения
    initializeConfirmationHandlers();
    
    // Обработчик поиска в таблице услуг мастеров
    initializeSearchHandlers();
    
    // Инициализируем переключение видов
    initializeViewToggles();
});

// Функции инициализации
function initializeModals() {
    // Добавляем обработчик для закрытия модального окна по клику на backdrop
    const modalElement = document.getElementById('editDayModal');
    if (modalElement) {
        modalElement.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
    
    // Добавляем обработчик для модального окна услуг мастеров
    const userServiceModal = document.getElementById('userServiceModal');
    if (userServiceModal) {
        userServiceModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeUserServiceModal();
            }
        });
    }
}

function initializeKeyboardHandlers() {
    // Добавляем обработчик для клавиши Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const editModal = document.getElementById('editDayModal');
            const userServiceModal = document.getElementById('userServiceModal');
            const confirmationModal = document.getElementById('confirmationModal');
            
            if (editModal && editModal.style.display === 'block') {
                closeModal();
            }
            if (userServiceModal && userServiceModal.style.display === 'block') {
                closeUserServiceModal();
            }
            if (confirmationModal && confirmationModal.style.display === 'block') {
                closeConfirmationModal();
            }
        }
    });
}

function initializeResizeHandlers() {
    // Добавляем обработчик изменения размера окна для расписания
    window.addEventListener('resize', function() {
        if (document.getElementById('schedule-container').style.display !== 'none') {
            toggleScheduleView();
        }
    });
}

function initializeConfirmationHandlers() {
    // Обработчики для модального окна подтверждения
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
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
    
    // Закрытие модального окна подтверждения при клике вне его
    const confirmationModal = document.getElementById('confirmationModal');
    if (confirmationModal) {
        confirmationModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmationModal();
            }
        });
    }
}

function initializeSearchHandlers() {
    // Обработчик поиска в таблице услуг мастеров
    const searchInput = document.getElementById('userServicesSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchUserServices(this.value);
        });
    }
}

function initializeViewToggles() {
    // Переключаем вид услуг мастеров
    toggleUserServicesView();
    
    // Добавляем обработчик изменения размера окна
    window.addEventListener('resize', function() {
        toggleUserServicesView();
    });
} 