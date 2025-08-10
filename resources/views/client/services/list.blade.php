@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>{{ __('messages.services') }}</h1>
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
                <!-- Данные будут загружены через JavaScript -->
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
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.confirm_delete') }}</h2>
                <span class="close" onclick="document.getElementById('confirmationModal').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.confirm_delete_service') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelDelete">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn-delete" id="confirmDelete">{{ __('messages.delete') }}</button>
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

@push('scripts')
    <script>
        // Переводы для JavaScript
        window.translations = {
            service_duration_hours_short: '{{ __("messages.service_duration_hours_short") }}',
            service_duration_minutes_short: '{{ __("messages.service_duration_minutes_short") }}',
            edit: '{{ __("messages.edit") }}',
            delete: '{{ __("messages.delete") }}',
            cancel: '{{ __("messages.cancel") }}',
            save: '{{ __("messages.save") }}',
            confirm_delete: '{{ __("messages.confirm_delete") }}',
            confirm_delete_service: '{{ __("messages.confirm_delete_service") }}'
        };
        
        
    </script>
    <script src="{{ asset('client/js/services.js') }}"></script>
@endpush
</div>
@endsection

