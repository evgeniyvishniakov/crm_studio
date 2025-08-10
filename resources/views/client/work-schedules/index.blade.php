@extends('client.layouts.app')

@section('title', 'График работы')

@section('content')

<div class="dashboard-container">
    <div class="settings-header">
        <h1>График работы</h1>
    </div>
    
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button" data-tab="schedule-overview">
            <i class="fa fa-calendar-week" style="margin-right:8px;"></i>Обзор
        </button>
        <button class="tab-button" data-tab="weekly-schedule">
            <i class="fa fa-calendar-alt" style="margin-right:8px;"></i>Расписание
        </button>
        <button class="tab-button" data-tab="time-offs">
            <i class="fa fa-umbrella-beach" style="margin-right:8px;"></i>Отпуска
        </button>
        <button class="tab-button" data-tab="schedule-reports">
            <i class="fa fa-chart-line" style="margin-right:8px;"></i>Отчеты
        </button>
    </div>
    

    
    <div class="settings-content">
        <!-- Вкладка обзора -->
        <div class="settings-pane" id="tab-schedule-overview">
            <h5>Обзор графика работы</h5>
            
            <!-- Статистика -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">Всего сотрудников</h5>
                                    <h3 class="mt-3 mb-3">{{ $stats['total_employees'] ?? 0 }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded">
                                        <i class="mdi mdi-account-group font-20 text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">Работает сегодня</h5>
                                    <h3 class="mt-3 mb-3">{{ $stats['working_today'] ?? 0 }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success rounded">
                                        <i class="mdi mdi-account-check font-20 text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">Записей на неделю</h5>
                                    <h3 class="mt-3 mb-3">{{ $stats['appointments_this_week'] ?? 0 }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-info rounded">
                                        <i class="mdi mdi-calendar-check font-20 text-info"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">Часов на неделю</h5>
                                    <h3 class="mt-3 mb-3">{{ $stats['hours_this_week'] ?? 0 }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-warning rounded">
                                        <i class="mdi mdi-clock-outline font-20 text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Расписание на текущую неделю -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">Расписание на неделю</h4>
                        <div class="calendar-nav">
                            <button id="schedulePrevBtn" class="calendar-nav-btn" onclick="previousWeek()" title="Предыдущая неделя">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="current-week-dates" class="calendar-month-title"></span>
                            <button id="scheduleNextBtn" class="calendar-nav-btn" onclick="nextWeek()" title="Следующая неделя">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Панель настроек отображения -->
                    <div class="schedule-display-settings mb-3">
                        <div class="settings-panel">
                            <span class="settings-title">Показать:</span>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="showAppointments" checked>
                                <label class="custom-control-label" for="showAppointments">
                                    Количество записей
                                </label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="showFreeHours" checked>
                                <label class="custom-control-label" for="showFreeHours">
                                    Свободные часы
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="table-striped schedule-overview-table">
                            <thead>
                                <tr>
                                    <th>Сотрудник</th>
                                    <th>ПН</th>
                                    <th>ВТ</th>
                                    <th>СР</th>
                                    <th>ЧТ</th>
                                    <th>ПТ</th>
                                    <th>СБ</th>
                                    <th>ВС</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentWeekSchedules ?? [] as $employeeSchedule)
                                <tr>
                                    <td>{{ $employeeSchedule['employee']->name }}</td>
                                    @foreach($employeeSchedule['schedule'] as $day)
                                    <td>
                                        @if($day['is_working'])
                                            <span class="schedule-time working">
                                                {{ $day['start_time'] }}-{{ $day['end_time'] }}
                                            </span>
                                            <div class="schedule-stats">
                                                <span class="appointments-count show-appointments">📅 
                                                    @php
                                                        $count = $day['appointments_count'] ?? 0;
                                                        $lastDigit = $count % 10;
                                                        $lastTwoDigits = $count % 100;
                                                        
                                                        if ($count === 0) {
                                                            echo 'Без записей';
                                                        } elseif ($lastTwoDigits >= 11 && $lastTwoDigits <= 14) {
                                                            echo $count . ' записей';
                                                        } elseif ($lastDigit === 1) {
                                                            echo $count . ' запись';
                                                        } elseif ($lastDigit >= 2 && $lastDigit <= 4) {
                                                            echo $count . ' записи';
                                                        } else {
                                                            echo $count . ' записей';
                                                        }
                                                    @endphp
                                                </span>
                                                @if(($day['free_hours'] ?? 0) > 0)
                                                    <span class="free-time show-free-hours">⏰ 
                                                        @if(($day['appointments_count'] ?? 0) === 0)
                                                            В ожидании
                                                        @else
                                                            {{ $day['free_hours'] }}ч свободно
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="schedule-time day-off">
                                                Выходной
                                            </span>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Предстоящие отпуска -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">Предстоящие отпуска</h4>
                    </div>

                    @if($upcomingTimeOffs->count() > 0)
                        <div class="table-wrapper">
                            <table class="table-striped schedule-overview-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Сотрудник</th>
                                        <th style="text-align: center;">Тип</th>
                                        <th style="text-align: center;">Период</th>
                                        <th style="text-align: center;">Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingTimeOffs as $timeOff)
                                    <tr>
                                        <td style="text-align: center;">{{ $timeOff->user->name }}</td>
                                        <td style="text-align: center;">{{ $timeOff->type_text }}</td>
                                        <td style="text-align: center;">
                                            {{ $timeOff->start_date->format('d.m.Y') }} - 
                                            {{ $timeOff->end_date->format('d.m.Y') }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="status-badge status-{{ $timeOff->status }}">
                                                {{ $timeOff->status_text }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-information-outline text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">Нет предстоящих отпусков</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Вкладка расписания -->
        <div class="settings-pane" id="tab-weekly-schedule" style="display: none;">
            <div class="clients-header">
                <div class="header-top">
                    <h1>Настройки расписания</h1>
                    <div class="header-actions">
                        <div class="form-group mb-3" style="margin-bottom: 0;">
                            <label for="schedule-user-select" style="margin-bottom: 8px; font-weight: 600; color: #333;">Выберите сотрудника</label>
                            <select class="form-control" id="schedule-user-select" style="min-width: 250px; border-radius: 8px; border: 1px solid #d1d5db; padding: 8px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease;">
                                <option value="">Выберите сотрудника...</option>
                                @foreach($allEmployees as $employee)
                                    @if($employee)
                                        <option value="{{ $employee->id }}" {{ $loop->first ? 'selected' : '' }}>
                                            {{ $employee->name ?? 'Удаленный пользователь' }} 
                                            ({{ config('roles.' . $employee->role, $employee->role) }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Расписание -->
            <div id="schedule-management-container" style="display: none;">
                <!-- Десктопная таблица -->
                <div class="table-wrapper" style="margin-top: 20px;">
                    <table class="table-striped sale-table" id="scheduleManagementTable" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: white; border: 1px solid #e5e7eb;">
                        <thead>
                            <tr>
                                <th style="text-align: center;">День недели</th>
                                <th style="text-align: center;">Рабочие часы</th>
                                <th style="text-align: center;">Статус</th>
                                <th style="text-align: center;">Примечания</th>
                                <th style="text-align: center;">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="schedule-management-tbody">
                            <!-- Расписание будет загружено через AJAX -->
                        </tbody>
                    </table>
                        </div>

                <!-- Мобильные карточки расписания -->
                <div class="schedule-cards" id="scheduleManagementCards" style="display: none;">
                    <!-- Карточки будут добавлены через JavaScript -->
                    </div>
                </div>

            <!-- Сообщение о выборе сотрудника -->
            <div id="select-employee-message" class="text-center py-5" style="margin-top: 40px;">
                <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                <h5>Выберите сотрудника</h5>
                <p class="text-muted">Выберите сотрудника из списка выше, чтобы настроить его расписание</p>
            </div>
        </div>

        <!-- Вкладка отпусков -->
        <div class="settings-pane" id="tab-time-offs" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Управление отпусками</h5>
                <button class="btn btn-primary" onclick="showTimeOffModal()">
                    <i class="fa fa-plus"></i> Добавить отпуск
                </button>
            </div>

            <!-- Таблица отпусков -->
            <div class="table-wrapper">
                <table class="table-striped schedule-overview-table" id="timeOffsTable" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: white; border: 1px solid #e5e7eb;">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Сотрудник</th>
                            <th style="text-align: center;">Тип</th>
                            <th style="text-align: center;">Период</th>
                            <th style="text-align: center;">Причина</th>
                            <th style="text-align: center;">Статус</th>
                            <th style="text-align: center;">Действия</th>
                        </tr>
                    </thead>
                    <tbody id="time-offs-tbody">
                        <!-- Данные будут загружены через AJAX -->
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin"></i> Загрузка...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Вкладка отчетов -->
        <div class="settings-pane" id="tab-schedule-reports" style="display: none;">
            <h5>Отчеты по работе</h5>
            
            <!-- Статистика по сотрудникам -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Статистика рабочего времени</h4>
                            <div class="text-center py-5">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <h5>Отчеты в разработке</h5>
                                <p class="text-muted">Детальные отчеты по рабочему времени будут доступны в следующей версии</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для редактирования расписания -->
<div id="editScheduleModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 style="margin: 0;">Настройка расписания</h5>
            <span style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeScheduleModal()">&times;</span>
        </div>
        <div>
            <form id="schedule-day-form">
                <input type="hidden" id="edit-schedule-day-of-week">
                <input type="hidden" id="edit-schedule-user-id">
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="edit-schedule-is-working">
                        <label class="custom-control-label" for="edit-schedule-is-working">
                            <strong>Рабочий день</strong>
                        </label>
                    </div>
                </div>

                <div id="schedule-working-hours-fields">
                    <div class="form-group" style="display: flex; gap: 15px; align-items: end;">
                        <div style="flex: 1;">
                            <label for="edit-schedule-start-time">Начало работы</label>
                            <input type="time" class="form-control" id="edit-schedule-start-time">
                        </div>
                        <div style="flex: 1;">
                            <label for="edit-schedule-end-time">Конец работы</label>
                            <input type="time" class="form-control" id="edit-schedule-end-time">
                        </div>
                        <div style="flex: 1;">
                            <label for="edit-schedule-booking-interval">Интервал записи (минуты)</label>
                            <input type="number" class="form-control" id="edit-schedule-booking-interval" min="15" max="120" step="15" value="30" placeholder="30">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-schedule-notes">Примечания</label>
                        <textarea class="form-control" id="edit-schedule-notes" rows="3" placeholder="Дополнительная информация..."></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="closeScheduleModal()">Отмена</button>
            <button type="button" class="btn-submit" onclick="saveScheduleDay()">Сохранить</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Переводы и данные для JavaScript
window.translations = {
    working: 'Работает',
    day_off: 'Выходной',
    vacation: 'Отпуск',
    sick_leave: 'Больничный',
    personal_leave: 'Личный отпуск',
    unpaid_leave: 'Отпуск без сохранения зарплаты'
};

// Функции управления вкладками (используем те же что в salary)
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация вкладок
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.settings-pane');

    // Функция переключения вкладки
    function switchTab(tabId) {
            // Убираем активный класс со всех кнопок
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // Скрываем все панели
            tabPanes.forEach(pane => pane.style.display = 'none');
            
            // Активируем текущую кнопку и панель
        const activeButton = document.querySelector(`[data-tab="${tabId}"]`);
        const activePane = document.getElementById('tab-' + tabId);
        
        if (activeButton && activePane) {
            activeButton.classList.add('active');
            activePane.style.display = 'block';
        }
        
        // Сохраняем активную вкладку в localStorage
        localStorage.setItem('workSchedules_activeTab', tabId);
    }

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            switchTab(tabId);
            
            // Загружаем данные при переходе на вкладку отпусков
            if (tabId === 'time-offs') {
                loadTimeOffsData();
            }
        });
    });
    
    // Восстанавливаем активную вкладку при загрузке
    const savedTab = localStorage.getItem('workSchedules_activeTab');
    if (savedTab) {
        switchTab(savedTab);
        // Загружаем данные для восстановленной вкладки
        if (savedTab === 'time-offs') {
            loadTimeOffsData();
        }
    } else {
        // Если нет сохраненной вкладки, показываем первую (обзор)
        switchTab('schedule-overview');
    }
    
    // Восстанавливаем сохраненную неделю
    const savedWeekOffset = localStorage.getItem('workSchedules_weekOffset');
    if (savedWeekOffset) {
        currentWeekOffset = parseInt(savedWeekOffset);
    }
    
    // Инициализация отображения недели
    updateWeekDisplay();
    
    // Загружаем данные только если это не текущая неделя
    if (currentWeekOffset !== 0) {
        loadWeekSchedule();
    }
    
    // Инициализируем настройки отображения статистики
    initDisplaySettings();
    
    // Обработчик для выбора сотрудника в управлении расписанием
    const scheduleUserSelect = document.getElementById('schedule-user-select');
    if (scheduleUserSelect) {
        scheduleUserSelect.addEventListener('change', function() {
            const employeeId = this.value;
            if (employeeId) {
                currentScheduleUserId = employeeId;
                loadEmployeeScheduleForManagement(employeeId);
                // Сохраняем выбранного сотрудника
                localStorage.setItem('workSchedules_selectedEmployee', employeeId);
            } else {
                hideScheduleManagement();
                localStorage.removeItem('workSchedules_selectedEmployee');
            }
        });
        
        // Восстанавливаем выбранного сотрудника
        const savedEmployee = localStorage.getItem('workSchedules_selectedEmployee');
        if (savedEmployee) {
            scheduleUserSelect.value = savedEmployee;
            currentScheduleUserId = savedEmployee;
            loadEmployeeScheduleForManagement(savedEmployee);
        } else if (scheduleUserSelect.value) {
            // Если есть выбранный по умолчанию сотрудник, загружаем его расписание
            currentScheduleUserId = scheduleUserSelect.value;
            loadEmployeeScheduleForManagement(scheduleUserSelect.value);
        }
    }
    
    // Обработчик для чекбокса в модальном окне
    const editScheduleIsWorking = document.getElementById('edit-schedule-is-working');
    if (editScheduleIsWorking) {
        editScheduleIsWorking.addEventListener('change', toggleScheduleWorkingHoursFields);
    }
    
    // Обработчик для закрытия модального окна при клике вне его
    const scheduleModal = document.getElementById('editScheduleModal');
    if (scheduleModal) {
        scheduleModal.addEventListener('click', function(event) {
            if (event.target === scheduleModal) {
                closeScheduleModal();
            }
        });
    }
    
    // Обработчик для клавиши Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const scheduleModal = document.getElementById('editScheduleModal');
            const timeOffModal = document.getElementById('timeOffModal');
            
            if (scheduleModal && scheduleModal.style.display === 'block') {
                closeScheduleModal();
            }
            if (timeOffModal && timeOffModal.style.display === 'block') {
                closeTimeOffModal();
            }
        }
    });
    
    // Обработчик для закрытия модального окна отпусков при клике вне его
    const timeOffModal = document.getElementById('timeOffModal');
    if (timeOffModal) {
        timeOffModal.addEventListener('click', function(event) {
            if (event.target === timeOffModal) {
                closeTimeOffModal();
            }
        });
    }
});

