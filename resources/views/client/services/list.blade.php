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
                <button class="btn-add-service" onclick="openModal()">
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
                            <button class="btn-edit">
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
        <div class="services-cards" id="servicesCards" style="display: none;">
            <!-- Карточки будут добавлены через JavaScript -->
        </div>

        <!-- Пагинация для мобильных карточек -->
        <div id="mobileServicesPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно для добавления услуги -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_new_service') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">{{ __('messages.service_name') }} *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="servicePrice">{{ __('messages.service_price') }}</label>
                        <input type="number" id="servicePrice" name="price" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.service_duration') }}</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="number" name="duration_hours" min="0" max="12" value="0" style="width: 60px;" placeholder="{{ __('messages.service_duration_hours') }}">
                            <span>{{ __('messages.hours_short') }}</span>
                            <input type="number" name="duration_minutes" min="0" max="59" value="0" style="width: 60px;" placeholder="{{ __('messages.service_duration_minutes') }}">
                            <span>{{ __('messages.minutes_short') }}</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
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
                <span class="close" onclick="closeEditModal()">&times;</span>
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
                    <div class="form-group">
                        <label for="editServicePrice">{{ __('messages.service_price') }}</label>
                        <input type="number" id="editServicePrice" name="price" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.service_duration') }}</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="number" name="duration_hours" min="0" max="12" value="0" style="width: 60px;" placeholder="{{ __('messages.service_duration_hours') }}">
                            <span>{{ __('messages.hours_short') }}</span>
                            <input type="number" name="duration_minutes" min="0" max="59" value="0" style="width: 60px;" placeholder="{{ __('messages.service_duration_minutes') }}">
                            <span>{{ __('messages.minutes_short') }}</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Функция форматирования валюты
        function formatCurrency(value) {
            if (window.CurrencyManager) {
                return window.CurrencyManager.formatAmount(value);
            } else {
                value = parseFloat(value);
                if (isNaN(value)) return '0';
                return (value % 1 === 0 ? value.toFixed(0) : value.toFixed(2)) + ' грн';
            }
        }

        // Функции для работы с модальным окном
        function openModal() {
            document.getElementById('addServiceModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addServiceModal').style.display = 'none';
            clearErrors();
        }

        function closeEditModal() {
            document.getElementById('editServiceModal').style.display = 'none';
            clearErrors('editServiceForm');
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            if (event.target == document.getElementById('addServiceModal')) {
                closeModal();
            }
            if (event.target == document.getElementById('editServiceModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
                currentDeleteRow = null;
                currentDeleteId = null;
            }
        }

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

        // Добавление новой услуги
        document.getElementById('addServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const servicesTableBody = document.getElementById('servicesTableBody');

            clearErrors();

                                    submitBtn.innerHTML = '<span class="loader"></span> {{ __('messages.add_service') }}...';
            submitBtn.disabled = true;

            fetch("/services", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.service) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `service-${data.service.id}`;

                        newRow.innerHTML = `
                            <td>${data.service.name}</td>
                            <td class="currency-amount" data-amount="${data.service.price}">${data.service.price ? formatCurrency(data.service.price) : '—'}</td>
                            <td>${formatDuration(data.service.duration)}</td>
                            <td class="actions-cell">
                                <button class="btn-edit">
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
                        `;

                        // Добавляем новую строку в начало таблицы
                        servicesTableBody.insertBefore(newRow, servicesTableBody.firstChild);

                        // Создаем новую карточку для мобильной версии
                        const servicesCards = document.getElementById('servicesCards');
                        const newCard = document.createElement('div');
                        newCard.className = 'service-card';
                        newCard.id = `service-card-${data.service.id}`;
                        
                        newCard.innerHTML = `
                            <div class="service-card-header">
                                <div class="service-main-info">
                                    <h3 class="service-name">${data.service.name}</h3>
                                </div>
                            </div>
                            <div class="service-info">
                                <div class="service-info-item">
                                    <span class="service-info-label">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                        Цена
                                    </span>
                                    <span class="service-info-value">${data.service.price ? formatCurrency(data.service.price) : '—'}</span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        Длительность
                                    </span>
                                    <span class="service-info-value">${formatDuration(data.service.duration)}</span>
                                </div>
                            </div>
                            <div class="service-actions">
                                <button class="btn-edit" title="Редактировать" onclick="openEditModal(${data.service.id})">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Изменить
                                </button>
                                <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${data.service.id})">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Удалить
                                </button>
                            </div>
                        `;

                        // Добавляем новую карточку в начало мобильного списка
                        if (servicesCards) {
                            servicesCards.insertBefore(newCard, servicesCards.firstChild);
                        }

                        // Показываем уведомление
                        window.showNotification('success', `{{ __('messages.service_successfully_added') }} "${data.service.name}"`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('{{ __('messages.error_loading_service_data') }}');
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors);
                        window.showNotification('error', '{{ __('messages.please_fix_form_errors') }}');
                    } else {
                        window.showNotification('error', error.message || '{{ __('messages.error_adding_service') }}');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Глобальные переменные для удаления
        let currentDeleteRow = null;
        let currentDeleteId = null;

        // Функция для показа модального окна подтверждения удаления
        function showDeleteConfirmation(serviceId) {
            currentDeleteRow = null;
            currentDeleteId = serviceId;
            document.getElementById('confirmationModal').style.display = 'block';
        }

        // Обработчик клика по кнопке удаления
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                const row = e.target.closest('tr');
                const card = e.target.closest('.service-card');
                
                let serviceId = null;
                
                if (row && row.id) {
                    // Десктопная версия - получаем ID из строки таблицы
                    serviceId = row.id.split('-')[1];
                    currentDeleteRow = row;
                } else if (card && card.id) {
                    // Мобильная версия - получаем ID из карточки
                    serviceId = card.id.split('-')[2]; // service-card-{id}
                    currentDeleteRow = card;
                }
                
                if (serviceId) {
                    currentDeleteId = serviceId;
                    document.getElementById('confirmationModal').style.display = 'block';
                }
            }
        });

        // Обработчики для модального окна подтверждения
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (currentDeleteId) {
                deleteService(currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });

        // Функция для удаления услуги
        function deleteService(rowOrId, serviceId) {
            let row;
            let card;
            
            if (typeof rowOrId === 'object' && rowOrId !== null && 'classList' in rowOrId) {
                // Вызов с двумя аргументами: (row, serviceId)
                row = rowOrId;
            } else {
                // Вызов с одним аргументом: (serviceId)
                serviceId = rowOrId;
                row = document.getElementById('service-' + serviceId);
                card = document.getElementById('service-card-' + serviceId);
            }
            
            if (row) row.classList.add('row-deleting');
            if (card) card.classList.add('row-deleting');

            fetch(`/services/${serviceId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка при удалении');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        setTimeout(() => {
                            if (row) row.remove();
                            if (card) card.remove();
                            window.showNotification('success', '{{ __('messages.service_successfully_deleted') }}');
                        }, 300);
                    }
                })
                .catch(error => {
                    if (row) row.classList.remove('row-deleting');
                    if (card) card.classList.remove('row-deleting');
                    window.showNotification('error', 'Не удалось удалить услугу');
                });
        }

        // Функции для работы с модальным окном редактирования
        function openEditModal(serviceId) {
            fetch(`/services/${serviceId}/edit`)
                .then(response => response.json())
                .then(service => {
                    const form = document.getElementById('editServiceForm');
                    form.querySelector('#editServiceId').value = service.id;
                    form.querySelector('#editServiceName').value = service.name;
                    form.querySelector('#editServicePrice').value = service.price || '';

                    // Вычисляем и устанавливаем длительность
                    const duration = service.duration || 0;
                    const hours = Math.floor(duration / 60);
                    const minutes = duration % 60;
                    form.querySelector('[name="duration_hours"]').value = hours;
                    form.querySelector('[name="duration_minutes"]').value = minutes;


                    document.getElementById('editServiceModal').style.display = 'block';
                })
                .catch(error => {
                    window.showNotification('error', '{{ __('messages.error_loading_service_data') }}');
                });
        }

        // Обработчик клика по кнопке редактирования
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-edit')) {
                const row = e.target.closest('tr');
                const card = e.target.closest('.service-card');
                
                let serviceId = null;
                
                if (row && row.id) {
                    // Десктопная версия - получаем ID из строки таблицы
                    serviceId = row.id.split('-')[1];
                } else if (card && card.id) {
                    // Мобильная версия - получаем ID из карточки
                    serviceId = card.id.split('-')[2]; // service-card-{id}
                }
                
                if (serviceId) {
                    openEditModal(serviceId);
                }
            }
        });

        // Обработчик отправки формы редактирования
        if (document.getElementById('editServiceForm')) {
            document.getElementById('editServiceForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const serviceId = document.getElementById('editServiceId').value;
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<span class="loader"></span> {{ __('messages.save') }}...';
                submitBtn.disabled = true;

                fetch(`/services/${serviceId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            updateServiceRow(data.service);
                            window.showNotification('success', '{{ __('messages.service_successfully_updated') }}');
                            closeEditModal();
                        }
                    })
                    .catch(error => {
                        if (error.errors) {
                            showErrors(error.errors, 'editServiceForm');
                            window.showNotification('error', '{{ __('messages.please_fix_form_errors') }}');
                        } else {
                            window.showNotification('error', '{{ __('messages.error_updating_service') }}');
                        }
                    })
                    .finally(() => {
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    });
            });
        }

        // Функция для обновления строки услуги в таблице
        function updateServiceRow(service) {
            const row = document.getElementById(`service-${service.id}`);
            if (!row) return;

            const cells = row.querySelectorAll('td');
            if (cells.length >= 2) {
                cells[0].textContent = service.name;
                const priceCell = cells[1];
                priceCell.className = 'currency-amount';
                priceCell.setAttribute('data-amount', service.price);
                priceCell.textContent = service.price ? formatCurrency(service.price) : '—';
                cells[2].textContent = formatDuration(service.duration);
            }
            
            // Обновляем карточку услуги в мобильной версии
            const card = document.getElementById(`service-card-${service.id}`);
            if (card) {
                // Обновляем название
                const nameElement = card.querySelector('.service-name');
                if (nameElement) {
                    nameElement.textContent = service.name;
                }
                
                // Обновляем цены
                const priceElements = card.querySelectorAll('.service-info-value');
                if (priceElements.length >= 2) {
                    priceElements[0].textContent = service.price ? formatCurrency(service.price) : '—';
                    priceElements[1].textContent = formatDuration(service.duration);
                }
            }
        }

        // AJAX-пагинация
        let currentPage = 1;
        let searchQuery = '';

        function loadPage(page, search = '') {
            currentPage = page;
            searchQuery = search;
            
            const params = new URLSearchParams();
            if (page > 1) params.append('page', page);
            if (search) params.append('search', search);
            
            fetch(`/services?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка загрузки данных');
                }
                return response.json();
            })
            .then(data => {
                updateTable(data.data);
                renderPagination(data.meta);
            })
            .catch(error => {
                window.showNotification('error', '{{ __('messages.loading_data_error') }}');
            });
        }

        function updateTable(services) {
            const tbody = document.getElementById('servicesTableBody');
            const servicesCards = document.getElementById('servicesCards');
            
            tbody.innerHTML = '';
            servicesCards.innerHTML = '';

            services.forEach(service => {
                // Создаем строку для десктопной таблицы
                const row = document.createElement('tr');
                row.id = `service-${service.id}`;
                row.innerHTML = `
                    <td>${service.name}</td>
                    <td class="currency-amount" data-amount="${service.price}">${service.price ? formatCurrency(service.price) : '—'}</td>
                    <td>${formatDuration(service.duration)}</td>
                    <td class="actions-cell">
                        <button class="btn-edit">
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
                `;
                tbody.appendChild(row);

                // Создаем карточку для мобильной версии
                const card = document.createElement('div');
                card.className = 'service-card';
                card.id = `service-card-${service.id}`;
                
                card.innerHTML = `
                    <div class="service-card-header">
                        <div class="service-main-info">
                            <h3 class="service-name">${service.name}</h3>
                        </div>
                    </div>
                    <div class="service-info">
                        <div class="service-info-item">
                            <span class="service-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Цена
                            </span>
                            <span class="service-info-value">${service.price ? formatCurrency(service.price) : '—'}</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Длительность
                            </span>
                            <span class="service-info-value">${formatDuration(service.duration)}</span>
                        </div>
                    </div>
                    <div class="service-actions">
                        <button class="btn-edit" title="Редактировать" onclick="openEditModal(${service.id})">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Изменить
                        </button>
                        <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${service.id})">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Удалить
                        </button>
                    </div>
                `;
                
                servicesCards.appendChild(card);
            });
        }

        function renderPagination(meta) {
            let paginationHtml = '';
            if (meta.last_page > 1) {
                paginationHtml += '<div class="pagination">';
                // Кнопка "<"
                paginationHtml += `<button class="page-btn" data-page="${meta.current_page - 1}" ${meta.current_page === 1 ? 'disabled' : ''}>&lt;</button>`;

                let pages = [];
                if (meta.last_page <= 7) {
                    // Показываем все страницы
                    for (let i = 1; i <= meta.last_page; i++) pages.push(i);
                } else {
                    // Всегда показываем первую
                    pages.push(1);
                    // Если текущая страница > 4, показываем троеточие
                    if (meta.current_page > 4) pages.push('...');
                    // Показываем 2 страницы до и после текущей
                    let start = Math.max(2, meta.current_page - 2);
                    let end = Math.min(meta.last_page - 1, meta.current_page + 2);
                    for (let i = start; i <= end; i++) pages.push(i);
                    // Если текущая страница < last_page - 3, показываем троеточие
                    if (meta.current_page < meta.last_page - 3) pages.push('...');
                    // Всегда показываем последнюю
                    pages.push(meta.last_page);
                }
                pages.forEach(p => {
                    if (p === '...') {
                        paginationHtml += `<span class="page-ellipsis">...</span>`;
                    } else {
                        paginationHtml += `<button class="page-btn${p === meta.current_page ? ' active' : ''}" data-page="${p}">${p}</button>`;
                    }
                });
                // Кнопка ">"
                paginationHtml += `<button class="page-btn" data-page="${meta.current_page + 1}" ${meta.current_page === meta.last_page ? 'disabled' : ''}>&gt;</button>`;
                paginationHtml += '</div>';
            }
            let pagContainer = document.getElementById('servicesPagination');
            if (!pagContainer) {
                pagContainer = document.createElement('div');
                pagContainer.id = 'servicesPagination';
                document.querySelector('.table-wrapper').appendChild(pagContainer);
            }
            pagContainer.innerHTML = paginationHtml;

            // Обновляем мобильную пагинацию
            let mobilePagContainer = document.getElementById('mobileServicesPagination');
            if (mobilePagContainer) {
                mobilePagContainer.innerHTML = paginationHtml;
            }

            // Навешиваем обработчики
            document.querySelectorAll('.page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (!isNaN(page) && !this.disabled) {
                        loadPage(page, searchQuery);
                    }
                });
            });
        }

        function handleSearch() {
            const searchInput = document.getElementById('searchInput');
            const query = searchInput.value.trim();
            
            // Сбрасываем на первую страницу при поиске
            loadPage(1, query);
        }

        // Обновляем функцию удаления услуги для обновления таблицы
        function deleteService(rowOrId, serviceId) {
            let row;
            let card;
            
            if (typeof rowOrId === 'object' && rowOrId !== null && 'classList' in rowOrId) {
                // Вызов с двумя аргументами: (row, serviceId)
                row = rowOrId;
            } else {
                // Вызов с одним аргументом: (serviceId)
                serviceId = rowOrId;
                row = document.getElementById('service-' + serviceId);
                card = document.getElementById('service-card-' + serviceId);
            }
            
            if (row) row.classList.add('row-deleting');
            if (card) card.classList.add('row-deleting');

            fetch(`/services/${serviceId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка при удалении');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        setTimeout(() => {
                            if (row) row.remove();
                            if (card) card.remove();
                            // Обновляем таблицу после удаления
                            loadPage(currentPage, searchQuery);
                            window.showNotification('success', '{{ __('messages.service_successfully_deleted') }}');
                        }, 300);
                    }
                })
                .catch(error => {
                    if (row) row.classList.remove('row-deleting');
                    if (card) card.classList.remove('row-deleting');
                    window.showNotification('error', 'Не удалось удалить услугу');
                });
        }

        // Функция для форматирования длительности
        function formatDuration(duration) {
            if (!duration || duration <= 0) return '—';
            const hours = Math.floor(duration / 60);
            const minutes = duration % 60;
            let result = '';
            if (hours > 0) result += hours + ' {{ __('messages.hours_short') }} ';
            if (minutes > 0) result += minutes + ' {{ __('messages.minutes_short') }}';
            return result.trim();
        }

        // Функция для переключения между десктопной и мобильной версией
        function toggleMobileView() {
            const tableWrapper = document.querySelector('.table-wrapper');
            const servicesCards = document.getElementById('servicesCards');
            const servicesPagination = document.getElementById('servicesPagination');
            const mobileServicesPagination = document.getElementById('mobileServicesPagination');
            
            if (window.innerWidth <= 768) {
                // Мобильная версия
                if (tableWrapper) tableWrapper.style.display = 'none';
                if (servicesCards) servicesCards.style.display = 'block';
                if (servicesPagination) servicesPagination.style.display = 'none';
                if (mobileServicesPagination) mobileServicesPagination.style.display = 'block';
            } else {
                // Десктопная версия
                if (tableWrapper) tableWrapper.style.display = 'block';
                if (servicesCards) servicesCards.style.display = 'none';
                if (servicesPagination) servicesPagination.style.display = 'block';
                if (mobileServicesPagination) mobileServicesPagination.style.display = 'none';
            }
        }

        // Инициализация первой загрузки
        let isInitialized = false;
        
        document.addEventListener('DOMContentLoaded', function() {
            if (!isInitialized) {
                isInitialized = true;
                loadPage(1);
                toggleMobileView(); // Переключаем на правильную версию
            }
        });

        // Обработчик изменения размера окна
        window.addEventListener('resize', function() {
            toggleMobileView();
        });
        
        // Функция для очистки полей длительности при фокусе
        function clearDurationFieldOnFocus() {
            const durationInputs = document.querySelectorAll('input[name="duration_hours"], input[name="duration_minutes"]');
            
            durationInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    if (this.value === '0') {
                        this.value = '';
                    }
                });
                
                input.addEventListener('blur', function() {
                    if (this.value === '' || this.value === null) {
                        this.value = '0';
                    }
                });
            });
        }
        
        // Инициализация очистки полей при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            clearDurationFieldOnFocus();
        });
        
        // Обновляем функцию openModal для добавления обработчиков к новым полям
        const originalOpenModal = openModal;
        openModal = function() {
            originalOpenModal();
            setTimeout(clearDurationFieldOnFocus, 100); // Небольшая задержка для создания полей
        };
        
        // Обновляем функцию openEditModal для добавления обработчиков к новым полям
        const originalOpenEditModal = openEditModal;
        openEditModal = function(serviceId) {
            originalOpenEditModal(serviceId);
            setTimeout(clearDurationFieldOnFocus, 100); // Небольшая задержка для создания полей
        };
    </script>
</div>
@endsection
