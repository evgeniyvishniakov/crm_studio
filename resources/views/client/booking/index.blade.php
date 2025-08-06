@extends('client.layouts.app')

@section('title', __('messages.web_booking'))

@section('content')

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
                                       {{ $bookingSettings->booking_enabled ? 'checked' : '' }}>
                                <label class="custom-control-label" for="booking_enabled">
                                    <strong>{{ __('messages.enable_web_booking') }}</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                                         <div class="form-col">
                         <div class="alert alert-info" id="booking-url-block" style="{{ $bookingSettings->booking_enabled ? 'display: block;' : 'display: none;' }}">
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

                <div class="form-row form-row--2col">
                    <div class="form-col">
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
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="require_confirmation" name="require_confirmation" 
                                       {{ $bookingSettings->require_confirmation ? 'checked' : '' }}>
                                <label class="custom-control-label" for="require_confirmation">
                                    {{ __('messages.require_confirmation') }}
                                </label>
                            </div>
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
                                     @if($user)
                                         @php
                                             $userActiveServices = $userServices->where('user_id', $user->id)->where('is_active_for_booking', true);
                                         @endphp
                                     <div class="master-item">
                                         <div class="master-info">
                                             <div class="master-name">{{ $user->name ?? __('messages.deleted_user') }}</div>
                                             <div class="master-details">
                                                 {{ $userActiveServices->count() }} {{ $userActiveServices->count() == 1 ? __('messages.service') : ($userActiveServices->count() < 5 ? __('messages.services_2') : __('messages.services_5')) }}
                                                 @if($userActiveServices->count() > 0 && $userActiveServices->min('price') > 0)
                                                     • {{ __('messages.from') }} <span class="currency-amount" data-amount="{{ $userActiveServices->min('price') }}">{{ \App\Helpers\CurrencyHelper::formatWithoutThousands($userActiveServices->min('price')) }}</span>
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
                                 @endif
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
                                                     {{ $masterNames }} • <span class="currency-amount" data-amount="{{ $avgPrice }}">{{ \App\Helpers\CurrencyHelper::formatWithoutThousands($avgPrice) }}</span>
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
                <div class="header-top">
                    <h1>{{ __('messages.schedule_settings') }}</h1>
                    <div class="header-actions">
                        <div class="form-group mb-3" style="margin-bottom: 0;">
                            <label for="user-select" style="margin-bottom: 8px; font-weight: 600; color: #333;">{{ __('messages.select_master') }}</label>
                            <select class="form-control" id="user-select" style="min-width: 250px; border-radius: 8px; border: 1px solid #d1d5db; padding: 8px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease;">
                                <option value="">{{ __('messages.select_master_placeholder') }}</option>
                                @foreach($users as $user)
                                    @if($user)
                                        <option value="{{ $user->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $user->name ?? __('messages.deleted_user') }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
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
                 <div class="header-top">
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
                                 <td class="currency-amount" data-amount="{{ $userService->price ?: ($userService->service ? $userService->service->price : 0) }}">{!! $userService->price ? \App\Helpers\CurrencyHelper::formatWithoutThousands($userService->price) : ($userService->service ? \App\Helpers\CurrencyHelper::formatWithoutThousands($userService->service->price) : __('messages.not_specified_price')) !!}</td>
                                 <td>{!! $userService->duration ? \App\Helpers\TimeHelper::formatDuration($userService->duration) : ($userService->service && $userService->service->duration ? \App\Helpers\TimeHelper::formatDuration($userService->service->duration) : __('messages.not_specified_duration')) !!}</td>
                                 <td>
                                     @if($userService->is_active_for_booking)
                                         <span class="status-badge active">{{ __('messages.active') }}</span>
                                     @else
                                         <span class="status-badge inactive">{{ __('messages.inactive') }}</span>
                                     @endif
                                 </td>
                                 <td class="actions-cell">
                                     <button type="button" class="btn-edit" onclick="editUserService({{ $userService->id }})" title="{{ __('messages.edit') }}">
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
                                     {!! $userService->price ? \App\Helpers\CurrencyHelper::formatWithoutThousands($userService->price) : ($userService->service ? \App\Helpers\CurrencyHelper::formatWithoutThousands($userService->service->price) : __('messages.not_specified_price')) !!}
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
                                     {!! $userService->duration ? \App\Helpers\TimeHelper::formatDuration($userService->duration) : ($userService->service && $userService->service->duration ? \App\Helpers\TimeHelper::formatDuration($userService->service->duration) : __('messages.not_specified_duration')) !!}
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
            <span style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeEditDayModal()">&times;</span>
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
            <button type="button" class="btn-cancel" onclick="closeEditDayModal()">{{ __('messages.cancel') }}</button>
            <button type="button" class="btn-save" onclick="saveDaySchedule()">{{ __('messages.save') }}</button>
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
            <button type="button" class="btn-save" onclick="saveUserService()">{{ __('messages.save') }}</button>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>{{ __('messages.confirm_delete') }}</h2>
            <span class="close" onclick="closeModal('confirmationModal')">&times;</span>
        </div>
        <div class="modal-body">
            <p>{{ __('messages.confirm_delete_connection') }}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" id="cancelDelete">{{ __('messages.cancel') }}</button>
            <button type="button" class="btn-delete" id="confirmDelete">{{ __('messages.delete') }}</button>
        </div>
    </div>
</div>

<!-- Уведомления создаются динамически глобальной функцией -->



@push('scripts')
<script src="{{ asset('client/js/booking.js') }}"></script>
<script src="{{ asset('client/js/booking-services.js') }}"></script>
@endpush
@endsection