// Переменные для работы с неделями
let currentWeekOffset = 0; // 0 = текущая неделя, -1 = прошлая, +1 = следующая

// Функции для календаря
function previousWeek() {
    currentWeekOffset--;
    localStorage.setItem('workSchedules_weekOffset', currentWeekOffset);
    loadWeekSchedule();
    updateWeekDisplay();
}

function nextWeek() {
    currentWeekOffset++;
    localStorage.setItem('workSchedules_weekOffset', currentWeekOffset);
    loadWeekSchedule();
    updateWeekDisplay();
}

function currentWeek() {
    currentWeekOffset = 0;
    localStorage.setItem('workSchedules_weekOffset', currentWeekOffset);
    loadWeekSchedule();
    updateWeekDisplay();
}

// Обновить отображение текущей недели
function updateWeekDisplay() {
    const today = new Date();
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - today.getDay() + 1 + (currentWeekOffset * 7)); // Понедельник
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6); // Воскресенье
    
    const startStr = startOfWeek.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' });
    const endStr = endOfWeek.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
    
    document.getElementById('current-week-dates').textContent = `${startStr} - ${endStr}`;
}

// Загрузить расписание недели
function loadWeekSchedule() {
    console.log('Loading week schedule for offset:', currentWeekOffset);
    
    fetch(`{{ route('work-schedules.week') }}?offset=${currentWeekOffset}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {

                
                updateOverviewScheduleTable(data.schedules);
                if (data.warning) {
                    // Показываем предупреждение только раз в месяц
                    showWarningOncePerMonth(data.warning);
                }
            } else {
                console.error('Ошибка загрузки данных недели:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка запроса данных недели:', error);
        });
}

// Переменные для управления расписанием
let currentScheduleUserId = null;
let scheduleManagementData = {};

// Функция загрузки расписания сотрудника для управления
function loadEmployeeScheduleForManagement(employeeId) {
    fetch(`{{ route("work-schedules.employee-schedule") }}?employee_id=${employeeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                scheduleManagementData = data.schedule;
                showScheduleManagement();
                renderScheduleManagementTable();
            } else {
                console.error('Ошибка загрузки расписания:', data.message);
                window.showNotification('error', 'Ошибка загрузки расписания: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке расписания:', error);
            window.showNotification('error', 'Произошла ошибка при загрузке расписания');
        });
}

