@extends('client.layouts.app')

@section('title', 'Веб-запись')

@section('content')
<style>
/* Стили для Bootstrap grid */
.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-md-6 {
    position: relative;
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    flex: 0 0 50%;
    max-width: 50%;
}

/* Стили для custom switches */
.custom-control {
    position: relative;
    display: block;
    min-height: 1.5rem;
    padding-left: 1.5rem;
}

.custom-control-input {
    position: absolute;
    left: 0;
    z-index: -1;
    width: 1rem;
    height: 1.25rem;
    opacity: 0;
}

.custom-control-label {
    position: relative;
    margin-bottom: 0;
    vertical-align: top;
    cursor: pointer;
}

.custom-switch .custom-control-label::before {
    left: -2.25rem;
    width: 1.75rem;
    pointer-events: all;
    border-radius: 0.5rem;
}

.custom-control-label::before {
    position: absolute;
    top: 0.25rem;
    left: -1.5rem;
    display: block;
    width: 1rem;
    height: 1rem;
    pointer-events: none;
    content: "";
    background-color: #fff;
    border: #adb5bd solid 1px;
}

.custom-switch .custom-control-label::after {
    top: calc(0.25rem + 2px);
    left: calc(-2.25rem + 2px);
    width: calc(1rem - 4px);
    height: calc(1rem - 4px);
    background-color: #adb5bd;
    border-radius: 0.5rem;
    transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.custom-control-label::after {
    position: absolute;
    top: 0.25rem;
    left: -1.5rem;
    display: block;
    width: 1rem;
    height: 1rem;
    content: "";
    background: no-repeat 50% / 50% 50%;
}

.custom-control-input:checked ~ .custom-control-label::before {
    color: #fff;
    border-color: #007bff;
    background-color: #007bff;
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::after {
    background-color: #fff;
    transform: translateX(0.75rem);
}

/* Стили для кнопок */
.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    color: #fff;
    background-color: #0069d9;
    border-color: #0062cc;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    color: #fff;
    background-color: #5a6268;
    border-color: #545b62;
}

/* Стили для форм */
.form-group {
    margin-bottom: 1rem;
}

.form-control {
    display: block;
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    color: #495057;
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

textarea.form-control {
    height: auto;
}

label {
    display: inline-block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

/* Стили для уведомлений (как на других страницах) */
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

.notification.show {
    display: block;
}

.notification.success {
    background: linear-gradient(135deg, #28a745, #34d399);
}

.notification.error {
    background: linear-gradient(135deg, #dc3545, #ef4444);
}

.notification .notification-icon {
    width: 24px;
    height: 24px;
    vertical-align: middle;
    margin-right: 8px;
}

.notification .notification-message {
    vertical-align: middle;
}

@keyframes shake {
    0% { transform: translateX(0); }
    20% { transform: translateX(-8px); }
    40% { transform: translateX(8px); }
    60% { transform: translateX(-6px); }
    80% { transform: translateX(6px); }
    100% { transform: translateX(0); }
}

.notification.shake {
    animation: shake 0.5s;
}

/* Стили для таблиц */
#userServicesTable, #scheduleTable {
    width: 100%;
    border-collapse: collapse;
}

#userServicesTable thead, #scheduleTable thead {
    background-color: #e0e4e9 !important;
    border-bottom: 1px solid #e5e7eb;
}

#userServicesTable th, #scheduleTable th {
    padding: 16px;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

#userServicesTable td, #scheduleTable td {
    padding: 14px;
    font-size: 14px;
    color: #4b5563;
    vertical-align: middle;
    text-align: center;
}

#userServicesTable tr:last-child td, #scheduleTable tr:last-child td {
    border-bottom: none;
}

/* Специальные стили для таблицы расписания */
#scheduleTable td:first-child {
    text-align: center;
    font-weight: 600;
}

#scheduleTable td:nth-child(2) {
    text-align: center;
}

#scheduleTable td:nth-child(3) {
    text-align: center;
}

#scheduleTable td:nth-child(4) {
    text-align: center;
    max-width: 200px;
}

#scheduleTable td:nth-child(5) {
    text-align: center;
}

/* Анимация для строк таблицы */


/* Стили для бейджей статуса */
.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.badge-success {
    background-color: #10b981;
    color: white;
}

.badge-secondary {
    background-color: #6b7280;
    color: white;
}

