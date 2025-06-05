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

        .btn-add-product {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-add-product:hover {
            background-color: #2563eb;
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
            align-items: center;
            margin-bottom: 20px;
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
            display: flex;
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

        .fc-event {
            border: none;
            padding: 2px 4px;
            margin: 1px 0;
            cursor: pointer;
        }

        .fc-day-grid-event {
            border-radius: 4px;
        }

        .fc-time-grid-event {
            border-radius: 4px;
            margin: 1px 0;
        }

        .fc-time {
            font-weight: bold;
        }

        .fc-title {
            font-size: 0.9em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fc-day-header {
            font-weight: 600;
            padding: 8px 0;
        }

        .fc-day-number {
            padding: 8px;
            font-weight: 600;
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
            padding: 8px 16px;
            border: 1px solid #e9ecef;
            background: #fff;
            border-radius: 8px;
            cursor: pointer;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .today-button:hover {
            background: #e9ecef;
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
    </style>

    <div class="container">
        @if($viewType === 'list')
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

                <div class="appointments-list table-wrapper" id="appointmentsList">
                    <table class="table-striped appointments-table" id="appointmentsTable">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Время</th>
                            <th>Клиент</th>
                            <th>Процедура</th>
                            <th>Стоимость</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($appointments as $appointment)
                            <tr data-appointment-id="{{ $appointment->id }}">
                                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                                <td>{{ $appointment->time }}</td>
                                <td>
                                    {{ $appointment->client->name }}
                                    @if($appointment->client->instagram)
                                        (<a href="https://instagram.com/{{ $appointment->client->instagram }}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                                            <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $appointment->client->instagram }}
                                        </a>)
                                    @endif
                                </td>
                                <td>{{ $appointment->service->name }}</td>
                                <td>{{ number_format($appointment->price, 2) }} грн</td>
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
            </div>
        @else
            <div id="calendarView">
                <div class="calendar-wrapper">
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
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('calendar')) {
                var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                    initialView: 'dayGridMonth',
                    headerToolbar: false, // Отключаем встроенную панель инструментов
                    locale: 'ru',
                    height: 'auto',
                    selectable: true,
                    editable: true,
                    events: '/appointments/calendar-events',
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    },
                    slotMinTime: '08:00:00',
                    slotMaxTime: '20:00:00',
                    allDaySlot: false,
                    slotDuration: '00:30:00',
                    eventClick: function(info) {
                        // Открываем модальное окно просмотра записи
                        viewAppointment(info.event.id);
                    },
                    dateClick: function(info) {
                        // Открываем модальное окно создания записи
                        openAppointmentModal(info.dateStr);
                    }
                });

                calendar.render();

                // Функция обновления заголовка
                function updateTitle() {
                    const monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 
                                      'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
                    const date = calendar.getDate();
                    const month = monthNames[date.getMonth()];
                    const year = date.getFullYear();
                    document.getElementById('currentMonthYear').textContent = `${month} ${year}`;
                }

                // Обработчики кнопок навигации
                document.querySelector('.prev-button').addEventListener('click', function() {
                    calendar.prev();
                    updateTitle();
                });

                document.querySelector('.next-button').addEventListener('click', function() {
                    calendar.next();
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

        // ... rest of existing code ...
    </script>
@endsection