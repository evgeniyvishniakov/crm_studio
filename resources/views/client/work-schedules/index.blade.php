@extends('client.layouts.app')

@section('title', 'График работы')

@section('content')

<div class="dashboard-container">
    <div class="settings-header">
        <h1>График работы</h1>
    </div>
    
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="schedule-overview">
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
                                        <th>Сотрудник</th>
                                        <th>Тип</th>
                                        <th>Период</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingTimeOffs as $timeOff)
                                    <tr>
                                        <td>{{ $timeOff->user->name }}</td>
                                        <td>{{ $timeOff->type_text }}</td>
                                        <td>
                                            {{ $timeOff->start_date->format('d.m.Y') }} - 
                                            {{ $timeOff->end_date->format('d.m.Y') }}
                                        </td>
                                        <td>
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Управление расписанием</h5>
                <button class="btn btn-primary" onclick="showScheduleModal()">
                    <i class="fa fa-plus"></i> Изменить расписание
                </button>
            </div>

            <!-- Календарный вид -->
            <div class="card">
                <div class="card-body">
                    <div id="schedule-calendar">
                        <!-- Здесь будет календарь расписаний -->
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5>Календарь расписаний</h5>
                            <p class="text-muted">Календарный вид будет реализован в следующей версии</p>
                        </div>
                    </div>
                </div>
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
                <table class="table-striped time-offs-table" id="timeOffsTable">
                    <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Тип</th>
                            <th>Период</th>
                            <th>Причина</th>
                            <th>Статус</th>
                            <th>Действия</th>
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

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Убираем активный класс со всех кнопок
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // Скрываем все панели
            tabPanes.forEach(pane => pane.style.display = 'none');
            
            // Активируем текущую кнопку и панель
            this.classList.add('active');
            document.getElementById('tab-' + tabId).style.display = 'block';
        });
    });
    
    // Инициализация отображения недели
    updateWeekDisplay();
});

// Переменные для работы с неделями
let currentWeekOffset = 0; // 0 = текущая неделя, -1 = прошлая, +1 = следующая

// Функции для календаря
function previousWeek() {
    currentWeekOffset--;
    loadWeekSchedule();
    updateWeekDisplay();
}

function nextWeek() {
    currentWeekOffset++;
    loadWeekSchedule();
    updateWeekDisplay();
}

function currentWeek() {
    currentWeekOffset = 0;
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
    // TODO: Здесь будет AJAX запрос к серверу для получения расписания конкретной недели
    console.log('Loading week schedule for offset:', currentWeekOffset);
    
    // Пока что просто обновляем отображение
    // В будущем здесь будет:
    // fetch(`/work-schedules/week?offset=${currentWeekOffset}`)
    //     .then(response => response.json())
    //     .then(data => updateScheduleTable(data));
}

// Функции для модальных окон
function showScheduleModal() {
    // TODO: Показать модальное окно изменения расписания
    alert('Модальное окно расписания будет реализовано');
}

function showTimeOffModal() {
    // TODO: Показать модальное окно добавления отпуска
    alert('Модальное окно отпуска будет реализовано');
}
</script>

<style>
/* Стили для расписания */
.schedule-time {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
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
</style>
@endpush
@endsection