/* Стили для карточек статистики */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 12px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Стили для списков мастеров и услуг */
.master-item, .service-item {
    transition: all 0.2s ease;
}

.master-item:hover, .service-item:hover {
    background: #f3f4f6 !important;
    border-color: #d1d5db !important;
    transform: translateX(2px);
}

.badge-pill {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
}

/* Простые стили для мастеров и услуг */
.master-item, .service-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    margin-bottom: 8px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.master-item:hover, .service-item:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.master-info, .service-info {
    flex: 1;
}

.master-name, .service-name {
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
    font-size: 14px;
}

.master-details, .service-details {
    font-size: 13px;
    color: #6b7280;
}

.master-status, .service-status {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
}

.role-badge {
    background: #f3f4f6;
    color: #374151;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active, .status-available {
    color: #10b981;
    font-size: 12px;
    font-weight: 500;
}

.status-inactive, .status-unavailable {
    color: #6b7280;
    font-size: 12px;
    font-weight: 500;
}
</style>

<div class="dashboard-container">
    <div class="settings-header">
        <h1>Веб-запись</h1>
    </div>
    
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="booking-settings">
            <i class="fa fa-cog" style="margin-right:8px;"></i>Настройки записи
        </button>
        <button class="tab-button" data-tab="schedule-settings">
            <i class="fa fa-calendar-alt" style="margin-right:8px;"></i>Настройки расписания
        </button>
        <button class="tab-button" data-tab="user-services">
            <i class="fa fa-user-cog" style="margin-right:8px;"></i>Услуги мастеров
        </button>
    </div>
    
    <div class="settings-content">
        <!-- Вкладка настроек записи -->
        <div class="settings-pane" id="tab-booking-settings">
            <form id="booking-settings-form">
                @csrf
                <h5>Настройки веб-записи</h5>
                
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="booking_enabled" name="booking_enabled" 
                                       {{ $project->booking_enabled ? 'checked' : '' }}>
                                <label class="custom-control-label" for="booking_enabled">
                                    <strong>Включить веб-запись</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                                         <div class="form-col">
                         <div class="alert alert-info" id="booking-url-block" style="{{ $project->booking_enabled ? 'display: block;' : 'display: none;' }}">
                             <strong>Ссылка для клиентов:</strong><br>
                             <div class="input-group mt-2">
                                 <input type="text" class="form-control" value="{{ $project->booking_url }}" readonly id="booking-url">
                                 <div class="input-group-append">
                                     <button type="button" class="btn btn-outline-secondary" onclick="copyBookingUrl()">
                                         <i class="fas fa-copy"></i> Копировать
                                     </button>
                                 </div>
                             </div>
                         </div>
                     </div>
                </div>

                <div class="form-row form-row--3col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="working_hours_start">Общие часы работы салона (начало)</label>
                            <input type="time" class="form-control" id="working_hours_start" name="working_hours_start" 
                                   value="{{ $bookingSettings->working_hours_start_formatted }}">
                            <small class="form-text text-muted">Используется только для отображения общего расписания салона</small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="working_hours_end">Общие часы работы салона (конец)</label>
                            <input type="time" class="form-control" id="working_hours_end" name="working_hours_end" 
                                   value="{{ $bookingSettings->working_hours_end_formatted }}">
                            <small class="form-text text-muted">Каждый мастер настраивает свое время работы индивидуально</small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="advance_booking_days">За сколько дней можно записаться</label>
                            <input type="number" class="form-control" id="advance_booking_days" name="advance_booking_days" 
                                   value="{{ $bookingSettings->advance_booking_days }}" min="1" max="365">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="allow_same_day_booking" name="allow_same_day_booking" 
                                   {{ $bookingSettings->allow_same_day_booking ? 'checked' : '' }}>
                            <label class="custom-control-label" for="allow_same_day_booking">
                                Разрешить запись в тот же день
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group mb-4">
                        <label for="about">О нас - описание салона</label>
                        <textarea class="form-control" id="about" name="about" rows="4" 
                                  placeholder="Краткое описание о салоне/компании для клиентов...">{{ $project->about }}</textarea>
                        <small class="form-text text-muted">Это описание будет отображаться на странице веб-записи</small>
                    </div>
                </div>

                <div class="form-row form-row--2col" style="display: flex; gap: 20px; margin-top: 30px;">
                     <div class="form-col" style="flex: 1;">
                         <div class="form-group mb-3">
                             <h6 style="margin-bottom: 15px; color: #333; font-weight: 600;">
                                 <i class="fas fa-users" style="margin-right: 8px; color: #007bff;"></i>
                                 Мастера ({{ $users->count() }})
                             </h6>
                             <div class="masters-list">
                                 @foreach($users as $user)
                                     @php
                                         $userActiveServices = $userServices->where('user_id', $user->id)->where('is_active_for_booking', true);
                                     @endphp
                                     <div class="master-item">
                                         <div class="master-info">
                                             <div class="master-name">{{ $user->name }}</div>
                                             <div class="master-details">
                                                 {{ $userActiveServices->count() }} {{ $userActiveServices->count() == 1 ? 'услуга' : ($userActiveServices->count() < 5 ? 'услуги' : 'услуг') }}
                                                 @if($userActiveServices->count() > 0)
                                                     • от {{ $userActiveServices->min('price') }} ₽
                                                 @endif
                                             </div>
                                         </div>
                                         <div class="master-status">
                                             <span class="role-badge">{{ $user->role }}</span>
                                             @if($userActiveServices->count() > 0)
                                                 <span class="status-active">Активен</span>
                                             @else
                                                 <span class="status-inactive">Неактивен</span>
                                             @endif
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     </div>
                     <div class="form-col" style="flex: 1;">
                         <div class="form-group mb-3">
                             <h6 style="margin-bottom: 15px; color: #333; font-weight: 600;">
                                 <i class="fas fa-concierge-bell" style="margin-right: 8px; color: #28a745;"></i>
                                 Услуги ({{ $services->count() }})
                             </h6>
                             <div class="services-list">
                                 @foreach($services as $service)
                                     @php
                                         $serviceActiveMasters = $userServices->where('service_id', $service->id)->where('is_active_for_booking', true);
                                         $mastersCount = $serviceActiveMasters->count();
                                         $avgPrice = $serviceActiveMasters->count() > 0 ? $serviceActiveMasters->avg('price') : $service->price;
                                         $masterNames = $serviceActiveMasters->pluck('user.name')->join(', ');
                                     @endphp
                                     <div class="service-item">
                                         <div class="service-info">
                                             <div class="service-name">{{ $service->name }}</div>
                                             <div class="service-details">
                                                 @if($mastersCount > 0)
                                                     {{ $masterNames }} • {{ number_format($avgPrice) }} ₽
                                                 @else
                                                     Нет мастеров • {{ number_format($service->price) }} ₽
                                                 @endif
                                             </div>
                                         </div>
                                         <div class="service-status">
                                             @if($mastersCount > 0)
                                                 <span class="status-available">Доступна</span>
                                             @else
                                                 <span class="status-unavailable">Недоступна</span>
                                             @endif
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     </div>
                 </div>



                <div class="form-row">
                    <div class="form-group mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Сохранить настройки
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Вкладка настроек расписания -->
        <div class="settings-pane" id="tab-schedule-settings" style="display: none;">
            <div class="clients-header">
                <h1>Настройки расписания мастеров</h1>
                <div id="notification"></div>
                <div class="header-actions">
                    <div class="form-group mb-3" style="margin-bottom: 0;">
                        <label for="user-select" style="margin-bottom: 8px; font-weight: 600; color: #333;">Выберите мастера</label>
                        <select class="form-control" id="user-select" style="min-width: 250px; border-radius: 8px; border: 1px solid #d1d5db; padding: 8px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease;">
                            <option value="">Выберите мастера...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Расписание -->
            <div id="schedule-container" style="display: none;">
                <div class="table-wrapper" style="margin-top: 20px;">
                    <table class="table-striped sale-table" id="scheduleTable" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: white; border: 1px solid #e5e7eb;">
                        <thead>
                            <tr>
                                <th style="text-align: center;">День недели</th>
                                <th style="text-align: center;">Рабочие часы</th>
                                <th style="text-align: center;">Статус</th>
                                <th style="text-align: center;">Примечания</th>
                                <th style="text-align: center;">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="schedule-tbody">
                            <!-- Расписание будет загружено через AJAX -->
                        </tbody>
                    </table>
                </div>


            </div>

            <!-- Сообщение о выборе мастера -->
            <div id="select-user-message" class="text-center py-5" style="margin-top: 40px;">
                <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                <h5>Выберите мастера</h5>
                <p class="text-muted">Выберите мастера из списка выше, чтобы настроить его расписание</p>
            </div>
        </div>

         <!-- Вкладка услуг мастеров -->
         <div class="settings-pane" id="tab-user-services" style="display: none;">
             <div class="clients-header">
                 <h1>Управление услугами мастеров</h1>
                 <div id="notification"></div>
                 <div class="header-actions">
                     <button class="btn-add-client" onclick="addUserService()">
                         <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                             <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                         </svg>
                         Добавить услугу мастеру
                     </button>

                     <div class="search-box">
                         <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                             <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                         </svg>
                         <input type="text" placeholder="Поиск по мастерам и услугам..." id="userServicesSearchInput" autocomplete="off">
                     </div>
                 </div>
             </div>

             <!-- Таблица услуг мастеров -->
             <div class="table-wrapper">
                 <table class="table-striped sale-table" id="userServicesTable">
                     <thead>
                         <tr>
                             <th>Мастер</th>
                             <th>Услуга</th>
                             <th>Цена</th>
                             <th>Длительность</th>
                             <th>Статус</th>
                             <th>Действия</th>
                         </tr>
                     </thead>
                     <tbody id="user-services-tbody">
                         @foreach($userServices as $userService)
                             <tr data-user-service-id="{{ $userService->id }}">
                                 <td>{{ $userService->user->name }}</td>
                                 <td>{{ $userService->service->name }}</td>
                                 <td>{{ $userService->price ? number_format($userService->price) . ' ₽' : 'Не указана' }}</td>
                                 <td>{{ $userService->duration ? $userService->duration . ' мин' : 'Не указано' }}</td>
                                 <td>
                                     @if($userService->is_active_for_booking)
                                         <span class="badge badge-success">Активна</span>
                                     @else
                                         <span class="badge badge-secondary">Неактивна</span>
                                     @endif
                                 </td>
                                 <td class="actions-cell">
                                     <button type="button" class="btn-view" onclick="editUserService({{ $userService->id }})" title="Редактировать">
                                         <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                             <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                         </svg>
                                     </button>
                                     <button type="button" class="btn-delete" onclick="deleteUserService({{ $userService->id }})" title="Удалить">
                                         <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                             <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                         </svg>
                                     </button>
                                 </td>
                             </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>

             @if($userServices->count() == 0)
                 <div class="text-center py-5">
                     <i class="fas fa-user-cog fa-3x text-muted mb-3"></i>
                     <h5>Нет настроенных услуг</h5>
                     <p class="text-muted">Добавьте услуги мастерам, чтобы они были доступны для веб-записи</p>
                 </div>
             @endif
         </div>
     </div>
 </div>

