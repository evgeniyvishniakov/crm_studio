@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>{{ __('messages.product_brands') }}</h1>
            <div id="notification" class="notification alert alert-success" role="alert">
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <span class="notification-message">{{ __('messages.brand_successfully_added') }}!</span>
            </div>
            <div class="brands-header-actions">
                <button class="btn-add-service" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.add_brand') }}
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
            <table class="table-striped services-table">
                <thead>
                <tr>
                    <th>{{ __('messages.brand_name') }}</th>
                    <th>{{ __('messages.brand_country') }}</th>
                    <th>{{ __('messages.brand_website') }}</th>
                    <th>{{ __('messages.brand_status') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="servicesTableBody">
                @foreach($brands as $brand)
                    <tr id="brand-{{ $brand->id }}">
                        <td>{{ $brand->name }}</td>
                        <td>{{ $brand->country ?? '—' }}</td>
                        <td>
                            @if($brand->website)
                                <a href="{{ $brand->website }}" target="_blank">{{ parse_url($brand->website, PHP_URL_HOST) }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $brand->status ? 'active' : 'inactive' }}">
                                {{ $brand->status ? __('messages.brand_active') : __('messages.brand_inactive') }}
                            </span>
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
            <div id="brandsPagination"></div>
        </div>

        <!-- Мобильные карточки брендов -->
        <div class="brands-cards" id="brandsCards" style="display: none;">
            <!-- Карточки будут добавлены через JavaScript -->
        </div>

        <!-- Пагинация для мобильных карточек -->
        <div id="mobileBrandsPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно для добавления бренда -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_new_brand') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">{{ __('messages.brand_name') }} *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceCountry">{{ __('messages.brand_country') }}</label>
                        <input type="text" id="serviceCountry" name="country">
                    </div>
                    <div class="form-group">
                        <label for="serviceWebsite">{{ __('messages.brand_website') }}</label>
                        <input type="url" id="serviceWebsite" name="website" placeholder="https://example.com">
                    </div>
                    <div class="form-group">
                        <label for="serviceDescription">{{ __('messages.category_description') }}</label>
                        <textarea id="serviceDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="serviceStatus">{{ __('messages.brand_status') }}</label>
                        <select id="serviceStatus" name="status">
                            <option value="1">{{ __('messages.brand_active') }}</option>
                            <option value="0">{{ __('messages.brand_inactive') }}</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add_brand') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>{{ __('messages.confirm_delete_brand') }}</h3>
            <p>{{ __('messages.confirm_delete_brand') }}</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">{{ __('messages.cancel') }}</button>
                <button id="confirmDelete" class="confirm-btn">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования бренда -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_brand') }}</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editServiceId" name="id">
                    <div class="form-group">
                        <label for="editServiceName">{{ __('messages.brand_name') }} *</label>
                        <input type="text" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editServiceCountry">{{ __('messages.brand_country') }}</label>
                        <input type="text" id="editServiceCountry" name="country">
                    </div>
                    <div class="form-group">
                        <label for="editServiceWebsite">{{ __('messages.brand_website') }}</label>
                        <input type="url" id="editServiceWebsite" name="website" placeholder="https://example.com">
                    </div>
                    <div class="form-group">
                        <label for="editServiceDescription">{{ __('messages.category_description') }}</label>
                        <textarea id="editServiceDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editServiceStatus">{{ __('messages.brand_status') }}</label>
                        <select id="editServiceStatus" name="status">
                            <option value="1">{{ __('messages.brand_active') }}</option>
                            <option value="0">{{ __('messages.brand_inactive') }}</option>
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

    <script>
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

        // Добавление нового бренда
        document.getElementById('addServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const servicesTableBody = document.getElementById('servicesTableBody');

            clearErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("{{ route('product-brands.store') }}", {
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
                    if (data.success && data.brand) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `brand-${data.brand.id}`;

                        newRow.innerHTML = `
                            <td>${data.brand.name}</td>
                            <td>${data.brand.country ?? '—'}</td>
                            <td>
                                ${data.brand.website ? `<a href="${data.brand.website}" target="_blank">${new URL(data.brand.website).hostname}</a>` : '—'}
                            </td>
                            <td>
                                <span class="status-badge ${data.brand.status ? 'active' : 'inactive'}">
                                    ${data.brand.status ? 'Активен' : 'Неактивен'}
                                </span>
                            </td>
                            <td class="actions-cell">
                                <button class="btn-edit">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Удалить
                                </button>
                            </td>
                        `;

                        // Добавляем новую строку в начало таблицы
                        servicesTableBody.insertBefore(newRow, servicesTableBody.firstChild);

                        // Создаем новую карточку для мобильной версии
                        const brandsCards = document.getElementById('brandsCards');
                        const newCard = document.createElement('div');
                        newCard.className = 'brand-card';
                        newCard.id = `brand-card-${data.brand.id}`;
                        
                        const websiteValue = data.brand.website 
                            ? `<a href="${data.brand.website}" target="_blank">${new URL(data.brand.website).hostname}</a>`
                            : '—';
                        
                        newCard.innerHTML = `
                            <div class="brand-card-header">
                                <div class="brand-main-info">
                                    <h3 class="brand-name">${data.brand.name}</h3>
                                    <span class="status-badge ${data.brand.status ? 'active' : 'inactive'}">
                                        ${data.brand.status ? '{{ __('messages.brand_active') }}' : '{{ __('messages.brand_inactive') }}'}
                                    </span>
                                </div>
                            </div>
                            <div class="brand-info">
                                <div class="brand-info-item">
                                    <span class="brand-info-label">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Страна
                                    </span>
                                    <span class="brand-info-value">${data.brand.country ?? '—'}</span>
                                </div>
                                <div class="brand-info-item">
                                    <span class="brand-info-label">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                            <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                        </svg>
                                        Сайт
                                    </span>
                                    <span class="brand-info-value">${websiteValue}</span>
                                </div>
                                <div class="brand-info-item">
                                    <span class="brand-info-label">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                        Описание
                                    </span>
                                    <span class="brand-info-value">${data.brand.description ?? '—'}</span>
                                </div>
                            </div>
                            <div class="brand-actions">
                                <button class="btn-edit" title="Редактировать" onclick="openEditModal(${data.brand.id})">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Изменить
                                </button>
                                <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${data.brand.id})">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Удалить
                                </button>
                            </div>
                        `;

                        // Добавляем новую карточку в начало мобильного списка
                        if (brandsCards) {
                            brandsCards.insertBefore(newCard, brandsCards.firstChild);
                        }

                        // Показываем уведомление
                        window.showNotification('success', `{{ __('messages.brand_successfully_added') }} "${data.brand.name}"`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('{{ __('messages.error_loading_brand_data') }}');
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors);
                        window.showNotification('error', '{{ __('messages.please_fix_form_errors') }}');
                    } else {
                        window.showNotification('error', error.message || '{{ __('messages.error_adding_brand') }}');
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
        function showDeleteConfirmation(brandId) {
            currentDeleteRow = null;
            currentDeleteId = brandId;
            document.getElementById('confirmationModal').style.display = 'block';
        }

        // Обработчик клика по кнопке удаления
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                const element = e.target.closest('tr') || e.target.closest('.brand-card');
                let brandId;
                
                if (element.classList.contains('brand-card')) {
                    brandId = element.id.split('-')[2]; // brand-card-{id}
                } else {
                    brandId = element.id.split('-')[1]; // brand-{id}
                }

                currentDeleteRow = element;
                currentDeleteId = brandId;

                document.getElementById('confirmationModal').style.display = 'block';
            }
        });

        // Обработчики для модального окна подтверждения
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (currentDeleteRow && currentDeleteId) {
                deleteBrand(currentDeleteRow, currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Функция для удаления бренда
        function deleteBrand(rowOrId, brandId) {
            let row;
            let card;
            
            if (typeof rowOrId === 'object' && rowOrId !== null && 'classList' in rowOrId) {
                // Вызов с двумя аргументами: (row, brandId)
                row = rowOrId;
            } else {
                // Вызов с одним аргументом: (brandId)
                brandId = rowOrId;
                row = document.getElementById('brand-' + brandId);
                card = document.getElementById('brand-card-' + brandId);
            }
            
            if (row) row.classList.add('row-deleting');
            if (card) card.classList.add('row-deleting');

            fetch(`/product-brands/${brandId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('{{ __('messages.error_deleting_brand') }}');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        setTimeout(() => {
                            if (row) row.remove();
                            if (card) card.remove();
                            window.showNotification('success', '{{ __('messages.brand_successfully_deleted') }}');
                        }, 300);
                    }
                })
                .catch(error => {
                    if (row) row.classList.remove('row-deleting');
                    if (card) card.classList.remove('row-deleting');
                    window.showNotification('error', '{{ __('messages.error_deleting_brand') }}');
                });
        }

        // Функции для работы с модальным окном редактирования
        function openEditModal(brandId) {
            fetch(`/product-brands/${brandId}/edit`)
                .then(response => response.json())
                .then(brand => {
                    document.getElementById('editServiceId').value = brand.id;
                    document.getElementById('editServiceName').value = brand.name;
                    document.getElementById('editServiceCountry').value = brand.country || '';
                    document.getElementById('editServiceWebsite').value = brand.website || '';
                    document.getElementById('editServiceDescription').value = brand.description || '';
                    document.getElementById('editServiceStatus').value = brand.status ? '1' : '0';

                    document.getElementById('editServiceModal').style.display = 'block';
                })
                .catch(error => {
                    window.showNotification('error', '{{ __('messages.error_loading_brand_data') }}');
                });
        }

        // Обработчик клика по кнопке редактирования
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-edit')) {
                const element = e.target.closest('tr') || e.target.closest('.brand-card');
                let brandId;
                
                if (element.classList.contains('brand-card')) {
                    brandId = element.id.split('-')[2]; // brand-card-{id}
                } else {
                    brandId = element.id.split('-')[1]; // brand-{id}
                }
                openEditModal(brandId);
            }
        });

        // Обработчик отправки формы редактирования
        document.getElementById('editServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const brandId = document.getElementById('editServiceId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> {{ __('messages.saving') }}...';
            submitBtn.disabled = true;

            fetch(`/product-brands/${brandId}`, {
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
                        updateBrandRow(data.brand);
                        window.showNotification('success', '{{ __('messages.brand_successfully_updated') }}');
                        closeEditModal();
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors, 'editServiceForm');
                        window.showNotification('error', '{{ __('messages.please_fix_form_errors') }}');
                    } else {
                        window.showNotification('error', '{{ __('messages.error_updating_brand') }}');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Функция для обновления строки бренда в таблице
        function updateBrandRow(brand) {
            const row = document.getElementById(`brand-${brand.id}`);
            if (!row) return;

            const cells = row.querySelectorAll('td');
            if (cells.length >= 4) {
                cells[0].textContent = brand.name;
                cells[1].textContent = brand.country ?? '—';

                // Обновляем вебсайт
                if (brand.website) {
                    cells[2].innerHTML = `<a href="${brand.website}" target="_blank">${new URL(brand.website).hostname}</a>`;
                } else {
                    cells[2].textContent = '—';
                }

                // Обновляем статус
                const statusBadge = cells[3].querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.className = `status-badge ${brand.status ? 'active' : 'inactive'}`;
                    statusBadge.textContent = brand.status ? '{{ __('messages.brand_active') }}' : '{{ __('messages.brand_inactive') }}';
                }
            }
            
            // Обновляем карточку бренда в мобильной версии
            const card = document.getElementById(`brand-card-${brand.id}`);
            if (card) {
                // Обновляем название
                const nameElement = card.querySelector('.brand-name');
                if (nameElement) {
                    nameElement.textContent = brand.name;
                }
                
                // Обновляем статус
                const statusBadge = card.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.className = `status-badge ${brand.status ? 'active' : 'inactive'}`;
                    statusBadge.textContent = brand.status ? '{{ __('messages.brand_active') }}' : '{{ __('messages.brand_inactive') }}';
                }
                
                // Обновляем страну
                const countryElement = card.querySelector('.brand-info-item:nth-child(1) .brand-info-value');
                if (countryElement) {
                    countryElement.textContent = brand.country ?? '—';
                }
                
                // Обновляем сайт
                const websiteElement = card.querySelector('.brand-info-item:nth-child(2) .brand-info-value');
                if (websiteElement) {
                    if (brand.website) {
                        websiteElement.innerHTML = `<a href="${brand.website}" target="_blank">${new URL(brand.website).hostname}</a>`;
                    } else {
                        websiteElement.textContent = '—';
                    }
                }
                
                // Обновляем описание
                const descriptionElement = card.querySelector('.brand-info-item:nth-child(3) .brand-info-value');
                if (descriptionElement) {
                    descriptionElement.textContent = brand.description ?? '—';
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
            
            fetch(`/product-brands?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                                            throw new Error('{{ __('messages.loading_data_error') }}');
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

        function updateTable(brands) {
            const tbody = document.getElementById('servicesTableBody');
            const brandsCards = document.getElementById('brandsCards');
            
            tbody.innerHTML = '';
            brandsCards.innerHTML = '';

            brands.forEach(brand => {
                // Создаем строку для десктопной таблицы
                const row = document.createElement('tr');
                row.id = `brand-${brand.id}`;
                
                const websiteHtml = brand.website 
                    ? `<a href="${brand.website}" target="_blank">${new URL(brand.website).hostname}</a>`
                    : '—';
                
                row.innerHTML = `
                    <td>${brand.name}</td>
                    <td>${brand.country ?? '—'}</td>
                    <td>${websiteHtml}</td>
                    <td>
                        <span class="status-badge ${brand.status ? 'active' : 'inactive'}">
                            ${brand.status ? '{{ __('messages.brand_active') }}' : '{{ __('messages.brand_inactive') }}'}
                        </span>
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
                `;
                tbody.appendChild(row);

                // Создаем карточку для мобильной версии
                const card = document.createElement('div');
                card.className = 'brand-card';
                card.id = `brand-card-${brand.id}`;
                
                const websiteValue = brand.website 
                    ? `<a href="${brand.website}" target="_blank">${new URL(brand.website).hostname}</a>`
                    : '—';
                
                card.innerHTML = `
                    <div class="brand-card-header">
                        <div class="brand-main-info">
                            <h3 class="brand-name">${brand.name}</h3>
                            <span class="status-badge ${brand.status ? 'active' : 'inactive'}">
                                ${brand.status ? '{{ __('messages.brand_active') }}' : '{{ __('messages.brand_inactive') }}'}
                            </span>
                        </div>
                    </div>
                    <div class="brand-info">
                        <div class="brand-info-item">
                            <span class="brand-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                Страна
                            </span>
                            <span class="brand-info-value">${brand.country ?? '—'}</span>
                        </div>
                        <div class="brand-info-item">
                            <span class="brand-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                </svg>
                                Сайт
                            </span>
                            <span class="brand-info-value">${websiteValue}</span>
                        </div>
                        <div class="brand-info-item">
                            <span class="brand-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Описание
                            </span>
                            <span class="brand-info-value">${brand.description ?? '—'}</span>
                        </div>
                    </div>
                    <div class="brand-actions">
                        <button class="btn-edit" title="Редактировать" onclick="openEditModal(${brand.id})">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Изменить
                        </button>
                        <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${brand.id})">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Удалить
                        </button>
                    </div>
                `;
                
                brandsCards.appendChild(card);
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
            let pagContainer = document.getElementById('brandsPagination');
            if (!pagContainer) {
                pagContainer = document.createElement('div');
                pagContainer.id = 'brandsPagination';
                document.querySelector('.table-wrapper').appendChild(pagContainer);
            }
            pagContainer.innerHTML = paginationHtml;

            // Обновляем мобильную пагинацию
            let mobilePagContainer = document.getElementById('mobileBrandsPagination');
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

        // Функция для переключения между десктопной и мобильной версией
        function toggleMobileView() {
            const tableWrapper = document.querySelector('.table-wrapper');
            const brandsCards = document.getElementById('brandsCards');
            const brandsPagination = document.getElementById('brandsPagination');
            const mobileBrandsPagination = document.getElementById('mobileBrandsPagination');
            
            if (window.innerWidth <= 768) {
                // Мобильная версия
                if (tableWrapper) tableWrapper.style.display = 'none';
                if (brandsCards) brandsCards.style.display = 'block';
                if (brandsPagination) brandsPagination.style.display = 'none';
                if (mobileBrandsPagination) mobileBrandsPagination.style.display = 'block';
            } else {
                // Десктопная версия
                if (tableWrapper) tableWrapper.style.display = 'block';
                if (brandsCards) brandsCards.style.display = 'none';
                if (brandsPagination) brandsPagination.style.display = 'block';
                if (mobileBrandsPagination) mobileBrandsPagination.style.display = 'none';
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
    </script>

    <style>
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: #e6f7ee;
            color: #10b759;
        }

        .status-badge.inactive {
            background-color: #fde8e8;
            color: #f05252;
        }

        .row-deleting {
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .loader {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 5px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        a {
            color: #3b82f6;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</div>
@endsection