// Функция показа управления расписанием
function showScheduleManagement() {
    document.getElementById('schedule-management-container').style.display = 'block';
    document.getElementById('select-employee-message').style.display = 'none';
}

// Функция скрытия управления расписанием
function hideScheduleManagement() {
    document.getElementById('schedule-management-container').style.display = 'none';
    document.getElementById('select-employee-message').style.display = 'block';
}

// Функция рендеринга таблицы управления расписанием
function renderScheduleManagementTable() {
    const tbody = document.getElementById('schedule-management-tbody');
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
        const dayData = scheduleManagementData[day.id] || {
            is_working: false,
            start_time: '09:00',
            end_time: '18:00',
            notes: '',
            booking_interval: 30
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
                <span class="text-muted">${dayData.notes || 'Нет примечаний'}</span>
            </td>
            <td>
                <button type="button" class="btn-edit" onclick="editScheduleDay(${day.id})" title="Редактировать" style="display: flex; align-items: center; gap: 6px;">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Редактировать
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Функция редактирования дня расписания
function editScheduleDay(dayOfWeek) {
    const dayData = scheduleManagementData[dayOfWeek] || {
        is_working: false,
        start_time: '09:00',
        end_time: '18:00',
        notes: '',
        booking_interval: 30
    };
    
    document.getElementById('edit-schedule-day-of-week').value = dayOfWeek;
    document.getElementById('edit-schedule-user-id').value = currentScheduleUserId;
    document.getElementById('edit-schedule-is-working').checked = dayData.is_working;
    document.getElementById('edit-schedule-start-time').value = dayData.start_time;
    document.getElementById('edit-schedule-end-time').value = dayData.end_time;
    document.getElementById('edit-schedule-notes').value = dayData.notes;
    document.getElementById('edit-schedule-booking-interval').value = dayData.booking_interval || 30;
    
    toggleScheduleWorkingHoursFields();
    
    document.getElementById('editScheduleModal').style.display = 'block';
}

// Функция переключения полей времени работы
function toggleScheduleWorkingHoursFields() {
    const isWorking = document.getElementById('edit-schedule-is-working').checked;
    const fields = document.getElementById('schedule-working-hours-fields');
    
    if (isWorking) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

// Функция закрытия модального окна
function closeScheduleModal() {
    document.getElementById('editScheduleModal').style.display = 'none';
    
    // Очищаем форму
    document.getElementById('edit-schedule-day-of-week').value = '';
    document.getElementById('edit-schedule-user-id').value = '';
    document.getElementById('edit-schedule-is-working').checked = false;
    document.getElementById('edit-schedule-start-time').value = '';
    document.getElementById('edit-schedule-end-time').value = '';
    document.getElementById('edit-schedule-notes').value = '';
    document.getElementById('edit-schedule-booking-interval').value = '30';
}

// Функция сохранения расписания дня
function saveScheduleDay() {
    const dayOfWeek = document.getElementById('edit-schedule-day-of-week').value;
    const employeeId = document.getElementById('edit-schedule-user-id').value;
    const isWorking = document.getElementById('edit-schedule-is-working').checked;
    const startTime = document.getElementById('edit-schedule-start-time').value;
    const endTime = document.getElementById('edit-schedule-end-time').value;
    const notes = document.getElementById('edit-schedule-notes').value;
    const bookingInterval = document.getElementById('edit-schedule-booking-interval').value;
    
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
    
    // Обновляем данные
    scheduleManagementData[dayOfWeek] = {
        is_working: isWorking,
        start_time: startTime,
        end_time: endTime,
        notes: notes,
        booking_interval: bookingInterval
    };
    
    // Сохраняем на сервере
    fetch('{{ route("work-schedules.save-employee-schedule") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            employee_id: employeeId,
            schedule: scheduleManagementData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message);
            
            // Закрываем модальное окно
            closeScheduleModal();
            
            // Обновляем таблицу
            renderScheduleManagementTable();
            
            // Обновляем данные на вкладке "Обзор" с учетом текущей недели
            loadWeekSchedule();
        } else {
            console.error('Ошибка сохранения:', data.message);
            window.showNotification('error', 'Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        window.showNotification('error', 'Произошла ошибка при сохранении');
    });
}

// Функция обновления данных на вкладке "Обзор"
function refreshOverviewData() {
    // Обновляем расписание на текущую неделю
    fetch('{{ route("work-schedules.refresh-overview") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateOverviewScheduleTable(data.currentWeekSchedules);
                updateOverviewStats(data.stats);
            }
        })
        .catch(error => {
            console.error('Ошибка обновления обзора:', error);
        });
}

