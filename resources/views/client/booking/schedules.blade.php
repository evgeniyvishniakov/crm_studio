@extends('client.layouts.app')

@section('title', 'Расписание мастеров - Веб-запись')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Расписание мастеров - Веб-запись
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Выбор мастера -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-select">Выберите мастера</label>
                                <select class="form-control" id="user-select">
                                    <option value="">Выберите мастера...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Расписание -->
                    <div id="schedule-container" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">Расписание на неделю</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>День недели</th>
                                                <th>Рабочие часы</th>
                                                <th>Статус</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody id="schedule-tbody">
                                            <!-- Расписание будет загружено через AJAX -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" onclick="saveSchedule()">
                                        <i class="fas fa-save"></i> Сохранить расписание
                                    </button>
                                    <a href="{{ route('client.booking.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Назад к настройкам
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Сообщение о выборе мастера -->
                    <div id="select-user-message" class="text-center py-5">
                        <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                        <h5>Выберите мастера</h5>
                        <p class="text-muted">Выберите мастера из списка выше, чтобы настроить его расписание</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для редактирования дня -->
<div class="modal fade" id="editDayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Настройка расписания</h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="day-schedule-form">
                    <input type="hidden" id="edit-day-of-week">
                    <input type="hidden" id="edit-user-id">
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit-is-working">
                            <label class="custom-control-label" for="edit-is-working">
                                <strong>Рабочий день</strong>
                            </label>
                        </div>
                    </div>

                    <div id="working-hours-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit-start-time">Начало работы</label>
                                    <input type="time" class="form-control" id="edit-start-time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit-end-time">Конец работы</label>
                                    <input type="time" class="form-control" id="edit-end-time">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit-notes">Примечания</label>
                            <textarea class="form-control" id="edit-notes" rows="3" placeholder="Дополнительная информация..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="saveDaySchedule()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Уведомление -->
<div id="notification" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999; display: none;">
    <div class="alert alert-success">
        <i class="fas fa-check"></i> <span id="notification-text"></span>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentUserId = null;
let scheduleData = {};



function loadUserSchedule(userId) {
    fetch(`{{ route('client.booking.get-user-schedule') }}?user_id=${userId}`)
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

function showSchedule() {
    document.getElementById('schedule-container').style.display = 'block';
    document.getElementById('select-user-message').style.display = 'none';
}

function hideSchedule() {
    document.getElementById('schedule-container').style.display = 'none';
    document.getElementById('select-user-message').style.display = 'block';
}

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
                    '<span class="badge badge-success">Рабочий</span>' : 
                    '<span class="badge badge-secondary">Выходной</span>'
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

function toggleWorkingHoursFields() {
    const isWorking = document.getElementById('edit-is-working').checked;
    const fields = document.getElementById('working-hours-fields');
    
    if (isWorking) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

// Добавляем обработчик события после загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('user-select');
    const editIsWorking = document.getElementById('edit-is-working');
    
    userSelect.addEventListener('change', function() {
        const userId = this.value;
        if (userId) {
            currentUserId = userId;
            loadUserSchedule(userId);
        } else {
            hideSchedule();
        }
    });
    
    // Добавляем обработчик для чекбокса
    if (editIsWorking) {
        editIsWorking.addEventListener('change', toggleWorkingHoursFields);
    }
});

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
    
    console.log('Расписание обновлено');
    showNotification('Расписание обновлено');
}

function saveSchedule() {
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
    
    fetch('{{ route("client.booking.save-user-schedule") }}', {
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
</script>
@endpush 