<!-- Модальное окно для редактирования дня -->
<div id="editDayModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 style="margin: 0;">Настройка расписания</h5>
            <span style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeModal()">&times;</span>
        </div>
        <div>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-start-time">Начало работы</label>
                                <input type="time" class="form-control" id="edit-start-time">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-end-time">Конец работы</label>
                                <input type="time" class="form-control" id="edit-end-time">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-booking-interval">Интервал (мин)</label>
                                <input type="number" class="form-control" id="edit-booking-interval" min="15" max="120" step="15" value="30" placeholder="30">
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
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="closeModal()">Отмена</button>
            <button type="button" class="btn-submit" onclick="saveDaySchedule()">Сохранить</button>
        </div>
    </div>
</div>

<!-- Модальное окно для управления услугами мастеров -->
<div id="userServiceModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 style="margin: 0;" id="userServiceModalTitle">Добавить услугу мастеру</h5>
            <span style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeUserServiceModal()">&times;</span>
        </div>
        <div>
            <form id="user-service-form">
                @csrf
                <input type="hidden" id="user-service-id">
                
                <div class="form-group">
                    <label for="modal-user-id">Мастер</label>
                    <select class="form-control" id="modal-user-id" name="user_id" required>
                        <option value="">Выберите мастера...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="modal-service-id">Услуга</label>
                    <select class="form-control" id="modal-service-id" name="service_id" required>
                        <option value="">Выберите услугу...</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="modal-is-active" name="is_active_for_booking" checked>
                        <label class="custom-control-label" for="modal-is-active">
                            Активна для веб-записи
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="modal-price">Цена (необязательно)</label>
                    <input type="number" class="form-control" id="modal-price" name="price" step="0.01" min="0" placeholder="Оставьте пустым для базовой цены услуги">
                </div>
                
                <div class="form-group">
                    <label for="modal-duration">Длительность в минутах (необязательно)</label>
                    <input type="number" class="form-control" id="modal-duration" name="duration" min="1" placeholder="Оставьте пустым для базовой длительности">
                </div>
                
                <div class="form-group">
                    <label for="modal-description">Описание (необязательно)</label>
                    <textarea class="form-control" id="modal-description" name="description" rows="3" placeholder="Дополнительное описание услуги у этого мастера"></textarea>
                </div>
            </form>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="closeUserServiceModal()">Отмена</button>
            <button type="button" class="btn-submit" onclick="saveUserService()">Сохранить</button>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-content">
        <h3>Подтверждение удаления</h3>
        <p>Вы уверены, что хотите удалить эту связь?</p>
        <div class="confirmation-buttons">
            <button class="cancel-btn" id="cancelDelete">Отмена</button>
            <button class="confirm-btn" id="confirmDeleteBtn">Удалить</button>
        </div>
    </div>