// Функция склонения слова "запись"
function declensionAppointments(count) {
    if (count === 0) {
        return 'Без записей';
    }
    
    const lastDigit = count % 10;
    const lastTwoDigits = count % 100;
    
    // Исключения для 11-14
    if (lastTwoDigits >= 11 && lastTwoDigits <= 14) {
        return count + ' записей';
    }
    
    // Склонения
    if (lastDigit === 1) {
        return count + ' запись';
    } else if (lastDigit >= 2 && lastDigit <= 4) {
        return count + ' записи';
    } else {
        return count + ' записей';
    }
}

// Функция обновления таблицы расписания на вкладке "Обзор"
function updateOverviewScheduleTable(schedules) {
    const tbody = document.querySelector('.schedule-overview-table tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    schedules.forEach(employeeSchedule => {
        const row = document.createElement('tr');
        let scheduleCells = '';
        
                employeeSchedule.schedule.forEach(day => {

            
            if (day.is_working) {
                let statsHtml = '';
                if (day.appointments_count !== undefined) {
                    statsHtml += `<span class="appointments-count show-appointments">📅 ${declensionAppointments(day.appointments_count)}</span>`;
                    if (day.free_hours > 0) {
                        if (day.appointments_count === 0) {
                            statsHtml += `<span class="free-time show-free-hours">⏰ В ожидании</span>`;
                        } else {
                            statsHtml += `<span class="free-time show-free-hours">⏰ ${day.free_hours}ч свободно</span>`;
                        }
                    }
                }
                
                scheduleCells += `
                    <td>
                        <span class="schedule-time working">
                            ${day.start_time}-${day.end_time}
                        </span>
                        ${statsHtml ? `<div class="schedule-stats">${statsHtml}</div>` : ''}
                    </td>
                `;
            } else {
                scheduleCells += `
                    <td>
                        <span class="schedule-time day-off">
                            Выходной
                        </span>
                    </td>
                `;
            }
        });
        
        row.innerHTML = `
            <td>${employeeSchedule.employee.name}</td>
            ${scheduleCells}
        `;
        tbody.appendChild(row);
    });
    
    // Применяем текущие настройки отображения
    const showAppointments = localStorage.getItem('workSchedules_showAppointments') !== 'false';
    const showFreeHours = localStorage.getItem('workSchedules_showFreeHours') !== 'false';
    toggleAppointmentsDisplay(showAppointments);
    toggleFreeHoursDisplay(showFreeHours);
}

