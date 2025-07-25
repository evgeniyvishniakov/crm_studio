@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>{{ __('messages.suppliers') }}</h1>
            <div id="notification" class="notification alert alert-success" role="alert">
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <span class="notification-message">{{ __('messages.supplier_successfully_added') }}!</span>
            </div>
            <div class="header-actions">
                <button class="btn-add-service" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.add_supplier') }}
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="{{ __('messages.search_placeholder') }}" onkeyup="handleSearch()">
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table-striped services-table">
                <thead>
                <tr>
                    <th>{{ __('messages.supplier_name') }}</th>
                    <th>{{ __('messages.supplier_contact_person') }}</th>
                    <th>{{ __('messages.supplier_phone') }}</th>
                    <th>{{ __('messages.supplier_email') }}</th>
                    <th>{{ __('messages.supplier_status') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="servicesTableBody">
                @foreach($suppliers as $supplier)
                    <tr id="supplier-{{ $supplier->id }}">
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_person ?? '—' }}</td>
                        <td>
                            @if($supplier->phone)
                                <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $supplier->status ? 'active' : 'inactive' }}">
                                {{ $supplier->status ? __('messages.supplier_active') : __('messages.supplier_inactive') }}
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
            <div id="suppliersPagination"></div>
        </div>
    </div>

    <!-- Модальное окно для добавления поставщика -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_new_supplier') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">{{ __('messages.supplier_name') }} *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceContactPerson">{{ __('messages.supplier_contact_person') }}</label>
                        <input type="text" id="serviceContactPerson" name="contact_person">
                    </div>
                    <div class="form-group">
                        <label for="servicePhone">{{ __('messages.supplier_phone') }}</label>
                        <input type="tel" id="servicePhone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="serviceEmail">{{ __('messages.supplier_email') }}</label>
                        <input type="email" id="serviceEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label for="serviceAddress">{{ __('messages.supplier_address') }}</label>
                        <input type="text" id="serviceAddress" name="address">
                    </div>
                    <div class="form-group">
                        <label for="serviceInstagram">{{ __('messages.supplier_instagram') }}</label>
                        <input type="text" id="serviceInstagram" name="instagram" placeholder="@username">
                    </div>
                    <div class="form-group">
                        <label for="serviceInn">{{ __('messages.supplier_inn') }}</label>
                        <input type="text" id="serviceInn" name="inn">
                    </div>
                    <div class="form-group">
                        <label for="serviceNote">{{ __('messages.supplier_note') }}</label>
                        <textarea id="serviceNote" name="note" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="serviceStatus">{{ __('messages.supplier_status') }}</label>
                        <select id="serviceStatus" name="status">
                            <option value="1">{{ __('messages.supplier_active') }}</option>
                            <option value="0">{{ __('messages.supplier_inactive') }}</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add_supplier') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>{{ __('messages.confirm_delete_supplier') }}</h3>
            <p>{{ __('messages.confirm_delete_supplier') }}</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">{{ __('messages.cancel') }}</button>
                <button id="confirmDelete" class="confirm-btn">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования поставщика -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_supplier') }}</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editServiceId" name="id">
                    <div class="form-group">
                        <label for="editServiceName">{{ __('messages.supplier_name') }} *</label>
                        <input type="text" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editServiceContactPerson">{{ __('messages.supplier_contact_person') }}</label>
                        <input type="text" id="editServiceContactPerson" name="contact_person">
                    </div>
                    <div class="form-group">
                        <label for="editServicePhone">{{ __('messages.supplier_phone') }}</label>
                        <input type="tel" id="editServicePhone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="editServiceEmail">{{ __('messages.supplier_email') }}</label>
                        <input type="email" id="editServiceEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label for="editServiceAddress">{{ __('messages.supplier_address') }}</label>
                        <input type="text" id="editServiceAddress" name="address">
                    </div>
                    <div class="form-group">
                        <label for="editServiceInstagram">{{ __('messages.supplier_instagram') }}</label>
                        <input type="text" id="editServiceInstagram" name="instagram" placeholder="@username">
                    </div>
                    <div class="form-group">
                        <label for="editServiceInn">{{ __('messages.supplier_inn') }}</label>
                        <input type="text" id="editServiceInn" name="inn">
                    </div>
                    <div class="form-group">
                        <label for="editServiceNote">{{ __('messages.supplier_note') }}</label>
                        <textarea id="editServiceNote" name="note" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editServiceStatus">{{ __('messages.supplier_status') }}</label>
                        <select id="editServiceStatus" name="status">
                            <option value="1">{{ __('messages.supplier_active') }}</option>
                            <option value="0">{{ __('messages.supplier_inactive') }}</option>
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

        // Функция для показа уведомлений
        function showNotification(type, message) {
            const notification = document.getElementById('notification');
            notification.className = `notification ${type} show`;

            const icon = type === 'success' ?
                '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>' :
                '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';

            notification.innerHTML = `
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    ${icon}
                </svg>
                <span class="notification-message">${message}</span>
            `;

            setTimeout(() => {
                notification.className = `notification ${type}`;
            }, 3000);
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

        // Добавление нового поставщика
        document.getElementById('addServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const servicesTableBody = document.getElementById('servicesTableBody');

            clearErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("{{ route('suppliers.store') }}", {
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
                    if (data.success && data.supplier) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `supplier-${data.supplier.id}`;

                        newRow.innerHTML = `
                            <td>${data.supplier.name}</td>
                            <td>${data.supplier.contact_person ?? '—'}</td>
                            <td>
                                ${data.supplier.phone ? `<a href="tel:${data.supplier.phone}">${data.supplier.phone}</a>` : '—'}
                            </td>
                            <td>
                                ${data.supplier.email ? `<a href="mailto:${data.supplier.email}">${data.supplier.email}</a>` : '—'}
                            </td>
                            <td>
                                <span class="status-badge ${data.supplier.status ? 'active' : 'inactive'}">
                                    ${data.supplier.status ? '{{ __('messages.supplier_active') }}' : '{{ __('messages.supplier_inactive') }}'}
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

                        // Добавляем новую строку в начало таблицы
                        servicesTableBody.insertBefore(newRow, servicesTableBody.firstChild);

                        // Показываем уведомление
                        showNotification('success', `{{ __('messages.supplier') }} "${data.supplier.name}" {{ __('messages.supplier_successfully_added') }}`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('{{ __('messages.server_did_not_return_supplier_data') }}');
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors);
                        showNotification('error', '{{ __('messages.please_fix_form_errors') }}');
                    } else {
                        showNotification('error', error.message || '{{ __('messages.error_adding_supplier') }}');
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

        // Обработчик клика по кнопке удаления
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                const row = e.target.closest('tr');
                const supplierId = row.id.split('-')[1];

                currentDeleteRow = row;
                currentDeleteId = supplierId;

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
                deleteSupplier(currentDeleteRow, currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Функция для удаления поставщика
        function deleteSupplier(row, supplierId) {
            row.classList.add('row-deleting');

            fetch(`/suppliers/${supplierId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('{{ __('messages.error_deleting_supplier') }}');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        setTimeout(() => {
                            row.remove();
                            showNotification('success', '{{ __('messages.supplier_successfully_deleted') }}');
                        }, 300);
                    }
                })
                .catch(error => {
                    row.classList.remove('row-deleting');
                    showNotification('error', '{{ __('messages.error_deleting_supplier') }}');
                });
        }

        // Функции для работы с модальным окном редактирования
        function openEditModal(supplierId) {
            fetch(`/suppliers/${supplierId}/edit`)
                .then(response => response.json())
                .then(supplier => {
                    document.getElementById('editServiceId').value = supplier.id;
                    document.getElementById('editServiceName').value = supplier.name;
                    document.getElementById('editServiceContactPerson').value = supplier.contact_person || '';
                    document.getElementById('editServicePhone').value = supplier.phone || '';
                    document.getElementById('editServiceEmail').value = supplier.email || '';
                    document.getElementById('editServiceAddress').value = supplier.address || '';
                    document.getElementById('editServiceInstagram').value = supplier.instagram || '';
                    document.getElementById('editServiceInn').value = supplier.inn || '';
                    document.getElementById('editServiceNote').value = supplier.note || '';
                    document.getElementById('editServiceStatus').value = supplier.status ? '1' : '0';

                    document.getElementById('editServiceModal').style.display = 'block';
                })
                .catch(error => {
                    showNotification('error', '{{ __('messages.error_loading_supplier_data') }}');
                });
        }

        // Обработчик клика по кнопке редактирования
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-edit')) {
                const row = e.target.closest('tr');
                const supplierId = row.id.split('-')[1];
                openEditModal(supplierId);
            }
        });

        // Обработчик отправки формы редактирования
        document.getElementById('editServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const supplierId = document.getElementById('editServiceId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> {{ __('messages.saving') }}...';
            submitBtn.disabled = true;

            fetch(`/suppliers/${supplierId}`, {
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
                        updateSupplierRow(data.supplier);
                        showNotification('success', '{{ __('messages.supplier_successfully_updated') }}');
                        closeEditModal();
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors, 'editServiceForm');
                        showNotification('error', '{{ __('messages.please_fix_form_errors') }}');
                    } else {
                        showNotification('error', '{{ __('messages.error_updating_supplier') }}');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Функция для обновления строки поставщика в таблице
        function updateSupplierRow(supplier) {
            const row = document.getElementById(`supplier-${supplier.id}`);
            if (!row) return;

            const cells = row.querySelectorAll('td');
            if (cells.length >= 5) {
                cells[0].textContent = supplier.name;
                cells[1].textContent = supplier.contact_person ?? '—';

                // Обновляем телефон
                if (supplier.phone) {
                    cells[2].innerHTML = `<a href="tel:${supplier.phone}">${supplier.phone}</a>`;
                } else {
                    cells[2].textContent = '—';
                }

                // Обновляем email
                if (supplier.email) {
                    cells[3].innerHTML = `<a href="mailto:${supplier.email}">${supplier.email}</a>`;
                } else {
                    cells[3].textContent = '—';
                }

                // Обновляем статус
                const statusBadge = cells[4].querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.className = `status-badge ${supplier.status ? 'active' : 'inactive'}`;
                    statusBadge.textContent = supplier.status ? '{{ __('messages.supplier_active') }}' : '{{ __('messages.supplier_inactive') }}';
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
            
            fetch(`/suppliers?${params.toString()}`, {
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
                showNotification('error', '{{ __('messages.loading_data_error') }}');
            });
        }

        function updateTable(suppliers) {
            const tbody = document.getElementById('servicesTableBody');
            tbody.innerHTML = '';

            suppliers.forEach(supplier => {
                const row = document.createElement('tr');
                row.id = `supplier-${supplier.id}`;
                
                const phoneHtml = supplier.phone 
                    ? `<a href="tel:${supplier.phone}">${supplier.phone}</a>`
                    : '—';
                
                const emailHtml = supplier.email 
                    ? `<a href="mailto:${supplier.email}">${supplier.email}</a>`
                    : '—';
                
                row.innerHTML = `
                    <td>${supplier.name}</td>
                    <td>${supplier.contact_person ?? '—'}</td>
                    <td>${phoneHtml}</td>
                    <td>${emailHtml}</td>
                    <td>
                        <span class="status-badge ${supplier.status ? 'active' : 'inactive'}">
                            ${supplier.status ? '{{ __('messages.supplier_active') }}' : '{{ __('messages.supplier_inactive') }}'}
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
            let pagContainer = document.getElementById('suppliersPagination');
            if (!pagContainer) {
                pagContainer = document.createElement('div');
                pagContainer.id = 'suppliersPagination';
                document.querySelector('.table-wrapper').appendChild(pagContainer);
            }
            pagContainer.innerHTML = paginationHtml;

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

        // Инициализация первой загрузки
        loadPage(1);
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
