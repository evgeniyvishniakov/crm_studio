@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <style>
/* Цвет текста событий в Неделя и День — белый */
#calendar .fc-timegrid-event .fc-event-main,
#calendar .fc-timegrid-event .fc-event-title,
#calendar .fc-timegrid-event .fc-event-time {
    color: #fff !important;
}

/* Возвращаем кружочки-статусы для dayGridMonth */
#calendar .fc-daygrid-event-dot {
    width: 8px !important;
    height: 8px !important;
    min-width: 8px !important;
    min-height: 8px !important;
    max-width: 8px !important;
    max-height: 8px !important;
    border-radius: 50% !important;
    display: inline-block;
    vertical-align: middle;
    margin: 0 2px 0 0;
    background: #fff !important;
    border-width: 2px !important;
    border-style: solid !important;
    box-sizing: border-box !important;
    pointer-events: none;
}
#calendar .fc-daygrid-event.status-completed .fc-daygrid-event-dot {
    border-color: #10b981 !important;
    background: #10b981 !important;
}
#calendar .fc-daygrid-event.status-pending .fc-daygrid-event-dot {
    border-color: #f59e0b !important;
    background: #f59e0b !important;
}
#calendar .fc-daygrid-event.status-cancelled .fc-daygrid-event-dot {
    border-color: #ef4444 !important;
    background: #ef4444 !important;
}
#calendar .fc-daygrid-event.status-rescheduled .fc-daygrid-event-dot {
    border-color: #3b82f6 !important;
    background: #3b82f6 !important;
}
        /* События FullCalendar в режиме День — всё в одну строку */