// Функция обновления статистики на вкладке "Обзор"
function updateOverviewStats(stats) {
    // Обновляем статистические карточки
    const totalEmployeesCard = document.querySelector('.card .mt-3.mb-3');
    if (totalEmployeesCard && stats.total_employees !== undefined) {
        totalEmployeesCard.textContent = stats.total_employees;
    }
    
    // Можно добавить обновление других статистических данных
    const workingTodayCards = document.querySelectorAll('.card .mt-3.mb-3');
    if (workingTodayCards[1] && stats.working_today !== undefined) {
        workingTodayCards[1].textContent = stats.working_today;
    }
}

// Функции для модальных окон
function showScheduleModal() {
    // Эта функция уже не используется, заменена на editScheduleDay
    console.log('showScheduleModal deprecated');
}

function showTimeOffModal(timeOffId = null) {
    const modal = document.getElementById('timeOffModal');
    const title = document.getElementById('timeOffModalTitle');
    const form = document.getElementById('timeOffForm');
    
    // Очищаем форму
    form.reset();
    document.getElementById('timeOffId').value = '';
    
    if (timeOffId) {
        // Режим редактирования
        title.textContent = 'Редактировать отпуск';
        loadTimeOffData(timeOffId);
    } else {
        // Режим создания
        title.textContent = 'Добавить отпуск';
        // Устанавливаем минимальную дату - сегодня
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('timeOffStartDate').min = today;
        document.getElementById('timeOffEndDate').min = today;
    }
    
    modal.style.display = 'block';
}

