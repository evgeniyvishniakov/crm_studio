@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>{{ __('messages.services') }}</h1>
            <div id="notification" class="notification alert alert-success" role="alert">
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <span class="notification-message">{{ __('messages.service_successfully_added') }}!</span>
            </div>
            <div class="services-header-actions">
                <button class="btn-add-service" onclick="openServiceModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.add_service') }}
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="{{ __('messages.search_placeholder') }}" onkeyup="handleSearch()">
                </div>
            </div>
        </div>

        <!-- Десктопная таблица -->
        <div class="table-wrapper">
            <table class=" table-striped services-table">
                <thead>
                <tr>
                    <th>{{ __('messages.service_name') }}</th>
                    <th>{{ __('messages.service_price') }}</th>
                    <th>{{ __('messages.service_duration') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="servicesTableBody">
                @foreach($services as $service)
                    <tr id="service-{{ $service->id }}">
                        <td>{{ $service->name }}</td>
                        <td class="currency-amount" data-amount="{{ $service->price }}">{{ $service->price ? ($service->price == (int)$service->price ? (int)$service->price : number_format($service->price, 2, '.', '')) . ' грн' : '—' }}</td>
                        <td>
                            @php
                                $hours = intdiv($service->duration, 60);
                                $minutes = $service->duration % 60;
                            @endphp
                            @if($service->duration > 0)
                                @if($hours > 0) {{ $hours }} {{ __('messages.hours_short') }} @endif @if($minutes > 0) {{ $minutes }} {{ __('messages.minutes_short') }} @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="actions-cell">
                            <button class="btn-edit" onclick="openEditModal({{ $service->id }})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                {{ __('messages.edit_short') }}
                            </button>
                            <button class="btn-delete">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ __('messages.delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            
            <!-- Пагинация будет добавлена через JavaScript -->
            <div id="servicesPagination"></div>
        </div>

        <!-- Мобильные карточки услуг -->
        <div class="services-cards" id="servicesCards">
            <!-- Карточки будут добавлены через JavaScript -->
        </div>

        <!-- Пагинация для мобильных карточек -->
        <div id="mobileServicesPagination"></div>
    </div>

    <!-- Модальное окно для добавления услуги -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_new_service') }}</h2>
                <span class="close" onclick="closeServiceModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">{{ __('messages.service_name') }} *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-row">
                    <div class="form-group">
                        <label for="servicePrice">{{ __('messages.service_price') }}</label>
                        <input type="number" id="servicePrice" name="price" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.service_duration') }}</label>
                            <div class="duration-fields">
                                <input type="number" name="duration_hours" min="0" max="12" value="0" class="duration-field" placeholder="{{ __('messages.service_duration_hours') }}">
                                <span class="duration-label">{{ __('messages.hours_short') }}</span>
                                <input type="number" name="duration_minutes" min="0" max="59" value="0" class="duration-field" placeholder="{{ __('messages.service_duration_minutes') }}">
                                <span class="duration-label">{{ __('messages.minutes_short') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeServiceModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add_service') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>{{ __('messages.confirm_delete_service') }}</h3>
            <p>{{ __('messages.confirm_delete_service') }}</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">{{ __('messages.cancel') }}</button>
                <button id="confirmDelete" class="confirm-btn">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования услуги -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_service') }}</h2>
                <span class="close" onclick="closeEditServiceModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editServiceId" name="id">
                    <div class="form-group">
                        <label for="editServiceName">{{ __('messages.service_name') }} *</label>
                        <input type="text" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-row">
                    <div class="form-group">
                        <label for="editServicePrice">{{ __('messages.service_price') }}</label>
                        <input type="number" id="editServicePrice" name="price" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.service_duration') }}</label>
                            <div class="duration-fields">
                                <input type="number" name="duration_hours" min="0" max="12" value="0" class="duration-field" placeholder="{{ __('messages.service_duration_hours') }}">
                                <span class="duration-label">{{ __('messages.hours_short') }}</span>
                                <input type="number" name="duration_minutes" min="0" max="59" value="0" class="duration-field" placeholder="{{ __('messages.service_duration_minutes') }}">
                                <span class="duration-label">{{ __('messages.minutes_short') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditServiceModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('client/js/services.js') }}"></script>
</div>
@endsection