</div>

<!-- Уведомление -->
<div id="notification" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999; display: none;">
</div>

@endsection

@push('scripts')
<script>
// Глобальные переменные
let currentUserId = null;
let scheduleData = {};
let currentDeleteUserServiceId = null;

// Обработка вкладок
document.addEventListener('DOMContentLoaded', function() {
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
    const form = document.getElementById('booking-settings-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
            
            // Правильно обрабатываем boolean значения
            const formDataObj = {};
            for (let [key, value] of formData.entries()) {
                if (key === 'booking_enabled' || key === 'allow_same_day_booking') {
                    formDataObj[key] = value === 'on' || value === 'true' || value === true;
                } else {
                    formDataObj[key] = value;
                }
            }
            
            fetch('{{ route("client.booking.update-settings") }}', {
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
                    showNotification('success', data.message);
                    
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
                    showNotification('error', 'Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                showNotification('error', 'Произошла ошибка при сохранении');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
    
    // Обработка выбора мастера для расписания
    const userSelect = document.getElementById('user-select');
    const editIsWorking = document.getElementById('edit-is-working');
    
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
    
    // Обработчики для модального окна подтверждения
    document.getElementById('cancelDelete').addEventListener('click', function() {
        closeConfirmationModal();
    });
    
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        confirmDeleteUserService();
    });
    
    // Закрытие модального окна подтверждения при клике вне его
    document.getElementById('confirmationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmationModal();
        }
    });
    
    // Обработчик поиска в таблице услуг мастеров
    document.getElementById('userServicesSearchInput').addEventListener('input', function() {
        searchUserServices(this.value);
    });
});

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
                showNotification('error', 'Ошибка загрузки расписания: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке расписания:', error);
            showNotification('error', 'Произошла ошибка при загрузке расписания');
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
            notes: '',
            booking_interval: null
        };
        
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
                    '<span style="color: #9ca3af; font-style: italic;">Нет примечаний</span>'
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