function closeTimeOffModal() {
    const modal = document.getElementById('timeOffModal');
    modal.style.display = 'none';
}

function loadTimeOffData(timeOffId) {
    fetch(`{{ route('work-schedules.time-offs.index') }}/${timeOffId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const timeOff = data.timeOff;
                document.getElementById('timeOffId').value = timeOff.id;
                document.getElementById('timeOffEmployee').value = timeOff.admin_user_id;
                document.getElementById('timeOffType').value = timeOff.type;
                document.getElementById('timeOffStartDate').value = timeOff.start_date;
                document.getElementById('timeOffEndDate').value = timeOff.end_date;
                document.getElementById('timeOffReason').value = timeOff.reason || '';
            } else {
                window.showNotification('error', 'Ошибка загрузки данных отпуска');
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки отпуска:', error);
            window.showNotification('error', 'Произошла ошибка при загрузке');
        });
}

function saveTimeOff() {
    const form = document.getElementById('timeOffForm');
    const formData = new FormData(form);
    
    // Валидация
    const employeeId = document.getElementById('timeOffEmployee').value;
    const type = document.getElementById('timeOffType').value;
    const startDate = document.getElementById('timeOffStartDate').value;
    const endDate = document.getElementById('timeOffEndDate').value;
    
    if (!employeeId) {
        window.showNotification('error', 'Выберите сотрудника');
        return;
    }
    
    if (!type) {
        window.showNotification('error', 'Выберите тип отпуска');
        return;
    }
    
    if (!startDate || !endDate) {
        window.showNotification('error', 'Укажите даты начала и окончания');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        window.showNotification('error', 'Дата начала не может быть позже даты окончания');
        return;
    }
    
    const timeOffId = document.getElementById('timeOffId').value;
    const url = timeOffId ? 
        `{{ route('work-schedules.time-offs.index') }}/${timeOffId}` : 
        `{{ route('work-schedules.time-offs.index') }}`;
    
    const method = timeOffId ? 'PUT' : 'POST';
    
    // Конвертируем FormData в JSON
    const data = {
        employee_id: employeeId,
        type: type,
        start_date: startDate,
        end_date: endDate,
        reason: document.getElementById('timeOffReason').value
    };
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message);
            closeTimeOffModal();
            loadTimeOffsData(); // Обновляем таблицу
        } else {
            window.showNotification('error', data.message || 'Ошибка при сохранении');
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        window.showNotification('error', 'Произошла ошибка при сохранении');
    });
}

function loadTimeOffsData() {
    const tbody = document.getElementById('time-offs-tbody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Загрузка...</td></tr>';
    
    fetch(`{{ route('work-schedules.time-offs.index') }}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTimeOffsTable(data.timeOffs);
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4" style="color: #dc3545;">Ошибка загрузки данных</td></tr>';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки отпусков:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4" style="color: #dc3545;">Ошибка загрузки данных</td></tr>';
        });
}

