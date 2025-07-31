@extends('client.layouts.app')

@section('title', __('messages.web_booking'))

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

/* Стили для уведомлений используют глобальные из notifications.css */

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
        <h1>{{ __('messages.web_booking') }}</h1>
    </div>
    
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="booking-settings">
            <i class="fa fa-cog" style="margin-right:8px;"></i>{{ __('messages.booking_settings') }}
        </button>
        <button class="tab-button" data-tab="schedule-settings">
            <i class="fa fa-calendar-alt" style="margin-right:8px;"></i>{{ __('messages.schedule_settings') }}
        </button>
        <button class="tab-button" data-tab="user-services">
            <i class="fa fa-user-cog" style="margin-right:8px;"></i>{{ __('messages.master_services') }}
        </button>
    </div>
    
    <div class="settings-content">
        <!-- Вкладка настроек записи -->
        <div class="settings-pane" id="tab-booking-settings">
            <form id="booking-settings-form">
                @csrf
                <h5>{{ __('messages.booking_settings') }}</h5>
                
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="booking_enabled" name="booking_enabled" 
                                       {{ $project->booking_enabled ? 'checked' : '' }}>
                                <label class="custom-control-label" for="booking_enabled">
                                    <strong>{{ __('messages.enable_web_booking') }}</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                                         <div class="form-col">
                         <div class="alert alert-info" id="booking-url-block" style="{{ $project->booking_enabled ? 'display: block;' : 'display: none;' }}">
                             <strong>{{ __('messages.client_link') }}</strong><br>
                             <div class="input-group mt-2">
                                 <input type="text" class="form-control" value="{{ $project->booking_url }}" readonly id="booking-url">
                                 <div class="input-group-append">
                                     <button type="button" class="btn btn-outline-secondary" onclick="copyBookingUrl()">
                                         <i class="fas fa-copy"></i> {{ __('messages.copy') }}
                                     </button>
                                 </div>
                             </div>
                         </div>
                     </div>
                </div>

                <div class="form-row form-row--3col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="working_hours_start">{{ __('messages.general_working_hours_start') }}</label>
                            <input type="time" class="form-control" id="working_hours_start" name="working_hours_start" 
                                   value="{{ $bookingSettings->working_hours_start_formatted }}">
                            <small class="form-text text-muted">{{ __('messages.working_hours_start_hint') }}</small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="working_hours_end">{{ __('messages.general_working_hours_end') }}</label>
                            <input type="time" class="form-control" id="working_hours_end" name="working_hours_end" 
                                   value="{{ $bookingSettings->working_hours_end_formatted }}">
                            <small class="form-text text-muted">{{ __('messages.working_hours_end_hint') }}</small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="advance_booking_days">{{ __('messages.advance_booking_days_label') }}</label>
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
                                {{ __('messages.allow_same_day_booking') }}
                            </label>
                        </div>
                    </div>
                </div>



                <div class="form-row form-row--2col" style="display: flex; gap: 20px; margin-top: 30px;">
                     <div class="form-col" style="flex: 1;">
                         <div class="form-group mb-3">
                             <h6 style="margin-bottom: 15px; color: #333; font-weight: 600;">
                                 <i class="fas fa-users" style="margin-right: 8px; color: #007bff;"></i>
                                 {{ __('messages.masters') }} ({{ $users->count() }})
                             </h6>
                             <div class="masters-list">
                                 @foreach($users as $user)
                                     @php
                                         $userActiveServices = $userServices->where('user_id', $user->id)->where('is_active_for_booking', true);
                                     @endphp
                                     <div class="master-item">
                                         <div class="master-info">
                                             <div class="master-name">{{ $user->name ?? __('messages.deleted_user') }}</div>
                                             <div class="master-details">
                                                 {{ $userActiveServices->count() }} {{ $userActiveServices->count() == 1 ? __('messages.service') : ($userActiveServices->count() < 5 ? __('messages.services_2') : __('messages.services_5')) }}
                                                 @if($userActiveServices->count() > 0 && $userActiveServices->min('price') > 0)
                                                     • {{ __('messages.from') }} <span class="currency-amount" data-amount="{{ $userActiveServices->min('price') }}">{{ \App\Helpers\CurrencyHelper::format($userActiveServices->min('price')) }}</span>
                                                 @endif
                                             </div>
                                         </div>
                                         <div class="master-status">
                                             <span class="role-badge">{{ $user->role }}</span>
                                             @if($userActiveServices->count() > 0)
                                                 <span class="status-active">{{ __('messages.active') }}</span>
                                             @else
                                                 <span class="status-inactive">{{ __('messages.inactive') }}</span>
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
                                 {{ __('messages.services') }} ({{ $services->count() }})
                             </h6>
                             <div class="services-list">
                                 @foreach($services as $service)
                                     @php
                                         $serviceActiveMasters = $userServices->where('service_id', $service->id)->where('is_active_for_booking', true);
                                         $mastersCount = $serviceActiveMasters->count();
                                         
                                         // Вычисляем среднюю цену с учетом активных цен мастеров
                                         if ($serviceActiveMasters->count() > 0) {
                                             $totalPrice = 0;
                                             $validPrices = 0;
                                             foreach ($serviceActiveMasters as $userService) {
                                                 $activePrice = $userService->price ?: $userService->service->price;
                                                 if ($activePrice > 0) {
                                                     $totalPrice += $activePrice;
                                                     $validPrices++;
                                                 }
                                             }
                                             $avgPrice = $validPrices > 0 ? $totalPrice / $validPrices : $service->price;
                                         } else {
                                             $avgPrice = $service->price;
                                         }
                                         
                                         $masterNames = $serviceActiveMasters->pluck('user.name')->join(', ');
                                     @endphp
                                     <div class="service-item">
                                         <div class="service-info">
                                             <div class="service-name">{{ $service->name ?? __('messages.deleted_service') }}</div>
                                             <div class="service-details">
                                                 @if($mastersCount > 0 && $avgPrice > 0)
                                                     {{ $masterNames }} • <span class="currency-amount" data-amount="{{ $avgPrice }}">{{ \App\Helpers\CurrencyHelper::format($avgPrice) }}</span>
                                                 @elseif($mastersCount > 0)
                                                     {{ $masterNames }}
                                                 @else
                                                     {{ __('messages.no_masters') }}
                                                 @endif
                                             </div>
                                         </div>
                                         <div class="service-status">
                                             @if($mastersCount > 0)
                                                 <span class="status-available">{{ __('messages.available') }}</span>
                                             @else
                                                 <span class="status-unavailable">{{ __('messages.unavailable') }}</span>
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
                            <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Вкладка настроек расписания -->
        <div class="settings-pane" id="tab-schedule-settings" style="display: none;">
            <div class="clients-header">
                <h1>{{ __('messages.schedule_settings') }}</h1>
                <div class="header-actions">
                    <div class="form-group mb-3" style="margin-bottom: 0;">
                        <label for="user-select" style="margin-bottom: 8px; font-weight: 600; color: #333;">{{ __('messages.select_master') }}</label>
                        <select class="form-control" id="user-select" style="min-width: 250px; border-radius: 8px; border: 1px solid #d1d5db; padding: 8px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease;">
                            <option value="">{{ __('messages.select_master_placeholder') }}</option>
                            @foreach($users as $user)
                                                                 <option value="{{ $user->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $user->name ?? __('messages.deleted_user') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Расписание -->
            <div id="schedule-container" style="display: none;">
                <!-- Десктопная таблица -->
                <div class="table-wrapper" style="margin-top: 20px;">
                    <table class="table-striped sale-table" id="scheduleTable" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: white; border: 1px solid #e5e7eb;">
                        <thead>
                            <tr>
                                <th style="text-align: center;">{{ __('messages.day_of_week') }}</th>
                                <th style="text-align: center;">{{ __('messages.working_hours') }}</th>
                                <th style="text-align: center;">{{ __('messages.status') }}</th>
                                <th style="text-align: center;">{{ __('messages.notes') }}</th>
                                <th style="text-align: center;">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="schedule-tbody">
                            <!-- Расписание будет загружено через AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- Мобильные карточки расписания -->
                <div class="schedule-cards" id="scheduleCards" style="display: none;">
                    <!-- Карточки будут добавлены через JavaScript -->
                </div>
            </div>

            <!-- Сообщение о выборе мастера -->
            <div id="select-user-message" class="text-center py-5" style="margin-top: 40px;">
                <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                <h5>{{ __('messages.select_master_message') }}</h5>
                <p class="text-muted">{{ __('messages.select_master_description') }}</p>
            </div>
        </div>

         <!-- Вкладка услуг мастеров -->
         <div class="settings-pane" id="tab-user-services" style="display: none;">
             <div class="clients-header">
                 <h1>{{ __('messages.master_services_management') }}</h1>
                 <div class="header-actions">
                     <button class="btn-add-client" onclick="addUserService()">
                         <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                             <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                         </svg>
                         {{ __('messages.add_service_to_master') }}
                     </button>

                     <div class="search-box">
                         <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                             <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                         </svg>
                         <input type="text" placeholder="{{ __('messages.search_masters_services') }}" id="userServicesSearchInput" autocomplete="off">
                     </div>
                 </div>
             </div>

             <!-- Таблица услуг мастеров -->
             <div class="table-wrapper">
                 <table class="table-striped sale-table" id="userServicesTable">
                     <thead>
                         <tr>
                             <th>{{ __('messages.master') }}</th>
                             <th>{{ __('messages.service_name') }}</th>
                             <th>{{ __('messages.price') }}</th>
                             <th>{{ __('messages.duration') }}</th>
                             <th>{{ __('messages.status') }}</th>
                             <th>{{ __('messages.actions') }}</th>
                         </tr>
                     </thead>
                     <tbody id="user-services-tbody">
                         @foreach($userServices as $userService)
                             <tr data-user-service-id="{{ $userService->id }}">
                                 <td>{{ $userService->user ? $userService->user->name : __('messages.deleted_user') }}</td>
                                 <td>{{ $userService->service ? $userService->service->name : __('messages.deleted_service') }}</td>
                                 <td class="currency-amount" data-amount="{{ $userService->price ?: ($userService->service ? $userService->service->price : 0) }}">{!! $userService->price ? \App\Helpers\CurrencyHelper::format($userService->price) : ($userService->service ? \App\Helpers\CurrencyHelper::format($userService->service->price) . ' <small class="text-muted">(' . __('messages.base_price') . ')</small>' : __('messages.not_specified_price')) !!}</td>
                                 <td>{!! $userService->duration ? \App\Helpers\TimeHelper::formatDuration($userService->duration) : ($userService->service && $userService->service->duration ? \App\Helpers\TimeHelper::formatDuration($userService->service->duration) . ' <small class="text-muted">(' . __('messages.base_duration') . ')</small>' : __('messages.not_specified_duration')) !!}</td>
                                 <td>
                                     @if($userService->is_active_for_booking)
                                         <span class="badge badge-success">{{ __('messages.active') }}</span>
                                     @else
                                         <span class="badge badge-secondary">{{ __('messages.inactive') }}</span>
                                     @endif
                                 </td>
                                 <td class="actions-cell">
                                     <button type="button" class="btn-view" onclick="editUserService({{ $userService->id }})" title="{{ __('messages.edit') }}">
                                         <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                             <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                         </svg>
                                     </button>
                                     <button type="button" class="btn-delete" onclick="deleteUserService({{ $userService->id }})" title="{{ __('messages.delete') }}">
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

             <!-- Мобильные карточки услуг мастеров -->
             <div class="user-services-cards" id="userServicesCards">
                 @foreach($userServices as $userService)
                     <div class="user-service-card" data-user-service-id="{{ $userService->id }}">
                         <div class="user-service-card-header">
                             <div class="user-service-main-info">
                                 <div class="user-service-master">{{ $userService->user ? $userService->user->name : __('messages.deleted_user') }}</div>
                                 <div class="user-service-name">{{ $userService->service ? $userService->service->name : __('messages.deleted_service') }}</div>
                             </div>
                             <div class="user-service-status">
                                 @if($userService->is_active_for_booking)
                                     <span class="status-badge active">{{ __('messages.active') }}</span>
                                 @else
                                     <span class="status-badge inactive">{{ __('messages.inactive') }}</span>
                                 @endif
                             </div>
                         </div>
                         <div class="user-service-info">
                             <div class="user-service-info-item">
                                 <div class="user-service-info-label">
                                     <svg viewBox="0 0 24 24" fill="currentColor">
                                         <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                     </svg>
                                     {{ __('messages.price') }}
                                 </div>
                                 <div class="user-service-info-value currency-amount" data-amount="{{ $userService->price ?: ($userService->service ? $userService->service->price : 0) }}">
                                     {!! $userService->price ? \App\Helpers\CurrencyHelper::format($userService->price) : ($userService->service ? \App\Helpers\CurrencyHelper::format($userService->service->price) . ' <small class="text-muted">(' . __('messages.base_price') . ')</small>' : __('messages.not_specified_price')) !!}
                                 </div>
                             </div>
                             <div class="user-service-info-item">
                                 <div class="user-service-info-label">
                                     <svg viewBox="0 0 24 24" fill="currentColor">
                                         <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                                         <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                     </svg>
                                     {{ __('messages.duration') }}
                                 </div>
                                 <div class="user-service-info-value">
                                     {!! $userService->duration ? \App\Helpers\TimeHelper::formatDuration($userService->duration) : ($userService->service && $userService->service->duration ? \App\Helpers\TimeHelper::formatDuration($userService->service->duration) . ' <small class="text-muted">(' . __('messages.base_duration') . ')</small>' : __('messages.not_specified_duration')) !!}
                                 </div>
                             </div>
                         </div>
                         <div class="user-service-actions">
                             <button type="button" class="btn-edit" onclick="editUserService({{ $userService->id }})">
                                 <svg viewBox="0 0 24 24" fill="currentColor">
                                     <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                 </svg>
                                 {{ __('messages.edit') }}
                             </button>
                             <button type="button" class="btn-delete" onclick="deleteUserService({{ $userService->id }})">
                                 <svg viewBox="0 0 24 24" fill="currentColor">
                                     <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                 </svg>
                                 {{ __('messages.delete') }}
                             </button>
                         </div>
                     </div>
                 @endforeach
             </div>

             @if($userServices->count() == 0)
                 <div class="text-center py-5">
                     <i class="fas fa-user-cog fa-3x text-muted mb-3"></i>
                     <h5>{{ __('messages.no_services_found') }}</h5>
                     <p class="text-muted">{{ __('messages.add_services_to_masters_for_web_booking') }}</p>
                 </div>
             @endif
         </div>
     </div>
 </div>