function toggleWorkingHoursFields() {
    const isWorking = document.getElementById('edit-is-working').checked;
    const fields = document.getElementById('working-hours-fields');
    
    if (isWorking) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
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
        showNotification('error', 'Укажите время начала и окончания работы');
        return;
    }
    
    if (isWorking && startTime >= endTime) {
        console.error('Неправильное время работы');
        showNotification('error', 'Время окончания должно быть позже времени начала');
        return;
    }
    
    // Валидация интервала
    if (!bookingInterval || bookingInterval < 15 || bookingInterval > 120) {
        console.error('Неправильный интервал');
        showNotification('error', 'Интервал должен быть от 15 до 120 минут');
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
    
    // Обновляем таблицу
    renderScheduleTable();
    
    // Сразу сохраняем в базу данных
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
            showNotification('success', 'Расписание успешно сохранено');
        } else {
            console.error('Ошибка сохранения:', data.message);
            showNotification('error', 'Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        showNotification('error', 'Произошла ошибка при сохранении');
    });
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
            showNotification('success', data.message);
        } else {
            console.error('Ошибка сохранения:', data.message);
            showNotification('error', 'Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        showNotification('error', 'Произошла ошибка при сохранении');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function copyBookingUrl() {
    const urlInput = document.getElementById('booking-url');
    urlInput.select();
    document.execCommand('copy');
    
    showNotification('success', 'Ссылка скопирована в буфер обмена');
}

function showNotification(type, message) {
    window.showNotification(type, message);
}

// Универсальная функция для показа уведомлений (как на других страницах)
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

// Функции для управления услугами мастеров
function addUserService() {
    // Очищаем форму
    document.getElementById('user-service-form').reset();
    document.getElementById('user-service-id').value = '';
    document.getElementById('modal-is-active').checked = true;
    
    // Обновляем заголовок модального окна
    document.getElementById('userServiceModalTitle').textContent = 'Добавить услугу мастеру';
    
    // Открываем модальное окно
    const modal = document.getElementById('userServiceModal');
    modal.style.display = 'block';
}

function editUserService(userServiceId) {
    // Загружаем данные услуги мастера
    fetch(`{{ route('client.booking.user-services.show', '') }}/${userServiceId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
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
            document.getElementById('userServiceModalTitle').textContent = 'Редактировать услугу мастеру';
            
            // Открываем модальное окно
            const modal = document.getElementById('userServiceModal');
            modal.style.display = 'block';
        } else {
            showNotification('error', 'Не удалось загрузить данные услуги');
        }
    })
    .catch(error => {
        console.error('Ошибка при загрузке данных:', error);
        showNotification('error', 'Произошла ошибка при загрузке данных');
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

    fetch(`{{ route('client.booking.user-services.destroy', '') }}/${userServiceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            
            // Удаляем строку из таблицы
            const row = document.querySelector(`tr[data-user-service-id="${userServiceId}"]`);
            if (row) {
                row.remove();
            }
            
            // Если таблица пустая, показываем сообщение
            const tbody = document.getElementById('user-services-tbody');
            if (tbody.children.length === 0) {
                const noServicesMessage = document.querySelector('#tab-user-services .text-center');
                if (noServicesMessage) {
                    noServicesMessage.style.display = 'block';
                }
            }
            
            // Обновляем статистику в первой вкладке
            updateStatistics();
        } else {
            showNotification('error', 'Ошибка: ' + data.message);
        }
        
        // Закрываем модальное окно подтверждения в любом случае
        closeConfirmationModal();
    })
    .catch(error => {
        console.error('Ошибка при удалении:', error);
        showNotification('error', 'Произошла ошибка при удалении');
        
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
    
    const url = userServiceId ? 
        `{{ route('client.booking.user-services.update', '') }}/${userServiceId}` : 
        '{{ route("client.booking.user-services.store") }}';
    
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
            showNotification('success', data.message);
            closeUserServiceModal();
            
            if (!userServiceId) {
                // Если это новая запись, добавляем её в таблицу
                if (data.userService) {
                    addUserServiceToTable(data.userService);
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
            showNotification('error', data.message || 'Произошла ошибка при сохранении');
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        showNotification('error', error.message || 'Произошла ошибка при сохранении');
    });
}

// Функция для добавления новой услуги в таблицу
function addUserServiceToTable(userService) {
    const tbody = document.getElementById('user-services-tbody');
    
    // Создаем новую строку
    const row = document.createElement('tr');
    row.setAttribute('data-user-service-id', userService.id); // Добавляем атрибут для идентификации
    row.innerHTML = `
        <td>${userService.user_name}</td>
        <td>${userService.service_name}</td>
        <td>${userService.active_price} ₽</td>
        <td>${userService.active_duration} мин</td>
        <td>
            ${userService.is_active_for_booking ? 
                '<span class="badge badge-success">Активна</span>' : 
                '<span class="badge badge-secondary">Неактивна</span>'
            }
        </td>
        <td class="actions-cell">
            <button type="button" class="btn-view" onclick="editUserService(${userService.id})" title="Редактировать">
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
    
    // Добавляем строку в таблицу
    tbody.appendChild(row);
    
    // Скрываем сообщение "Нет настроенных услуг" если оно есть
    const noServicesMessage = document.querySelector('#tab-user-services .text-center');
    if (noServicesMessage) {
        noServicesMessage.style.display = 'none';
    }
}

// Функция для обновления услуги в таблице
function updateUserServiceInTable(userService) {
    const row = document.querySelector(`tr[data-user-service-id="${userService.id}"]`);
    if (row) {
        row.innerHTML = `
            <td>${userService.user_name}</td>
            <td>${userService.service_name}</td>
            <td>${userService.active_price} ₽</td>
            <td>${userService.active_duration} мин</td>
            <td>
                ${userService.is_active_for_booking ? 
                    '<span class="badge badge-success">Активна</span>' : 
                    '<span class="badge badge-secondary">Неактивна</span>'
                }
            </td>
            <td class="actions-cell">
                <button type="button" class="btn-view" onclick="editUserService(${userService.id})" title="Редактировать">
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
}

// Функция для обновления статистики в первой вкладке
function updateStatistics() {
    // Получаем актуальные данные через AJAX
    fetch('{{ route("client.booking.user-services.get-user-services", "all") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateMastersList(data.userServices);
            updateServicesList(data.userServices);
        }
    })
    .catch(error => {
        console.error('Ошибка при обновлении статистики:', error);
    });
}