function renderTimeOffsTable(timeOffs) {
    const tbody = document.getElementById('time-offs-tbody');
    
    if (timeOffs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4" style="color: #6c757d; font-style: italic;">Нет данных об отпусках</td></tr>';
        return;
    }
    
    const typeNames = {
        vacation: 'Отпуск',
        sick_leave: 'Больничный', 
        personal_leave: 'Личный отпуск',
        unpaid_leave: 'Отпуск без содержания'
    };
    
    const statusNames = {
        pending: 'Ожидает',
        approved: 'Одобрено',
        rejected: 'Отклонено',
        cancelled: 'Отменено'
    };
    
    tbody.innerHTML = timeOffs.map(timeOff => `
        <tr>
            <td style="text-align: center;">${timeOff.user ? timeOff.user.name : 'Удаленный пользователь'}</td>
            <td style="text-align: center;">${typeNames[timeOff.type] || timeOff.type}</td>
            <td style="text-align: center;">${formatDate(timeOff.start_date)} - ${formatDate(timeOff.end_date)}</td>
            <td style="text-align: center;">${timeOff.reason || '-'}</td>
            <td style="text-align: center;">
                <span class="status-badge status-${timeOff.status}">
                    ${statusNames[timeOff.status] || timeOff.status}
                </span>
            </td>
            <td style="text-align: center;" class="actions-cell">
                <button class="btn-edit" onclick="showTimeOffModal(${timeOff.id})" title="Редактировать">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                </button>
                <button class="btn-delete" onclick="deleteTimeOff(${timeOff.id})" title="Удалить">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                </button>
            </td>
        </tr>
    `).join('');
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('ru-RU');
}

function deleteTimeOff(timeOffId) {
    if (!confirm('Вы уверены, что хотите удалить этот отпуск?')) {
        return;
    }
    
    fetch(`{{ route('work-schedules.time-offs.index') }}/${timeOffId}`, {
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
            loadTimeOffsData(); // Обновляем таблицу
        } else {
            window.showNotification('error', data.message || 'Ошибка при удалении');
        }
    })
    .catch(error => {
        console.error('Ошибка при удалении:', error);
        window.showNotification('error', 'Произошла ошибка при удалении');
    });
}

// Функции управления настройками отображения
function initDisplaySettings() {
    // Загружаем сохраненные настройки
    const showAppointments = localStorage.getItem('workSchedules_showAppointments') !== 'false';
    const showFreeHours = localStorage.getItem('workSchedules_showFreeHours') !== 'false';
    
    // Устанавливаем состояние чекбоксов
    document.getElementById('showAppointments').checked = showAppointments;
    document.getElementById('showFreeHours').checked = showFreeHours;
    
    // Применяем настройки
    toggleAppointmentsDisplay(showAppointments);
    toggleFreeHoursDisplay(showFreeHours);
    
    // Добавляем обработчики событий
    document.getElementById('showAppointments').addEventListener('change', function() {
        const show = this.checked;
        localStorage.setItem('workSchedules_showAppointments', show);
        toggleAppointmentsDisplay(show);
    });
    
    document.getElementById('showFreeHours').addEventListener('change', function() {
        const show = this.checked;
        localStorage.setItem('workSchedules_showFreeHours', show);
        toggleFreeHoursDisplay(show);
    });
}

function toggleAppointmentsDisplay(show) {
    const elements = document.querySelectorAll('.show-appointments');
    elements.forEach(element => {
        element.style.display = show ? 'inline-block' : 'none';
    });
}

function toggleFreeHoursDisplay(show) {
    const elements = document.querySelectorAll('.show-free-hours');
    elements.forEach(element => {
        element.style.display = show ? 'inline-block' : 'none';
    });
}

// Функция показа предупреждения только раз в месяц
function showWarningOncePerMonth(message) {
    const warningKey = 'workSchedules_lastWarningShown';
    const lastShown = localStorage.getItem(warningKey);
    const now = new Date();
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(now.getMonth() - 1);
    
    // Если предупреждение не показывалось или прошел месяц
    if (!lastShown || new Date(lastShown) < oneMonthAgo) {
        window.showNotification('info', message);
        localStorage.setItem(warningKey, now.toISOString());
    }
}
</script>

<style>
/* Стили для расписания */
.schedule-time {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 500;
}