<!-- Модальное окно для редактирования дня -->
<div id="editDayModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 style="margin: 0;">{{ __('messages.schedule_settings') }}</h5>
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
                            <strong>{{ __('messages.working_day') }}</strong>
                        </label>
                    </div>
                </div>

                <div id="working-hours-fields">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-start-time">{{ __('messages.work_start') }}</label>
                                <input type="time" class="form-control" id="edit-start-time">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-end-time">{{ __('messages.work_end') }}</label>
                                <input type="time" class="form-control" id="edit-end-time">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-booking-interval">{{ __('messages.interval_minutes') }}</label>
                                <input type="number" class="form-control" id="edit-booking-interval" min="15" max="120" step="15" value="30" placeholder="30">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-notes">{{ __('messages.notes') }}</label>
                        <textarea class="form-control" id="edit-notes" rows="3" placeholder="{{ __('messages.additional_info') }}"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
            <button type="button" class="btn-submit" onclick="saveDaySchedule()">{{ __('messages.save') }}</button>
        </div>
    </div>
</div>

<!-- Модальное окно для управления услугами мастеров -->
<div id="userServiceModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 style="margin: 0;" id="userServiceModalTitle">{{ __('messages.add_master_service') }}</h5>
            <span style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeUserServiceModal()">&times;</span>
        </div>
        <div>
            <form id="user-service-form">
                @csrf
                <input type="hidden" id="user-service-id">
                
                <div class="form-group">
                    <label for="modal-user-id">{{ __('messages.master_employee') }}</label>
                    <select class="form-control" id="modal-user-id" name="user_id" required>
                        <option value="">{{ __('messages.select_master') }}</option>
                        @foreach($users as $user)
                                                         <option value="{{ $user->id }}">{{ $user->name ?? __('messages.deleted_user') }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="modal-service-id">{{ __('messages.service') }}</label>
                    <select class="form-control" id="modal-service-id" name="service_id" required>
                        <option value="">{{ __('messages.select_service') }}</option>
                        @foreach($services as $service)
                                                         <option value="{{ $service->id }}">{{ $service->name ?? __('messages.deleted_service') }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="modal-is-active" name="is_active_for_booking" checked>
                        <label class="custom-control-label" for="modal-is-active">
                            {{ __('messages.active_for_web_booking') }}
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="modal-price">{{ __('messages.price_optional') }}</label>
                    <input type="number" class="form-control" id="modal-price" name="price" step="0.01" min="0" placeholder="{{ __('messages.leave_empty_for_base_price') }}">
                </div>
                
                <div class="form-group">
                    <label for="modal-duration">{{ __('messages.duration_minutes_optional') }}</label>
                    <input type="number" class="form-control" id="modal-duration" name="duration" min="1" placeholder="{{ __('messages.leave_empty_for_base_duration') }}">
                </div>
                
                <div class="form-group">
                    <label for="modal-description">{{ __('messages.description_optional') }}</label>
                    <textarea class="form-control" id="modal-description" name="description" rows="3" placeholder="{{ __('messages.additional_service_description') }}"></textarea>
                </div>
            </form>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="closeUserServiceModal()">{{ __('messages.cancel') }}</button>
            <button type="button" class="btn-submit" onclick="saveUserService()">{{ __('messages.save') }}</button>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-content">
        <h3>{{ __('messages.confirmation_delete') }}</h3>
        <p>{{ __('messages.confirm_delete_connection') }}</p>
        <div class="confirmation-buttons">
            <button class="cancel-btn" id="cancelDelete">{{ __('messages.cancel') }}</button>
            <button class="confirm-btn" id="confirmDeleteBtn">{{ __('messages.delete_connection') }}</button>
        </div>
    </div>
</div>

<!-- Уведомления создаются динамически глобальной функцией -->

@endsection

@push('scripts')
<script>
// Глобальные переменные
let currentUserId = null;
let scheduleData = {};
let currentDeleteUserServiceId = null;

// Переводы для JavaScript
const translations = {
    'add_master_service': '{{ __('messages.add_master_service') }}',
    'edit_service_to_master': '{{ __('messages.edit_service_to_master') }}',
    'error_loading_service_data': '{{ __('messages.error_loading_service_data') }}',
    'error_loading_data': '{{ __('messages.error_loading_data') }}',
    'error_saving': '{{ __('messages.error_saving') }}',
    'error_loading_schedule': '{{ __('messages.error_loading_schedule') }}',
    'error_deleting': '{{ __('messages.error_deleting') }}',
    'saving': '{{ __('messages.saving') }}'
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
    
    // Добавляем обработчик изменения размера окна для расписания
    window.addEventListener('resize', function() {
        if (document.getElementById('schedule-container').style.display !== 'none') {
            toggleScheduleView();
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
    // Очищаем предыдущие данные
    scheduleData = {};
    
    fetch(`{{ route('client.booking.get-user-schedule') }}?user_id=${userId}`)
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
        { id: 1, name: '{{ __('messages.monday') }}' },
        { id: 2, name: '{{ __('messages.tuesday') }}' },
        { id: 3, name: '{{ __('messages.wednesday') }}' },
        { id: 4, name: '{{ __('messages.thursday') }}' },
        { id: 5, name: '{{ __('messages.friday') }}' },
        { id: 6, name: '{{ __('messages.saturday') }}' },
        { id: 0, name: '{{ __('messages.sunday') }}' }
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
                    '<span style="color: #6b7280;">{{ __('messages.day_off') }}</span>'
                }
                ${dayData.is_working && dayData.booking_interval ? 
                    `<br><small style="color: #3b82f6;">{{ __('messages.interval') }}: ${dayData.booking_interval} {{ __('messages.minutes') }}</small>` : 
                    ''
                }
            </td>
            <td>
                ${dayData.is_working ? 
                    '<span class="badge badge-success">{{ __('messages.working') }}</span>' : 
                    '<span class="badge badge-secondary">{{ __('messages.day_off') }}</span>'
                }
            </td>
            <td>
                ${dayData.notes ? 
                    `<span style="color: #6b7280; font-size: 13px;">${dayData.notes}</span>` : 
                    '<span style="color: #9ca3af; font-style: italic;">{{ __('messages.no_notes') }}</span>'
                }
            </td>
            <td class="actions-cell">
                <button type="button" class="btn-view" onclick="editDay(${day.id})" title="{{ __('messages.edit') }}" style="display: flex; align-items: center; gap: 6px;">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    {{ __('messages.edit') }}
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
                            '<span class="status-badge working">{{ __('messages.working') }}</span>' : 
                            '<span class="status-badge day-off">{{ __('messages.day_off') }}</span>'
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
                        {{ __('messages.working_hours') }}
                    </div>
                    <div class="schedule-info-value">
                        ${dayData.is_working ? 
                            `${dayData.start_time} - ${dayData.end_time}` : 
                            '{{ __('messages.day_off') }}'
                        }
                    </div>
                </div>
                ${dayData.is_working && dayData.booking_interval ? `
                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        {{ __('messages.interval') }}
                    </div>
                    <div class="schedule-info-value">
                        ${dayData.booking_interval} {{ __('messages.minutes') }}
                    </div>
                </div>
                ` : ''}
                ${dayData.notes ? `
                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                        {{ __('messages.notes') }}
                    </div>
                    <div class="schedule-info-value">
                        ${dayData.notes}
                    </div>
                </div>
                ` : ''}
            </div>
            <div class="schedule-actions">
                <button type="button" class="btn-edit" onclick="editDay(${day.id})" title="{{ __('messages.edit') }}">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    {{ __('messages.edit') }}
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

function saveSchedule() {
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + translations.saving;
    
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
            window.showNotification('success', data.message);
        } else {
            console.error('Ошибка сохранения:', data.message);
            window.showNotification('error', 'Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        window.showNotification('error', translations.error_saving);
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
    
    window.showNotification('success', 'Ссылка скопирована в буфер обмена');
}

// Используем глобальную функцию уведомлений напрямую

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
        `{{ route('client.booking.user-services.update', '') }}/${userServiceId}` : 
        '{{ route("client.booking.user-services.store") }}';
    
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

// Функция для добавления новой услуги в таблицу
function addUserServiceToTable(userService) {
    const tbody = document.getElementById('user-services-tbody');
    const userServicesCards = document.getElementById('userServicesCards');
    
    // Создаем новую строку для таблицы
    const row = document.createElement('tr');
    row.setAttribute('data-user-service-id', userService.id);
    
    // Создаем ячейки
    const nameCell = document.createElement('td');
    nameCell.textContent = userService.user_name;
    
    const serviceCell = document.createElement('td');
    serviceCell.textContent = userService.service_name;
    
    const priceCell = document.createElement('td');
    priceCell.className = 'currency-amount';
    priceCell.setAttribute('data-amount', userService.price || userService.service_price);
    if (userService.price) {
        priceCell.textContent = window.CurrencyManager ? window.CurrencyManager.formatAmount(userService.price) : (userService.price + ' ₽');
    } else {
        const formattedPrice = window.CurrencyManager ? window.CurrencyManager.formatAmount(userService.service_price) : (userService.service_price + ' ₽');
        priceCell.innerHTML = formattedPrice + ' <small class="text-muted">({{ __('messages.base_price') }})</small>';
    }
    
    const durationCell = document.createElement('td');
    if (userService.duration) {
        durationCell.textContent = formatDuration(userService.duration);
    } else if (userService.service_duration) {
        durationCell.innerHTML = formatDuration(userService.service_duration) + ' <small class="text-muted">({{ __('messages.base_duration') }})</small>';
    } else {
        durationCell.textContent = '{{ __('messages.not_specified_duration') }}';
    }
    
    const statusCell = document.createElement('td');
    if (userService.is_active_for_booking) {
        statusCell.innerHTML = '<span class="badge badge-success">{{ __('messages.active') }}</span>';
    } else {
        statusCell.innerHTML = '<span class="badge badge-secondary">{{ __('messages.inactive') }}</span>';
    }
    
    const actionsCell = document.createElement('td');
    actionsCell.className = 'actions-cell';
    actionsCell.innerHTML = `
        <button type="button" class="btn-view" onclick="editUserService(${userService.id})" title="{{ __('messages.edit') }}">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
            </svg>
        </button>
        <button type="button" class="btn-delete" onclick="deleteUserService(${userService.id})" title="{{ __('messages.delete') }}">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
            </svg>
        </button>
    `;
    
    // Добавляем ячейки в строку
    row.appendChild(nameCell);
    row.appendChild(serviceCell);
    row.appendChild(priceCell);
    row.appendChild(durationCell);
    row.appendChild(statusCell);
    row.appendChild(actionsCell);
    
    // Добавляем строку в таблицу
    tbody.appendChild(row);
    
    // Создаем мобильную карточку
    if (userServicesCards) {
        const card = document.createElement('div');
        card.className = 'user-service-card';
        card.setAttribute('data-user-service-id', userService.id);
        
        const priceValue = userService.price ? 
            (window.CurrencyManager ? window.CurrencyManager.formatAmount(userService.price) : (userService.price + ' ₽')) :
            (window.CurrencyManager ? window.CurrencyManager.formatAmount(userService.service_price) : (userService.service_price + ' ₽')) + ' <small class="text-muted">({{ __('messages.base_price') }})</small>';
        
        const durationValue = userService.duration ? 
            formatDuration(userService.duration) :
            (userService.service_duration ? formatDuration(userService.service_duration) + ' <small class="text-muted">({{ __('messages.base_duration') }})</small>' : '{{ __('messages.not_specified_duration') }}');
        
        const statusBadge = userService.is_active_for_booking ? 
            '<span class="status-badge active">{{ __('messages.active') }}</span>' :
            '<span class="status-badge inactive">{{ __('messages.inactive') }}</span>';
        
        card.innerHTML = `
            <div class="user-service-card-header">
                <div class="user-service-main-info">
                    <div class="user-service-master">${userService.user_name}</div>
                    <div class="user-service-name">${userService.service_name}</div>
                </div>
                <div class="user-service-status">
                    ${statusBadge}
                </div>
            </div>
            <div class="user-service-info">
                <div class="user-service-info-item">
                    <div class="user-service-info-label">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        {{ __('messages.price') }}
                    </div>
                    <div class="user-service-info-value currency-amount" data-amount="${userService.price || userService.service_price}">
                        ${priceValue}
                    </div>
                </div>
                <div class="user-service-info-item">
                    <div class="user-service-info-label">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                            <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                        {{ __('messages.duration') }}
                    </div>
                    <div class="user-service-info-value">
                        ${durationValue}
                    </div>
                </div>
            </div>
            <div class="user-service-actions">
                <button type="button" class="btn-edit" onclick="editUserService(${userService.id})">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    {{ __('messages.edit') }}
                </button>
                <button type="button" class="btn-delete" onclick="deleteUserService(${userService.id})">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                    {{ __('messages.delete') }}
                </button>
            </div>
        `;
        
        userServicesCards.appendChild(card);
    }
    
    // Скрываем сообщение "Нет настроенных услуг" если оно есть
    const noServicesMessage = document.querySelector('#tab-user-services .text-center');
    if (noServicesMessage) {
        noServicesMessage.style.display = 'none';
    }
}

// Функция для обновления услуги в таблице
function updateUserServiceInTable(userService) {
    console.log('updateUserServiceInTable вызвана с данными:', userService);
    
    const row = document.querySelector(`tr[data-user-service-id="${userService.id}"]`);
    const card = document.querySelector(`.user-service-card[data-user-service-id="${userService.id}"]`);
    
    console.log('Найденные элементы:', {
        row: !!row,
        card: !!card,
        rowSelector: `tr[data-user-service-id="${userService.id}"]`,
        cardSelector: `.user-service-card[data-user-service-id="${userService.id}"]`
    });
    
    if (row) {
        // Очищаем строку
        row.innerHTML = '';
        
        // Создаем ячейки
        const nameCell = document.createElement('td');
        nameCell.textContent = userService.user_name;
        
        const serviceCell = document.createElement('td');
        serviceCell.textContent = userService.service_name;
        
        const priceCell = document.createElement('td');
        priceCell.className = 'currency-amount';
        priceCell.setAttribute('data-amount', userService.price || userService.service_price);
        if (userService.price) {
            priceCell.textContent = window.CurrencyManager ? window.CurrencyManager.formatAmount(userService.price) : (userService.price + ' ₽');
        } else {
            const formattedPrice = window.CurrencyManager ? window.CurrencyManager.formatAmount(userService.service_price) : (userService.service_price + ' ₽');
            priceCell.innerHTML = formattedPrice + ' <small class="text-muted">({{ __('messages.base_price') }})</small>';
        }
        
        const durationCell = document.createElement('td');
        if (userService.duration) {
            durationCell.textContent = formatDuration(userService.duration);
        } else if (userService.service_duration) {
            durationCell.innerHTML = formatDuration(userService.service_duration) + ' <small class="text-muted">({{ __('messages.base_duration') }})</small>';
        } else {
            durationCell.textContent = '{{ __('messages.not_specified_duration') }}';
        }
        
        const statusCell = document.createElement('td');
        if (userService.is_active_for_booking) {
            statusCell.innerHTML = '<span class="badge badge-success">{{ __('messages.active') }}</span>';
        } else {
            statusCell.innerHTML = '<span class="badge badge-secondary">{{ __('messages.inactive') }}</span>';
        }
        
        const actionsCell = document.createElement('td');
        actionsCell.className = 'actions-cell';
        actionsCell.innerHTML = `
            <button type="button" class="btn-view" onclick="editUserService(${userService.id})" title="{{ __('messages.edit') }}">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
            </button>
            <button type="button" class="btn-delete" onclick="deleteUserService(${userService.id})" title="{{ __('messages.delete') }}">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
            </button>
        `;
        
        // Добавляем ячейки в строку
        row.appendChild(nameCell);
        row.appendChild(serviceCell);
        row.appendChild(priceCell);
        row.appendChild(durationCell);
        row.appendChild(statusCell);
        row.appendChild(actionsCell);
    }
    
    // Обновляем мобильную карточку
    if (card) {
        console.log('Обновляем мобильную карточку для услуги:', userService.id);
        
        const priceValue = userService.price ? 
            (window.CurrencyManager ? window.CurrencyManager.formatAmount(userService.price) : (userService.price + ' ₽')) :
            (window.CurrencyManager ? window.CurrencyManager.formatAmount(userService.service_price) : (userService.service_price + ' ₽')) + ' <small class="text-muted">({{ __('messages.base_price') }})</small>';
        
        const durationValue = userService.duration ? 
            formatDuration(userService.duration) :
            (userService.service_duration ? formatDuration(userService.service_duration) + ' <small class="text-muted">({{ __('messages.base_duration') }})</small>' : '{{ __('messages.not_specified_duration') }}');
        
        const statusBadge = userService.is_active_for_booking ? 
            '<span class="status-badge active">{{ __('messages.active') }}</span>' :
            '<span class="status-badge inactive">{{ __('messages.inactive') }}</span>';
        
        // Обновляем содержимое карточки
        const masterElement = card.querySelector('.user-service-master');
        const serviceElement = card.querySelector('.user-service-name');
        const statusElement = card.querySelector('.user-service-status');
        const priceElement = card.querySelector('.user-service-info-value.currency-amount');
        const durationElement = card.querySelector('.user-service-info-item:last-child .user-service-info-value');
        
        console.log('Найденные элементы:', {
            masterElement: !!masterElement,
            serviceElement: !!serviceElement,
            statusElement: !!statusElement,
            priceElement: !!priceElement,
            durationElement: !!durationElement
        });
        
        if (masterElement) {
            masterElement.textContent = userService.user_name;
            console.log('Обновлен мастер:', userService.user_name);
        }
        if (serviceElement) {
            serviceElement.textContent = userService.service_name;
            console.log('Обновлена услуга:', userService.service_name);
        }
        if (statusElement) {
            statusElement.innerHTML = statusBadge;
            console.log('Обновлен статус:', statusBadge);
        }
        if (priceElement) {
            priceElement.setAttribute('data-amount', userService.price || userService.service_price);
            priceElement.innerHTML = priceValue;
            console.log('Обновлена цена:', priceValue);
        }
        if (durationElement) {
            durationElement.innerHTML = durationValue;
            console.log('Обновлена длительность:', durationValue);
        }
    } else {
        console.log('Мобильная карточка не найдена для услуги:', userService.id);
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
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateMastersList(data.userServices);
            updateServicesList(data.userServices);
        } else {
            console.error('Ошибка при получении данных:', data.message || 'Неизвестная ошибка');
        }
    })
    .catch(error => {
        console.error('Ошибка при обновлении статистики:', error);
        // Не показываем уведомление пользователю, так как это фоновое обновление
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
            
            if (servicesCount > 0 && minPrice > 0) {
                const formattedPrice = formatCurrency(minPrice);
                detailsElement.innerHTML = `${servicesCount} ${servicesCount == 1 ? 'услуга' : (servicesCount < 5 ? 'услуги' : 'услуг')} • от <span class="currency-amount" data-amount="${minPrice}">${formattedPrice}</span>`;
            } else {
                detailsElement.textContent = `${servicesCount} ${servicesCount == 1 ? 'услуга' : (servicesCount < 5 ? 'услуги' : 'услуг')}`;
            }
            
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
            // Вычисляем среднюю цену с учетом активных цен мастеров
            let avgPrice = 0;
            if (activeMasters.length > 0) {
                let totalPrice = 0;
                let validPrices = 0;
                activeMasters.forEach(master => {
                    const activePrice = master.price || master.service_price || 0;
                    if (activePrice > 0) {
                        totalPrice += activePrice;
                        validPrices++;
                    }
                });
                avgPrice = validPrices > 0 ? totalPrice / validPrices : 0;
            }
            const masterNames = activeMasters.map(m => m.user_name).join(', ');
            
            const detailsElement = item.querySelector('.service-details');
            const statusElement = item.querySelector('.service-status');
            
            if (mastersCount > 0 && avgPrice > 0) {
                const formattedPrice = formatCurrency(avgPrice);
                detailsElement.innerHTML = `${masterNames} • <span class="currency-amount" data-amount="${avgPrice}">${formattedPrice}</span>`;
            } else if (mastersCount > 0) {
                detailsElement.textContent = masterNames;
            } else {
                detailsElement.textContent = 'Нет мастеров';
            }
            
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

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Переключаем вид услуг мастеров
    toggleUserServicesView();
    
    // Добавляем обработчик изменения размера окна
    window.addEventListener('resize', function() {
        toggleUserServicesView();
    });
});
</script>
@endpush 