// Функция для обновления списка мастеров
function updateMastersList(userServices) {
    const mastersList = document.querySelector('.masters-list');
    if (!mastersList) return;
    
    // Получаем уникальных мастеров
    const masters = {};
    userServices.forEach(us => {
        if (!masters[us.user_id]) {
            masters[us.user_id] = {
                name: us.user_name,
                services: [],
                role: us.user_role || 'master'
            };
        }
        masters[us.user_id].services.push(us);
    });
    
    // Обновляем HTML для каждого мастера
    const masterItems = mastersList.querySelectorAll('.master-item');
    masterItems.forEach((item, index) => {
        const masterName = item.querySelector('.master-name').textContent;
        const master = Object.values(masters).find(m => m.name === masterName);
        
        if (master) {
            const activeServices = master.services.filter(s => s.is_active_for_booking);
            const servicesCount = activeServices.length;
            const minPrice = activeServices.length > 0 ? Math.min(...activeServices.map(s => s.price || 0)) : 0;
            
            const detailsElement = item.querySelector('.master-details');
            const statusElement = item.querySelector('.master-status');
            
            detailsElement.textContent = `${servicesCount} ${servicesCount == 1 ? 'услуга' : (servicesCount < 5 ? 'услуги' : 'услуг')} • от ${minPrice.toLocaleString()} ₽`;
            
            if (servicesCount > 0) {
                statusElement.innerHTML = `
                    <span class="role-badge">${master.role}</span>
                    <span class="status-active">Активен</span>
                `;
            } else {
                statusElement.innerHTML = `
                    <span class="role-badge">${master.role}</span>
                    <span class="status-inactive">Неактивен</span>
                `;
            }
        }
    });
}

