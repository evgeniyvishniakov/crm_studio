@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>Типы клиентов</h1>
            <div id="notification" class="notification alert alert-success" role="alert">
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <span class="notification-message">Тип клиента успешно добавлен!</span>
            </div>
            <div class="header-actions">
                <button class="btn-add-service" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Добавить тип клиента
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" placeholder="Поиск...">
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class=" table-striped services-table">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Скидка</th>
                    <th>Статус</th>
                    <th class="actions-column">Действия</th>
                </tr>
                </thead>
                <tbody id="servicesTableBody">
                @foreach($clientTypes as $clientType)
                    <tr id="client-type-{{ $clientType->id }}">
                        <td>{{ $clientType->name }}</td>
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
                                {{ $clientType->status ? 'Активен' : 'Неактивен' }}
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
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Модальное окно для добавления типа клиента -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Добавить новый тип клиента</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">Название *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceDescription">Описание</label>
                        <textarea id="serviceDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="serviceDiscount">Скидка (%)</label>
                        <input type="number" id="serviceDiscount" name="discount" min="0" max="100" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="serviceStatus">Статус</label>
                        <select id="serviceStatus" name="status">
                            <option value="1">Активен</option>
                            <option value="0">Неактивен</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить этот тип клиента?</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">Отмена</button>
                <button id="confirmDelete" class="confirm-btn">Удалить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования типа клиента -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Редактировать тип клиента</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editServiceId" name="id">
                    <div class="form-group">
                        <label for="editServiceName">Название *</label>
                        <input type="text" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editServiceDescription">Описание</label>
                        <textarea id="editServiceDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editServiceDiscount">Скидка (%)</label>
                        <input type="number" id="editServiceDiscount" name="discount" min="0" max="100" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="editServiceStatus">Статус</label>
                        <select id="editServiceStatus" name="status">
                            <option value="1">Активен</option>
                            <option value="0">Неактивен</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить</button>
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

        // Добавление нового типа клиента
        document.getElementById('addServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const servicesTableBody = document.getElementById('servicesTableBody');

            clearErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("{{ route('client-types.store') }}", {
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
                    if (data.success && data.clientType) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `client-type-${data.clientType.id}`;

                        newRow.innerHTML = `
                            <td>${data.clientType.name}</td>
                            <td>${data.clientType.description ?? '—'}</td>
                            <td>
                                ${data.clientType.discount !== null ? (Number(parseFloat(data.clientType.discount)) % 1 === 0 ? Number(parseFloat(data.clientType.discount)) : parseFloat(data.clientType.discount).toFixed(2)) + '%' : '—'}
                            </td>
                            <td>
                                <span class="status-badge ${data.clientType.status ? 'active' : 'inactive'}">
                                    ${data.clientType.status ? 'Активен' : 'Неактивен'}
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

                        // Показываем уведомление
                        showNotification('success', `Тип клиента "${data.clientType.name}" успешно добавлен`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('Сервер не вернул данные типа клиента');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);

                    if (error.errors) {
                        showErrors(error.errors);
                        showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        showNotification('error', error.message || 'Произошла ошибка при добавлении типа клиента');
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
                const clientTypeId = row.id.split('-')[2]; // client-type-1 => 1

                currentDeleteRow = row;
                currentDeleteId = clientTypeId;

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
                deleteClientType(currentDeleteRow, currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Функция для удаления типа клиента
        function deleteClientType(row, clientTypeId) {
            row.classList.add('row-deleting');

            fetch(`/client-types/${clientTypeId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject({status: response.status, ...err}));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        setTimeout(() => {
                            row.remove();
                            showNotification('success', 'Тип клиента успешно удален');
                        }, 300);
                    }
                })
                .catch(error => {
                    // console.error('Ошибка:', error);
                    row.classList.remove('row-deleting');
                    if (error.status === 403 && error.message) {
                        showNotification('error', error.message);
                    } else {
                        showNotification('error', 'Не удалось удалить тип клиента');
                    }
                });
        }

        // Функции для работы с модальным окном редактирования
        function openEditModal(clientTypeId) {
            fetch(`/client-types/${clientTypeId}/edit`)
                .then(response => response.json())
                .then(clientType => {
                    document.getElementById('editServiceId').value = clientType.id;
                    document.getElementById('editServiceName').value = clientType.name;
                    document.getElementById('editServiceDescription').value = clientType.description || '';
                    document.getElementById('editServiceDiscount').value = clientType.discount || '';
                    document.getElementById('editServiceStatus').value = clientType.status ? '1' : '0';

                    document.getElementById('editServiceModal').style.display = 'block';
                })
                .catch(error => {
                    // console.error('Ошибка загрузки данных:', error);
                    showNotification('error', 'Не удалось загрузить данные типа клиента');
                });
        }

        // Обработчик клика по кнопке редактирования
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-edit')) {
                const row = e.target.closest('tr');
                const clientTypeId = row.id.split('-')[2];
                openEditModal(clientTypeId);
            }
        });

        // Обработчик отправки формы редактирования
        document.getElementById('editServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const clientTypeId = document.getElementById('editServiceId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            fetch(`/client-types/${clientTypeId}`, {
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
                        return response.json().then(err => Promise.reject({status: response.status, ...err}));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateClientTypeRow(data.clientType);
                        showNotification('success', 'Изменения успешно сохранены');
                        closeEditModal();
                    }
                })
                .catch(error => {
                    // console.error('Ошибка:', error);
                    if (error.status === 403 && error.message) {
                        showNotification('error', error.message);
                    } else if (error.errors) {
                        showErrors(error.errors, 'editServiceForm');
                        showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        showNotification('error', 'Ошибка при сохранении изменений');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Функция для обновления строки типа клиента в таблице
        function updateClientTypeRow(clientType) {
            const row = document.getElementById(`client-type-${clientType.id}`);
            if (!row) return;

            const cells = row.querySelectorAll('td');
            if (cells.length >= 4) {
                cells[0].textContent = clientType.name;
                cells[1].textContent = clientType.description ?? '—';
                cells[2].textContent = clientType.discount !== null ? (Number(parseFloat(clientType.discount)) % 1 === 0 ? Number(parseFloat(clientType.discount)) : parseFloat(clientType.discount).toFixed(2)) + '%' : '—';

                // Обновляем статус
                const statusBadge = cells[3].querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.className = `status-badge ${clientType.status ? 'active' : 'inactive'}`;
                    statusBadge.textContent = clientType.status ? 'Активен' : 'Неактивен';
                }
            }
        }
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
    </style>
</div>
@endsection
