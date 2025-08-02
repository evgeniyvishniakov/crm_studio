@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>{{ __('messages.client_types') }}</h1>
            {{-- Удаляю старый notification-контейнер и стили уведомлений, теперь используется универсальный notification --}}
            <div class="client-types-header-actions">
                <button class="btn-add-service" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.add_client_type') }}
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="{{ __('messages.search') }}..." onkeyup="handleSearch()">
                </div>
            </div>
        </div>

        <!-- Десктопная таблица -->
        <div class="table-wrapper">
            <table class=" table-striped services-table">
                <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.discount') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="servicesTableBody">
                @foreach($clientTypes as $clientType)
                    <tr id="client-type-{{ $clientType->id }}">
                        <td>{{ $clientType->translated_name }}</td>
                        <td>{{ $clientType->description ?? '—' }}</td>
                        <td>
                            @if($clientType->discount !== null)
                                {{ $clientType->discount == (int)$clientType->discount ? (int)$clientType->discount : number_format($clientType->discount, 2, '.', '') }}%
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $clientType->status ? 'active' : 'inactive' }}">
                                {{ $clientType->status ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            @if(!$clientType->is_global)
                                <button class="btn-edit" onclick="openEditModal({{ $clientType->id }})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    {{ __('messages.edit_short') }}
                                </button>
                                <button class="btn-delete" onclick="showDeleteConfirmation({{ $clientType->id }})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('messages.delete') }}
                                </button>
                            @else
                                <span class="text-muted">{{ __('messages.system') }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Мобильные карточки типов клиентов -->
        <div class="client-types-cards" id="clientTypesCards" style="display: none;">
            @foreach($clientTypes as $clientType)
                <div class="client-type-card" id="client-type-card-{{ $clientType->id }}">
                    <div class="client-type-card-header">
                        <div class="client-type-main-info">
                            <h3 class="client-type-name">{{ $clientType->translated_name }}</h3>
                            <span class="status-badge {{ $clientType->status ? 'active' : 'inactive' }}">
                                {{ $clientType->status ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </div>
                    </div>
                    <div class="client-type-info">
                        <div class="client-type-info-item">
                            <span class="client-type-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Описание
                            </span>
                            <span class="client-type-info-value">{{ $clientType->description ?? '—' }}</span>
                        </div>
                        <div class="client-type-info-item">
                            <span class="client-type-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                                </svg>
                                Скидка
                            </span>
                            <span class="client-type-info-value">
                                @if($clientType->discount !== null)
                                    {{ $clientType->discount == (int)$clientType->discount ? (int)$clientType->discount : number_format($clientType->discount, 2, '.', '') }}%
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                    </div>
                    @if(!$clientType->is_global)
                        <div class="client-type-actions">
                            <button class="btn-edit" title="Редактировать" onclick="openEditModal({{ $clientType->id }})">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Редактировать
                            </button>
                            <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation({{ $clientType->id }})">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Удалить
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Пагинация для мобильных карточек -->
        <div id="mobileClientTypesPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно для добавления типа клиента -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_new_client_type') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">{{ __('messages.name') }} *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceDescription">{{ __('messages.description') }}</label>
                        <textarea id="serviceDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="serviceDiscount">{{ __('messages.discount') }} (%)</label>
                        <input type="number" id="serviceDiscount" name="discount" min="0" max="100" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="serviceStatus">{{ __('messages.status') }}</label>
                        <select id="serviceStatus" name="status">
                            <option value="1">{{ __('messages.active') }}</option>
                            <option value="0">{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add') }}</button>
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
                <p>{{ __('messages.are_you_sure_you_want_to_delete_this_client_type') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelDelete">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn-delete" id="confirmDelete">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования типа клиента -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_client_type') }}</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editServiceId" name="id">
                    <div class="form-group">
                        <label for="editServiceName">{{ __('messages.name') }} *</label>
                        <input type="text" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editServiceDescription">{{ __('messages.description') }}</label>
                        <textarea id="editServiceDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editServiceDiscount">{{ __('messages.discount') }} (%)</label>
                        <input type="number" id="editServiceDiscount" name="discount" min="0" max="100" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="editServiceStatus">{{ __('messages.status') }}</label>
                        <select id="editServiceStatus" name="status">
                            <option value="1">{{ __('messages.active') }}</option>
                            <option value="0">{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('client/js/client-types.js') }}"></script>
@endpush
@endsection
