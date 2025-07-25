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

                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="booking_interval">Интервал записи</label>
                            <select class="form-control" id="booking_interval" name="booking_interval">
                                <option value="15" {{ $bookingSettings->booking_interval == 15 ? 'selected' : '' }}>15 минут</option>
                                <option value="30" {{ $bookingSettings->booking_interval == 30 ? 'selected' : '' }}>30 минут</option>
                                <option value="45" {{ $bookingSettings->booking_interval == 45 ? 'selected' : '' }}>45 минут</option>
                                <option value="60" {{ $bookingSettings->booking_interval == 60 ? 'selected' : '' }}>1 час</option>
                            </select>
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

                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="working_hours_start">Начало рабочего дня</label>
                            <input type="time" class="form-control" id="working_hours_start" name="working_hours_start" 
                                   value="{{ $bookingSettings->working_hours_start_formatted }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="working_hours_end">Конец рабочего дня</label>
                            <input type="time" class="form-control" id="working_hours_end" name="working_hours_end" 
                                   value="{{ $bookingSettings->working_hours_end_formatted }}">
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
                        <label for="booking_instructions">Инструкции для клиентов</label>
                        <textarea class="form-control" id="booking_instructions" name="booking_instructions" rows="4" 
                                  placeholder="Дополнительные инструкции для клиентов...">{{ $bookingSettings->booking_instructions }}</textarea>
                    </div>
                </div>

                                 <div class="form-row form-row--2col" style="display: flex; gap: 20px; margin-top: 30px;">
                     <div class="form-col" style="flex: 1;">
                         <div class="form-group mb-3">
                             <h6 style="margin-bottom: 15px; color: #333; font-weight: 600;">
                                 <i class="fas fa-users" style="margin-right: 8px; color: #007bff;"></i>
                                 Мастера ({{ $users->count() }})
                             </h6>
                                                           <div class="list-group">
                                  @foreach($users as $user)
                                      @php
                                          $userActiveServices = $activeUserServices->where('user_id', $user->id);
                                          $servicesCount = $userActiveServices->count();
                                      @endphp
                                      <div class="list-group-item d-flex justify-content-between align-items-center" style="border: 1px solid #dee2e6; border-radius: 4px; margin-bottom: 8px; padding: 12px 15px; background: #fff;">
                                          <div>
                                              <span style="font-weight: 500; color: #333;">{{ $user->name }}</span>
                                              <br>
                                              <small class="text-muted">
                                                  {{ $servicesCount }} {{ $servicesCount == 1 ? 'услуга' : ($servicesCount < 5 ? 'услуги' : 'услуг') }} для записи
                                              </small>
                                          </div>
                                          <div>
                                              <span class="badge badge-primary badge-pill">{{ $user->role }}</span>
                                              @if($servicesCount > 0)
                                                  <span class="badge badge-success badge-pill ml-1">Активен</span>
                                              @else
                                                  <span class="badge badge-secondary badge-pill ml-1">Неактивен</span>
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
                                                           <div class="list-group">
                                  @foreach($services as $service)
                                      @php
                                          $serviceActiveMasters = $activeUserServices->where('service_id', $service->id);
                                          $mastersCount = $serviceActiveMasters->count();
                                      @endphp
                                      <div class="list-group-item d-flex justify-content-between align-items-center" style="border: 1px solid #dee2e6; border-radius: 4px; margin-bottom: 8px; padding: 12px 15px; background: #fff;">
                                          <div>
                                              <span style="font-weight: 500; color: #333;">{{ $service->name }}</span>
                                              <br>
                                              <small class="text-muted">
                                                  {{ $mastersCount }} {{ $mastersCount == 1 ? 'мастер' : ($mastersCount < 5 ? 'мастера' : 'мастеров') }} оказывает
                                              </small>
                                          </div>
                                          <div>
                                              <span class="badge badge-success badge-pill">{{ number_format($service->price) }} ₽</span>
                                              @if($mastersCount > 0)
                                                  <span class="badge badge-primary badge-pill ml-1">Доступна</span>
                                              @else
                                                  <span class="badge badge-secondary badge-pill ml-1">Недоступна</span>
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
            <h5>Настройки расписания мастеров</h5>
            
            <div class="form-row form-row--2col">
                <div class="form-col">
                    <div class="form-group mb-3">
                        <label for="user-select">Выберите мастера</label>
                        <select class="form-control" id="user-select">
                            <option value="">Выберите мастера...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-col"></div>
            </div>

            <!-- Расписание -->
            <div id="schedule-container" style="display: none;">
                <div class="form-row">
                    <div class="form-col">
                        <h6>Расписание на неделю</h6>
                        
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

         <!-- Вкладка услуг мастеров -->
         <div class="settings-pane" id="tab-user-services" style="display: none;">
             <h5>Управление услугами мастеров</h5>
             
             <div class="form-row">
                 <div class="form-group mb-3">
                     <button type="button" class="btn btn-primary" onclick="addUserService()">
                         <i class="fas fa-plus"></i> Добавить услугу мастеру
                     </button>
                 </div>
             </div>

             <!-- Таблица услуг мастеров -->
             <div class="table-responsive">
                 <table class="table table-bordered">
                     <thead class="thead-light">
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
                         @foreach($activeUserServices as $userService)
                             <tr data-user-service-id="{{ $userService->id }}">
                                 <td>{{ $userService->user->name }}</td>
                                 <td>{{ $userService->service->name }}</td>
                                 <td>{{ $userService->active_price }} ₽</td>
                                 <td>{{ $userService->active_duration }} мин</td>
                                 <td>
                                     @if($userService->is_active_for_booking)
                                         <span class="badge badge-success">Активна</span>
                                     @else
                                         <span class="badge badge-secondary">Неактивна</span>
                                     @endif
                                 </td>
                                 <td>
                                     <button type="button" class="btn btn-sm btn-outline-primary" onclick="editUserService({{ $userService->id }})">
                                         <i class="fas fa-edit"></i>
                                     </button>
                                     <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUserService({{ $userService->id }})">
                                         <i class="fas fa-trash"></i>
                                     </button>
                                 </td>
                             </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>

             @if($activeUserServices->count() == 0)
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
        <div style="text-align: right; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Отмена</button>
            <button type="button" class="btn btn-primary" onclick="saveDaySchedule()">Сохранить</button>
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
        <div style="text-align: right; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="closeUserServiceModal()">Отмена</button>
            <button type="button" class="btn btn-primary" onclick="saveUserService()">Сохранить</button>
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
    const tabButtons = document.querySelectorAll('.tab-button');
    const settingsPanes = document.querySelectorAll('.settings-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Убираем активный класс со всех кнопок
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Добавляем активный класс к текущей кнопке
            this.classList.add('active');
            
            // Скрываем все панели
            settingsPanes.forEach(pane => pane.style.display = 'none');
            // Показываем нужную панель
            document.getElementById('tab-' + targetTab).style.display = 'block';
        });
    });
    
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
            
            fetch('{{ route("client.booking.update-settings") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
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
                     if (formData.get('booking_enabled')) {
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
}

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
        showNotification('error', 'Укажите время начала и окончания работы');
        return;
    }
    
    if (isWorking && startTime >= endTime) {
        console.error('Неправильное время работы');
        showNotification('error', 'Время окончания должно быть позже времени начала');
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
    closeModal();
    
    // Обновляем таблицу
    renderScheduleTable();
    
    console.log('Расписание обновлено');
    showNotification('success', 'Расписание обновлено');
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
    document.getElementById('user-service-id').value = '';
    document.getElementById('modal-user-id').value = '';
    document.getElementById('modal-service-id').value = '';
    document.getElementById('modal-is-active').checked = true;
    document.getElementById('modal-price').value = '';
    document.getElementById('modal-duration').value = '';
    document.getElementById('modal-description').value = '';
    
    document.getElementById('userServiceModalTitle').textContent = 'Добавить услугу мастеру';
    
    // Простое отображение модального окна
    const modal = document.getElementById('userServiceModal');
    modal.style.display = 'block';
}

