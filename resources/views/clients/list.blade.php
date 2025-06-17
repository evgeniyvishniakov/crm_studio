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
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Информация о клиенте</h2>
                <span class="close" onclick="closeViewModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="appointment-details-modal">
                    <div class="details-header">
                        <div class="client-info">
                            <div class="client-avatar">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32"><path d="M12 12c2.7 0 4.5-1.8 4.5-4.5S14.7 3 12 3 7.5 4.8 7.5 7.5 9.3 12 12 12zm0 2c-3 0-9 1.5-9 4.5V21h18v-2.5c0-3-6-4.5-9-4.5z"/></svg>
                            </div>
                            <div>
                                <div class="client-name" id="viewClientName"></div>
                                <div id="viewClientInstagram"></div>
                            </div>
                        </div>
                        <div class="client-type-block" id="viewClientType"></div>
                    </div>

                    <div class="details-row" id="viewClientContacts">
                        <!-- Контактная информация будет добавлена через JavaScript -->
                    </div>

                    <div class="card procedures-card">
                        <div class="card-title accordion-header" onclick="toggleAccordion('proceduresAccordion')">
                            Процедуры
                            <svg class="accordion-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </div>
                        <div class="accordion-content" id="proceduresAccordion">
                            <div class="procedures-list" id="viewClientProcedures">
                                <!-- Список процедур будет добавлен через JavaScript -->
                            </div>
                        </div>
                    </div>

                    <div class="card sales-card">
                        <div class="card-title accordion-header" onclick="toggleAccordion('salesAccordion')">
                            Продажи
                            <svg class="accordion-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </div>
                        <div class="accordion-content" id="salesAccordion">
                            <div class="products-section" id="viewClientProducts">
                                <!-- Список товаров будет добавлен через JavaScript -->
                            </div>
                        </div>
                    </div>

                    <div class="details-footer">
                        <span>Потрачено: <b id="viewClientTotal">0 грн</b></span>
                        <button type="button" class="btn-cancel" onclick="closeViewModal()">Закрыть</button>
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

        .appointment-details-modal {
            padding: 20px;
        }

        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .client-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e5e7eb;
        }

        .client-name {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
        }

        .client-type-block {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
        }

        .details-row {
            display: flex;
            gap: 24px;
            margin-bottom: 20px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 1px 1px 4px rgba(1, 0, 0, 0.3);
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
        }

        .accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 12px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .accordion-icon {
            width: 24px;
            height: 24px;
            transition: transform 0.3s ease;
        }

        .accordion-header.active .accordion-icon {
            transform: rotate(180deg);
        }

        .accordion-content {
            display: none;
            padding: 16px 0;
        }

        .accordion-content.active {
            display: block;
        }

        .procedures-list, .products-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .procedure-item, .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .details-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .btn-cancel, .btn-submit {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .btn-submit {
            background: #2563eb;
            color: white;
            border: none;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .btn-submit:hover {
            background: #1d4ed8;
        }

        .procedure-item {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
        }

        .procedure-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .procedure-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 16px;
        }

        .procedure-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .procedure-status.completed {
            background-color: #dcfce7;
            color: #059669;
        }

        .procedure-status.pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .procedure-status.cancelled {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .procedure-status.rescheduled {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .procedure-details {
            display: flex;
            gap: 16px;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
            justify-content: space-between;
        }

        .procedure-date, .procedure-price {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .procedure-notes {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 4px;
        }

        .procedure-notes .icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px;
            color: #6b7280;
            text-align: center;
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            margin-bottom: 16px;
            color: #9ca3af;
        }

        .sale-item {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
        }

        .sale-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .sale-date {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #6b7280;
            font-size: 14px;
        }

        .sale-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .sale-status.completed {
            background-color: #dcfce7;
            color: #059669;
        }

        .sale-status.pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .sale-status.cancelled {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .sale-status.refunded {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .sale-products {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            background: #ffffff;
            border-radius: 6px;
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .product-name {
            font-weight: 500;
            color: #1f2937;
        }

        .product-quantity {
            font-size: 12px;
            color: #6b7280;
        }

        .product-price {
            font-weight: 500;
            color: #1f2937;
        }

        .sale-notes {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 4px;
        }

        .sale-notes .icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .procedure-info{
            width: 100%;
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
            if (!name) return '#e5e7eb';

            const colors = ['#dbeafe', '#f3e8ff', '#fce7f3', '#fef3c7', '#dcfce7', '#e0f2fe'];
            const hash = name.split('').reduce((acc, char) => char.charCodeAt(0) + acc, 0);
            return colors[hash % colors.length];
        }

        // Функция для генерации цвета текста аватара
        function getAvatarTextColor(name) {
            if (!name) return '#6b7280';

            const colors = ['#2563eb', '#7c3aed', '#db2777', '#d97706', '#059669', '#0369a1'];
            const hash = name.split('').reduce((acc, char) => char.charCodeAt(0) + acc, 0);
            return colors[hash % colors.length];
        }

        // Функция для получения инициалов
        function getInitials(name) {
            if (!name) return '??';

            const parts = name.split(' ');
            return parts.length >= 2
                ? `${parts[0][0]}${parts[1][0]}`.toUpperCase()
                : name.substring(0, 2).toUpperCase();
        }

        // Функция для инициализации аватара
        function initializeAvatar(avatar) {
            if (!avatar) return;

            const name = avatar.dataset.name;
            if (!name) return;

            try {
                avatar.style.backgroundColor = getAvatarColor(name);
                const initials = avatar.querySelector('span');
                if (initials) {
                    initials.style.color = getAvatarTextColor(name);
                    initials.textContent = getInitials(name);
                }
            } catch (error) {
                console.error('Ошибка при инициализации аватара:', error);
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
            initializeAvatar(avatar);
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
            const modal = document.getElementById('viewClientModal');
            if (event.target === modal) {
                closeViewModal();
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

        // Функция для закрытия всех аккордеонов
        function closeAllAccordions() {
            const accordions = document.querySelectorAll('.accordion-content');
            const headers = document.querySelectorAll('.accordion-header');

            accordions.forEach(content => {
                content.classList.remove('active');
            });

            headers.forEach(header => {
                header.classList.remove('active');
            });
        }

        // Функция для форматирования суммы
        function formatAmount(amount) {
            const roundedAmount = Math.round(amount * 100) / 100;
            return roundedAmount % 1 === 0
                ? `${roundedAmount} грн`
                : `${roundedAmount.toFixed(2)} грн`;
        }

        // Функция для открытия модального окна просмотра
        function openViewModal(clientId) {
            if (!clientId) {
                console.error('ID клиента не указан');
                return;
            }

            const modal = document.getElementById('viewClientModal');
            if (!modal) {
                console.error('Модальное окно не найдено');
                return;
            }

            // Закрываем все аккордеоны перед открытием модального окна
            closeAllAccordions();

            modal.style.display = 'block';

            fetch(`/clients/${clientId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(client => {
                    if (!client) {
                        throw new Error('Данные клиента не получены');
                    }

                    console.log('Client data:', client);
                    console.log('Appointments:', client.appointments);
                    console.log('Sales:', client.sales);

                    let totalProceduresAmount = 0;
                    let totalSalesAmount = 0;

                    // Устанавливаем имя и аватар
                    const nameElement = document.getElementById('viewClientName');
                    const avatar = document.getElementById('viewClientAvatar');

                    if (nameElement && client.name) {
                        nameElement.textContent = client.name;
                    }

                    if (avatar) {
                        const initials = getInitials(client.name || '');
                        const bgColor = getAvatarColor(client.name);
                        const textColor = getAvatarTextColor(client.name);

                        avatar.innerHTML = `<span style="color: ${textColor};">${initials}</span>`;
                        avatar.style.backgroundColor = bgColor;
                    }

                    // Устанавливаем Instagram
                    const instagramContainer = document.getElementById('viewClientInstagram');
                    if (instagramContainer) {
                        if (client.instagram) {
                            instagramContainer.innerHTML = `
                                <a href="https://instagram.com/${client.instagram}" target="_blank" style="color:#2196f3;">
                                    @${client.instagram}
                                </a>
                            `;
                        } else {
                            instagramContainer.innerHTML = '';
                        }
                    }

                    // Устанавливаем тип клиента
                    const typeContainer = document.getElementById('viewClientType');
                    if (typeContainer) {
                        if (client.client_type) {
                            typeContainer.innerHTML = `
                                <span class="client-type-badge" style="background-color: ${client.client_type.color || '#e5e7eb'}">
                                    ${client.client_type.name}
                                    ${client.client_type.discount ? `<span class="discount-badge">-${client.client_type.discount}%</span>` : ''}
                                </span>
                            `;
                        } else {
                            typeContainer.innerHTML = '<span class="client-type-badge">Новый клиент</span>';
                        }
                    }

                    // Устанавливаем контактную информацию
                    const contactsContainer = document.getElementById('viewClientContacts');
                    if (contactsContainer) {
                        let contactsHtml = '';
                        if (client.phone) {
                            contactsHtml += `
                                <div class="contact-item">
                                    <span class="contact-label">Телефон:</span>
                                    <span class="contact-value">${client.phone}</span>
                                </div>
                            `;
                        }
                        if (client.email) {
                            contactsHtml += `
                                <div class="contact-item">
                                    <span class="contact-label">Email:</span>
                                    <span class="contact-value">${client.email}</span>
                                </div>
                            `;
                        }
                        contactsContainer.innerHTML = contactsHtml;
                    }

                    // Устанавливаем процедуры
                    const proceduresContainer = document.getElementById('viewClientProcedures');
                    if (proceduresContainer) {
                        if (client.appointments && client.appointments.length > 0) {
                            let proceduresHtml = '';
                            client.appointments.forEach(appointment => {
                                console.log('Processing appointment:', appointment);
                                totalProceduresAmount += parseFloat(appointment.price || 0);

                                // Форматируем дату и время
                                let formattedDate = 'Дата не указана';
                                let formattedTime = '';

                                if (appointment.date) {
                                    try {
                                        // Пробуем разные форматы даты
                                        let date;
                                        if (appointment.date.includes('T')) {
                                            // Формат ISO
                                            date = new Date(appointment.date);
                                        } else if (appointment.date.includes('-')) {
                                            // Формат YYYY-MM-DD
                                            const [year, month, day] = appointment.date.split('-');
                                            date = new Date(year, month - 1, day);
                                        } else if (appointment.date.includes('.')) {
                                            // Формат DD.MM.YYYY
                                            const [day, month, year] = appointment.date.split('.');
                                            date = new Date(year, month - 1, day);
                                        }

                                        if (date && !isNaN(date.getTime())) {
                                            formattedDate = date.toLocaleDateString('ru-RU');
                                        }
                                    } catch (error) {
                                        console.error('Error parsing date:', error);
                                    }
                                }

                                if (appointment.time) {
                                    try {
                                        // Форматируем время
                                        const timeParts = appointment.time.split(':');
                                        if (timeParts.length >= 2) {
                                            const hours = timeParts[0].padStart(2, '0');
                                            const minutes = timeParts[1].padStart(2, '0');
                                            formattedTime = `${hours}:${minutes}`;
                                        }
                                    } catch (error) {
                                        console.error('Error parsing time:', error);
                                    }
                                }

                                proceduresHtml += `
                                    <div class="procedure-item">
                                        <div class="procedure-info">
                                            <div class="procedure-details">
                                                <span class="procedure-date">

                                                    ${formattedDate} ${formattedTime ? formattedTime : ''}
                                                </span>
                                                <span class="procedure-status ${appointment.status || 'pending'}">${getStatusText(appointment.status)}</span>
                                            </div>
                                            <div class="procedure-header">
                                                <span class="procedure-name">${appointment.service ? appointment.service.name : 'Неизвестная услуга'}</span>
                                                <span class="procedure-price">${appointment.price || 0} грн</span>
                                            </div>

                                            ${appointment.notes ? `
                                                <div class="procedure-notes">
                                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    ${appointment.notes}
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                `;
                            });
                            proceduresContainer.innerHTML = proceduresHtml;
                        } else {
                            proceduresContainer.innerHTML = `
                                <div class="empty-state">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14.5h-2v-2h2v2zm0-4h-2V7h2v5.5z"/>
                                    </svg>
                                    <div>Нет выполненных процедур</div>
                                </div>
                            `;
                        }
                    }

                    // Устанавливаем продажи
                    const productsContainer = document.getElementById('viewClientProducts');
                    if (productsContainer) {
                        if (client.sales && client.sales.length > 0) {
                            let productsHtml = '';

                            client.sales.forEach(sale => {
                                console.log('Processing sale:', sale);

                                // Форматируем дату продажи
                                let formattedDate = 'Дата не указана';
                                if (sale.created_at) {
                                    try {
                                        const date = new Date(sale.created_at);
                                        if (!isNaN(date.getTime())) {
                                            formattedDate = date.toLocaleDateString('ru-RU');
                                        }
                                    } catch (error) {
                                        console.error('Error parsing sale date:', error);
                                    }
                                }

                                if (sale.items) {
                                    sale.items.forEach(item => {
                                        totalSalesAmount += (parseFloat(item.retail_price || 0) * parseFloat(item.quantity || 1));
                                    });
                                }

                                productsHtml += `
                                    <div class="sale-item">
                                        <div class="sale-header">
                                            <span class="sale-date">
                                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                                ${formattedDate}
                                            </span>
                                            <span class="sale-status ${sale.status || 'completed'}">${getSaleStatusText(sale.status)}</span>
                                        </div>
                                        <div class="sale-products">
                                            ${sale.items ? sale.items.map(item => `
                                                <div class="product-item">
                                                    <div class="product-info">
                                                        <span class="product-name">${item.product ? item.product.name : 'Неизвестный товар'}</span>
                                                        <span class="product-quantity">${item.quantity || 1} шт.</span>
                                                    </div>
                                                    <span class="product-price">${(item.retail_price || 0) * (item.quantity || 1)} грн</span>
                                                </div>
                                            `).join('') : ''}
                                        </div>
                                        ${sale.notes ? `
                                            <div class="sale-notes">
                                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                ${sale.notes}
                                            </div>
                                        ` : ''}
                                    </div>
                                `;
                            });

                            productsContainer.innerHTML = productsHtml;
                        } else {
                            productsContainer.innerHTML = `
                                <div class="empty-state">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14.5h-2v-2h2v2zm0-4h-2V7h2v5.5z"/>
                                    </svg>
                                    <div>Нет продаж</div>
                                </div>
                            `;
                        }

                        // Обновляем общую сумму
                        const totalAmount = totalProceduresAmount + totalSalesAmount;
                        console.log('Total procedures amount:', totalProceduresAmount);
                        console.log('Total sales amount:', totalSalesAmount);
                        console.log('Total amount:', totalAmount);

                        const totalElement = document.getElementById('viewClientTotal');
                        if (totalElement) {
                            totalElement.textContent = formatAmount(totalAmount);
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка при получении данных клиента:', error);
                    showNotification('error', 'Ошибка при загрузке данных клиента');
                });
        }

        // Функция для переключения аккордеона
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const header = content?.previousElementSibling;

            if (content && header) {
                content.classList.toggle('active');
                header.classList.toggle('active');
            }
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Инициализация аватаров клиентов
                const avatars = document.querySelectorAll('.client-avatar');
                if (avatars) {
                    avatars.forEach(avatar => {
                        initializeAvatar(avatar);
                    });
                }

                // Инициализация кнопок просмотра
                const viewButtons = document.querySelectorAll('.btn-view');
                if (viewButtons) {
                    viewButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const row = this.closest('tr');
                            if (row) {
                                const clientId = row.id.split('-')[1];
                                if (clientId) {
                                    openViewModal(clientId);
                                }
                            }
                        });
                    });
                }
            } catch (error) {
                console.error('Ошибка при инициализации:', error);
            }
        });

        // Функция для закрытия модального окна
        function closeViewModal() {
            const modal = document.getElementById('viewClientModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function getSaleStatusText(status) {
            const statusMap = {
                'completed': 'Выполнено',
                'pending': 'Ожидает',
                'cancelled': 'Отменено',
                'refunded': 'Возвращено'
            };
            return statusMap[status] || status;
        }

        function getStatusText(status) {
            const statusMap = {
                'completed': 'Выполнено',
                'pending': 'Ожидает',
                'cancelled': 'Отменено',
                'rescheduled': 'Перенесено'
            };
            return statusMap[status] || status;
        }
    </script>
@endsection
