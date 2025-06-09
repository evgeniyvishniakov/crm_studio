@extends('layouts.app')

@section('content')
<style>
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 4px;
        color: #fff;
        z-index: 9999;
        display: none;
    }

    .notification.success {
        background-color: #4CAF50;
    }

    .notification.error {
        background-color: #f44336;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 500;
        display: inline-block;
        text-align: center;
        min-width: 100px;
    }

    .status-pending {
        background-color: #FFC107;
        color: #000;
    }

    .status-completed {
        background-color: #4CAF50;
        color: #fff;
    }

    .status-cancelled {
        background-color: #F44336;
        color: #fff;
    }

    .status-rescheduled {
        background-color: #FF9800;
        color: #fff;
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }

    .products-table th,
    .products-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .products-table th {
        background-color: #f8fafc;
        font-weight: 600;
    }

    .products-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .btn-delete-product {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        padding: 0.25rem;
        transition: color 0.2s;
    }

    .btn-delete-product:hover {
        color: #dc2626;
    }

    .btn-delete-product .icon {
        width: 1.25rem;
        height: 1.25rem;
    }


    .btn-add-product .icon {
        width: 1.25rem;
        height: 1.25rem;
    }
    /* Стили для календаря */
    .calendar-wrapper {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .calendar-view-switcher {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .fc-daygrid-day{
        cursor: pointer;
    }
    .view-switch-btn {
        padding: 8px 16px;
        border: 1px solid #e9ecef;
        background: #fff;
        border-radius: 8px;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .view-switch-btn:hover {
        background: #e9ecef;
    }

    .view-switch-btn.active {
        background: #2196f3;
        color: #fff;
        border-color: #2196f3;
    }

    .today-button {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }

    .today-button:hover {
        background: #218838;
    }

    .calendar-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
        padding: 8px 16px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        display: inline-block;
    }

    .calendar-nav {

        gap: 10px;
    }

    .calendar-nav-btn {
        background: #f8f9fa;
        border: none;
        border-radius: 8px;
        padding: 8px 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #2c3e50;
    }

    .calendar-nav-btn:hover {
        background: #e9ecef;
    }

    /* Стили для FullCalendar */
    #calendar {
        margin-top: 20px;
    }

    .fc-toolbar {
        display: none !important;
    }

    .fc-event {
        border: none !important;
        padding: 2px 4px !important;
        margin: 1px 0 !important;
        cursor: pointer;
        border-radius: 4px !important;
        position: relative;
    }

    .fc-time-grid-event {
        border-radius: 4px;
        margin: 1px 0;
    }

    .fc-time {
        font-weight: bold !important;
    }

    .fc-title {
        font-size: 0.9em !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .fc-day-header {
        font-weight: 600 !important;
        padding: 8px 0 !important;
    }

    .fc-day-number {
        padding: 8px !important;
        font-weight: 600 !important;
    }

    .fc-today {
        background-color: #e3f2fd !important;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    .calendar-weekday {
        text-align: center;
        font-weight: 600;
        color: #6c757d;
        padding: 10px;
        font-size: 0.9rem;
    }

    .calendar-day {
        aspect-ratio: 1;
        border-radius: 8px;
        background: #f8f9fa;
        padding: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .calendar-day:hover {
        background: #e9ecef;
    }

    .calendar-day.today {
        background: #e3f2fd;
        border: 2px solid #2196f3;
    }

    .calendar-day.selected {
        background: #bbdefb;
    }

    .calendar-day.has-events {
        background: #fff;
    }

    .calendar-day.has-events::after {
        content: '';
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #2196f3;
    }

    .calendar-day.other-month {
        opacity: 0.5;
    }

    .calendar-day-number {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .calendar-day-events {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .calendar-legend {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    /* Адаптивность для мобильных устройств */
    @media (max-width: 768px) {
        .calendar-wrapper {
            padding: 10px;
        }

        .calendar-title {
            font-size: 1.2rem;
        }

        .calendar-weekday {
            font-size: 0.8rem;
            padding: 5px;
        }

        .calendar-day {
            padding: 4px;
        }

        .calendar-day-number {
            font-size: 0.9rem;
        }
    }



    /* Стили для записей в календаре */
    .appointment-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin: 0 2px;
        display: inline-block;
    }

    .appointment-dot.confirmed { background: #4CAF50; }
    .appointment-dot.pending { background: #FFC107; }
    .appointment-dot.cancelled { background: #F44336; }

    /* Стили для недельного вида */
    .week-view {
        display: none;
    }

    .week-view.active {
        display: block;
    }

    .week-timeline {
        display: grid;
        grid-template-columns: 60px repeat(7, 1fr);
        gap: 1px;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .timeline-hour {
        padding: 10px;
        border-right: 1px solid #e9ecef;
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .timeline-slot {
        height: 60px;
        border-bottom: 1px solid #e9ecef;
        padding: 4px;
        position: relative;
    }

    .appointment-card {
        position: absolute;
        left: 4px;
        right: 4px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        color: #fff;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
    }

    .appointment-card.confirmed { background: rgba(76, 175, 80, 0.9); }
    .appointment-card.pending { background: rgba(255, 193, 7, 0.9); }
    .appointment-card.cancelled { background: rgba(244, 67, 54, 0.9); }

    /* Стили для дневного вида */
    .day-view {
        display: none;
    }

    .day-view.active {
        display: block;
    }

    .day-timeline {
        display: grid;
        grid-template-columns: 60px 1fr;
        gap: 1px;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    /* Стили для годового вида */
    .year-view {
        display: none;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .year-view.active {
        display: grid;
    }

    .mini-month {
        background: #fff;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .mini-month-header {
        text-align: center;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .mini-month-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        font-size: 0.8rem;
    }

    .mini-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        cursor: pointer;
    }

    .mini-day:hover {
        background: #e9ecef;
    }

    .mini-day.has-events {
        color: #2196f3;
        font-weight: 600;
    }

    /* Стили для переключателя видов */
    .calendar-view-switcher {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .view-switch-btn {
        padding: 8px 16px;
        border: 1px solid #e9ecef;
        background: #fff;
        border-radius: 8px;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .view-switch-btn.active {
        background: #2196f3;
        color: #fff;
        border-color: #2196f3;
    }

    .today-button {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }

    .today-button:hover {
        background: #218838;
    }

    /* Стили для календаря */
    #calendar {
        margin-top: 20px;
    }

    .fc-toolbar {
        display: none !important;
    }

    .fc-event {
        border: none !important;
        padding: 2px 4px !important;
        margin: 1px 0 !important;
        cursor: pointer;
        border-radius: 4px !important;
    }

    /* Стили для всплывающей подсказки */
    .appointment-tooltip {
        position: fixed;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        display: none;
        width: 250px;
    }

    .tooltip-btn {
        pointer-events: auto; /* Включаем события только для кнопок */
    }

    .appointment-tooltip-content {
        margin-bottom: 10px;
    }

    .appointment-tooltip-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .tooltip-btn {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
    }

    .tooltip-btn-edit {
        background-color: #4CAF50;
        color: white;
    }

    .tooltip-btn-delete {
        background-color: #f44336;
        color: white;
    }

    .tooltip-btn .icon {
        width: 16px;
        height: 16px;
    }
</style>

<div class="appointments-container">
    <div class="appointments-header">
        <h1>Записи</h1>
        <div id="notification"></div>
        <div class="header-actions">
            <div class="view-switcher">
                <button class="btn-view-switch {{ $viewType === 'list' ? 'active' : '' }}" data-view="list">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Список
                </button>
                <button class="btn-view-switch {{ $viewType === 'calendar' ? 'active' : '' }}" data-view="calendar">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    Календарь
                </button>
            </div>
            <button class="btn-add-appointment" id="addAppointmentBtn">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Добавить запись
            </button>
            <div class="search-box">
                <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" placeholder="Поиск..." id="searchInput">
            </div>
        </div>
    </div>

    @if($viewType === 'list')
    <div class="appointments-list table-wrapper" id="appointmentsList">
        <table class="table-striped appointments-table" id="appointmentsTable">
            <thead>
            <tr>
                <th>Дата</th>
                <th>Время</th>
                <th>Клиент</th>
                <th>Процедура</th>
                <th>Стоимость</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($appointments as $appointment)
            <tr data-appointment-id="{{ $appointment->id }}">
                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</td>
                <td>
                    {{ $appointment->client->name }}
                    @if($appointment->client->instagram)
                        <br />
                    (<a href="https://instagram.com/{{ $appointment->client->instagram }}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                        <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $appointment->client->instagram }}
                    </a>)
                    @endif
                </td>
                <td>{{ $appointment->service->name }}</td>
                <td>{{ number_format($appointment->price) }} грн</td>
                <td>
                    <span class="status-badge status-{{ $appointment->status }}">
                        @php
                            $statusNames = [
                                'pending' => 'Ожидается',
                                'completed' => 'Завершено',
                                'cancelled' => 'Отменено',
                                'rescheduled' => 'Перенесено'
                            ];
                        @endphp
                        {{ $statusNames[$appointment->status] ?? 'Ожидается' }}
                    </span>
                </td>
                <td>
                    <div class="appointment-actions actions-cell">
                        <button class="btn-view" data-appointment-id="{{ $appointment->id }}" title="Просмотр">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            Просмотр
                        </button>
                        <button class="btn-edit" data-appointment-id="{{ $appointment->id }}" title="Редактировать">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                            Ред.
                        </button>
                        <button class="btn-delete" data-appointment-id="{{ $appointment->id }}"  title="Удалить">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Удалить
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div id="calendarView">
        <div class="calendar-wrapper">
            <!-- Шаблон для всплывающей подсказки -->
            <div id="appointmentTooltip" class="appointment-tooltip">
                <div class="appointment-tooltip-content"></div>
                <div class="appointment-tooltip-actions">
                    <button class="tooltip-btn tooltip-btn-edit">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        Редактировать
                    </button>
                    <button class="tooltip-btn tooltip-btn-delete">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Удалить
                    </button>
                </div>
            </div>
            <div class="calendar-header">
                <div class="calendar-view-switcher">
                    <button class="view-switch-btn today-button">Сегодня</button>
                    <button class="view-switch-btn" data-view="timeGridDay">День</button>
                    <button class="view-switch-btn" data-view="timeGridWeek">Неделя</button>
                    <button class="view-switch-btn active" data-view="dayGridMonth">Месяц</button>
                </div>
                <div class="calendar-nav">
                    <button class="calendar-nav-btn prev-button">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <span id="currentMonthYear" class="calendar-title">Декабрь 2023</span>
                    <button class="calendar-nav-btn next-button">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div id="calendar"></div>
        </div>
    </div>

    <script>
        // Функция подтверждения удаления записи
    async function confirmDeleteAppointment(e, id) {
        e.preventDefault();
        if (confirm('Вы уверены, что хотите удалить эту запись?')) {
            await deleteAppointment(id);
        }
    }

    let calendar; // Делаем переменную глобальной
    let activeEvent = null;
    const tooltip = document.getElementById('appointmentTooltip');

    document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('calendar')) {

                // Добавляем обработчики для самой подсказки
                tooltip.addEventListener('mouseenter', () => {
                    if (activeEvent) {
                        tooltip.style.display = 'block';
                    }
                });

                tooltip.addEventListener('mouseleave', () => {
                    if (!activeEvent) {
                        tooltip.style.display = 'none';
                    }
                });

                calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                    initialView: 'dayGridMonth',
                    headerToolbar: false,
                    locale: 'ru',
                    height: 'auto',
                    selectable: true,
                    editable: true,
                    events: function(info, successCallback, failureCallback) {
                        console.log('=== Загрузка событий календаря ===');
                        fetch('/appointments/calendar-events')
                            .then(response => response.json())
                            .then(events => {
                                console.log('Загруженные события:', events);
                                // Проверяем формат ID у первого события
                                if (events.length > 0) {
                                    console.log('Пример ID первого события:', events[0].id);
                                    console.log('Тип ID:', typeof events[0].id);
                                }
                                successCallback(events);
                            })
                            .catch(error => {
                                console.error('Ошибка загрузки событий:', error);
                                failureCallback(error);
                            });
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    },
                    slotMinTime: '08:00:00',
                    slotMaxTime: '20:00:00',
                    allDaySlot: false,
                    slotDuration: '00:30:00',

                    // Обработчик наведения на событие
                    eventMouseEnter: function(info) {
                        activeEvent = info.event;
                        const event = info.event;
                        const tooltipContent = tooltip.querySelector('.appointment-tooltip-content');
                        const editBtn = tooltip.querySelector('.tooltip-btn-edit');
                        const deleteBtn = tooltip.querySelector('.tooltip-btn-delete');

                        // Форматируем время
                        const startTime = event.start ? new Date(event.start).toLocaleTimeString('ru-RU', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '';

                        // Формируем содержимое всплывающей подсказки
                        tooltipContent.innerHTML = `
                            <p><strong>Время:</strong> ${startTime}</p>
                            <p><strong>Клиент:</strong> ${event.extendedProps.client}</p>
                            <p><strong>Процедура:</strong> ${event.extendedProps.service}</p>
                            <p><strong>Цена:</strong> ${event.extendedProps.price} грн</p>
                            <p><strong>Статус:</strong> ${getStatusName(event.extendedProps.status)}</p>
                        `;

                        // Позиционируем всплывающую подсказку
                        const eventEl = info.el;
                        const rect = eventEl.getBoundingClientRect();

                        // Получаем размеры окна
                        const windowWidth = window.innerWidth;
                        const windowHeight = window.innerHeight;

                        // Получаем размеры подсказки
                        const tooltipWidth = 250; // ширина подсказки
                        const tooltipHeight = tooltip.offsetHeight;

                        // Рассчитываем позицию
                        let left = rect.left - tooltipWidth - 5; // Всегда размещаем слева от события с отступом 5px
                        let top = rect.top;

                        // Если подсказка выходит за левый край экрана, размещаем её справа от события
                        if (left < 0) {
                            left = rect.right + 5;
                        }

                        // Проверяем, не выходит ли подсказка за пределы экрана снизу
                        if (top + tooltipHeight > windowHeight) {
                            top = windowHeight - tooltipHeight - 5; // 5px отступ снизу
                        }

                        // Применяем позицию
                        tooltip.style.top = `${top}px`;
                        tooltip.style.left = `${left}px`;
                        tooltip.style.display = 'block';

                        // Добавляем обработчики для кнопок
                        editBtn.onclick = (e) => {
                            activeEvent = null;
                            tooltip.style.display = 'none';
                            editAppointment(event.id);
                        };
                        deleteBtn.onclick = (e) => {
                            activeEvent = null;
                            tooltip.style.display = 'none';
                            confirmDeleteAppointment(e, event.id);
                        };
                    },

                    // Скрываем всплывающую подсказку при уходе курсора
                    eventMouseLeave: function() {
                        activeEvent = null;
                        // Даем небольшую задержку, чтобы можно было навести на подсказку
                        setTimeout(() => {
                            if (!activeEvent && !tooltip.matches(':hover')) {
                                tooltip.style.display = 'none';
                            }
                        }, 100);
                    },

                    eventClick: function(info) {
                        tooltip.style.display = 'none';
                        viewAppointment(info.event.id);
                    },

                    dateClick: function(info) {
                        createAppointment(info.dateStr);
                    }
                });

                calendar.render();

                // Функция удаления записи
                async function deleteAppointment(id) {
                        try {
                            const response = await fetch(`/appointments/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            const data = await response.json();

                                        if (data.success) {
                // Обновляем календарь
                if (typeof calendar !== 'undefined' && calendar) {
                    calendar.refetchEvents();
                }
                tooltip.style.display = 'none';
                showNotification('Запись успешно удалена', 'success');
                toggleModal('confirmationModal', false);
                            } else {
                                throw new Error(data.message || 'Ошибка при удалении записи');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            showNotification(error.message || 'Ошибка при удалении записи', 'error');
                        }
                        currentDeleteId = null;
                        isDeletingAppointment = false;
                }

                // Функция создания записи при клике на дату
                function createAppointment(dateStr) {
                    // Устанавливаем выбранную дату в поле формы
                    document.querySelector('#appointmentModal input[name="date"]').value = dateStr;
                    // Открываем модальное окно
                    toggleModal('appointmentModal');
                }

                // Функция обновления заголовка
                function updateTitle() {
                    const monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                                    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
                    const date = calendar.getDate();
                    const view = calendar.view.type;
                    let title = '';

                    if (view === 'timeGridDay') {
                        // Для дневного вида показываем дату в формате "1 января 2024"
                        title = `${date.getDate()} ${monthNames[date.getMonth()].toLowerCase()} ${date.getFullYear()}`;
                    } else if (view === 'timeGridWeek') {
                        // Для недельного вида показываем номер недели
                        const weekNumber = Math.ceil((date.getDate() + new Date(date.getFullYear(), date.getMonth(), 1).getDay()) / 7);
                        title = `${weekNumber}-я неделя ${monthNames[date.getMonth()].toLowerCase()} ${date.getFullYear()}`;
                    } else {
                        // Для месячного вида оставляем как есть
                        title = `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
                    }

                    document.getElementById('currentMonthYear').textContent = title;
                }

                // Обработчики кнопок навигации
                document.querySelector('.prev-button').addEventListener('click', function() {
                    const view = calendar.view.type;
                    if (view === 'timeGridDay') {
                        calendar.prev(); // Переход на предыдущий день
                    } else if (view === 'timeGridWeek') {
                        calendar.prev(); // Переход на предыдущую неделю
                    } else {
                        calendar.prev(); // Переход на предыдущий месяц
                    }
                    updateTitle();
                });

                document.querySelector('.next-button').addEventListener('click', function() {
                    const view = calendar.view.type;
                    if (view === 'timeGridDay') {
                        calendar.next(); // Переход на следующий день
                    } else if (view === 'timeGridWeek') {
                        calendar.next(); // Переход на следующую неделю
                    } else {
                        calendar.next(); // Переход на следующий месяц
                    }
                    updateTitle();
                });

                // Обработчики кнопок переключения вида
                document.querySelectorAll('.view-switch-btn[data-view]').forEach(button => {
                    button.addEventListener('click', function() {
                        // Удаляем активный класс у всех кнопок
                        document.querySelectorAll('.view-switch-btn').forEach(btn => {
                            btn.classList.remove('active');
                        });
                        // Добавляем активный класс текущей кнопке
                        this.classList.add('active');
                        // Переключаем вид календаря
                        calendar.changeView(this.dataset.view);
                    });
                });

                // Обработчик кнопки "Сегодня"
                document.querySelector('.today-button').addEventListener('click', function() {
                    calendar.today();
                    updateTitle();
                });

                // Обновляем заголовок при изменении даты
                calendar.on('datesSet', function() {
                    updateTitle();
                });

                // Инициализация заголовка
                updateTitle();
            }
        });

        // Функция для открытия/закрытия модальных окон
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
    </script>
@endif
</div>

<!-- Модальное окно добавления записи -->
<div id="appointmentModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2>Добавить запись</h2>
            <span class="close" onclick="closeAppointmentModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="appointmentForm">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Дата *</label>
                        <input type="date" name="date" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Время *</label>
                        <input type="time" name="time" required class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Клиент *</label>
                        <div class="client-search-container">
                            <input type="text" class="client-search-input form-control"
                                   placeholder="Начните вводить имя, инстаграм или email клиента..."
                                   oninput="searchClients(this)"
                                   onfocus="searchClients(this)">
                            <div class="client-dropdown" style="display: none;">
                                <div class="client-dropdown-list"></div>
                            </div>
                            <select name="client_id" class="form-control client-select" style="display: none;" required>
                                <option value="">Выберите клиента</option>
                                @foreach($clients as $client)
                                <option value="{{ $client['id'] }}">
                                    {{ $client['name'] }}
                                    @if($client['instagram']) ({{ $client['instagram'] }}) @endif
                                    @if($client['phone']) - {{ $client['phone'] }} @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Процедура *</label>
                        <select name="service_id" class="form-control" required>
                            <option value="">Выберите процедуру</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                {{ $service->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Стоимость (Грн)</label> <!-- Убрал * -->
                    <input type="number" step="0.01" name="price" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label>Примечания</label>
                    <textarea name="notes" rows="2" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label>Статус</label>
                    <select name="status" class="form-control">
                        <option value="pending">Ожидается</option>
                        <option value="completed">Завершено</option>
                        <option value="cancelled">Отменено</option>
                        <option value="rescheduled">Перенесено</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeAppointmentModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить запись</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования записи -->
<div id="editAppointmentModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2>Редактировать запись</h2>
            <span class="close" onclick="closeEditAppointmentModal()">&times;</span>
        </div>
        <div class="modal-body" id="editAppointmentModalBody">
            <!-- Контент будет загружен динамически -->
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-content">
        <h3>Подтверждение удаления</h3>
        <p>Вы уверены, что хотите удалить эту запись?</p>
        <div class="confirmation-buttons">
            <button class="cancel-btn" id="cancelDelete">Отмена</button>
            <button class="confirm-btn" id="confirmDeleteBtn">Удалить</button>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра записи -->
<div id="viewAppointmentModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2>Детали записи</h2>
            <span class="close" onclick="closeViewAppointmentModal()">&times;</span>
        </div>
        <div class="modal-body" id="viewAppointmentModalBody">
            <!-- Контент будет загружен динамически -->
        </div>
    </div>
</div>

<script>
    // Глобальные переменные
    let currentDeleteId = null;
    let allClients = @json($clients->toArray());
    let allServices = @json($services);
    let currentAppointmentId = null;
    let allProducts = @json($products);
    let temporaryProducts = [];
    let isDeletingAppointment = false;
    let currentDeleteIndex = null;
    let currentDeleteProductId = null;

    // Общие функции
    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

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

    function getStatusName(status) {
        const statusNames = {
            'pending': 'Ожидается',
            'completed': 'Завершено',
            'cancelled': 'Отменено',
            'rescheduled': 'Перенесено'
        };
        return statusNames[status] || 'Ожидается';
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete-product')) {
            const btn = e.target.closest('.btn-delete-product');
            currentDeleteProductId = parseInt(btn.dataset.productId);
            isDeletingAppointment = false;

            toggleModal('confirmationModal');
            document.querySelector('#confirmationModal p').textContent = 'Вы уверены, что хотите удалить этот товар?';
        }
    });




    function closeAppointmentModal() {
        toggleModal('appointmentModal', false);
        document.getElementById('appointmentForm').reset();
        clearErrors('appointmentForm');
    }

    function closeEditAppointmentModal() {
        toggleModal('editAppointmentModal', false);
    }

    function closeViewAppointmentModal() {
        toggleModal('viewAppointmentModal', false);
    }

    function showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        if (!notification) return;

        notification.textContent = message;
        notification.className = `notification ${type}`;
        notification.style.display = 'block';

        // Очищаем предыдущий таймер, если он есть
        if (notification.hideTimeout) {
            clearTimeout(notification.hideTimeout);
        }

        // Устанавливаем новый таймер
        notification.hideTimeout = setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    // Функции для работы с записями
    async function viewAppointment(id) {
        currentAppointmentId = id;
        toggleModal('viewAppointmentModal');
        const modalBody = document.getElementById('viewAppointmentModalBody');
        modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

        try {
            const response = await fetch(`/appointments/${id}/view`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                renderViewAppointmentModal(data, modalBody);
            } else {
                throw new Error(data.message || 'Ошибка загрузки данных');
            }
        } catch (error) {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    Ошибка: ${escapeHtml(error.message)}
                </div>
                <button class="btn-cancel" onclick="toggleModal('viewAppointmentModal', false)">Закрыть</button>
            `;
        }
    }

    function renderViewAppointmentModal(data, modalBody) {
        const { appointment, sales = [], products = [] } = data;
        temporaryProducts = [...sales]; // Инициализируем временный список товаров

        const servicePrice = parseFloat(appointment.price) || 0;
        const productsTotal = temporaryProducts.reduce((sum, sale) => {
            const price = parseFloat(sale.price || 0);
            return sum + (parseInt(sale.quantity) * price);
        }, 0);

        const totalAmount = servicePrice + productsTotal;

        modalBody.innerHTML = `
        <input type="hidden" id="appointmentId" value="${appointment.id}">
        <input type="hidden" name="date" value="${appointment.date}">
        <div class="appointment-details">
            <div class="detail-row">
                <div class="detail-row-no-flex">
                    <span class="detail-label">Дата:</span>
                    <span class="detail-value">${new Date(appointment.date).toLocaleDateString('ru-RU')}</span>
                </div>
                <div class="detail-row-no-flex">
                    <span class="detail-label">Время:</span>
                    <span class="detail-value">${escapeHtml(appointment.time.split(':').slice(0, 2).join(':'))}</span>
                </div>
                <div class="detail-row-no-flex">
                    <span class="detail-label">Клиент:</span>
                    <span class="detail-value client-name">${escapeHtml(appointment.client.name)}${appointment.client.instagram ? ` (@${escapeHtml(appointment.client.instagram)})` : ''}</span>
                </div>
            </div>
            <div class="detail-row">
                <span class="detail-label">Статус:</span>
                <span class="detail-value">
                    <span class="status-badge status-${appointment.status}">
                        ${getStatusName(appointment.status)}
                    </span>
                </span>
            </div>

            <h3>Процедура</h3>
            <div class="services-section">
                <div class="service-item">
                    <span class="service-name">${escapeHtml(appointment.service.name)}</span>
                    <span class="service-price">${servicePrice.toFixed(2)} грн</span>
                </div>
            </div>

            <h3>Продажи</h3>
            <div class="products-section">
                ${renderProductsList(temporaryProducts)}
                <button class="btn-add-product" id="showAddProductFormBtn">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить
                </button>
                <div id="addProductForm" style="display: none; margin-top: 20px;">
                    <div class="form-row-appointment">
                        <div class="form-group" style="flex: 2;">
                            <label>Товар *</label>
                            <div class="product-search-container">
                                <input type="text" class="product-search-input form-control"
                                       id="productSearchInput"
                                       placeholder="Начните вводить название товара..."
                                       oninput="searchProducts(this)"
                                       onfocus="showProductDropdown(this)">
                                <div class="product-dropdown" style="display: none;">
                                    <div class="product-dropdown-list"></div>
                                </div>
                                <input type="hidden" id="selectedProductId" name="product_id">
                            </div>
                        </div>
                    </div>
                    <div id="productDetails" class="form-row-appointment" style="display: none; margin-top: 15px;">
                        <div class="form-group-appointment">
                            <label>Количество *</label>
                            <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="form-group-appointment">
                            <label>Опт</label>
                            <input type="number" step="0.01" id="productWholesale" class="form-control" readonly style="background-color: #f0f0f0;">
                        </div>
                        <div class="form-group-appointment">
                            <label>Цена *</label>
                            <input type="number" step="0.01" id="productPrice" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-actions" style="margin-top: 15px;">
                        <button type="button" class="btn-cancel" id="cancelAddProduct">Отмена</button>
                        <button type="button" class="btn-submit" id="submitAddProduct">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                            Добавить
                        </button>
                    </div>
                </div>
            </div>

            <div class="total-amount">
                <span class="total-label">Итого:</span>
                <span class="total-value">${totalAmount.toFixed(2)} грн</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeViewAppointmentModal()">Закрыть</button>
                <button type="button" class="btn-submit" id="saveAppointmentChanges">Сохранить изменения</button>
            </div>
        </div>`;

        setupProductHandlers();
    }

    function renderProductsList(sales) {
        if (!sales || sales.length === 0) return '<p>Товары не добавлены</p>';

        return `
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Количество</th>
                        <th>Розничная цена</th>
                        <th>Оптовая цена</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    ${sales.map((sale, index) => {
            const total = sale.quantity * sale.price;
            return `
                        <tr data-index="${index}">
                            <td>${escapeHtml(sale.name)}</td>
                            <td>${sale.quantity}</td>
                            <td>${parseFloat(sale.price).toFixed(2)} грн</td>
                            <td>${parseFloat(sale.purchase_price).toFixed(2)} грн</td>
                            <td>${total.toFixed(2)} грн</td>
                            <td>
                                <button class="btn-delete btn-delete-product"
                                        data-product-id="${sale.product_id}"
                                        title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        `;
        }).join('')}
                </tbody>
            </table>`;
    }


    async function addNewProcedureToAppointment() {
        const appointmentId = document.getElementById('appointmentId').value;
        const selectedServiceId = prompt("Введите ID новой процедуры:");
        const newPrice = prompt("Введите цену для новой процедуры:");

        if (!selectedServiceId || !newPrice) {
            alert("Процедура и цена обязательны");
            return;
        }

        const response = await fetch(`/appointments/${appointmentId}/add-procedure`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                service_id: selectedServiceId,
                price: newPrice,
            }),
        });

        const result = await response.json();

        if (result.success) {
            alert("Процедура добавлена как новая запись");
        } else {
            alert("Ошибка: " + result.message);
        }
    }




    function setupProductHandlers() {
        // Используем делегирование событий для динамически создаваемых элементов
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('viewAppointmentModal');
            if (!modal) return;

            // Показать/скрыть форму добавления товара
            if (e.target && e.target.id === 'showAddProductFormBtn') {
                const form = modal.querySelector('#addProductForm');
                const btn = modal.querySelector('#showAddProductFormBtn');
                if (form && btn) {
                    form.style.display = 'block';
                    btn.style.display = 'none';
                }
                return;
            }

            // Отмена добавления товара
            if (e.target && e.target.id === 'cancelAddProduct') {
                const form = modal.querySelector('#addProductForm');
                const btn = modal.querySelector('#showAddProductFormBtn');
                if (form && btn) {
                    form.style.display = 'none';
                    btn.style.display = 'block';
                    resetProductForm();
                }
                return;
            }

            // Добавление товара
            if (e.target && e.target.id === 'submitAddProduct') {
                e.preventDefault();
                addProductToAppointment();
                return;
            }

            // Удаление товара
            if (e.target.closest('.btn-delete-product')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-delete-product');
                currentDeleteProductId = btn.dataset.productId;
                currentDeleteIndex = btn.closest('tr')?.dataset.index;
                isDeletingAppointment = false;

                toggleModal('confirmationModal');
                document.querySelector('#confirmationModal p').textContent = 'Вы уверены, что хотите удалить этот товар?';
            }
            if (e.target && e.target.id === 'showAddServiceFormBtn') {
                document.getElementById('addServiceForm').style.display = 'block';
                e.target.style.display = 'none';
            }

            // Отмена
            if (e.target && e.target.id === 'cancelAddService') {
                document.getElementById('addServiceForm').style.display = 'none';
                document.getElementById('showAddServiceFormBtn').style.display = 'inline-block';
            }

            // Сохранить
            if (e.target && e.target.id === 'submitAddService') {
                addProcedureToAppointment();
            }
        });

        document.getElementById('cancelDelete')?.addEventListener('click', function() {
            toggleModal('confirmationModal', false);
            currentDeleteId = null;
            currentDeleteIndex = null;
            isDeletingAppointment = false;
        });



        // Сохранение изменений
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'saveAppointmentChanges') {
                saveAppointmentChanges();
            }
        });
    }


    function resetProductForm() {
        const modal = document.getElementById('viewAppointmentModal');
        if (!modal) return;

        const form = modal.querySelector('#addProductForm');
        if (form) {
            const searchInput = form.querySelector('#productSearchInput');
            const productIdInput = form.querySelector('#selectedProductId');
            const quantityInput = form.querySelector('#productQuantity');
            const priceInput = form.querySelector('#productPrice');
            const wholesaleInput = form.querySelector('#productWholesale');
            const productDetails = form.querySelector('#productDetails');

            if (searchInput) searchInput.value = '';
            if (productIdInput) productIdInput.value = '';
            if (quantityInput) quantityInput.value = '1';
            if (priceInput) priceInput.value = '';
            if (wholesaleInput) wholesaleInput.value = '';
            if (productDetails) productDetails.style.display = 'none';

            // Скрываем выпадающий список
            const dropdown = form.querySelector('.product-dropdown');
            if (dropdown) dropdown.style.display = 'none';
        }
    }

    // Функции для работы с товарами
    function addProductToAppointment() {
        const modal = document.getElementById('viewAppointmentModal');
        if (!modal) {
            showNotification('Модальное окно не найдено', 'error');
            return;
        }

        const productId = modal.querySelector('#selectedProductId')?.value;
        const quantity = modal.querySelector('#productQuantity')?.value;
        const price = modal.querySelector('#productPrice')?.value;
        const productName = modal.querySelector('#productSearchInput')?.value;

        console.log('Adding product:', { productId, quantity, price, productName });

        // Проверки
        if (!productId || productId === '') {
            showNotification('Пожалуйста, выберите товар', 'error');
            return;
        }

        if (!quantity || parseInt(quantity) <= 0) {
            showNotification('Укажите корректное количество', 'error');
            return;
        }

        if (!price || parseFloat(price) <= 0) {
            showNotification('Укажите корректную цену', 'error');
            return;
        }

        // Находим товар
        const product = allProducts.find(p => p.id == productId);
        if (!product) {
            showNotification('Товар не найден', 'error');
            return;
        }

        // Добавляем товар в список
        temporaryProducts.push({
            product_id: productId,
            name: product.name,
            price: parseFloat(price),
            purchase_price: product.purchase_price || 0,
            quantity: parseInt(quantity)
        });

        // Обновляем отображение
        updateProductsList();

        // Очищаем форму
        resetProductForm();

        // Скрываем форму и показываем кнопку добавления
        const form = modal.querySelector('#addProductForm');
        const btn = modal.querySelector('#showAddProductFormBtn');
        if (form && btn) {
            form.style.display = 'none';
            btn.style.display = 'inline-block';
        }

        showNotification('Товар успешно добавлен');
    }



    function updateProductsList() {
        const modal = document.getElementById('viewAppointmentModal');
        if (!modal) return;

        const productsSection = modal.querySelector('.products-section');
        if (!productsSection) return;

        // Получаем все товары из базы данных для отображения актуальной информации
        // Используем существующую переменную allProducts

        productsSection.innerHTML = `
        ${renderProductsList(temporaryProducts, allProducts)}
        <button class="btn-add-product" id="showAddProductFormBtn">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            Добавить
        </button>
        <div id="addProductForm" style="display: none; margin-top: 20px;">
            <div class="form-row-appointment">
                <div class="form-group" style="flex: 2;">
                    <label>Товар *</label>
                    <div class="product-search-container">
                        <input type="text" class="product-search-input form-control"
                               id="productSearchInput"
                               placeholder="Начните вводить название товара..."
                               oninput="searchProducts(this)"
                               onfocus="showProductDropdown(this)">
                        <div class="product-dropdown" style="display: none;">
                            <div class="product-dropdown-list"></div>
                        </div>
                        <input type="hidden" id="selectedProductId" name="product_id">
                    </div>
                </div>
                <div class="form-group-appointment">
                    <label>Количество *</label>
                    <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
                </div>
                <div class="form-group-appointment">
                    <label>Опт</label>
                    <input type="number" step="0.01" id="productWholesale" class="form-control" readonly style="background-color: #f0f0f0;">
                </div>
                <div class="form-group-appointment">
                    <label>Цена *</label>
                    <input type="number" step="0.01" id="productPrice" class="form-control" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" id="cancelAddProduct">Отмена</button>
                <button type="button" class="btn-submit" id="submitAddProduct"><svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>Добавить</button>
            </div>
        </div>
    `;

        updateTotalAmount();

        const btnAdd = modal.querySelector('#showAddProductFormBtn');
        const form = modal.querySelector('#addProductForm');
        const cancelBtn = modal.querySelector('#cancelAddProduct');
        const submitBtn = modal.querySelector('#submitAddProduct');

        // ✅ ПРИ ЗАГРУЗКЕ: если форма скрыта — кнопка появляется
        if (btnAdd && form) {
            // Показываем кнопку, если форма скрыта
            if (form.style.display === 'none') {
                btnAdd.style.display = 'inline-block';
            } else {
                btnAdd.style.display = 'none';
            }

            btnAdd.addEventListener('click', () => {
                btnAdd.style.display = 'none';
                form.style.display = 'block';
            });
        }

        // Отмена добавления товара
        if (cancelBtn && form && btnAdd) {
            cancelBtn.addEventListener('click', () => {
                form.style.display = 'none';
                btnAdd.style.display = 'inline-block';
                resetProductForm();
            });
        }

        // Подтвердить добавление
        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                addProductToAppointment();
            });
        }


        if (btnAdd && form) {
            btnAdd.addEventListener('click', () => {
                btnAdd.style.display = 'none';
                form.style.display = 'block';
            });
        }

        updateTotalAmount();
    }


    document.querySelector('.modal-footer')?.appendChild(document.getElementById('saveAppointmentChanges'));

    function updateTotalAmount() {
        const servicePrice = parseFloat(document.querySelector('.service-item span:nth-child(2)')?.textContent || '0');
        const productsTotal = temporaryProducts.reduce((sum, product) => {
            return sum + (parseInt(product.quantity) * parseFloat(product.price));
        }, 0);
        const totalAmount = servicePrice + productsTotal;

        const totalElement = document.querySelector('.total-value');
        if (totalElement) {
            totalElement.textContent = `${totalAmount.toFixed(2)} грн`;
        }
    }



    // Функции для поиска товаров
    function searchProducts(inputElement) {
        const searchTerm = inputElement.value.trim().toLowerCase();
        console.log('Поисковый запрос:', searchTerm);
        console.log('Доступные товары:', allProducts);

        const dropdown = inputElement.nextElementSibling;
        const dropdownList = dropdown.querySelector('.product-dropdown-list');

        if (searchTerm.length === 0) {
            dropdown.style.display = 'none';
            return;
        }

        const filteredProducts = allProducts.filter(product => {
            const nameMatch = product.name?.toLowerCase().includes(searchTerm) || false;
            console.log('Проверяем товар:', product.name, 'Совпадение:', nameMatch);
            return nameMatch;
        }).slice(0, 5);

        console.log('Найденные товары:', filteredProducts);

        if (filteredProducts.length > 0) {
            dropdownList.innerHTML = filteredProducts.map(product => {
                const name = escapeHtml(product.name || '');
                const price = product.retail_price || product.price || 0;

                return `
                        <div class="product-dropdown-item"
                             data-product-id="${product.id}"
                             data-price="${price}"
                             onclick="selectProduct(this, '${product.id}', '${name}', ${price})">
                            ${name} (${product.quantity || 0} шт)
                        </div>
                    `;
            }).join('');
            dropdown.style.display = 'block';
        } else {
            dropdownList.innerHTML = '<div class="product-dropdown-item no-results">Товары не найдены</div>';
            dropdown.style.display = 'block';
        }
    }

    function selectProduct(element, productId, name, price) {
        const modal = document.getElementById('viewAppointmentModal');
        if (!modal) return;

        const form = modal.querySelector('#addProductForm');
        if (!form) return;

        const searchInput = form.querySelector('#productSearchInput');
        const hiddenInput = form.querySelector('#selectedProductId');
        const priceInput = form.querySelector('#productPrice');
        const wholesaleInput = form.querySelector('#productWholesale');
        const dropdown = form.querySelector('.product-dropdown');
        const productDetails = form.querySelector('#productDetails');

        if (searchInput) searchInput.value = name;
        if (hiddenInput) hiddenInput.value = productId;
        if (dropdown) dropdown.style.display = 'none';

        // Находим товар в списке всех товаров
        const product = allProducts.find(p => p.id == productId);
        if (product) {
            // Заполняем цены
            if (priceInput) priceInput.value = product.price || product.retail_price || 0;
            if (wholesaleInput) wholesaleInput.value = product.purchase_price || 0;

            // Показываем детали товара
            if (productDetails) productDetails.style.display = 'flex';
        }

        console.log('Selected product:', { productId, name, price, formValues: {
                searchInput: searchInput?.value,
                hiddenInput: hiddenInput?.value,
                priceInput: priceInput?.value,
                wholesaleInput: wholesaleInput?.value
            }});
    }

    function formatPrice(price) {
        return parseFloat(price).toFixed(2);
    }

    // Закрытие выпадающего списка при клике вне его
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.product-search-container')) {
            document.querySelectorAll('.product-dropdown').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });

    function renderAddProductForm(products) {
        return `
            <div class="form-group">
                <label>Товар</label>
                <select id="productSelect" class="form-control">
                    <option value="">Выберите товар</option>
                    ${products.map(p => {
            const quantity = p.warehouse?.quantity || 0;
            if (quantity <= 0) return '';

            const retailPrice = parseFloat(p.warehouse?.retail_price || 0);
            const wholesalePrice = parseFloat(p.warehouse?.wholesale_price || 0);
            return `
                        <option value="${p.id}"
                                data-quantity="${quantity}"
                                data-retail-price="${retailPrice}"
                                data-wholesale-price="${wholesalePrice}"
                                data-name="${escapeHtml(p.name)}">
                            ${escapeHtml(p.name)} (${retailPrice.toFixed(2)} грн, остаток: ${quantity})
                        </option>
                    `;
        }).join('')}
                </select>
            </div>

            <div class="form-group">
                <label>Название товара</label>
                <input type="text" id="productNameDisplay" class="form-control" readonly>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Оптовая цена (грн)</label>
                    <input type="number" step="0.01" id="productWholesalePrice" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Розничная цена (грн) *</label>
                    <input type="number" step="0.01" id="productRetailPrice" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>Количество *</label>
                <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
            </div>

            <button class="btn-submit" id="submitAddProduct">Добавить товар</button>
            <button class="btn-cancel" id="cancelAddProduct">Отмена</button>
        `;
    }

    function setupAddProductHandlers() {
        document.getElementById('productSelect')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption?.value) {
                const maxQuantity = parseInt(selectedOption.dataset.quantity);
                document.getElementById('productQuantity').max = maxQuantity;
                document.getElementById('productQuantity').value = Math.min(1, maxQuantity);
                document.getElementById('productNameDisplay').value = selectedOption.dataset.name || '';
                document.getElementById('productWholesalePrice').value = selectedOption.dataset.wholesalePrice || '';
                document.getElementById('productRetailPrice').value = selectedOption.dataset.retailPrice || '';
            } else {
                document.getElementById('productNameDisplay').value = '';
                document.getElementById('productWholesalePrice').value = '';
                document.getElementById('productRetailPrice').value = '';
            }
        });

        document.querySelector('.btn-add-product')?.addEventListener('click', function() {
            document.getElementById('addProductForm').style.display = 'block';
            this.style.display = 'none';
        });

        document.getElementById('cancelAddProduct')?.addEventListener('click', function() {
            document.getElementById('addProductForm').style.display = 'none';
            document.querySelector('.btn-add-product').style.display = 'block';
        });

        document.getElementById('submitAddProduct')?.addEventListener('click', async function() {
            await addProductToAppointment();
        });

        // Обработчик удаления товара
        document.querySelectorAll('.btn-delete-product').forEach(btn => {
            btn.addEventListener('click', async function() {
                const saleId = this.dataset.saleId;
                await deleteProductFromAppointment(saleId);
            });
        });
        document.getElementById('saveAppointmentChanges')?.addEventListener('click', async function() {
            // Здесь можно добавить логику для сохранения всех изменений
            showNotification('Изменения сохранены');
            closeViewAppointmentModal();
            // При необходимости обновите данные на странице
        });

    }


    async function deleteProductFromAppointment() {
        try {
            const appointmentId = document.getElementById('appointmentId')?.value;
            if (!appointmentId) {
                showNotification('Не удалось определить запись', 'error');
                return;
            }

            // Если это временный товар (еще не сохраненный)
            if (currentDeleteIndex !== null) {
                temporaryProducts.splice(currentDeleteIndex, 1);
                updateProductsList();
                updateTotalAmount();
                showNotification('Товар удален');
                return;
            }

            // Если товар уже сохранен в базе
            if (currentDeleteProductId) {
                const response = await fetch(`/appointments/${appointmentId}/remove-product`, { // Измените URL на правильный
                    method: 'POST', // Используйте POST вместо DELETE, если нужно
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ product_id: currentDeleteProductId })
                });

                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Ошибка удаления с сервера');

                // После успешного удаления перезагружаем данные записи
                await viewAppointment(appointmentId);
                showNotification('Товар успешно удален');
            }
        } catch (err) {
            console.error(err);
            showNotification(err.message || 'Ошибка при удалении товара', 'error');
        } finally {
            currentDeleteProductId = null;
            currentDeleteIndex = null;
        }
    }

    // Функции для работы с клиентами
    function searchClients(inputElement) {
        const searchTerm = inputElement.value.trim().toLowerCase();
        const dropdown = inputElement.nextElementSibling;
        const dropdownList = dropdown.querySelector('.client-dropdown-list');

        if (searchTerm.length === 0) {
            dropdown.style.display = 'none';
            return;
        }

        const filteredClients = allClients.filter(client => {
            const nameMatch = client.name?.toLowerCase().includes(searchTerm) || false;
            const instagramMatch = client.instagram?.toLowerCase().includes(searchTerm) || false;
            const emailMatch = client.email?.toLowerCase().includes(searchTerm) || false;
            const phoneMatch = client.phone?.includes(searchTerm) || false;

            return nameMatch || instagramMatch || emailMatch || phoneMatch;
        }).slice(0, 5); // Ограничиваем до 5 результатов

        if (filteredClients.length > 0) {
            dropdownList.innerHTML = filteredClients.map(client => {
                const name = escapeHtml(client.name || '');
                const instagram = client.instagram ? `(@${escapeHtml(client.instagram)})` : '';
                const phone = client.phone ? ` - ${escapeHtml(client.phone)}` : '';

                return `
                    <div class="client-dropdown-item"
                         data-client-id="${client.id}"
                         onclick="selectClient(this, '${client.id}', '${name} ${instagram}')">
                        ${name} ${instagram} ${phone}
                    </div>
                `;
            }).join('');
            dropdown.style.display = 'block';
        } else {
            dropdownList.innerHTML = '<div class="client-dropdown-item no-results">Клиенты не найдены</div>';
            dropdown.style.display = 'block';
        }
    }

    function selectClient(element, clientId, clientName) {
        const container = element.closest('.client-search-container');
        const input = container.querySelector('.client-search-input');
        const select = container.querySelector('.client-select');
        const dropdown = container.querySelector('.client-dropdown');

        input.value = clientName.trim();
        select.value = clientId;
        dropdown.style.display = 'none';
    }

    // Функции для работы с записями
    async function editAppointment(id) {
        if (!id) {
            showNotification('Ошибка: ID записи не указан', 'error');
            return;
        }

        currentAppointmentId = id;
        toggleModal('editAppointmentModal');
        const modalBody = document.getElementById('editAppointmentModalBody');
        modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

        try {
            const response = await fetch(`/appointments/${id}/edit`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                renderEditAppointmentForm(data.appointment);
            } else {
                throw new Error(data.message || 'Ошибка при загрузке записи');
            }
        } catch (error) {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    Ошибка: ${escapeHtml(error.message)}
                </div>
                <button class="btn-cancel" onclick="toggleModal('editAppointmentModal', false)">Закрыть</button>
            `;
        }
    }

    function renderEditAppointmentForm(appointment) {
        const modalBody = document.getElementById('editAppointmentModalBody');
        modalBody.innerHTML = `
            <form id="editAppointmentForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="${appointment.id}">
                <div class="form-row">
                    <div class="form-group">
                        <label>Дата *</label>
                        <input type="date" name="date" value="${formatDateForInput(appointment.date)}" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Время *</label>
                        <input type="time" name="time" value="${escapeHtml(appointment.time)}" required class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Клиент *</label>
                        <select name="client_id" class="form-control" required>
                            <option value="">Выберите клиента</option>
                            ${allClients.map(client => `
                                <option value="${client.id}" ${client.id == appointment.client_id ? 'selected' : ''}>
                                    ${escapeHtml(client.name)}
                                    ${client.instagram ? `(${escapeHtml(client.instagram)})` : ''}
                                    ${client.phone ? ` - ${escapeHtml(client.phone)}` : ''}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Процедура *</label>
                        <select name="service_id" class="form-control" required>
                            <option value="">Выберите процедуру</option>
                            ${allServices.map(service => `
                                <option value="${service.id}"
                                        data-price="${service.price}"
                                        ${service.id == appointment.service_id ? 'selected' : ''}>
                                    ${escapeHtml(service.name)}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Стоимость (Грн)</label>
                    <input type="number" step="0.01" name="price" value="${parseFloat(appointment.price).toFixed(2)}" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label>Примечания</label>
                    <textarea name="notes" rows="2" class="form-control">${escapeHtml(appointment.notes || '')}</textarea>
                </div>

                <div class="form-group">
                    <label>Статус</label>
                    <select name="status" class="form-control">
                        <option value="pending" ${appointment.status === 'pending' ? 'selected' : ''}>Ожидается</option>
                        <option value="completed" ${appointment.status === 'completed' ? 'selected' : ''}>Завершено</option>
                        <option value="cancelled" ${appointment.status === 'cancelled' ? 'selected' : ''}>Отменено</option>
                        <option value="rescheduled" ${appointment.status === 'rescheduled' ? 'selected' : ''}>Перенесено</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditAppointmentModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить изменения</button>
                </div>
            </form>
        `;

        // Обработчик изменения выбора процедуры
        document.querySelector('#editAppointmentModal [name="service_id"]')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const priceInput = document.querySelector('#editAppointmentModal [name="price"]');
            if (selectedOption?.dataset.price) {
                priceInput.value = selectedOption.dataset.price;
            }
        });

        // Обработчик формы редактирования
        document.getElementById('editAppointmentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            await submitEditAppointmentForm(this, currentAppointmentId);
        });
    }

    async function submitEditAppointmentForm(form, appointmentId) {
        clearErrors('editAppointmentForm');
        const formData = new FormData(form);

        try {
            formData.append('_method', 'PUT');

            const response = await fetch(`/appointments/${appointmentId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Запись успешно обновлена');
                closeEditAppointmentModal();

                // Обновляем календарь
                if (typeof calendar !== 'undefined' && calendar) {
                    calendar.refetchEvents();
                }

                // Обновляем строку в таблице
                const row = document.querySelector(`tr[data-appointment-id="${data.appointment.id}"]`);
                if (row) {
                    row.innerHTML = `
                        <td>${new Date(data.appointment.date).toLocaleDateString('ru-RU')}</td>
                        <td>${escapeHtml(data.appointment.time.split(':').slice(0, 2).join(':'))}</td>
                        <td>
                            ${escapeHtml(data.appointment.client.name)}
                            ${data.appointment.client.instagram ? `
                                (<a href="https://instagram.com/${escapeHtml(data.appointment.client.instagram)}" class="instagram-link" target="_blank" rel="noopener noreferrer">@${escapeHtml(data.appointment.client.instagram)}</a>)` : ''}
                        </td>
                        <td>${escapeHtml(data.appointment.service.name)}</td>
                        <td>${parseFloat(data.appointment.price).toFixed(2)} грн</td>
                        <td>
                            <span class="status-badge status-${data.appointment.status}">
                                ${getStatusName(data.appointment.status)}
                            </span>
                        </td>
                        <td>
                            <div class="appointment-actions actions-cell">
                                <button class="btn-view" data-appointment-id="${data.appointment.id}" title="Просмотр">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Просмотр
                                </button>
                                <button class="btn-edit" data-appointment-id="${data.appointment.id}" title="Редактировать">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete" data-appointment-id="${data.appointment.id}" title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Удалить
                                </button>
                            </div>
                        </td>
                    `;
                }
            } else if (data.errors) {
                displayErrors(data.errors, 'editAppointmentForm');
            } else {
                throw new Error(data.message || 'Ошибка при обновлении записи');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message || 'Ошибка при обновлении записи', 'error');
        }
    }

    function confirmDeleteAppointment(event, id) {
        event.preventDefault();
        currentDeleteId = id;
        isDeletingAppointment = true; // Добавляем флаг
        toggleModal('confirmationModal');
    }

    async function deleteAppointment(id) {
        try {
            const response = await fetch(`/appointments/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                // Обновляем календарь если он существует
                if (typeof calendar !== 'undefined' && calendar) {
                    calendar.refetchEvents();
                }

                // Удаляем строку из таблицы если она существует
                const row = document.querySelector(`tr[data-appointment-id="${id}"]`);
                if (row) {
                    row.remove();
                }

                // Закрываем модальные окна
                toggleModal('confirmationModal', false);
                if (currentAppointmentId === id) {
                    toggleModal('viewAppointmentModal', false);
                }

                showNotification('Запись успешно удалена');
            } else {
                throw new Error(data.message || 'Ошибка при удалении записи');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message || 'Ошибка при удалении записи', 'error');
        }
    }




    function formatDateForInput(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function clearErrors(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        const errorElements = form.querySelectorAll('.error-message');
        errorElements.forEach(el => el.remove());

        const errorInputs = form.querySelectorAll('.is-invalid');
        errorInputs.forEach(el => el.classList.remove('is-invalid'));
    }

    function displayErrors(errors, formId) {
        clearErrors(formId);
        const form = document.getElementById(formId);
        if (!form) return;

        for (const [field, messages] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                errorElement.textContent = messages.join(', ');
                input.parentNode.insertBefore(errorElement, input.nextSibling);
            }
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        // Обработчики кнопок
        document.getElementById('addAppointmentBtn')?.addEventListener('click', () => {
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('#appointmentModal input[name="date"]').value = today;
            toggleModal('appointmentModal');
        });

        // Единый обработчик для кнопки отмены
        document.getElementById('cancelDelete')?.addEventListener('click', (e) => {
            e.stopPropagation(); // Предотвращаем всплытие события
            toggleModal('confirmationModal', false);
        });

        // Единый обработчик для кнопки подтверждения удаления
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', async (e) => {
            e.stopPropagation(); // Предотвращаем всплытие события
            if (isDeletingAppointment) {
                await deleteAppointment(currentDeleteId);
            } else {
                await deleteProductFromAppointment();
            }
            toggleModal('confirmationModal', false);
        });

        // Обработчик формы добавления записи
        document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAppointmentForm(this);
        });

        // Обработчик изменения выбора процедуры
        document.querySelector('[name="service_id"]')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const priceInput = document.querySelector('[name="price"]');
            if (selectedOption?.dataset.price) {
                priceInput.value = selectedOption.dataset.price;
            }
        });

        // Обработчик переключения между видами
        document.querySelectorAll('.btn-view-switch').forEach(btn => {
            btn.addEventListener('click', function() {
                window.location.href = `/appointments?view=${this.dataset.view}`;
            });
        });

        // Поиск по таблице
        document.getElementById('searchInput')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('#appointmentsTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
            });
        });

        // Делегирование событий
        document.addEventListener('click', function(e) {
            const target = e.target.closest('button');
            if (!target) return;

            if (target.classList.contains('btn-view')) {
                e.preventDefault();
                viewAppointment(target.dataset.appointmentId);
            }
            if (target.classList.contains('btn-edit')) {
                e.preventDefault();
                editAppointment(target.dataset.appointmentId);
            }
            if (target.classList.contains('btn-delete')) {
                e.preventDefault();
                confirmDeleteAppointment(e, target.dataset.appointmentId);
            }
            if (e.target == document.getElementById('appointmentModal')) {
                toggleModal('appointmentModal', false);
            }
            if (e.target == document.getElementById('editAppointmentModal')) {
                toggleModal('editAppointmentModal', false);
            }
            if (e.target == document.getElementById('viewAppointmentModal')) {
                toggleModal('viewAppointmentModal', false);
            }
            if (e.target == document.getElementById('confirmationModal')) {
                toggleModal('confirmationModal', false);
            }
            if (e.target && e.target.id === 'saveAppointmentChanges') {
                saveAppointmentChanges();
            }
        });
    });
    async function saveAppointmentChanges() {
        const modal = document.getElementById('viewAppointmentModal');
        const appointmentId = modal.querySelector('#appointmentId')?.value;

        if (!appointmentId) {
            showNotification('ID записи не найден', 'error');
            return;
        }

        // Get basic appointment data
        const dateText = modal.querySelector('.detail-row:nth-child(1) .detail-value')?.textContent;
        const timeElement = modal.querySelector('.detail-row-no-flex:nth-child(2) .detail-value');
        const time = timeElement?.textContent?.trim();

        console.log('Debug time:', {
            timeElement,
            timeText: timeElement?.textContent,
            time
        });

        // Получаем имя клиента из правильного элемента
        const clientElement = modal.querySelector('.client-name');
        const clientNameWithInstagram = clientElement?.textContent?.trim() || '';

        // Извлекаем только имя клиента, обрабатывая оба случая
        const clientName = clientNameWithInstagram.includes('(@')
            ? clientNameWithInstagram.split('(@')[0].trim()
            : clientNameWithInstagram.trim();

        // Получаем имя услуги из правильного элемента
        const serviceElement = modal.querySelector('.service-name');
        const serviceName = serviceElement?.textContent?.trim();

        const priceText = modal.querySelector('.service-price')?.textContent;
        const price = parseFloat(priceText?.replace('грн', '').trim()) || 0;

        // Format date
        let formattedDate = '';
        if (dateText) {
            const [day, month, year] = dateText.split('.');
            formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
        }

        // Find client and service IDs with improved error handling
        const client = clientName ? allClients.find(c => c.name.trim() === clientName) : null;
        const service = serviceName ? allServices.find(s => s.name.trim() === serviceName) : null;

        // Подробная проверка данных перед отправкой
        const validationErrors = [];

        if (!formattedDate) validationErrors.push('Дата не указана');
        if (!time) validationErrors.push('Время не указано');
        if (!clientName) validationErrors.push('Имя клиента не найдено в форме');
        if (!client || !client.id) validationErrors.push(`Не удалось найти клиента "${clientName}" в списке`);
        if (!serviceName) validationErrors.push('Название услуги не найдено в форме');
        if (!service || !service.id) validationErrors.push(`Не удалось найти услугу "${serviceName}" в списке`);
        if (isNaN(price) || price < 0) validationErrors.push('Некорректная цена');

        if (validationErrors.length > 0) {
            showNotification('Ошибки валидации:\n' + validationErrors.join('\n'), 'error');
            return;
        }

        // Подготавливаем данные для отправки
        const requestData = {
            date: formattedDate,
            time: time,
            client_id: client.id,
            service_id: service.id,
            price: price,
            sales: temporaryProducts.map(p => ({
                product_id: p.product_id,
                quantity: parseInt(p.quantity),
                price: parseFloat(p.price),
                purchase_price: parseFloat(p.purchase_price || 0)
            }))
        };

        console.log('Отправляемые данные:', requestData);

        try {
            const response = await fetch(`/appointments/${appointmentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

            const data = await response.json();

            if (!response.ok) {
                console.error('Ответ сервера:', data);
                if (data.errors) {
                    const errorMessages = Object.entries(data.errors)
                        .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                        .join('\n');
                    throw new Error(`Ошибки валидации:\n${errorMessages}`);
                }
                throw new Error(data.message || `Ошибка сохранения (${response.status})`);
            }

            if (data.success) {
                showNotification('Изменения успешно сохранены');
                toggleModal('viewAppointmentModal', false);

                // Обновляем календарь если он существует
                if (typeof calendar !== 'undefined' && calendar) {
                    calendar.refetchEvents();
                }

                // Обновляем строку в таблице если она существует
                if (data.appointment) {
                    updateAppointmentRow(data.appointment);
                }
            } else {
                throw new Error(data.message || 'Ошибка сохранения');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message || 'Ошибка при сохранении', 'error');
        }
    }



    function formatDateForPayload(dateString) {
        if (!dateString) return '';
        // Try both possible date formats (from table view and modal view)
        if (dateString.includes('.')) {
            // Format: dd.mm.yyyy
            const [day, month, year] = dateString.split('.');
            return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
        } else {
            // Format: yyyy-mm-dd (already correct)
            return dateString;
        }
    }

    function showProductDropdown(input) {
        console.log('showProductDropdown вызван');
        console.log('Все товары:', allProducts);

        const dropdown = input.nextElementSibling;
        const dropdownList = dropdown.querySelector('.product-dropdown-list');

        if (input.value.length > 0) {
            searchProducts(input);
        } else {
            // Показываем первые 5 товаров
            const availableProducts = allProducts.slice(0, 5);
            console.log('Доступные товары:', availableProducts);

            if (availableProducts.length === 0) {
                dropdownList.innerHTML = '<div class="product-dropdown-item">Нет доступных товаров</div>';
            } else {
                dropdownList.innerHTML = availableProducts.map(product => {
                    const name = escapeHtml(product.name || '');
                    const retailPrice = product.price || 0;
                    const wholesalePrice = product.purchase_price || 0;

                    return `
                            <div class="product-dropdown-item"
                                 data-product-id="${product.id}"
                                 data-retail-price="${retailPrice}"
                                 data-wholesale-price="${wholesalePrice}"
                                 onclick="selectProduct(this, '${product.id}', '${name}', ${retailPrice})">
                                ${name} (${product.quantity} шт)
                            </div>
                        `;
                }).join('');
            }
            dropdown.style.display = 'block';
        }
    }

    function updateProductsTable() {
        const modal = document.getElementById('viewAppointmentModal');
        if (!modal) return;

        const tableContainer = modal.querySelector('.products-section');
        if (!tableContainer) return;

        // Создаем структуру таблицы
        tableContainer.innerHTML = `
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Количество</th>
                            <th>Розничная цена</th>
                            <th>Оптовая цена</th>
                            <th>Сумма</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${temporaryProducts.map((product, index) => {
            const retailPrice = parseFloat(product.price);
            const wholesalePrice = parseFloat(product.purchase_price);
            const quantity = parseInt(product.quantity);
            const total = retailPrice * quantity;

            return `
                                <tr data-index="${index}">
                                    <td>${product.name}</td>
                                    <td>${quantity}</td>
                                    <td>${retailPrice.toFixed(2)} грн</td>
                                    <td>${wholesalePrice.toFixed(2)} грн</td>
                                    <td>${total.toFixed(2)} грн</td>
                                    <td>
                                        <button class="btn-delete btn-delete-product" onclick="deleteProduct(${index})" data-product-id="${product.product_id}" title="Удалить">
                                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            `;
        }).join('')}
                    </tbody>
                </table>
                <button class="btn-add-product" id="showAddProductFormBtn">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить
                </button>
                <div id="addProductForm" style="display: none; margin-top: 20px;">
                    <div class="form-row-appointment">
                        <div class="form-group" style="flex: 2;">
                            <label>Товар *</label>
                            <div class="product-search-container">
                                <input type="text" class="product-search-input form-control"
                                       id="productSearchInput"
                                       placeholder="Начните вводить название товара..."
                                       oninput="searchProducts(this)"
                                       onfocus="showProductDropdown(this)">
                                <div class="product-dropdown" style="display: none;">
                                    <div class="product-dropdown-list"></div>
                                </div>
                                <input type="hidden" id="selectedProductId" name="product_id">
                            </div>
                        </div>
                        <div class="form-group-appointment">
                            <label>Количество *</label>
                            <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="form-group-appointment">
                            <label>Опт</label>
                            <input type="number" step="0.01" id="productWholesale" class="form-control" readonly style="background-color: #f0f0f0;">
                        </div>
                        <div class="form-group-appointment">
                            <label>Цена *</label>
                            <input type="number" step="0.01" id="productPrice" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" id="cancelAddProduct">Отмена</button>
                        <button type="button" class="btn-submit" id="submitAddProduct"><svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>Добавить</button>
            </div>
        </div>
    `;

        // Обновляем общую сумму
        updateTotalAmount();

        // Добавляем обработчики событий
        const btnAdd = modal.querySelector('#showAddProductFormBtn');
        const form = modal.querySelector('#addProductForm');
        const cancelBtn = modal.querySelector('#cancelAddProduct');
        const submitBtn = modal.querySelector('#submitAddProduct');

        if (btnAdd && form) {
            btnAdd.addEventListener('click', () => {
                btnAdd.style.display = 'none';
                form.style.display = 'block';
            });
        }

        if (cancelBtn && form && btnAdd) {
            cancelBtn.addEventListener('click', () => {
                form.style.display = 'none';
                btnAdd.style.display = 'inline-block';
                resetProductForm();
            });
        }

        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                addProductToAppointment();
            });
        }
    }

    function deleteProduct(index) {
        temporaryProducts.splice(index, 1);
        updateProductsTable();

        // После удаления товара сбрасываем форму и показываем кнопку добавления
        const modal = document.getElementById('viewAppointmentModal');
        if (modal) {
            const btnAdd = modal.querySelector('#showAddProductFormBtn');
            const form = modal.querySelector('#addProductForm');
            if (btnAdd && form) {
                resetProductForm();
                form.style.display = 'none';
                btnAdd.style.display = 'inline-block';
            }
        }
    }

    function updateProductQuantity(index, newQuantity) {
        if (index >= 0 && index < temporaryProducts.length) {
            temporaryProducts[index].quantity = parseInt(newQuantity) || 1;
            updateProductsTable();
        }
    }

    function checkFormState() {
        const modal = document.getElementById('viewAppointmentModal');
        if (!modal) return;

        const form = modal.querySelector('#addProductForm');
        if (!form) return;

        const searchInput = form.querySelector('#productSearchInput');
        const hiddenInput = form.querySelector('#selectedProductId');
        const priceInput = form.querySelector('#productPrice');
        const quantityInput = form.querySelector('#productQuantity');

        console.log('Form state:', {
            searchInput: searchInput?.value,
            hiddenInput: hiddenInput?.value,
            priceInput: priceInput?.value,
            quantityInput: quantityInput?.value
        });
    }

    // Добавляем проверку состояния формы после каждого важного действия
    const originalResetProductForm = resetProductForm;
    resetProductForm = function() {
        originalResetProductForm();
        console.log('After reset:');
        checkFormState();
    };

    const originalSelectProduct = selectProduct;
    selectProduct = function(...args) {
        originalSelectProduct.apply(this, args);
        console.log('After select:');
        checkFormState();
    };

    const originalAddProductToAppointment = addProductToAppointment;
    addProductToAppointment = function() {
        console.log('Before add:');
        checkFormState();
        originalAddProductToAppointment();
        console.log('After add:');
        checkFormState();
    };

    function escapeHtml(text) {
        return text.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[c]);
    }


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

    async function submitAppointmentForm(form) {
        clearErrors('appointmentForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('/appointments', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Запись успешно создана');
                closeAppointmentModal();

                if (typeof calendar !== 'undefined' && calendar) {
                    calendar.refetchEvents();
                }

                // Добавляем новую запись в таблицу без перезагрузки
                const appointment = data.appointment;
                const tbody = document.querySelector('#appointmentsTable tbody');

                if (tbody) {
                    const newRow = document.createElement('tr');
                    newRow.setAttribute('data-appointment-id', appointment.id);

                    newRow.innerHTML = `
                        <td>${new Date(appointment.date).toLocaleDateString('ru-RU')}</td>
                        <td>${escapeHtml(appointment.time.split(':').slice(0, 2).join(':'))}</td>
                        <td>
                            ${escapeHtml(appointment.client.name)}
                            ${appointment.client.instagram ? `
                                (<a href="https://instagram.com/${escapeHtml(appointment.client.instagram)}" class="instagram-link" target="_blank" rel="noopener noreferrer">@${escapeHtml(appointment.client.instagram)}</a>)` : ''}
                        </td>
                        <td>${escapeHtml(appointment.service.name)}</td>
                        <td>${parseFloat(appointment.price).toFixed(2)} грн</td>
                        <td>
                            <span class="status-badge status-${appointment.status}">
                                ${getStatusName(appointment.status)}
                            </span>
                        </td>
                        <td>
                            <div class="appointment-actions actions-cell">
                                <button class="btn-view" data-appointment-id="${data.appointment.id}" title="Просмотр">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Просмотр
                                </button>
                                <button class="btn-edit" data-appointment-id="${data.appointment.id}" title="Редактировать">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete" data-appointment-id="${data.appointment.id}" title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Удалить
                                </button>
                            </div>
                        </td>
                    `;

                    tbody.insertBefore(newRow, tbody.firstChild);
                }
            } else if (data.errors) {
                displayErrors(data.errors, 'appointmentForm');
            } else {
                throw new Error(data.message || 'Ошибка при создании записи');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message || 'Ошибка при создании записи', 'error');
        }
    }

    async function submitEditAppointmentForm(form, appointmentId) {
        clearErrors('editAppointmentForm');
        const formData = new FormData(form);

        try {
            formData.append('_method', 'PUT');

            const response = await fetch(`/appointments/${appointmentId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Запись успешно обновлена');
                closeEditAppointmentModal();

                // Обновляем календарь
                if (typeof calendar !== 'undefined' && calendar) {
                    calendar.refetchEvents();
                }

                // Обновляем строку в таблице
                const row = document.querySelector(`tr[data-appointment-id="${data.appointment.id}"]`);
                if (row) {
                    row.innerHTML = `
                        <td>${new Date(data.appointment.date).toLocaleDateString('ru-RU')}</td>
                        <td>${escapeHtml(data.appointment.time.split(':').slice(0, 2).join(':'))}</td>
                        <td>
                            ${escapeHtml(data.appointment.client.name)}
                            ${data.appointment.client.instagram ? `
                                (<a href="https://instagram.com/${escapeHtml(data.appointment.client.instagram)}" class="instagram-link" target="_blank" rel="noopener noreferrer">@${escapeHtml(data.appointment.client.instagram)}</a>)` : ''}
                        </td>
                        <td>${escapeHtml(data.appointment.service.name)}</td>
                        <td>${parseFloat(data.appointment.price).toFixed(2)} грн</td>
                        <td>
                            <span class="status-badge status-${data.appointment.status}">
                                ${getStatusName(data.appointment.status)}
                            </span>
                        </td>
                        <td>
                            <div class="appointment-actions actions-cell">
                                <button class="btn-view" data-appointment-id="${data.appointment.id}" title="Просмотр">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Просмотр
                                </button>
                                <button class="btn-edit" data-appointment-id="${data.appointment.id}" title="Редактировать">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete" data-appointment-id="${data.appointment.id}" title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Удалить
                                </button>
                            </div>
                        </td>
                    `;
                }
            } else if (data.errors) {
                displayErrors(data.errors, 'editAppointmentForm');
            } else {
                throw new Error(data.message || 'Ошибка при обновлении записи');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message || 'Ошибка при обновлении записи', 'error');
        }
    }

    function getStatusName(status) {
        const statusNames = {
            'pending': 'Ожидается',
            'completed': 'Завершено',
            'cancelled': 'Отменено',
            'rescheduled': 'Перенесено'
        };
        return statusNames[status] || 'Ожидается';
    }
</script>
@endsection