.schedule-time.working {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.schedule-time.day-off {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Стили для статистики записей */
.schedule-stats {
    margin-top: 4px;
    flex-direction: column;
    gap: 2px;
}

.appointments-count,
.free-time {
    font-size: 11px;
    padding: 1px 4px;
    border-radius: 3px;
    font-weight: 500;
    display: inline-block;
}

.appointments-count {
    background-color: #e3f2fd;
    color: #0d47a1;
    border: 1px solid #bbdefb;
}

.free-time {
    background-color: #fff3e0;
    color: #e65100;
    border: 1px solid #ffcc02;
}

/* Цветовая индикация загрузки */
.schedule-stats .appointments-count:empty + .free-time {
    background-color: #f3e5f5;
    color: #7b1fa2;
    border: 1px solid #e1bee7;
}

/* Стили для панели настроек отображения */
.schedule-display-settings {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 16px;
}

.settings-panel {
    display: flex;
    align-items: center;
    gap: 25px;
    flex-wrap: wrap;
}

.settings-title {
    font-weight: 500;
    color: #495057;
    font-size: 14px;
    margin-right: 10px;
}

/* Скрытие элементов статистики */
.show-appointments[style*="display: none"],
.show-free-hours[style*="display: none"] {
    display: none !important;
}

/* Адаптивность панели настроек */
@media (max-width: 768px) {
    .settings-panel {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .settings-title {
        margin-bottom: 8px;
        margin-right: 0;
    }
}

/* Стили календарной навигации - как в dashboard */
.calendar-nav {
    display: flex;
    align-items: center;
    gap: 12px;
}

.calendar-nav-btn {
    width: 32px;
    height: 32px;
    border: 1px solid #d1d5db;
    background: #ffffff;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #6b7280;
}

.calendar-nav-btn:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
}

.calendar-nav-btn:active {
    background: #f3f4f6;
    transform: scale(0.95);
}

.calendar-month-title {
    font-weight: 500;
    color: #374151;
    font-size: 14px;
    min-width: 180px;
    text-align: center;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.status-badge.status-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-badge.status-approved {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-badge.status-rejected {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status-badge.working {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-badge.day-off {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Стили для модального окна отпусков */
.time-off-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    padding: 20px;
    box-sizing: border-box;
}

.time-off-modal-content {
    background-color: white;
    margin: 50px auto;
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.time-off-modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.time-off-modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.time-off-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.time-off-modal-close:hover {
    color: #374151;
}

.time-off-modal-body {
    padding: 25px;
}

.time-off-form-group {
    margin-bottom: 20px;
}

.time-off-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #374151;
}

.time-off-form-group input,
.time-off-form-group select,
.time-off-form-group textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.time-off-form-group input:focus,
.time-off-form-group select:focus,
.time-off-form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.time-off-form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.time-off-modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.time-off-date-row {
    display: flex;
    gap: 15px;
}

.time-off-date-row .time-off-form-group {
    flex: 1;
}

/* Стили для кнопки удаления */
.btn-delete {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    margin-left: 5px;
    transition: background-color 0.2s;
}

.btn-delete:hover {
    background-color: #c82333;
}

.btn-delete .icon {
    width: 14px;
    height: 14px;
}

/* Стили для статусов отпусков */
.status-badge.status-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-badge.status-approved {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-badge.status-rejected {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status-badge.status-cancelled {
    background-color: #e2e3e5;
    color: #6c757d;
    border: 1px solid #d6d8db;
}
</style>

<!-- Модальное окно для отпусков -->
<div id="timeOffModal" class="time-off-modal">
    <div class="time-off-modal-content">
        <div class="time-off-modal-header">
            <h3 class="time-off-modal-title" id="timeOffModalTitle">Добавить отпуск</h3>
            <button class="time-off-modal-close" onclick="closeTimeOffModal()">&times;</button>
        </div>
        <div class="time-off-modal-body">
            <form id="timeOffForm">
                <input type="hidden" id="timeOffId" name="time_off_id">
                
                <div class="time-off-form-group">
                    <label for="timeOffEmployee">Сотрудник *</label>
                    <select id="timeOffEmployee" name="employee_id" required>
                        <option value="">Выберите сотрудника</option>
                        @foreach($allEmployees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->name }} ({{ config('roles.' . $employee->role, $employee->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="time-off-form-group">
                    <label for="timeOffType">Тип отпуска *</label>
                    <select id="timeOffType" name="type" required>
                        <option value="">Выберите тип</option>
                        <option value="vacation">Отпуск</option>
                        <option value="sick_leave">Больничный</option>
                        <option value="personal_leave">Личный отпуск</option>
                        <option value="unpaid_leave">Отпуск без содержания</option>
                    </select>
                </div>

                <div class="time-off-date-row">
                    <div class="time-off-form-group">
                        <label for="timeOffStartDate">Дата начала *</label>
                        <input type="date" id="timeOffStartDate" name="start_date" required>
                    </div>
                    <div class="time-off-form-group">
                        <label for="timeOffEndDate">Дата окончания *</label>
                        <input type="date" id="timeOffEndDate" name="end_date" required>
                    </div>
                </div>

                <div class="time-off-form-group">
                    <label for="timeOffReason">Причина</label>
                    <textarea id="timeOffReason" name="reason" placeholder="Опишите причину отпуска (необязательно)"></textarea>
                </div>
            </form>
        </div>
        <div class="time-off-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeTimeOffModal()">Отмена</button>
            <button type="button" class="btn-submit" onclick="saveTimeOff()">Сохранить</button>
        </div>
    </div>
</div>

@endpush
@endsection
