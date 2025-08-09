// ===== СКРИПТЫ ДЛЯ BOOKING SCHEDULES =====

// Глобальные переменные
let currentUserId = null;
let scheduleData = {};

// Функция загрузки расписания пользователя
function loadUserSchedule(userId) {
    fetch(`/booking/schedules/user?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                scheduleData = data.schedule;
                showSchedule();
                renderScheduleTable();
            } else {
                console.error('Ошибка загрузки расписания:', data.message);
                showNotification('Ошибка загрузки расписания: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке расписания:', error);
            showNotification('Произошла ошибка при загрузке расписания', 'error');
        });
}

// Функция показа расписания
function showSchedule() {
    document.getElementById('schedule-container').style.display = 'block';
    document.getElementById('select-user-message').style.display = 'none';
}

// Функция скрытия расписания
function hideSchedule() {
    document.getElementById('schedule-container').style.display = 'none';
    document.getElementById('select-user-message').style.display = 'block';
}

// Функция рендеринга таблицы расписания
function renderScheduleTable() {
    const tbody = document.getElementById('schedule-tbody');
    tbody.innerHTML = '';
    
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
            notes: ''
        };
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${day.name}</strong></td>
            <td>
                ${dayData.is_working ? 
                    `${dayData.start_time} - ${dayData.end_time}` : 
                    '<span class="text-muted">Выходной</span>'
                }
            </td>
            <td>
                ${dayData.is_working ? 
                                    '<span class="status-badge working">Рабочий</span>' :
                '<span class="status-badge day-off">Выходной</span>'
                }
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editDay(${day.id})">
                    <i class="fas fa-edit"></i> Изменить
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Функция редактирования дня
function editDay(dayOfWeek) {
    const dayData = scheduleData[dayOfWeek] || {
        is_working: false,
        start_time: '09:00',
        end_time: '18:00',
        notes: ''
    };
    
    document.getElementById('edit-day-of-week').value = dayOfWeek;
    document.getElementById('edit-user-id').value = currentUserId;
    document.getElementById('edit-is-working').checked = dayData.is_working;
    document.getElementById('edit-start-time').value = dayData.start_time;
    document.getElementById('edit-end-time').value = dayData.end_time;
    document.getElementById('edit-notes').value = dayData.notes;
    
    toggleWorkingHoursFields();
    
    const modal = new bootstrap.Modal(document.getElementById('editDayModal'));
    modal.show();
}

// Функция переключения полей времени работы
function toggleWorkingHoursFields() {
    const isWorking = document.getElementById('edit-is-working').checked;
    const fields = document.getElementById('working-hours-fields');
    
    if (isWorking) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

// Функция сохранения расписания дня
function saveDaySchedule() {
    const dayOfWeek = document.getElementById('edit-day-of-week').value;
    const userId = document.getElementById('edit-user-id').value;
    const isWorking = document.getElementById('edit-is-working').checked;
    const startTime = document.getElementById('edit-start-time').value;
    const endTime = document.getElementById('edit-end-time').value;
    const notes = document.getElementById('edit-notes').value;
    
    // Валидация
    if (isWorking && (!startTime || !endTime)) {
        console.error('Не указано время работы');
        showNotification('Укажите время начала и окончания работы', 'error');
        return;
    }
    
    if (isWorking && startTime >= endTime) {
        console.error('Неправильное время работы');
        showNotification('Время окончания должно быть позже времени начала', 'error');
        return;
    }
    
    // Обновляем данные
    scheduleData[dayOfWeek] = {
        is_working: isWorking,
        start_time: startTime,
        end_time: endTime,
        notes: notes
    };
    
    // Закрываем модальное окно
    const modal = bootstrap.Modal.getInstance(document.getElementById('editDayModal'));
    if (modal) {
        modal.hide();
    }
    
    // Обновляем таблицу
    renderScheduleTable();
    
    
    showNotification('Расписание обновлено');
}

// Функция сохранения всего расписания
function saveSchedule() {
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
    
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
            showNotification(data.message);
        } else {
            console.error('Ошибка сохранения:', data.message);
            showNotification('Ошибка: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        showNotification('Произошла ошибка при сохранении', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Функция показа уведомлений
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const notificationText = document.getElementById('notification-text');
    
    if (!notification || !notificationText) {
        console.error('Notification elements not found');
        return;
    }
    
    const alert = notification.querySelector('.alert');
    if (!alert) {
        console.error('Alert element not found');
        return;
    }
    
    notificationText.textContent = message;
    
    // Убираем старые классы
    alert.className = 'alert';
    alert.classList.add('alert-' + type);
    
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('user-select');
    const editIsWorking = document.getElementById('edit-is-working');
    
    // Обработчик изменения выбора пользователя
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
    }
    
    // Обработчик для чекбокса
    if (editIsWorking) {
        editIsWorking.addEventListener('change', toggleWorkingHoursFields);
    }
});

// Функция закрытия модального окна для schedules.blade.php
function closeEditDayModal() {
    const modal = document.getElementById('editDayModal');
    if (modal) {
        modal.style.display = 'none';
    }
}