// Функция для обновления списка услуг
function updateServicesList(userServices) {
    const servicesList = document.querySelector('.services-list');
    if (!servicesList) return;
    
    // Получаем уникальные услуги
    const services = {};
    userServices.forEach(us => {
        if (!services[us.service_id]) {
            services[us.service_id] = {
                name: us.service_name,
                masters: [],
                price: us.price || 0
            };
        }
        services[us.service_id].masters.push(us);
    });
    
    // Обновляем HTML для каждой услуги
    const serviceItems = servicesList.querySelectorAll('.service-item');
    serviceItems.forEach((item, index) => {
        const serviceName = item.querySelector('.service-name').textContent;
        const service = Object.values(services).find(s => s.name === serviceName);
        
        if (service) {
            const activeMasters = service.masters.filter(m => m.is_active_for_booking);
            const mastersCount = activeMasters.length;
            const avgPrice = activeMasters.length > 0 ? 
                activeMasters.reduce((sum, m) => sum + (m.price || 0), 0) / activeMasters.length : 0;
            const masterNames = activeMasters.map(m => m.user_name).join(', ');
            
            const detailsElement = item.querySelector('.service-details');
            const statusElement = item.querySelector('.service-status');
            
            detailsElement.textContent = mastersCount > 0 ? 
                `${masterNames} • ${avgPrice.toLocaleString()} ₽` : 
                'Нет мастеров • 0 ₽';
            
            if (mastersCount > 0) {
                statusElement.innerHTML = `<span class="status-available">Доступна</span>`;
            } else {
                statusElement.innerHTML = `<span class="status-unavailable">Недоступна</span>`;
            }
        } else {
            const detailsElement = item.querySelector('.service-details');
            const statusElement = item.querySelector('.service-status');
            
            detailsElement.textContent = 'Нет мастеров • 0 ₽';
            statusElement.innerHTML = `<span class="status-unavailable">Недоступна</span>`;
        }
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
</script>
@endpush 