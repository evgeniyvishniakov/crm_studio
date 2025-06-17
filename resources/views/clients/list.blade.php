@extends('layouts.app')

@section('content')

    <div class="clients-container">
        <div class="clients-header">
            <h1>Клиенты</h1>
            <div id="notification"></div>
            <div class="header-actions">
                <button class="btn-add-client" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Добавить клиента
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
            <table class="table-striped clients-table">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Инстаграм</th>
                    <th>Контакты</th>
                    <th>Тип клиента</th>
                    <th class="actions-column">Действия</th>
                </tr>
                </thead>
                <tbody id="clientsTableBody">
                <!-- Строки будут добавляться динамически через JavaScript -->
                @foreach($clients as $client)
                    <tr id="client-{{ $client->id }}">
                        <td>
                            <div class="client-info">
                                <div class="client-avatar" data-name="{{ $client->name }}">
                                    <span>{{ substr($client->name, 0, 2) }}</span>
                                </div>
                                <div class="client-details">
                                    <div class="client-name">{{ $client->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($client->instagram)
                                <a href="https://instagram.com/{{ $client->instagram }}" target="_blank" class="instagram-link">
                                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                    </svg>
                                    {{ $client->instagram }}
                                </a>
                            @endif
                        </td>
                        <td>
                            <div class="contacts-details">
                                @if($client->phone)
                                    <div class="phone"><i class="fa fa-phone"></i> {{ $client->phone }}</div>
                                @endif
                                @if($client->email)
                                    <div class="email"><i class="fa fa-envelope"></i>{{ $client->email }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="client-status">
                                @if($client->client_type_id)
                                    <span class="client-type-badge" style="background-color: {{ $client->clientType->color ?? '#e5e7eb' }}">
                                        {{ $client->clientType->name }}
                                        @if($client->clientType->discount)
                                            <span class="discount-badge">-{{ $client->clientType->discount }}%</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="client-type-badge">Новый клиент</span>
                                @endif
                            </div>
                        </td>
                        <td class="actions-cell" style="vertical-align: middle;">
                            <button class="btn-view">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>

                            </button>
                            <button class="btn-edit" >
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>

                            </button>
                            <button class="btn-delete" >
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>

                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Модальное окно для добавления клиента -->
    <div id="addClientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Добавить нового клиента</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addClientForm">
                    @csrf
                    <div class="form-group">
                        <label for="clientName">Имя *</label>
                        <input type="text" id="clientName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="clientInstagram">Инстаграм</label>
                        <input type="text" id="clientInstagram" name="instagram">
                    </div>
                    <div class="form-group">
                        <label for="clientPhone">Телефон</label>
                        <input type="tel" id="clientPhone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="clientEmail">Почта</label>
                        <input type="email" id="clientEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Тип клиента</label>
                        <select class="form-control" name="client_type_id">
                            <option value="">Выберите тип</option>
                            @foreach($clientTypes as $type)
                            <option value="{{ $type->id }}" data-discount="{{ $type->discount }}" data-description="{{ $type->description }}">
                                {{ $type->name }}
                                @if($type->discount)
                                (Скидка: {{ $type->discount }}%)
                                @endif
                            </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted type-description"></small>
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
            <p>Вы уверены, что хотите удалить этого клиента?</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">Отмена</button>
                <button id="confirmDelete" class="confirm-btn">Удалить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования клиента -->
    <div id="editClientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Редактировать клиента</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editClientForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editClientId" name="id">
                    <div class="form-group">
                        <label for="editClientName">Имя *</label>
                        <input type="text" id="editClientName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editClientInstagram">Инстаграм</label>
                        <input type="text" id="editClientInstagram" name="instagram">
                    </div>
                    <div class="form-group">
                        <label for="editClientPhone">Телефон</label>
                        <input type="tel" id="editClientPhone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="editClientEmail">Почта</label>
                        <input type="email" id="editClientEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Тип клиента</label>
                        <select class="form-control" name="client_type_id" id="editClientType">
                            <option value="">Выберите тип</option>
                            @foreach($clientTypes as $type)
                            <option value="{{ $type->id }}" data-discount="{{ $type->discount }}" data-description="{{ $type->description }}">
                                {{ $type->name }}
                                @if($type->discount)
                                (Скидка: {{ $type->discount }}%)
                                @endif
                            </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted edit-type-description"></small>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно для просмотра клиента -->
    <div id="viewClientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Информация о клиенте</h2>
                <span class="close" onclick="closeViewModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="client-view-info">
                    <div class="client-view-header">
                        <div class="client-view-avatar" id="viewClientAvatar"></div>
                        <div class="client-view-name" id="viewClientName"></div>
                    </div>
                    
                    <div class="client-view-section">
                        <h3>Тип клиента</h3>
                        <div class="client-view-type" id="viewClientType"></div>
                    </div>

                    <div class="client-view-section">
                        <h3>Контактная информация</h3>
                        <div class="client-view-contacts" id="viewClientContacts"></div>
                    </div>

                    <div class="client-view-section">
                        <h3>Социальные сети</h3>
                        <div class="client-view-social" id="viewClientSocial"></div>
                    </div>

                    <div class="client-view-section">
                        <h3>Дополнительная информация</h3>
                        <div class="client-view-notes" id="viewClientNotes"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .client-type-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            color: #1f2937;
            background-color: #e5e7eb;
            transition: all 0.2s ease;
        }

        .client-type-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .discount-badge {
            margin-left: 4px;
            padding: 2px 6px;
            border-radius: 8px;
            background-color: #10b981;
            color: white;
            font-size: 11px;
            font-weight: 600;
        }

        .client-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .client-view-info {
            padding: 20px;
        }

        .client-view-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .client-view-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 600;
            color: white;
        }

        .client-view-name {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
        }

        .client-view-section {
            margin-bottom: 24px;
        }

        .client-view-section h3 {
            font-size: 16px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 12px;
        }

        .client-view-type {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            background-color: #e5e7eb;
        }

        .client-view-contacts,
        .client-view-social,
        .client-view-notes {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .client-view-contacts div,
        .client-view-social div {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4b5563;
        }

        .client-view-contacts i,
        .client-view-social i {
            width: 20px;
            color: #6b7280;
        }
    </style>

    <script>
        // Функции для работы с модальным окном
        function openModal() {
            document.getElementById('addClientModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addClientModal').style.display = 'none';
            // Очищаем ошибки при закрытии модального окна
            clearErrors();
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            if (event.target == document.getElementById('addClientModal')) {
                closeModal();
            }
        }

        // Функция для генерации цвета аватара
        function getAvatarColor(name) {
            const colors = ['#dbeafe', '#f3e8ff', '#fce7f3', '#fef3c7', '#dcfce7', '#e0f2fe'];
            const hash = name.split('').reduce((acc, char) => char.charCodeAt(0) + acc, 0);
            return colors[hash % colors.length];
        }

        // Функция для генерации цвета текста аватара
        function getAvatarTextColor(name) {
            const colors = ['#2563eb', '#7c3aed', '#db2777', '#d97706', '#059669', '#0369a1'];
            const hash = name.split('').reduce((acc, char) => char.charCodeAt(0) + acc, 0);
            return colors[hash % colors.length];
        }

        // Функция для получения инициалов
        function getInitials(name) {
            const parts = name.split(' ');
            return parts.length >= 2
                ? `${parts[0][0]}${parts[1][0]}`.toUpperCase()
                : name.substring(0, 2).toUpperCase();
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
        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.has-error').forEach(el => {
                el.classList.remove('has-error');
            });
        }

        // Функция для отображения ошибок
        function showErrors(errors) {
            clearErrors();

            Object.entries(errors).forEach(([field, messages]) => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const inputGroup = input.closest('.form-group');
                    inputGroup.classList.add('has-error');

                    const errorElement = document.createElement('div');
                    errorElement.className = 'error-message';

                    // Исправлено: берем первое сообщение из массива полностью
                    errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;

                    errorElement.style.color = '#f44336';
                    errorElement.style.marginTop = '5px';
                    errorElement.style.fontSize = '0.85rem';

                    inputGroup.appendChild(errorElement);
                }
            });
        }

        // Добавление нового клиента
        document.getElementById('addClientForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const clientsTableBody = document.getElementById('clientsTableBody');

            clearErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("/clients", {
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
                    if (data.success && data.client) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `client-${data.client.id}`;

                        // Форматируем статус
                        let statusText = 'Новый клиент';
                        if (data.client.status === 'regular') statusText = 'Постоянный клиент';
                        if (data.client.status === 'vip') statusText = 'VIP клиент';

                        // Форматируем Instagram ссылку
                        let instagramLink = '';
                        if (data.client.instagram) {
                            instagramLink = `
                    <a href="https://instagram.com/${data.client.instagram}" target="_blank" class="instagram-link">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                    </svg>
                                    ${data.client.instagram}
                                </a>
                            `;
                        }

                        // Создаем HTML для новой строки
                        newRow.innerHTML = `
                <td>
                    <div class="client-info">
                        <div class="client-avatar" style="background-color: ${getAvatarColor(data.client.name)};">
                            <span style="color: ${getAvatarTextColor(data.client.name)};">${getInitials(data.client.name)}</span>
                        </div>
                        <div class="client-details">
                            <div class="client-name">${data.client.name}</div>
                            <div class="client-status">${statusText}</div>
                        </div>
                    </div>
                </td>
                <td>${instagramLink}</td>
                <td>${data.client.phone || ''}</td>
                <td>${data.client.email || ''}</td>
                <td class="actions-cell" style="vertical-align: middle;">
                    <button class="btn-view">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>

                    </button>
                    <button class="btn-edit">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>

                    </button>
                    <button class="btn-delete">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>

                    </button>
                </td>
            `;

                        // Добавляем новую строку в начало таблицы
                        clientsTableBody.insertBefore(newRow, clientsTableBody.firstChild);

                        // Показываем уведомление
                        showNotification('success', `Клиент ${data.client.name} успешно добавлен`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('Сервер не вернул данные клиента');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);

                    if (error.errors) {
                        showErrors(error.errors);
                        showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        showNotification('error', error.message || 'Произошла ошибка при добавлении клиента');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Инициализация аватаров клиентов
        document.querySelectorAll('.client-avatar').forEach(avatar => {
            const name = avatar.dataset.name;
            avatar.style.backgroundColor = getAvatarColor(name);
            avatar.querySelector('span').style.color = getAvatarTextColor(name);
            avatar.querySelector('span').textContent = getInitials(name);
        });

        // Проверка существующих данных при вводе (on blur)
        document.querySelectorAll('#addClientForm input').forEach(input => {
            input.addEventListener('blur', function() {
                const fieldName = this.name;
                const fieldValue = this.value.trim();

                // Очищаем предыдущую ошибку для этого поля
                const inputGroup = this.closest('.form-group');
                inputGroup.classList.remove('has-error');
                const existingError = inputGroup.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }

                // Проверяем только заполненные поля
                if (fieldValue === '') return;

                // Проверяем только определенные поля
                if (!['instagram', 'phone', 'email'].includes(fieldName)) return;

                fetch(`/clients/check?field=${fieldName}&value=${encodeURIComponent(fieldValue)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            inputGroup.classList.add('has-error');

                            const errorElement = document.createElement('div');
                            errorElement.className = 'error-message';

                            // Русские сообщения об ошибках
                            const errorMessages = {
                                'instagram': 'Клиент с таким Instagram уже существует',
                                'phone': 'Клиент с таким номером телефона уже существует',
                                'email': 'Клиент с такой почтой уже существует'
                            };

                            errorElement.textContent = errorMessages[fieldName] || 'Это значение уже используется';
                            errorElement.style.color = '#f44336';
                            errorElement.style.marginTop = '5px';
                            errorElement.style.fontSize = '0.85rem';

                            inputGroup.appendChild(errorElement);
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при проверке:', error);
                    });
            });
        });

        function getFieldName(field) {
            const names = {
                'instagram': 'Instagram',
                'phone': 'номером телефона',
                'email': 'email'
            };
            return names[field] || field;
        }




        // Глобальные переменные для удаления
        let currentDeleteRow = null;
        let currentDeleteId = null;

        // Обработчик клика по кнопке удаления
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                const row = e.target.closest('tr');
                const clientId = row.id.split('-')[1];

                // Сохраняем ссылку на удаляемую строку
                currentDeleteRow = row;
                currentDeleteId = clientId;

                // Показываем модальное окно подтверждения
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
                deleteClient(currentDeleteRow, currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Функция для удаления клиента
        function deleteClient(row, clientId) {
            // Добавляем класс для анимации
            row.classList.add('row-deleting');

            // Отправляем запрос на удаление
            fetch(`/clients/${clientId}`, {
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
                        // Удаляем строку после завершения анимации
                        setTimeout(() => {
                            row.remove();
                            showNotification('success', 'Клиент успешно удален');
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    row.classList.remove('row-deleting');
                    showNotification('error', 'Не удалось удалить клиента');
                });
        }

        // Закрытие модального окна при клике вне его
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
                currentDeleteRow = null;
                currentDeleteId = null;
            }
        });
        // Функции для работы с модальным окном редактирования
        function openEditModal(clientId) {
            fetch(`/clients/${clientId}`)
                .then(response => response.json())
                .then(client => {
                    document.getElementById('editClientId').value = client.id;
                    document.getElementById('editClientName').value = client.name;
                    document.getElementById('editClientInstagram').value = client.instagram || '';
                    document.getElementById('editClientPhone').value = client.phone || '';
                    document.getElementById('editClientEmail').value = client.email || '';
                    document.getElementById('editClientType').value = client.client_type_id || '';
                    
                    // Обновляем описание типа клиента
                    const typeSelect = document.getElementById('editClientType');
                    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
                    const description = selectedOption.dataset.description;
                    document.querySelector('.edit-type-description').textContent = description || '';
                    
                    document.getElementById('editClientModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Ошибка при получении данных клиента:', error);
                    showNotification('Ошибка при загрузке данных клиента', 'error');
                });
        }

        function closeEditModal() {
            document.getElementById('editClientModal').style.display = 'none';
        }

        // Обработчик клика по кнопке редактирования
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-edit')) {
                const row = e.target.closest('tr');
                const clientId = row.id.split('-')[1];
                openEditModal(clientId);
            }
        });

        // Обработчик отправки формы редактирования
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const clientId = document.getElementById('editClientId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            fetch(`/clients/${clientId}`, {
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
                        // Обновляем строку в таблице
                        updateClientRow(data.client);
                        showNotification('success', 'Изменения успешно сохранены');
                        closeEditModal();
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    if (error.errors) {
                        showErrors(error.errors, 'editClientForm');
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

        // Функция для обновления строки клиента в таблице
        function updateClientRow(client) {
            const row = document.getElementById(`client-${client.id}`);
            if (!row) return;

            // Обновляем аватар
            const avatar = row.querySelector('.client-avatar');
            if (avatar) {
                avatar.style.backgroundColor = getAvatarColor(client.name);
                const initials = avatar.querySelector('span');
                initials.style.color = getAvatarTextColor(client.name);
                initials.textContent = getInitials(client.name);
            }

            // Обновляем имя
            const nameCell = row.querySelector('.client-name');
            if (nameCell) nameCell.textContent = client.name;

            // Обновляем Instagram
            const instagramCell = row.querySelector('.instagram-link');
            if (instagramCell) {
                if (client.instagram) {
                    instagramCell.href = `https://instagram.com/${client.instagram}`;
                    instagramCell.textContent = client.instagram;
                    instagramCell.style.display = 'inline-flex';
                } else {
                    instagramCell.style.display = 'none';
                }
            }

            // Обновляем контакты
            const contactsCell = row.querySelector('.contacts-details');
            if (contactsCell) {
                let contactsHtml = '';
                if (client.phone) {
                    contactsHtml += `<div class="phone"><i class="fa fa-phone"></i> ${client.phone}</div>`;
                }
                if (client.email) {
                    contactsHtml += `<div class="email"><i class="fa fa-envelope"></i> ${client.email}</div>`;
                }
                contactsCell.innerHTML = contactsHtml;
            }

            // Обновляем тип клиента
            const typeCell = row.querySelector('.client-status');
            if (typeCell) {
                if (client.client_type) {
                    typeCell.innerHTML = `
                        <span class="client-type-badge" style="background-color: ${client.client_type.color || '#e5e7eb'}">
                            ${client.client_type.name}
                            ${client.client_type.discount ? `<span class="discount-badge">-${client.client_type.discount}%</span>` : ''}
                        </span>
                    `;
                } else {
                    typeCell.innerHTML = '<span class="client-type-badge">Новый клиент</span>';
                }
            }
        }

        // Обновите функцию showErrors для работы с формой редактирования
        function showErrors(errors, formId = 'addClientForm') {
            clearErrors(formId);

            Object.entries(errors).forEach(([field, messages]) => {
                const input = document.querySelector(`#${formId} [name="${field}"]`);
                if (input) {
                    const inputGroup = input.closest('.form-group');
                    inputGroup.classList.add('has-error');

                    const errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;

                    inputGroup.appendChild(errorElement);
                }
            });
        }

        function clearErrors(formId = 'addClientForm') {
            const form = document.getElementById(formId);
            if (form) {
                form.querySelectorAll('.error-message').forEach(el => el.remove());
                form.querySelectorAll('.has-error').forEach(el => {
                    el.classList.remove('has-error');
                });
            }
        }

        // Добавляем обработчик изменения типа клиента
        document.querySelectorAll('select[name="client_type_id"]').forEach(select => {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const description = selectedOption.dataset.description;
                const descriptionElement = this.parentElement.querySelector('.type-description');
                
                if (description) {
                    descriptionElement.textContent = description;
                } else {
                    descriptionElement.textContent = '';
                }
            });
        });

        // Обработчик изменения типа клиента в форме добавления
        document.querySelector('select[name="client_type_id"]').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.dataset.description;
            document.querySelector('.type-description').textContent = description || '';
        });

        // Обработчик изменения типа клиента в форме редактирования
        document.querySelector('#editClientType').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.dataset.description;
            document.querySelector('.edit-type-description').textContent = description || '';
        });

        // Функция для открытия модального окна просмотра
        function openViewModal(clientId) {
            fetch(`/clients/${clientId}`)
                .then(response => response.json())
                .then(client => {
                    // Устанавливаем имя и аватар
                    document.getElementById('viewClientName').textContent = client.name;
                    const avatar = document.getElementById('viewClientAvatar');
                    avatar.textContent = getInitials(client.name);
                    avatar.style.backgroundColor = getAvatarColor(client.name);
                    avatar.style.color = getAvatarTextColor(client.name);

                    // Устанавливаем тип клиента
                    const typeContainer = document.getElementById('viewClientType');
                    if (client.client_type) {
                        typeContainer.innerHTML = `
                            <span class="client-type-badge" style="background-color: ${client.client_type.color || '#e5e7eb'}">
                                ${client.client_type.name}
                                ${client.client_type.discount ? `<span class="discount-badge">-${client.client_type.discount}%</span>` : ''}
                            </span>
                            ${client.client_type.description ? `<div class="type-description">${client.client_type.description}</div>` : ''}
                        `;
                    } else {
                        typeContainer.innerHTML = '<span class="client-type-badge">Новый клиент</span>';
                    }

                    // Устанавливаем контактную информацию
                    const contactsContainer = document.getElementById('viewClientContacts');
                    contactsContainer.innerHTML = '';
                    if (client.phone) {
                        contactsContainer.innerHTML += `<div><i class="fa fa-phone"></i> ${client.phone}</div>`;
                    }
                    if (client.email) {
                        contactsContainer.innerHTML += `<div><i class="fa fa-envelope"></i> ${client.email}</div>`;
                    }

                    // Устанавливаем социальные сети
                    const socialContainer = document.getElementById('viewClientSocial');
                    socialContainer.innerHTML = '';
                    if (client.instagram) {
                        socialContainer.innerHTML += `
                            <div>
                                <i class="fa fa-instagram"></i>
                                <a href="https://instagram.com/${client.instagram}" target="_blank">${client.instagram}</a>
                            </div>
                        `;
                    }
                    if (client.telegram) {
                        socialContainer.innerHTML += `
                            <div>
                                <i class="fa fa-telegram"></i>
                                <a href="https://t.me/${client.telegram}" target="_blank">${client.telegram}</a>
                            </div>
                        `;
                    }

                    // Устанавливаем дополнительные заметки
                    const notesContainer = document.getElementById('viewClientNotes');
                    notesContainer.innerHTML = client.notes || 'Нет дополнительной информации';

                    // Открываем модальное окно
                    document.getElementById('viewClientModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Ошибка при получении данных клиента:', error);
                    showNotification('Ошибка при загрузке данных клиента', 'error');
                });
        }

        // Функция для закрытия модального окна просмотра
        function closeViewModal() {
            document.getElementById('viewClientModal').style.display = 'none';
        }

        // Обновляем обработчики кнопок просмотра
        document.querySelectorAll('.btn-view').forEach(button => {
            button.addEventListener('click', function() {
                const clientId = this.closest('tr').id.split('-')[1];
                openViewModal(clientId);
            });
        });
    </script>
@endsection