#calendar .fc-timegrid-event .fc-event-main {
    display: flex;
    align-items: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    gap: 0.5em;
    font-size: 1.08em !important;
    padding: 0 2px;
}
/* Пересекающиеся события: визуально одно над другим, усиленная тень, без уменьшения ширины */
#calendar .fc-timegrid-event {
    box-shadow: 0 6px 24px rgba(60, 72, 88, 0.5), 0 1.5px 4px rgba(60, 72, 88, 0.10);
    border-radius: 10px !important;

    z-index: 2;
}
#calendar .fc-timegrid-event.fc-event-end {
    margin-right: 0 !important;
}
#calendar .fc-timegrid-event .fc-event-time {
    font-weight: 700;
    margin-right: 0.5em;
    color: #fff !important;
    flex-shrink: 0;
}
#calendar .fc-timegrid-event .fc-event-title {
    color: #fff !important;
    font-weight: 500;
    flex-shrink: 1;
    overflow: hidden;
    text-overflow: ellipsis;
}
/* Новое требование: .fc-v-event .fc-event-main-frame в одну строку, выравнивание по низу */
#calendar .fc-v-event .fc-event-main-frame {
    flex-direction: row !important;
    height: auto !important;
    align-items: end !important;
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
            background: linear-gradient(135deg, #eba70e 60%, #f3c138 100%);
            color: #fff;
        }
        #calendar .fc-timegrid-event .event-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 8px;
    vertical-align: middle;
    border: 2px solid #fff;
    box-shadow: 0 1px 4px rgba(60,72,88,0.10);
}
#calendar .fc-timegrid-event .event-dot.status-completed { background: #10b981; }
#calendar .fc-timegrid-event .event-dot.status-pending { background: #f59e0b; }
#calendar .fc-timegrid-event .event-dot.status-cancelled { background: #ef4444; }
#calendar .fc-timegrid-event .event-dot.status-rescheduled { background: #3b82f6; }
    
        .status-completed {
            background: linear-gradient(135deg, #4CAF50 60%, #56bb93 100%);
            color: #fff;
        }
    
        .status-cancelled {
            background: linear-gradient(135deg, #F44336 60%, #eb7171 100%);
            color: #fff;
        }
    
        .status-rescheduled {
            background: linear-gradient(135deg, #ae1ee9 60%, #bc79c9 100%);
            color: #fff;
        }
    
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        #calendar .fc-timegrid-event .event-chip.status-completed,
#calendar .fc-timegrid-event .event-chip.status-pending,
#calendar .fc-timegrid-event .event-chip.status-cancelled,
#calendar .fc-timegrid-event .event-chip.status-rescheduled {
    background: transparent !important;
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
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            padding: 4px 8px;
            margin: 0 2px;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-delete-product:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }
    
        .btn-delete-product .icon {
            width: 1.25rem;
            height: 1.25rem;
        }


        
    
        /* Стили для календаря */
        .calendar-wrapper {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(60, 72, 88, 0.10), 0 1.5px 4px rgba(60, 72, 88, 0.06);
            padding: 32px 24px 24px 24px;
            margin-bottom: 32px;
            transition: box-shadow 0.3s;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 24px;
        }
   
        .calendar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #22223b;
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            padding: 0;
        }
        .calendar-title:before {
            content: "";
            display: inline-block;
            width: 28px;
            height: 28px;
            background: url('data:image/svg+xml;utf8,<svg fill="%232563eb" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="5" width="18" height="16" rx="4"/><rect x="7" y="2" width="2" height="6" rx="1"/><rect x="15" y="2" width="2" height="6" rx="1"/></svg>') no-repeat center/contain;
            margin-right: 8px;
        }
        .calendar-nav {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .calendar-nav-btn {
            background: #f3f4f6;
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #2563eb;
            box-shadow: 0 1px 4px rgba(60, 72, 88, 0.08);
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .calendar-nav-btn:hover {
            background: #e0e7ff;
            color: #1d4ed8;
            box-shadow: 0 2px 8px rgba(60, 72, 88, 0.12);
        }
        .calendar-view-switcher {
            display: flex;
            gap: 12px;
        }
        .view-switch-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            border: 2px solid #3b82f6;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .view-switch-btn:hover {
            
            border-color: #3b82f6;
            color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        .view-switch-btn.active {
                background: linear-gradient(135deg, #3b82f6, #60a5fa);
                border: 2px solid #3b82f6;
        }
        .fc {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            font-size: 1rem;
        }
        .fc-daygrid-day {
            cursor: pointer;
            border-radius: 10px;
            transition: background 0.15s;
        }
        .fc-daygrid-day:hover {
            background: #f3f4f6;
        }
        .fc-day-today {
            background: #e0e7ff !important;
            border-radius: 10px;
        }
        #calendar .fc-daygrid-event {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }
        #calendar .fc-daygrid-event.status-completed,
        #calendar .fc-daygrid-event.status-pending,
        #calendar .fc-daygrid-event.status-cancelled,
        #calendar .fc-daygrid-event.status-rescheduled {
            background: transparent !important;
        }
        .fc-daygrid-event:hover {
            box-shadow: 0 4px 16px rgba(60, 72, 88, 0.18);
        }
        .fc-event-main {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .appointment-tooltip {
            position: fixed;
            background: #fff;
            border: none;
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 8px 32px rgba(60, 72, 88, 0.18);
            z-index: 1000;
            display: none;
            width: 270px;
            font-size: 1rem;
            color: #22223b;
            animation: fadeInTooltip 0.18s ease;
        }
        @keyframes fadeInTooltip {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .appointment-tooltip-content p {
            margin: 0 0 6px 0;
            font-size: 1rem;
        }
        .appointment-tooltip-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 10px;
        }
        .tooltip-btn {
            border-radius: 6px;
            font-size: 0.98rem;
            padding: 6px 14px;
            font-weight: 500;
            box-shadow: 0 1px 4px rgba(60, 72, 88, 0.08);
            transition: background 0.2s, color 0.2s;
        }
        .fc-scrollgrid, .fc-scrollgrid-section {
            border: none !important;
        }
        .fc-col-header-cell {
            background: none;
            font-weight: 700;
            color: #64748b;
            font-size: 1.05rem;
            border-bottom: 1.5px solid #e5e7eb !important;
            padding: 10px 0;
        }
        .fc-daygrid-day-number {
            font-weight: 600;
            color: #22223b;
            font-size: 1.08rem;
            margin-bottom: 2px;
        }
        .fc-day-other {
            opacity: 0.45;
        }
        /* Мобильная адаптация */
        @media (max-width: 900px) {
            .calendar-wrapper {
                padding: 10px 2px 10px 2px;
            }
            .calendar-title {
                font-size: 1.1rem;
            }
            .calendar-header {
                flex-direction: column;
                gap: 10px;
            }
        }
        @media (max-width: 600px) {
            .calendar-title {
                font-size: 1rem;
            }
            .calendar-nav-btn {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }
            .view-switch-btn {
                font-size: 0.95rem;
                padding: 6px 10px;
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
            
            color: #fff;
            
        }
    
        .today-button {
            background: #28a745;
            color: white !important;
            border-color: #28a745;
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
    
        /* --- Новый стиль для модального окна просмотра --- */
        .appointment-details-modal {
            padding: 24px 16px 0 16px;
            font-size: 1.05rem;
        }
        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }
        .client-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .client-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            color: #2196f3;
        }
        .client-name {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .status-block {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            
        }
        
        .details-row {
            display: flex;
            gap: 32px;
            margin-bottom: 18px;
        }
        .details-label {
            color: #888;
            font-weight: 500;
            margin-right: 4px;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            padding: 18px 20px;
            margin-bottom: 18px;
        }
        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #2196f3;
        }
        .procedure-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.08rem;
        }
        .procedure-price {
            font-weight: 600;
            color: #4CAF50;
            font-size: 1.1rem;
        }
        .sales-card .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #bbb;
            font-size: 1rem;
            padding: 18px 0 8px 0;
        }
        .sales-card .empty-state svg {
            width: 38px;
            height: 38px;
            margin-bottom: 8px;
            opacity: 0.5;
        }
        .details-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            border-top: 1px solid #eee;
            padding: 18px 0 0 0;
            margin-top: 10px;
            font-size: 1.1rem;
        }
        .details-footer span {
            margin-right: auto;
            font-weight: 600;
        }
        @media (max-width: 600px) {
            .details-header, .details-row, .details-footer { flex-direction: column; gap: 8px; align-items: flex-start; }
            .card { padding: 12px 8px; }
        }
        /* --- Пагинация как в Клиентах --- */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px;
            margin: 24px 0 0 0;
            flex-wrap: wrap;
            padding-bottom: 50px;
        }
        .page-btn {
            min-width: 36px;
            height: 36px;
            border: none;
            background: #f3f4f6;
            color: #2563eb;
            font-weight: 500;
            font-size: 16px;
            border-radius: 6px;
            margin: 0 2px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            outline: none;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-btn.active,
        .page-btn:hover:not(:disabled) {
            background: #2563eb;
            color: #fff;
        }
        .page-btn:disabled {
            background: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }
        .page-ellipsis {
            min-width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 18px;
            font-weight: 600;
            user-select: none;
        }
        @media (max-width: 600px) {
            .pagination {
                gap: 2px;
            }
            .page-btn, .page-ellipsis {
                min-width: 28px;
                height: 28px;
                font-size: 14px;
            }
        }

        /* Современная пагинация */
        
    </style>
    <style>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    color: #fff;
    font-size: 1rem;
    z-index: 1050;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 250px;
    text-align: center;
}
.notification.success {
    background: linear-gradient(135deg, #28a745, #34d399);
}
.notification.error {
    background: linear-gradient(135deg, #dc3545, #ef4444);
}
</style>
    <div class="appointments-header">
        <h1>Записи</h1>
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

    <div id="appointmentsList" style="{{ $viewType === 'calendar' ? 'display:none;' : '' }}">
        <div class="appointments-list table-wrapper">
            <table class="table-striped appointments-table" id="appointmentsTable">
                <thead>
                <tr>
                    <th>Дата и время</th>
                    <th>Клиент</th>
                    <th>Услуга</th>
                    <th>Мастер</th>
                    <th>Статус</th>
                    <th>Стоимость</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($appointments as $appointment)
                <tr data-appointment-id="{{ $appointment->id }}">
                    <td>
                        {{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}
                        <br>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</small>
                    </td>
                    <td>
                        {{ $appointment->client->name }}
                        @if($appointment->client->instagram)
                            <br />
                        <a href="https://instagram.com/{{ $appointment->client->instagram }}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                            <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $appointment->client->instagram }}
                        </a>
                        @endif
                    </td>
                    <td>{{ $appointment->service->name }}</td>
                    <td>{{ $appointment->user->name ?? 'Не назначен' }}</td>
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
                    <td class="currency-amount" data-amount="{{ $appointment->price }}">{{ \App\Helpers\CurrencyHelper::format($appointment->price) }}</td>
                    <td>
                        <div class="appointment-actions actions-cell">
                            <button class="btn-view" data-appointment-id="{{ $appointment->id }}" title="Просмотр">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <button class="btn-edit" data-appointment-id="{{ $appointment->id }}" title="Редактировать">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                            </button>
                            <button class="btn-delete" data-appointment-id="{{ $appointment->id }}"  title="Удалить">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination" id="appointmentsPagination" style="justify-content: center; margin-top: 20px;"></div>
        </div>
    </div>
    <div id="calendarView" style="{{ $viewType === 'list' ? 'display:none;' : '' }}">
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
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
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
                    <button class="calendar-nav-btn calendar-prev-button">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <span id="currentMonthYear" class="calendar-title">Декабрь 2023</span>
                    <button class="calendar-nav-btn calendar-next-button">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div id="calendar"></div>
        </div>
    </div>

    <script>

    let calendar; // Делаем переменную глобальной
    let activeEvent = null;
    const tooltip = document.getElementById('appointmentTooltip');

    document.addEventListener('DOMContentLoaded', function() {
        // Проверяем URL на наличие параметра для создания записи с дашборда
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'create' && urlParams.has('date')) {
            const date = urlParams.get('date');
            const modal = document.getElementById('appointmentModal');
            const form = document.getElementById('appointmentForm');
            
            if (modal && form) {
                // Сбрасываем форму
                form.reset();
                
                // Устанавливаем заголовок
                const title = modal.querySelector('.modal-title');
                if(title) title.textContent = 'Добавить запись';

                // Устанавливаем дату
                const dateInput = form.querySelector('input[name="date"]');
                if (dateInput) dateInput.value = date;

                // Открываем модальное окно
                toggleModal('appointmentModal', true);
            }
        }

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
                eventContent: function(info) {
                    const viewType = info.view.type;
                    if (viewType === 'timeGridDay' && info.event.extendedProps.title_day) {
                        return {
                            html: `<span style=\"color:#fff; font-weight:600; font-size:1.08em;\">${info.event.extendedProps.title_day}</span>`
                        };
                    }
                    if (viewType === 'timeGridWeek' && info.event.extendedProps.title_week) {
                        const parts = info.event.extendedProps.title_week.split(' ');
                        const time = parts.shift();
                        const name = parts.join(' ');
                        let status = 'completed';
                        if (info.event.classNames.includes('status-pending')) status = 'pending';
                        if (info.event.classNames.includes('status-cancelled')) status = 'cancelled';
                        if (info.event.classNames.includes('status-rescheduled')) status = 'rescheduled';
                        return {
                            html: `<span class=\"event-chip status-${status}\">\n<span class=\"event-dot status-${status}\"></span>\n<span class=\"event-time\">${time}</span>\n<span class=\"event-title\">${name}</span>\n</span>`
                        };
                    }
                    return true;
                },
                eventDidMount: function(info) {
                    if (info.view.type === 'timeGridWeek' || info.view.type === 'timeGridDay') {
                        // Убираем только тень и границу, оставляем заливку
                        info.el.style.boxShadow = 'none';
                        info.el.style.border = 'none';
                        
                        // Делаем текст белым более эффективно с максимальной специфичностью
                        info.el.style.setProperty('color', '#ffffff', 'important');
                        
                        // Применяем белый цвет ко всем дочерним элементам с максимальной специфичностью
                        const allElements = info.el.querySelectorAll('*');
                        allElements.forEach(el => {
                            el.style.setProperty('color', '#ffffff', 'important');
                        });
                        
                        // Дополнительно для заголовка и времени с максимальной специфичностью
                        const eventTitle = info.el.querySelector('.fc-event-title');
                        if (eventTitle) {
                            eventTitle.style.setProperty('color', '#ffffff', 'important');
                            eventTitle.style.setProperty('text-shadow', '0 1px 2px rgba(0,0,0,0.5)', 'important');
                        }
                        
                        const eventTime = info.el.querySelector('.fc-event-time');
                        if (eventTime) {
                            eventTime.style.setProperty('color', '#ffffff', 'important');
                            eventTime.style.setProperty('text-shadow', '0 1px 2px rgba(0,0,0,0.5)', 'important');
                        }
                        
                        // Применяем ко всем элементам событий
                        const eventElements = info.el.querySelectorAll('.fc-event-main, .fc-event-title, .fc-event-time');
                        eventElements.forEach(el => {
                            el.style.setProperty('color', '#ffffff', 'important');
                            el.style.setProperty('text-shadow', '0 1px 2px rgba(0,0,0,0.5)', 'important');
                        });
                    }
                },
                events: function(info, successCallback, failureCallback) {
                    fetch('/appointments/calendar-events')
                        .then(response => response.json())
                        .then(events => {
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
                slotMaxTime: '21:00:00',
                allDaySlot: false,
                slotDuration: '00:30:00',
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                slotEventOverlap: false,

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
                        <p><strong>Услуга:</strong> ${event.extendedProps.service}</p>
                        <p><strong>Цена:</strong> <span class="currency-amount" data-amount="${event.extendedProps.price}">${formatPrice(event.extendedProps.price)}</span></p>
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
                    },

                    eventDrop: function(info) {
                        // Перетаскивание события (записи)
                        const appointmentId = info.event.id;
                        const newDate = info.event.startStr.slice(0, 10);
                        const newTime = info.event.startStr.slice(11, 16); // HH:mm
                        fetch(`/appointments/${appointmentId}/move`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ date: newDate, time: newTime })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.showNotification('success', 'Дата и время записи и продаж успешно обновлены');
                                calendar.refetchEvents();
                            } else {
                                window.showNotification('error', data.message || 'Ошибка при переносе записи');
                                info.revert();
                            }
                        })
                        .catch(() => {
                            window.showNotification('error', 'Ошибка при переносе записи');
                            info.revert();
                        });
                    },
                    // Добавляем скрытие tooltip при начале drag&drop
                    eventDragStart: function(info) {
                        tooltip.style.display = 'none';
                    },
                });

                calendar.render();

                // Функция создания записи при клике на дату
                function createAppointment(dateStr) {
                    const modal = document.getElementById('appointmentModal');
                    const form = document.getElementById('appointmentForm');

                    // Сбрасываем форму и очищаем ошибки
                    form.reset();
                    clearErrors('appointmentForm');

                    const date = new Date(dateStr);

                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const dateString = `${year}-${month}-${day}`;

                    // Устанавливаем выбранную дату в поле формы
                    form.querySelector('input[name="date"]').value = dateString;

                    // Если в строке есть время (для timeGrid видов), устанавливаем его
                    if (dateStr.includes('T')) {
                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');
                        form.querySelector('input[name="time"]').value = `${hours}:${minutes}`;
                    } else {
                        form.querySelector('input[name="time"]').value = ''; // Очищаем время для month view
                    }

                    // Автоматически выбираем админа текущего проекта
                    const userSelect = form.querySelector('select[name="user_id"]');
                    if (userSelect) {
                        const adminUser = allUsers.find(u => u.role === 'admin' && u.project_id == currentProjectId);
                        if (adminUser) {
                            userSelect.value = adminUser.id;
                        }
                    }

                    // Открываем модальное окно
                    toggleModal('appointmentModal', true);
                }

                // Функция обновления заголовка
                function updateTitle() {
                    const monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                                    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
                    const date = calendar.getDate();
                    const view = calendar.view;
                    let title = '';

                    if (view.type === 'timeGridDay') {
                        // Для дневного вида показываем дату в формате "1 января 2024"
                        title = `${date.getDate()} ${monthNames[date.getMonth()].toLowerCase()} ${date.getFullYear()}`;
                    } else if (view.type === 'timeGridWeek') {
                        // Для недельного вида используем стандартный заголовок с диапазоном дат
                        title = view.title;
                    } else {
                        // Для месячного вида оставляем как есть
                        title = `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
                    }

                    document.getElementById('currentMonthYear').textContent = title;
                }

                // Обработчики кнопок навигации
                document.querySelector('.calendar-prev-button').addEventListener('click', function() {
                    calendar.prev();
                    updateTitle();
                });

                document.querySelector('.calendar-next-button').addEventListener('click', function() {
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

        </script>
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
                                       onfocus="searchClients(this)" autocomplete="off">
                                <input type="hidden" name="client_id" class="client-id-hidden" value="">
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
                            <label>Услуга *</label>
                            <select name="service_id" class="form-control" required>
                                <option value="">Выберите услугу</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}" 
                                        data-price="{{ $service->price }}"
                                        data-duration="{{ $service->duration }}">
                                    {{ $service->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Мастер/Сотрудник</label>
                            <select name="user_id" class="form-control">
                                <option value="">Не назначен</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Длительность</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="number" name="duration_hours" min="0" value="0" style="width: 60px;" class="form-control">
                                <span style="margin-right: 10px;">час.</span>
                                <input type="number" name="duration_minutes" min="0" max="59" value="0" style="width: 60px;" class="form-control">
                                <span>мин.</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Стоимость</label>
                            <input type="number" step="0.01" name="price" class="form-control" min="0">
                        </div>
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
        let allUsers = @json($users);
        let currentProjectId = {{ auth()->user()->project_id }};
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

        function showNotification(type, message) {
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
            temporaryProducts = [...sales];
            const servicePrice = parseFloat(appointment.price) || 0;
            const productsTotal = temporaryProducts.reduce((sum, sale) => {
                const price = parseFloat(sale.price || 0);
                return sum + (parseInt(sale.quantity) * price);
            }, 0);
            const totalAmount = servicePrice + productsTotal;
            const statusNames = {
                'pending': 'Ожидается',
                'completed': 'Завершено',
                'cancelled': 'Отменено',
                'rescheduled': 'Перенесено'
            };
            // Добавляем скрытое поле с client_id
            modalBody.innerHTML = `
    <input type="hidden" id="appointmentId" value="${appointment.id}">
    <input type="hidden" id="clientId" value="${appointment.client_id}">
    <input type="hidden" name="date" value="${appointment.date}">
    <input type="hidden" name="time" value="${appointment.time}">
    <div class="appointment-details-modal">
        <div class="details-header">
            <div class="client-info">
                <div class="client-avatar">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32"><path d="M12 12c2.7 0 4.5-1.8 4.5-4.5S14.7 3 12 3 7.5 4.8 7.5 7.5 9.3 12 12 12zm0 2c-3 0-9 1.5-9 4.5V21h18v-2.5c0-3-6-4.5-9-4.5z"/></svg>
                </div>
                <div>
                    <div class="client-name">${escapeHtml(appointment.client.name)}</div>
                    ${appointment.client.instagram ? `<a href="https://instagram.com/${escapeHtml(appointment.client.instagram)}" target="_blank" style="color:#2196f3;">@${escapeHtml(appointment.client.instagram)}</a>` : ''}
                </div>
            </div>
            <div class="status-block status-${appointment.status}">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20"><circle cx="9" cy="9" r="8"/></svg>
                <span>${statusNames[appointment.status] || 'Ожидается'}</span>
            </div>
        </div>
        <div class="details-row">
            <div><span class="details-label">Дата:</span> ${new Date(appointment.date).toLocaleDateString('ru-RU')}</div>
            <div><span class="details-label">Время:</span> ${escapeHtml(appointment.time.split(':').slice(0, 2).join(':'))}</div>
        </div>
        <div class="details-row">
            <div><span class="details-label">Мастер:</span> ${appointment.user ? escapeHtml(appointment.user.name) : 'Не назначен'}</div>
        </div>
        <div class="card procedure-card">
            <div class="card-title">Услуга</div>
            <div class="procedure-info">
                <span class="service-name">${escapeHtml(appointment.service.name)}</span>
                <span class="procedure-price currency-amount" data-amount="${servicePrice}">${formatPrice(servicePrice)}</span>
            </div>
        </div>
        <div class="card sales-card">
            <div class="card-title">Продажи</div>
            <div class="products-section">
                ${temporaryProducts.length === 0 ? `<div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14.5h-2v-2h2v2zm0-4h-2V7h2v5.5z"/></svg>
                    <div>Товары не добавлены</div>
                </div>` : renderProductsList(temporaryProducts)}
                <button class="btn-add-appointment btn-add-product" id="showAddProductFormBtn">
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
                                       onfocus="showProductDropdown(this)" autocomplete="off">
                                <div class="product-dropdown" style="display: none;">
                                    <div class="product-dropdown-list"></div>
                                </div>
                                <input type="hidden" id="selectedProductId" name="product_id">
                            </div>
                        </div>
                    </div>
                    <div id="productDetails" class="form-row-appointment" style="display: none; margin-top: 15px;">
                        <div class="form-group-appointment" style="display: none;">
                            <label>Количество *</label>
                            <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="form-group-appointment" style="display: none;">
                            <label>Опт</label>
                            <input type="number" step="0.01" id="productWholesale" class="form-control" readonly style="background-color: #f0f0f0;">
                        </div>
                        <div class="form-group-appointment" style="display: none;">
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
        </div>
        <div class="details-footer">
            <span>Итого: <b class="currency-amount" data-amount="${totalAmount}">${formatPrice(totalAmount)}</b></span>
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
                                <td class="currency-amount" data-amount="${sale.price}">${formatPrice(sale.price)}</td>
                                <td class="currency-amount" data-amount="${sale.purchase_price}">${formatPrice(sale.purchase_price)}</td>
                                <td class="currency-amount" data-amount="${total}">${formatPrice(total)}</td>
                                <td>
                                    <button class="btn-delete btn-delete-product"
                                            data-product-id="${sale.product_id}"
                                            title="Удалить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
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
            const selectedServiceId = prompt("Введите ID новой услуги:");
            const newPrice = prompt("Введите цену для новой услуги:");

            if (!selectedServiceId || !newPrice) {
                alert("Услуга и цена обязательны");
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
                alert("Услуга добавлена как новая запись");
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
                        resetProductForm();
                        
                        // Включаем кнопку "Добавить" в форме
                        const submitBtn = modal.querySelector('#submitAddProduct');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                        }
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

                // Скрываем все остальные поля формы
                const otherFields = form.querySelectorAll('.form-group-appointment');
                otherFields.forEach(field => {
                    field.style.display = 'none';
                });
            }
        }

        // Функции для работы с товарами
        function addProductToAppointment() {
            const modal = document.getElementById('viewAppointmentModal');
            if (!modal) {
                window.showNotification('error', 'Модальное окно не найдено');
                return;
            }



            const productId = modal.querySelector('#selectedProductId')?.value;
            const quantity = modal.querySelector('#productQuantity')?.value;
            const price = modal.querySelector('#productPrice')?.value;
            const productName = modal.querySelector('#productSearchInput')?.value;

            // Проверки
            if (!productId || productId === '' || !productName || productName === '') {
                window.showNotification('error', 'Пожалуйста, начните вводить название товара и выберите его из появившегося списка');
                return;
            }

            if (!quantity || parseInt(quantity) <= 0) {
                window.showNotification('error', 'Укажите корректное количество');
                return;
            }

            if (!price || parseFloat(price) <= 0) {
                window.showNotification('error', 'Укажите корректную цену');
                return;
            }

            // Находим товар в доступных товарах
            let product = allProducts.find(p => p.id == productId);
            if (!product) {
                window.showNotification('error', 'Выбранный товар не найден в списке доступных товаров');
                return;
            }

            // Проверяем, есть ли уже такой товар в списке
            const existingProductIndex = temporaryProducts.findIndex(p => p.product_id == productId);
            
            if (existingProductIndex !== -1) {
                // Если товар уже есть, обновляем количество
                temporaryProducts[existingProductIndex].quantity += parseInt(quantity);
            } else {
                // Если товара нет, добавляем новый
                temporaryProducts.push({
                    product_id: productId,
                    name: product.name,
                    price: parseFloat(price),
                    purchase_price: product.purchase_price || 0,
                    quantity: parseInt(quantity)
                });
            }

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

            // Отключаем кнопку "Добавить" в форме
            const submitBtn = modal.querySelector('#submitAddProduct');
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            if (existingProductIndex !== -1) {
                window.showNotification('success', `Количество товара "${product.name}" обновлено`);
            } else {
                window.showNotification('success', `Товар "${product.name}" успешно добавлен`);
            }
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
                                   onfocus="showProductDropdown(this)" autocomplete="off">
                            <div class="product-dropdown" style="display: none;">
                                <div class="product-dropdown-list"></div>
                            </div>
                            <input type="hidden" id="selectedProductId" name="product_id">
                        </div>
                    </div>
                    <div id="productDetails" class="form-row-appointment" style="display: flex; margin-top: 15px;">
                        <div class="form-group-appointment" style="display: none;">
                        <label>Количество *</label>
                        <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
                    </div>
                        <div class="form-group-appointment" style="display: none;">
                        <label>Опт</label>
                        <input type="number" step="0.01" id="productWholesale" class="form-control" readonly style="background-color: #f0f0f0;">
                    </div>
                        <div class="form-group-appointment" style="display: none;">
                        <label>Цена *</label>
                        <input type="number" step="0.01" id="productPrice" class="form-control" required>
                        </div>
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

            updateTotalAmount();
        }

        function renderAppointmentsTable(appointments) {
            const tbody = document.querySelector('#appointmentsTable tbody');
            if (!tbody) return;
            tbody.innerHTML = renderAppointmentsList(appointments);
        }
        function renderAppointmentsList(appointments) {
            return appointments.map(appointment => `
                <tr data-appointment-id="${appointment.id}">
                    <td>
                        ${formatDate(appointment.date)}
                        <br>
                        <small class="text-muted">${appointment.time ? appointment.time.slice(0,5) : ''}</small>
                    </td>
                    <td>
                        ${appointment.client ? appointment.client.name : 'Клиент удален'}
                        ${appointment.client && appointment.client.instagram ? `
                            <br>
                            <a href="https://instagram.com/${appointment.client.instagram}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                                <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;width:16px;height:16px;">
                                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                                </svg>
                                ${appointment.client.instagram}
                            </a>
                        ` : ''}
                    </td>
                    <td>${appointment.service ? appointment.service.name : 'Услуга удалена'}</td>
                    <td>${appointment.user ? appointment.user.name : 'Не назначен'}</td>
                    <td><span class="status-badge status-${appointment.status}">${getStatusName(appointment.status)}</span></td>
                    <td class="currency-amount" data-amount="${appointment.price}">${formatPrice(appointment.price)}</td>
                    <td>
                        <div class="appointment-actions actions-cell">
                            <button class="btn-view" data-appointment-id="${appointment.id}" title="Просмотр">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <button class="btn-edit" data-appointment-id="${appointment.id}" title="Редактировать">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                            </button>
                            <button class="btn-delete" data-appointment-id="${appointment.id}"  title="Удалить">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }


        document.querySelector('.modal-footer')?.appendChild(document.getElementById('saveAppointmentChanges'));

        function updateTotalAmount() {
            const modal = document.getElementById('viewAppointmentModal');
            if (!modal) return;

            // Получаем цену услуги
            const priceElement = modal.querySelector('.procedure-price');
            const servicePrice = parseFloat(priceElement?.getAttribute('data-amount')) || 0;

            // Сумма товаров
            const productsTotal = temporaryProducts.reduce((sum, product) => {
                return sum + (parseInt(product.quantity) * parseFloat(product.price));
            }, 0);

            const totalAmount = servicePrice + productsTotal;

            // Обновляем итог в футере
            const totalElement = modal.querySelector('.details-footer span b');
            if (totalElement) {
                totalElement.className = 'currency-amount';
                totalElement.setAttribute('data-amount', totalAmount);
                totalElement.textContent = formatPrice(totalAmount);
            }
        }



        // Функции для поиска товаров
        function searchProducts(inputElement) {
            const searchTerm = inputElement.value.trim().toLowerCase();

            const dropdown = inputElement.nextElementSibling;
            const dropdownList = dropdown.querySelector('.product-dropdown-list');

            if (searchTerm.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            // Получаем ID уже добавленных товаров (приводим к числам для корректного сравнения)
            const addedProductIds = temporaryProducts.map(p => parseInt(p.product_id));
            
            const filteredProducts = allProducts.filter(product => {
                const nameMatch = product.name?.toLowerCase().includes(searchTerm) || false;
                const notAlreadyAdded = !addedProductIds.includes(parseInt(product.id));

                return nameMatch && notAlreadyAdded;
            }).slice(0, 5);
            
            if (filteredProducts.length > 0) {
                const dropdownHTML = filteredProducts.map(product => {
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
                
                dropdownList.innerHTML = dropdownHTML;
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

            showProductFields();
        }

        function formatPrice(price) {
            if (window.CurrencyManager) {
                return window.CurrencyManager.formatAmount(price);
            } else {
                const parsedPrice = parseFloat(price);
                if (isNaN(parsedPrice)) return price;
                return (Number.isInteger(parsedPrice) ? parsedPrice.toString() : parsedPrice.toFixed(2)) + ' грн';
            }
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
                                    ${escapeHtml(p.name)} (${formatPrice(retailPrice)}, остаток: ${quantity})
                            </option>
                        `;
            }).join('')}
                    </select>
                </div>`;
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
                window.showNotification('success', 'Изменения сохранены');
                closeViewAppointmentModal();
                // При необходимости обновите данные на странице
            });

        }


        async function deleteProductFromAppointment() {
            try {
                const appointmentId = document.getElementById('appointmentId')?.value;
                if (!appointmentId) {
                    window.showNotification('error', 'Не удалось определить запись');
                    return;
                }

                // Если это временный товар (еще не сохраненный)
                if (currentDeleteIndex !== null) {
                    temporaryProducts.splice(currentDeleteIndex, 1);
                    updateProductsList();
                    updateTotalAmount();
                    window.showNotification('success', 'Товар удален');
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
                    window.showNotification('success', 'Товар успешно удален');
                }
            } catch (err) {
                console.error(err);
                window.showNotification('error', err.message || 'Ошибка при удалении товара');
            } finally {
                currentDeleteProductId = null;
                currentDeleteIndex = null;
            }
        }

        // Функции для работы с клиентами
        function searchClients(inputElement) {
            const searchTerm = inputElement.value.trim().toLowerCase();
            const container = inputElement.closest('.client-search-container');
            if (!container) {
                console.error('Client search container not found');
                return;
            }
            const dropdown = container.querySelector('.client-dropdown');
            const dropdownList = container.querySelector('.client-dropdown-list');
            if (!dropdown || !dropdownList) {
                console.error('Client dropdown elements not found');
                return;
            }
            if (searchTerm.length === 0) {
                dropdown.style.display = 'none';
                return;
            }
            if (!Array.isArray(allClients)) {
                console.error('allClients is not an array:', allClients);
                return;
            }
            const filteredClients = allClients.filter(client => {
                const nameMatch = client.name?.toLowerCase().includes(searchTerm) || false;
                const instagramMatch = client.instagram?.toLowerCase().includes(searchTerm) || false;
                const emailMatch = client.email?.toLowerCase().includes(searchTerm) || false;
                const phoneMatch = client.phone?.toString().includes(searchTerm) || false;
                return nameMatch || instagramMatch || emailMatch || phoneMatch;
            }).slice(0, 5);
            if (filteredClients.length > 0) {
                dropdownList.innerHTML = filteredClients.map(client => {
                    const name = escapeHtml(client.name || '');
                    const instagram = client.instagram ? `(@${escapeHtml(client.instagram)})` : '';
                    const phone = client.phone ? ` - ${escapeHtml(client.phone.toString())}` : '';
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
            if (!container) return;
            const input = container.querySelector('.client-search-input');
            const select = container.querySelector('.client-select');
            const dropdown = container.querySelector('.client-dropdown');
            const hiddenInput = container.querySelector('.client-id-hidden');
            if (input) input.value = clientName.trim();
            if (select) select.value = clientId;
            if (hiddenInput) hiddenInput.value = clientId;
            if (dropdown) dropdown.style.display = 'none';
        }

        // Функции для работы с записями
        async function editAppointment(id) {
            if (!id) {
                window.showNotification('error', 'Ошибка: ID записи не указан');
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
                    allUsers = data.users; // Убедитесь, что allUsers обновляется
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
                            <div class="client-search-container">
                                <input type="text" class="client-search-input form-control"
                                       placeholder="Начните вводить имя, инстаграм или email клиента..."
                                       value="${escapeHtml(getClientDisplayName(appointment.client_id))}"
                                       oninput="searchClients(this)"
                                       onfocus="searchClients(this)" autocomplete="off">
                                <input type="hidden" name="client_id" class="client-id-hidden" value="${appointment.client_id}">
                                <div class="client-dropdown" style="display: none;">
                                    <div class="client-dropdown-list"></div>
                                </div>
                                <select name="client_id" class="form-control client-select" style="display: none;" required>
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
                        </div>
                        <div class="form-group">
                            <label>Услуга *</label>
                            <select name="service_id" class="form-control" required>
                                <option value="">Выберите услугу</option>
                                ${allServices.map(service => `
                                    <option value="${service.id}"
                                            data-price="${service.price}"
                                            data-duration="${service.duration}"
                                            ${service.id == appointment.service_id ? 'selected' : ''}>
                                        ${escapeHtml(service.name)}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Мастер/Сотрудник</label>
                            <select name="user_id" class="form-control">
                                <option value="">Не назначен</option>
                                ${allUsers.map(user => `
                                    <option value="${user.id}" ${appointment.user_id == user.id ? 'selected' : ''}>
                                        ${escapeHtml(user.name)}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Длительность</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="number" name="duration_hours" min="0" value="${appointment.duration_hours || 0}" style="width: 60px;" class="form-control">
                                <span style="margin-right: 10px;">час.</span>
                                <input type="number" name="duration_minutes" min="0" max="59" value="${appointment.duration_minutes || 0}" style="width: 60px;" class="form-control">
                                <span>мин.</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Стоимость</label>
                            <input type="number" step="0.01" name="price" value="${Number(appointment.price) % 1 === 0 ? Number(appointment.price) : Number(appointment.price).toFixed(2)}" class="form-control" min="0">
                        </div>
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
            const editServiceSelect = document.querySelector('#editAppointmentForm [name="service_id"]');
            if(editServiceSelect) {
                editServiceSelect.addEventListener('change', () => handleServiceChange(editServiceSelect));
            }

            // Обработчик формы редактирования
            document.getElementById('editAppointmentForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();
                await submitEditAppointmentForm(this, currentAppointmentId);
            });
        }

        function getClientDisplayName(clientId) {
            const client = allClients.find(c => c.id == clientId);
            if (!client) return '';
            let str = client.name || '';
            if (client.instagram) str += ` (@${client.instagram})`;
            if (client.phone) str += ` - ${client.phone}`;
            return str;
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

                    window.showNotification('success', 'Запись успешно удалена');
                } else {
                    throw new Error(data.message || 'Ошибка при удалении записи');
                }
            } catch (error) {
                console.error('Error:', error);
                window.showNotification('error', error.message || 'Ошибка при удалении записи');
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
                const modal = document.getElementById('appointmentModal');
                const form = modal.querySelector('#appointmentForm');
                
                // Сбрасываем форму и очищаем ошибки
                form.reset();
                clearErrors('appointmentForm');

                // Устанавливаем сегодняшнюю дату
                const today = new Date().toISOString().split('T')[0];
                form.querySelector('input[name="date"]').value = today;

                // Автоматически выбираем админа текущего проекта
                const userSelect = form.querySelector('select[name="user_id"]');
                if (userSelect) {
                    const adminUser = allUsers.find(u => u.role === 'admin' && u.project_id == currentProjectId);
                    if (adminUser) {
                        userSelect.value = adminUser.id;
                    }
                }
                
                // Открываем модальное окно
                toggleModal('appointmentModal', true);
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
                    document.querySelectorAll('.btn-view-switch').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const view = this.dataset.view;
                    if (view === 'list') {
                        document.getElementById('appointmentsList').style.display = '';
                        document.getElementById('calendarView').style.display = 'none';
                    } else if (view === 'calendar') {
                        document.getElementById('appointmentsList').style.display = 'none';
                        document.getElementById('calendarView').style.display = '';
                        if (typeof calendar !== 'undefined' && calendar) {
                            setTimeout(() => calendar.updateSize(), 100);
                        }
                    }
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

            // Обработчики для поиска клиентов
            document.querySelectorAll('.client-search-input').forEach(input => {
                input.addEventListener('input', function() {
                    searchClients(this);
                });
                input.addEventListener('focus', function() {
                    if (this.value.trim().length > 0) {
                        searchClients(this);
                    }
                });
            });
            // Закрытие выпадающего списка при клике вне его
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.client-search-container')) {
                    document.querySelectorAll('.client-dropdown').forEach(dropdown => {
                        dropdown.style.display = 'none';
                    });
                }
            });
        });
        async function saveAppointmentChanges() {
            const modal = document.getElementById('viewAppointmentModal');
            const appointmentId = modal.querySelector('#appointmentId')?.value;
            const clientId = modal.querySelector('#clientId')?.value;

            if (!clientId) {
                window.showNotification('error', 'Не удалось определить клиента');
                return;
            }

            const serviceElement = modal.querySelector('.service-name');
            const serviceName = serviceElement?.textContent?.trim();
            const service = serviceName ? allServices.find(s => s.name.trim() === serviceName) : null;
            const priceElement = modal.querySelector('.procedure-price');
            const price = parseFloat(priceElement?.getAttribute('data-amount')) || 0;

            const date = modal.querySelector('input[name="date"]')?.value || '';
            const time = modal.querySelector('input[name="time"]')?.value || '';

            const requestData = {
                client_id: clientId,
                service_id: service?.id || '',
                price: price,
                date: date,
                time: time,
                sales: temporaryProducts.map(p => ({
                    product_id: p.product_id,
                    quantity: parseInt(p.quantity),
                    price: parseFloat(p.price),
                    purchase_price: parseFloat(p.purchase_price || 0)
                }))
            };

            // Валидация на клиенте
            if (!requestData.service_id) {
                window.showNotification('error', 'Необходимо указать услугу');
                return;
            }



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
                    // Показываем ошибки валидации сервера, если есть
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        window.showNotification('error', errorMessages);
                        return;
                    }
                    throw new Error(data.message || 'Ошибка сохранения');
                }

                if (data.success) {
                    window.showNotification('success', 'Изменения успешно сохранены');
                    toggleModal('viewAppointmentModal', false);
                    if (typeof calendar !== 'undefined' && calendar) {
                        calendar.refetchEvents();
                    }
                } else {
                    throw new Error(data.message || 'Ошибка сохранения');
                }
            } catch (error) {
                console.error('Error:', error);
                window.showNotification('error', error.message || 'Ошибка при сохранении');
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
            const dropdown = input.nextElementSibling;
            const dropdownList = dropdown.querySelector('.product-dropdown-list');

            if (input.value.length > 0) {
                searchProducts(input);
            } else {
                // Получаем ID уже добавленных товаров (приводим к числам для корректного сравнения)
                const addedProductIds = temporaryProducts.map(p => parseInt(p.product_id));
                
                // Показываем первые 5 товаров, исключая уже добавленные
                const availableProducts = allProducts.filter(product => {
                    return !addedProductIds.includes(parseInt(product.id));
                }).slice(0, 5);

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
                                    <td class="currency-amount" data-amount="${retailPrice}">${formatPrice(retailPrice)}</td>
                                    <td class="currency-amount" data-amount="${wholesalePrice}">${formatPrice(wholesalePrice)}</td>
                                    <td class="currency-amount" data-amount="${total}">${formatPrice(total)}</td>
                                    <td>
                                        <button class="btn-delete btn-delete-product" onclick="deleteProduct(${index})" data-product-id="${product.product_id}" title="Удалить">
                                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
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
                                           onfocus="showProductDropdown(this)" autocomplete="off">
                                    <div class="product-dropdown" style="display: none;">
                                        <div class="product-dropdown-list"></div>
                                    </div>
                                    <input type="hidden" id="selectedProductId" name="product_id">
                                </div>
                            </div>
                            <div class="form-group-appointment" style="display: none;">
                                <label>Количество *</label>
                                <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="form-group-appointment" style="display: none;">
                                <label>Опт</label>
                                <input type="number" step="0.01" id="productWholesale" class="form-control" readonly style="background-color: #f0f0f0;">
                            </div>
                            <div class="form-group-appointment" style="display: none;">
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
                    resetProductForm(); // <-- добавьте эту строку!
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


        }

        // Добавляем проверку состояния формы после каждого важного действия
        const originalResetProductForm = resetProductForm;
        resetProductForm = function() {
            originalResetProductForm();
            checkFormState();
        };

        const originalSelectProduct = selectProduct;
        selectProduct = function(...args) {
            originalSelectProduct.apply(this, args);
            checkFormState();
        };

        const originalAddProductToAppointment = addProductToAppointment;
        addProductToAppointment = function() {
            checkFormState();
            originalAddProductToAppointment();
            checkFormState();
        };


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
                    window.showNotification('success', 'Запись успешно создана');
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
                            <td>
                                ${new Date(appointment.date).toLocaleDateString('ru-RU')}
                                <br>
                                <small class="text-muted">${escapeHtml(appointment.time.split(':').slice(0, 2).join(':'))}</small>
                            </td>
                            <td>
                                ${escapeHtml(appointment.client.name)}
                                ${appointment.client.instagram ? `
                                    (<a href="https://instagram.com/${escapeHtml(appointment.client.instagram)}" class="instagram-link" target="_blank" rel="noopener noreferrer">@${escapeHtml(appointment.client.instagram)}</a>)` : ''}
                            </td>
                            <td>${escapeHtml(appointment.service.name)}</td>
                            <td>${appointment.user ? escapeHtml(appointment.user.name) : 'Не назначен'}</td>
                            <td><span class="status-badge status-${appointment.status}">${getStatusName(appointment.status)}</span></td>
                            <td class="currency-amount" data-amount="${appointment.price}">${formatPrice(appointment.price)}</td>
                            <td>
                                <div class="appointment-actions actions-cell">
                                    <button class="btn-view" data-appointment-id="${data.appointment.id}" title="Просмотр">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <button class="btn-edit" data-appointment-id="${data.appointment.id}" title="Редактировать">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </button>
                                    <button class="btn-delete" data-appointment-id="${data.appointment.id}" title="Удалить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
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
                window.showNotification('error', error.message || 'Ошибка при создании записи');
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
                    window.showNotification('success', 'Запись успешно обновлена');
                    closeEditAppointmentModal();

                    // Обновляем календарь
                    if (typeof calendar !== 'undefined' && calendar) {
                        calendar.refetchEvents();
                    }

                    // Обновляем строку в таблице
                    const row = document.querySelector(`tr[data-appointment-id="${data.appointment.id}"]`);
                    if (row) {
                        row.innerHTML = `
                            <td>
                                ${new Date(data.appointment.date).toLocaleDateString('ru-RU')}
                                <br>
                                <small class="text-muted">${escapeHtml(data.appointment.time.split(':').slice(0, 2).join(':'))}</small>
                            </td>
                            <td>
                                ${escapeHtml(data.appointment.client.name)}
                                ${data.appointment.client.instagram ? `
                                    (<a href="https://instagram.com/${escapeHtml(data.appointment.client.instagram)}" class="instagram-link" target="_blank" rel="noopener noreferrer">@${escapeHtml(data.appointment.client.instagram)}</a>)` : ''}
                            </td>
                            <td>${escapeHtml(data.appointment.service.name)}</td>
                            <td>${data.appointment.user ? escapeHtml(data.appointment.user.name) : 'Не назначен'}</td>
                            <td><span class="status-badge status-${data.appointment.status}">${getStatusName(data.appointment.status)}</span></td>
                            <td class="currency-amount" data-amount="${data.appointment.price}">${formatPrice(data.appointment.price)}</td>
                            <td>
                                <div class="appointment-actions actions-cell">
                                    <button class="btn-view" data-appointment-id="${data.appointment.id}" title="Просмотр">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <button class="btn-edit" data-appointment-id="${data.appointment.id}" title="Редактировать">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </button>
                                    <button class="btn-delete" data-appointment-id="${data.appointment.id}" title="Удалить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
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
                window.showNotification('error', error.message || 'Ошибка при обновлении записи');
            }
        }

        function renderProductsTable() {
            return `
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Количество</th>
                            <th>Розничная цена</th>
                            <th>Оптовая цена</th>
                            <th>Итого</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${temporaryProducts.map((product, index) => {
                            const retailPrice = parseFloat(product.price);
                            const wholesalePrice = parseFloat(product.wholesale_price || 0);
                            const quantity = parseInt(product.quantity);
                            const total = retailPrice * quantity;

                            return `
                                <tr data-index="${index}">
                                    <td>${product.name}</td>
                                    <td>${quantity}</td>
                                    <td class="currency-amount" data-amount="${retailPrice}">${formatPrice(retailPrice)}</td>
                                    <td class="currency-amount" data-amount="${wholesalePrice}">${formatPrice(wholesalePrice)}</td>
                                    <td class="currency-amount" data-amount="${total}">${formatPrice(total)}</td>
                                    <td>
                                        <button class="btn-delete btn-delete-product" onclick="deleteProduct(${index})" data-product-id="${product.product_id}" title="Удалить">
                                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>`;
        }
        

        function updateProductDetails(productId) {
            const product = allProducts.find(p => p.id == productId);
            if (product) {
                document.getElementById('productNameDisplay').value = product.name;
                document.getElementById('productWholesalePrice').value = formatPrice(product.wholesale_price || 0);
                document.getElementById('productRetailPrice').value = formatPrice(product.retail_price || product.price || 0);
                document.getElementById('productQuantity').max = product.quantity || 0;
            }
        }

        function showProductFields() {
            const form = document.querySelector('#addProductForm');
            if (form) {
                const otherFields = form.querySelectorAll('.form-group-appointment');
                otherFields.forEach(field => {
                    field.style.display = 'block';
                });
            }
        }

        // Пагинация для списка записей
        

      

        function renderAppointmentsPagination(meta) {
            let paginationHtml = '';
            if (meta.last_page > 1) {
                paginationHtml += '<div class="pagination">';
                paginationHtml += `<button class="page-btn" data-page="${meta.current_page - 1}" ${meta.current_page === 1 ? 'disabled' : ''}>&lt;<\/button>`;
                let pages = [];
                if (meta.last_page <= 7) {
                    for (let i = 1; i <= meta.last_page; i++) pages.push(i);
                } else {
                    pages.push(1);
                    if (meta.current_page > 4) pages.push('...');
                    let start = Math.max(2, meta.current_page - 2);
                    let end = Math.min(meta.last_page - 1, meta.current_page + 2);
                    for (let i = start; i <= end; i++) pages.push(i);
                    if (meta.current_page < meta.last_page - 3) pages.push('...');
                    pages.push(meta.last_page);
                }
                pages.forEach(p => {
                    if (p === '...') {
                        paginationHtml += `<span class="page-ellipsis">...<\/span>`;
                    } else {
                        paginationHtml += `<button class="page-btn${p === meta.current_page ? ' active' : ''}" data-page="${p}">${p}<\/button>`;
                    }
                });
                paginationHtml += `<button class="page-btn" data-page="${meta.current_page + 1}" ${meta.current_page === meta.last_page ? 'disabled' : ''}>&gt;<\/button>`;
                paginationHtml += '<\/div>';
            }
            let pagContainer = document.getElementById('appointmentsPagination');
            if (pagContainer) pagContainer.innerHTML = paginationHtml;
            document.querySelectorAll('#appointmentsPagination .page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (!isNaN(page) && !this.disabled) {
                        loadAppointments(page);
                    }
                });
            });
        }

        function loadAppointments(page = 1) {
            fetch(`/appointments/ajax?page=${page}`)
                .then(res => res.json())
                .then(response => {
                    renderAppointmentsTable(response.data); // обновляет строки таблицы
                    renderAppointmentsPagination(response.meta); // обновляет пагинацию!
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
    loadAppointments(1);
});

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString('ru-RU');
        }

        window.showNotification = function(type, message) {
            let notification = document.getElementById('notification');
            if (!notification) {
                notification = document.createElement('div');
                notification.id = 'notification';
                document.body.appendChild(notification);
            }
            notification.className = `notification ${type} show shake`;
            const icon = type === 'success'
                ? '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>'
                : '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';
            notification.innerHTML = `
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    ${icon}
                </svg>
                <span class="notification-message">${message}</span>
            `;
            notification.addEventListener('animationend', function handler() {
                notification.classList.remove('shake');
                notification.removeEventListener('animationend', handler);
            });
            setTimeout(() => {
                notification.className = `notification ${type}`;
            }, 3000);
        };

        // Функция для открытия модального окна добавления записи
        function openAddAppointmentModal() {
            const modal = document.getElementById('addAppointmentModal');
            const modalBody = modal.querySelector('.modal-body');
            
            // Очищаем форму
            const form = modalBody.querySelector('form');
            form.reset();
            
            // Автоматически выбираем админа текущего проекта
            const userSelect = form.querySelector('select[name="user_id"]');
            if (userSelect) {
                // Находим пользователя с ролью admin из текущего проекта
                const adminUser = allUsers.find(u => u.role === 'admin' && u.project_id === currentProjectId);
                if (adminUser) {
                    userSelect.value = adminUser.id.toString();
                }
            }
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Функция для обработки изменения услуги
        function handleServiceChange(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const form = selectElement.closest('form');
            
            if (selectedOption) {
                const price = selectedOption.dataset.price;
                const duration = parseInt(selectedOption.dataset.duration || 0);
                
                const priceInput = form.querySelector('[name="price"]');
                if (priceInput) {
                    priceInput.value = price || '';
                }
                
                const hoursInput = form.querySelector('[name="duration_hours"]');
                const minutesInput = form.querySelector('[name="duration_minutes"]');
                
                if (hoursInput && minutesInput) {
                    hoursInput.value = Math.floor(duration / 60);
                    minutesInput.value = duration % 60;
                }
            }
        }
    
        document.addEventListener('DOMContentLoaded', function() {
            // Привязываем обработчик к модальному окну добавления
            const addServiceSelect = document.querySelector('#appointmentForm [name="service_id"]');
            if(addServiceSelect) {
                addServiceSelect.addEventListener('change', () => handleServiceChange(addServiceSelect));
            }
        });

        // --- Функция для обновления графиков сотрудников ---
        function updateEmployeesAnalytics(params = '') {
            // 1. Топ-5 сотрудников по объему продаж
            fetch('/analytics/top-employees-by-sales' + (params ? '?' + params : ''))
                .then(r => r.json())
                .then(data => {
                    const ctx = document.getElementById('topEmployeesBar').getContext('2d');
                    if (charts.topEmployeesBar) charts.topEmployeesBar.destroy();
                    charts.topEmployeesBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Сумма продаж',
                                data: data.data,
                                backgroundColor: '#6366f1'
                            }]
                        },
                        options: {scales: {y: {beginAtZero: true}}}
                    });
                });

            // 2. Структура продаж по сотрудникам
            fetch('/analytics/employees-sales-structure' + (params ? '?' + params : ''))
                .then(r => r.json())
                .then(data => {
                    const ctx = document.getElementById('employeesStructurePie').getContext('2d');
                    if (charts.employeesStructurePie) charts.employeesStructurePie.destroy();
                    charts.employeesStructurePie = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: [
                                    '#2563eb','#10b981','#f59e42','#7c3aed','#ef4444','#0ea5e9'
                                ]
                            }]
                        }
                    });
                });

            // 3. Динамика продаж по сотрудникам
            fetch('/analytics/employees-sales-dynamics' + (params ? '?' + params : ''))
                .then(r => r.json())
                .then(data => {
                    const ctx = document.getElementById('employeesDynamicsChart').getContext('2d');
                    if (charts.employeesDynamicsChart) charts.employeesDynamicsChart.destroy();
                    const datasets = (data.datasets || []).map((ds, i) => ({
                        label: ds.label,
                        data: ds.data,
                        borderColor: ['#2563eb','#10b981','#f59e42','#7c3aed','#ef4444','#0ea5e9'][i % 6],
                        backgroundColor: 'rgba(0,0,0,0)',
                        fill: false,
                        tension: 0.4
                    }));
                    charts.employeesDynamicsChart = new Chart(ctx, {
                        type: 'line',
                        data: {labels: data.labels, datasets},
                        options: {scales: {y: {beginAtZero: true}}}
                    });
                });

            // 4. Средняя сумма продажи по сотрудникам
            fetch('/analytics/employees-average-sale' + (params ? '?' + params : ''))
                .then(r => r.json())
                .then(data => {
                    const ctx = document.getElementById('employeesAverageBar').getContext('2d');
                    if (charts.employeesAverageBar) charts.employeesAverageBar.destroy();
                    charts.employeesAverageBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Средняя сумма продажи',
                                data: data.data,
                                backgroundColor: '#f59e42'
                            }]
                        },
                        options: {scales: {y: {beginAtZero: true}}}
                    });
                });
        }
    </script>
</div>
@endsection