function editUserService(userServiceId) {
    // Здесь нужно загрузить данные связи и открыть модальное окно
    // Пока что просто показываем уведомление
    showNotification('info', 'Функция редактирования будет добавлена позже');
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
    })
    .catch(error => {
        console.error('Ошибка при удалении:', error);
        showNotification('error', 'Произошла ошибка при удалении');
    });
    currentDeleteUserServiceId = null; // Сбрасываем ID для следующего удаления
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            closeUserServiceModal();
            
            // Если это новая запись, добавляем её в таблицу
            if (!userServiceId && data.userService) {
                addUserServiceToTable(data.userService);
            }
            
            // Обновляем статистику в первой вкладке
            updateStatistics();
            
            // Очищаем форму
            document.getElementById('user-service-form').reset();
            document.getElementById('modal-is-active').checked = true;
        } else {
            showNotification('error', 'Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        showNotification('error', 'Произошла ошибка при сохранении');
    });
}

// Функция для добавления новой услуги в таблицу
function addUserServiceToTable(userService) {
    const tbody = document.getElementById('user-services-tbody');
    
    // Создаем новую строку
    const row = document.createElement('tr');
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
        <td>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editUserService(${userService.id})">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUserService(${userService.id})">
                <i class="fas fa-trash"></i>
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

// Функция для обновления статистики в первой вкладке
function updateStatistics() {
    // Пока что просто показываем уведомление об успешном обновлении
    // В будущем можно добавить AJAX запрос для обновления статистики без перезагрузки
    console.log('Статистика обновлена');
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
</script>
@endpush 