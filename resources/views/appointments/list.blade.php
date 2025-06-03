@extends('layouts.app')

@section('content')
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: #fff;
            z-index: 9999;
            display: none;
        }

        .notification.success {
            background-color: #4CAF50;
        }

        .notification.error {
            background-color: #f44336;
        }
    </style>

    <div class="appointments-container">
        <div class="appointments-header">
            <h1>Записи</h1>
            <div id="notification"></div>
            <div class="header-actions">
                <div class="view-switcher">
                    <button class="btn-view-switch {{ $viewType === 'list' ? 'active' : '' }}" data-view="list">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Список
                    </button>
                    <button class="btn-view-switch {{ $viewType === 'calendar' ? 'active' : '' }}" data-view="calendar">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        Календарь
                    </button>
                </div>
                <button class="btn-add-appointment" id="addAppointmentBtn">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить запись
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" placeholder="Поиск..." id="searchInput">
                </div>
            </div>
        </div>

        @if($viewType === 'list')
            <div class="appointments-list table-wrapper" id="appointmentsList">
                <table class="table-striped appointments-table" id="appointmentsTable">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Время</th>
                        <th>Клиент</th>
                        <th>Процедура</th>
                        <th>Стоимость</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($appointments as $appointment)
                        <tr data-appointment-id="{{ $appointment->id }}">
                            <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                            <td>{{ $appointment->time }}</td>
                            <td>
                                {{ $appointment->client->name }}
                                @if($appointment->client->instagram)
                                    (<a href="https://instagram.com/{{ $appointment->client->instagram }}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                                        <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $appointment->client->instagram }}
                                    </a>)
                                @endif
                            </td>
                            <td>{{ $appointment->service->name }}</td>
                            <td>{{ number_format($appointment->price, 2) }} грн</td>
                            <td>
                                <div class="appointment-actions actions-cell">
                                    <button class="btn-view" data-appointment-id="{{ $appointment->id }}" title="Просмотр">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Просмотр
                                    </button>
                                    <button class="btn-edit" data-appointment-id="{{ $appointment->id }}" title="Редактировать">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                        Ред.
                                    </button>
                                    <button class="btn-delete" data-appointment-id="{{ $appointment->id }}"  title="Удалить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Удалить
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div id="calendarView" class="calendar-view">
                <!-- Календарь будет здесь -->
                <p>Календарный вид будет реализован позже</p>
            </div>
        @endif
    </div>

    <!-- Модальное окно добавления записи -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Добавить запись</h2>
                <span class="close" onclick="closeAppointmentModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="appointmentForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Дата *</label>
                            <input type="date" name="date" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Время *</label>
                            <input type="time" name="time" required class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Клиент *</label>
                            <div class="client-search-container">
                                <input type="text" class="client-search-input form-control"
                                       placeholder="Начните вводить имя, инстаграм или email клиента..."
                                       oninput="searchClients(this)"
                                       onfocus="searchClients(this)">
                                <div class="client-dropdown" style="display: none;">
                                    <div class="client-dropdown-list"></div>
                                </div>
                                <select name="client_id" class="form-control client-select" style="display: none;" required>
                                    <option value="">Выберите клиента</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client['id'] }}">
                                            {{ $client['name'] }}
                                            @if($client['instagram']) ({{ $client['instagram'] }}) @endif
                                            @if($client['phone']) - {{ $client['phone'] }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Процедура *</label>
                            <select name="service_id" class="form-control" required>
                                <option value="">Выберите процедуру</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Стоимость (Грн)</label> <!-- Убрал * -->
                        <input type="number" step="0.01" name="price" class="form-control" min="0">
                    </div>

                    <div class="form-group">
                        <label>Примечания</label>
                        <textarea name="notes" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeAppointmentModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить запись</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования записи -->
    <div id="editAppointmentModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Редактировать запись</h2>
                <span class="close" onclick="closeEditAppointmentModal()">&times;</span>
            </div>
            <div class="modal-body" id="editAppointmentModalBody">
                <!-- Контент будет загружен динамически -->
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить эту запись?</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelDelete">Отмена</button>
                <button class="confirm-btn" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно просмотра записи -->
    <div id="viewAppointmentModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Детали записи</h2>
                <span class="close" onclick="closeViewAppointmentModal()">&times;</span>
            </div>
            <div class="modal-body" id="viewAppointmentModalBody">
                <!-- Контент будет загружен динамически -->
            </div>
        </div>
    </div>

    <script>
        // Глобальные переменные
        let currentDeleteId = null;
        let allClients = @json($clients->toArray());
        let allServices = @json($services);
        let currentAppointmentId = null;
        let allProducts = @json($products);
        let temporaryProducts = [];
        let isDeletingAppointment = false;
        let currentDeleteIndex = null;
        let currentDeleteProductId = null;

        // Основные функции работы с модальными окнами
        function toggleModal(modalId, show = true) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = show ? 'block' : 'none';
                document.body.style.overflow = show ? 'hidden' : '';
            }
        }


        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete-product')) {
                const btn = e.target.closest('.btn-delete-product');
                currentDeleteProductId = parseInt(btn.dataset.productId);
                isDeletingAppointment = false;

                toggleModal('confirmationModal');
                document.querySelector('#confirmationModal p').textContent = 'Вы уверены, что хотите удалить этот товар?';
            }
        });


        document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {
            if (isDeletingAppointment) {
                deleteAppointment(currentDeleteId);
            } else {
                deleteProductFromAppointment(); // ✅ Только тут вызывается
            }

            toggleModal('confirmationModal', false);
        });

        function closeAppointmentModal() {
            toggleModal('appointmentModal', false);
            document.getElementById('appointmentForm').reset();
            clearErrors('appointmentForm');
        }

        function closeEditAppointmentModal() {
            toggleModal('editAppointmentModal', false);
        }

        function closeViewAppointmentModal() {
            toggleModal('viewAppointmentModal', false);
        }

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            if (!notification) return;

            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';

            // Очищаем предыдущий таймер, если он есть
            if (notification.hideTimeout) {
                clearTimeout(notification.hideTimeout);
            }

            // Устанавливаем новый таймер
            notification.hideTimeout = setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // Функции для работы с записями
        async function viewAppointment(id) {
            currentAppointmentId = id;
            toggleModal('viewAppointmentModal');
            const modalBody = document.getElementById('viewAppointmentModalBody');
            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

            try {
                const response = await fetch(`/appointments/${id}/view`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    renderViewAppointmentModal(data, modalBody);
                } else {
                    throw new Error(data.message || 'Ошибка загрузки данных');
                }
            } catch (error) {
                console.error('Error:', error);
                modalBody.innerHTML = `
                <div class="alert alert-danger">
                    Ошибка: ${escapeHtml(error.message)}
                </div>
                <button class="btn-cancel" onclick="toggleModal('viewAppointmentModal', false)">Закрыть</button>
            `;
            }
        }

        function renderViewAppointmentModal(data, modalBody) {
            const { appointment, sales = [], products = [] } = data;
            temporaryProducts = [...sales]; // Инициализируем временный список товаров

            const servicePrice = parseFloat(appointment.price) || 0;
            const productsTotal = temporaryProducts.reduce((sum, sale) => {
                const price = parseFloat(sale.price || 0);
                return sum + (parseInt(sale.quantity) * price);
            }, 0);

            const totalAmount = servicePrice + productsTotal;

            modalBody.innerHTML = `
        <input type="hidden" id="appointmentId" value="${appointment.id}">
        <input type="hidden" name="date" value="${appointment.date}">
        <div class="appointment-details">
            <div class="detail-row">
                <span class="detail-label">Дата:</span>
                <span class="detail-value">${new Date(appointment.date).toLocaleDateString('ru-RU')}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Время:</span>
                <span class="detail-value">${escapeHtml(appointment.time)}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Клиент:</span>
                <span class="detail-value">${escapeHtml(appointment.client.name)}</span>
            </div>

            <h3>Процедуры</h3>
            <div class="services-section">
                <div class="service-item">
                    <span>${escapeHtml(appointment.service.name)}</span>
                    <span>${servicePrice.toFixed(2)} грн</span>
                </div>
            </div>

            <h3>Товары клиента на эту дату</h3>
            <div class="products-section">
                ${renderProductsList(temporaryProducts)}
                <button class="btn-add-product" id="showAddProductFormBtn">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить товар
                </button>
                <div id="addProductForm" style="display: none; margin-top: 20px;">
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label>Товар *</label>
                            <div class="product-search-container">
                                <input type="text" class="product-search-input form-control"
                                       id="productSearchInput"
                                       placeholder="Начните вводить название товара..."
                                       oninput="searchProducts(this)"
                                       onfocus="showProductDropdown(this)">
                                <div class="product-dropdown" style="display: none;">
                                    <div class="product-dropdown-list"></div>
                                </div>
                                <input type="hidden" id="selectedProductId" name="product_id">
                            </div>
                            <div class="form-group">
                                <label>Количество *</label>
                                <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="form-group">
                                <label>Цена *</label>
                                <input type="number" step="0.01" id="productPrice" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" id="cancelAddProduct">Отмена</button>
                            <button type="button" class="btn-submit" id="submitAddProduct">Добавить</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="total-amount">
                <span class="total-label">Итого:</span>
                <span class="total-value">${totalAmount.toFixed(2)} грн</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeViewAppointmentModal()">Закрыть</button>
                <button type="button" class="btn-submit" id="saveAppointmentChanges">Сохранить изменения</button>
            </div>
        </div>`;

            setupProductHandlers();
        }

        function renderProductsList(sales) {
            if (!sales || sales.length === 0) return '<p>Товары не добавлены</p>';

            return `
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Количество</th>
                        <th>Розничная цена</th>
                        <th>Оптовая цена</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    ${sales.map((sale, index) => {
                        const total = sale.quantity * sale.price;
                        return `
                        <tr data-index="${index}">
                            <td>${escapeHtml(sale.name)}</td>
                            <td>${sale.quantity}</td>
                            <td>${parseFloat(sale.price).toFixed(2)} грн</td>
                            <td>${parseFloat(sale.purchase_price).toFixed(2)} грн</td>
                            <td>${total.toFixed(2)} грн</td>
                            <td>
                                <button class="btn-delete btn-delete-product"
                                        data-product-id="${sale.product_id}"
                                        title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>`;
        }


        async function addNewProcedureToAppointment() {
            const appointmentId = document.getElementById('appointmentId').value;
            const selectedServiceId = prompt("Введите ID новой процедуры:");
            const newPrice = prompt("Введите цену для новой процедуры:");

            if (!selectedServiceId || !newPrice) {
                alert("Процедура и цена обязательны");
                return;
            }

            const response = await fetch(`/appointments/${appointmentId}/add-procedure`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    service_id: selectedServiceId,
                    price: newPrice,
                }),
            });

            const result = await response.json();

            if (result.success) {
                alert("Процедура добавлена как новая запись");
            } else {
                alert("Ошибка: " + result.message);
            }
        }




        function setupProductHandlers() {
            // Используем делегирование событий для динамически создаваемых элементов
            document.addEventListener('click', function(e) {
                const modal = document.getElementById('viewAppointmentModal');
                if (!modal) return;

                // Показать/скрыть форму добавления товара
                if (e.target && e.target.id === 'showAddProductFormBtn') {
                    const form = modal.querySelector('#addProductForm');
                    const btn = modal.querySelector('#showAddProductFormBtn');
                    if (form && btn) {
                        form.style.display = 'block';
                        btn.style.display = 'none';
                    }
                    return;
                }

                // Отмена добавления товара
                if (e.target && e.target.id === 'cancelAddProduct') {
                    const form = modal.querySelector('#addProductForm');
                    const btn = modal.querySelector('#showAddProductFormBtn');
                    if (form && btn) {
                        form.style.display = 'none';
                        btn.style.display = 'block';
                        resetProductForm();
                    }
                    return;
                }

                // Добавление товара
                if (e.target && e.target.id === 'submitAddProduct') {
                    e.preventDefault();
                    addProductToAppointment();
                    return;
                }

                // Удаление товара
                if (e.target.closest('.btn-delete-product')) {
                    e.preventDefault();
                    const btn = e.target.closest('.btn-delete-product');
                    currentDeleteProductId = btn.dataset.productId;
                    currentDeleteIndex = btn.closest('tr')?.dataset.index;
                    isDeletingAppointment = false;

                    toggleModal('confirmationModal');
                    document.querySelector('#confirmationModal p').textContent = 'Вы уверены, что хотите удалить этот товар?';
                }
                if (e.target && e.target.id === 'showAddServiceFormBtn') {
                    document.getElementById('addServiceForm').style.display = 'block';
                    e.target.style.display = 'none';
                }

                // Отмена
                if (e.target && e.target.id === 'cancelAddService') {
                    document.getElementById('addServiceForm').style.display = 'none';
                    document.getElementById('showAddServiceFormBtn').style.display = 'inline-block';
                }

                // Сохранить
                if (e.target && e.target.id === 'submitAddService') {
                    addProcedureToAppointment();
                }
            });

            document.getElementById('cancelDelete')?.addEventListener('click', function() {
                toggleModal('confirmationModal', false);
                currentDeleteId = null;
                currentDeleteIndex = null;
                isDeletingAppointment = false;
            });

            // Обработчик кнопки подтверждения удаления в модальном окне
            document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {
                if (currentDeleteId !== null) {
                    if (isDeletingAppointment) {
                        deleteAppointment(currentDeleteId); // Удаление записи
                    } else {
                        deleteProductFromAppointment(currentDeleteId); // Удаление товара
                    }
                    toggleModal('confirmationModal', false);
                    currentDeleteId = null;
                    isDeletingAppointment = false;
                }
            });

            // Обработчик кнопки отмены в модальном окне
            document.getElementById('cancelDelete')?.addEventListener('click', function() {
                toggleModal('confirmationModal', false);
                currentDeleteId = null;
            });

            // Сохранение изменений
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'saveAppointmentChanges') {
                    saveAppointmentChanges();
                }
            });
        }

        async function addProcedureToAppointment() {
            const appointmentId = document.getElementById('appointmentId').value;
            const serviceId = document.getElementById('serviceSelect').value;
            const price = document.getElementById('servicePrice').value;

            if (!serviceId || !price) {
                showNotification('Заполните процедуру и цену', 'error');
                return;
            }

            const response = await fetch(`/appointments/${appointmentId}/add-procedure`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ service_id: serviceId, price })
            });

            const data = await response.json();

            if (data.success) {
                // Визуально вставляем в список
                const servicesSection = document.querySelector('.services-section');
                const service = allServices.find(s => s.id == serviceId);
                const el = document.createElement('div');
                el.classList.add('service-item');
                el.innerHTML = `<span>${service.name}</span><span>${parseFloat(price).toFixed(2)} грн</span>`;
                servicesSection.appendChild(el);

                // Сброс формы
                document.getElementById('addServiceForm').style.display = 'none';
                document.getElementById('showAddServiceFormBtn').style.display = 'inline-block';
                document.getElementById('serviceSelect').value = '';
                document.getElementById('servicePrice').value = '';

                updateTotalAmount();
                showNotification('Процедура добавлена');
            } else {
                showNotification(data.message || 'Ошибка добавления процедуры', 'error');
            }
        }



        function resetProductForm() {
            const modal = document.getElementById('viewAppointmentModal');
            if (!modal) return;

            const searchInput = modal.querySelector('#productSearchInput');
            const productIdInput = modal.querySelector('#selectedProductId');
            const quantityInput = modal.querySelector('#productQuantity');
            const priceInput = modal.querySelector('#productPrice');

            if (searchInput) searchInput.value = '';
            if (productIdInput) productIdInput.value = '';
            if (quantityInput) quantityInput.value = '1';
            if (priceInput) priceInput.value = '';
        }

        // Функции для работы с товарами
        async function addProductToAppointment() {
            const modal = document.getElementById('viewAppointmentModal');
            if (!modal) return;

            const productId = modal.querySelector('#selectedProductId')?.value;
            const quantity = modal.querySelector('#productQuantity')?.value;
            const price = modal.querySelector('#productPrice')?.value;
            const productName = modal.querySelector('#productSearchInput')?.value;

            // Проверки
            if (!productId) {
                showNotification('Пожалуйста, выберите товар', 'error');
                return;
            }

            const realProduct = allProducts.find(p => p.id == productId);
            if (!realProduct) {
                showNotification('Некорректный товар. Выберите товар из выпадающего списка.', 'error');
                return;
            }

            if (!quantity || parseInt(quantity) <= 0) {
                showNotification('Укажите корректное количество', 'error');
                return;
            }

            if (!price || parseFloat(price) <= 0) {
                showNotification('Укажите корректную цену', 'error');
                return;
            }

            // Получаем оптовую цену из продукта
            const purchasePrice = parseFloat(realProduct.purchase_price || realProduct.purchasePrice || 0);

            // Добавляем товар с названием и оптовой ценой
            temporaryProducts.push({
                product_id: productId,
                name: productName || realProduct.name, // Сохраняем название товара
                quantity: quantity,
                price: price,
                purchase_price: purchasePrice // Сохраняем оптовую цену
            });

            resetProductForm();
            updateProductsList();
        }



        function updateProductsList() {
            const modal = document.getElementById('viewAppointmentModal');
            if (!modal) return;

            const productsSection = modal.querySelector('.products-section');
            if (!productsSection) return;

            // Получаем все товары из базы данных для отображения актуальной информации
            const allProducts = @json($products);

            productsSection.innerHTML = `
        ${renderProductsList(temporaryProducts, allProducts)}
        <button class="btn-add-product" id="showAddProductFormBtn">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            Добавить товар
        </button>
        <div id="addProductForm" style="display: none; margin-top: 20px;">
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label>Товар *</label>
                    <div class="product-search-container">
                        <input type="text" class="product-search-input form-control"
                               id="productSearchInput"
                               placeholder="Начните вводить название товара..."
                               oninput="searchProducts(this)"
                               onfocus="showProductDropdown(this)">
                        <div class="product-dropdown" style="display: none;">
                            <div class="product-dropdown-list"></div>
                        </div>
                        <input type="hidden" id="selectedProductId" name="product_id">
                    </div>
                </div>
                <div class="form-group">
                    <label>Количество *</label>
                    <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label>Цена *</label>
                    <input type="number" step="0.01" id="productPrice" class="form-control" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" id="cancelAddProduct">Отмена</button>
                <button type="button" class="btn-submit" id="submitAddProduct">Добавить</button>
            </div>
        </div>
    `;

            updateTotalAmount();

            const btnAdd = modal.querySelector('#showAddProductFormBtn');
            const form = modal.querySelector('#addProductForm');
            const cancelBtn = modal.querySelector('#cancelAddProduct');
            const submitBtn = modal.querySelector('#submitAddProduct');

            // ✅ ПРИ ЗАГРУЗКЕ: если форма скрыта — кнопка появляется
            if (btnAdd && form) {
                // Показываем кнопку, если форма скрыта
                if (form.style.display === 'none') {
                    btnAdd.style.display = 'inline-block';
                } else {
                    btnAdd.style.display = 'none';
                }

                btnAdd.addEventListener('click', () => {
                    btnAdd.style.display = 'none';
                    form.style.display = 'block';
                });
            }

            // Отмена добавления товара
            if (cancelBtn && form && btnAdd) {
                cancelBtn.addEventListener('click', () => {
                    form.style.display = 'none';
                    btnAdd.style.display = 'inline-block';
                    resetProductForm();
                });
            }

            // Подтвердить добавление
            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    addProductToAppointment();
                });
            }


            if (btnAdd && form) {
                btnAdd.addEventListener('click', () => {
                    btnAdd.style.display = 'none';
                    form.style.display = 'block';
                });
            }

            updateTotalAmount();
        }


        document.querySelector('.modal-footer')?.appendChild(document.getElementById('saveAppointmentChanges'));

        function updateTotalAmount() {
            const servicePrice = parseFloat(document.querySelector('.service-item span:nth-child(2)')?.textContent || '0');
            const productsTotal = temporaryProducts.reduce((sum, product) => {
                return sum + (parseInt(product.quantity) * parseFloat(product.price));
            }, 0);
            const totalAmount = servicePrice + productsTotal;

            const totalElement = document.querySelector('.total-value');
            if (totalElement) {
                totalElement.textContent = `${totalAmount.toFixed(2)} грн`;
            }
        }



        // Функции для поиска товаров
        function searchProducts(input) {
            const searchTerm = input.value.toLowerCase();
            const dropdown = input.nextElementSibling;
            const dropdownList = dropdown.querySelector('.product-dropdown-list');

            if (searchTerm.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            const filteredProducts = allProducts.filter(product => {
                const nameMatch = product.name?.toLowerCase().includes(searchTerm) || false;
                const quantity = product.warehouse?.quantity || 0;
                return nameMatch && quantity > 0;
            });

            if (filteredProducts.length === 0) {
                dropdownList.innerHTML = '<div class="product-dropdown-item">Товары не найдены</div>';
            } else {
                dropdownList.innerHTML = filteredProducts.map(product => {
                    const retailPrice = parseFloat(product.warehouse?.retail_price) || 0;
                    const purchasePrice = parseFloat(product.warehouse?.purchase_price) || 0;
                    const formattedRetailPrice = !isNaN(retailPrice) ? retailPrice.toFixed(2) : '0.00';
                    const formattedPurchasePrice = !isNaN(purchasePrice) ? purchasePrice.toFixed(2) : '0.00';
                    const quantity = product.warehouse?.quantity || 0;

                    return `
                <div class="product-dropdown-item"
                     onclick="selectProduct(this, '${escapeHtml(product.name)}', ${product.id}, ${retailPrice}, ${purchasePrice})">
                    ${escapeHtml(product.name)} (${quantity} шт, розн: ${formattedRetailPrice} грн, опт: ${formattedPurchasePrice} грн)
                </div>
            `;
                }).join('');
            }

            dropdown.style.display = 'block';
        }


        function showProductDropdown(input) {
            if (input.value.length > 0) {
                searchProducts(input);
            } else {
                const dropdown = input.nextElementSibling;
                const dropdownList = dropdown.querySelector('.product-dropdown-list');

                const availableProducts = allProducts.filter(p => {
                    const quantity = p.warehouse?.quantity || 0;
                    return quantity > 0;
                });

                if (availableProducts.length === 0) {
                    dropdownList.innerHTML = '<div class="product-dropdown-item">Нет доступных товаров</div>';
                } else {
                    dropdownList.innerHTML = availableProducts.map(product => {
                        // Safely get and format the price
                        const price = parseFloat(product.warehouse?.retail_price) || 0;
                        const formattedPrice = !isNaN(price) ? price.toFixed(2) : '0.00';
                        const quantity = product.warehouse?.quantity || 0;

                        return `
                    <div class="product-dropdown-item"
                         onclick="selectProduct(this, '${escapeHtml(product.name)}', ${product.id}, ${price})">
                        ${escapeHtml(product.name)} (${quantity} шт, ${formattedPrice} грн)
                    </div>
                `;
                    }).join('');
                }
                dropdown.style.display = 'block';
            }
        }

        function selectProduct(element, productName, productId, retailPrice, purchasePrice) {
            const container = element.closest('.product-search-container');
            const input = container.querySelector('.product-search-input');
            const dropdown = container.querySelector('.product-dropdown');
            const productIdInput = container.querySelector('#selectedProductId');
            const priceInput = document.getElementById('productPrice');

            if (input && productIdInput && priceInput) {
                input.value = productName;
                productIdInput.value = productId;
                priceInput.value = retailPrice;
                dropdown.style.display = 'none';

                // Сохраняем оптовую цену в data-атрибут для последующего использования
                element.dataset.purchasePrice = purchasePrice;

                // Установка максимального количества
                const product = allProducts.find(p => p.id === productId);
                const maxQuantity = product?.warehouse?.quantity || 0;
                const quantityInput = document.getElementById('productQuantity');
                if (quantityInput) {
                    quantityInput.max = maxQuantity;
                    quantityInput.value = Math.min(1, maxQuantity);
                }
            }
        }


        // Закрытие выпадающего списка при клике вне его
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.product-search-container')) {
                document.querySelectorAll('.product-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });

        function renderAddProductForm(products) {
            return `
            <div class="form-group">
                <label>Товар</label>
                <select id="productSelect" class="form-control">
                    <option value="">Выберите товар</option>
                    ${products.map(p => {
                const quantity = p.warehouse?.quantity || 0;
                if (quantity <= 0) return '';

                const retailPrice = parseFloat(p.warehouse?.retail_price || 0);
                const wholesalePrice = parseFloat(p.warehouse?.wholesale_price || 0);
                return `
                        <option value="${p.id}"
                                data-quantity="${quantity}"
                                data-retail-price="${retailPrice}"
                                data-wholesale-price="${wholesalePrice}"
                                data-name="${escapeHtml(p.name)}">
                            ${escapeHtml(p.name)} (${retailPrice.toFixed(2)} грн, остаток: ${quantity})
                        </option>
                    `;
            }).join('')}
                </select>
            </div>

            <div class="form-group">
                <label>Название товара</label>
                <input type="text" id="productNameDisplay" class="form-control" readonly>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Оптовая цена (грн)</label>
                    <input type="number" step="0.01" id="productWholesalePrice" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Розничная цена (грн) *</label>
                    <input type="number" step="0.01" id="productRetailPrice" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>Количество *</label>
                <input type="number" id="productQuantity" class="form-control" min="1" value="1" required>
            </div>

            <button class="btn-submit" id="submitAddProduct">Добавить товар</button>
            <button class="btn-cancel" id="cancelAddProduct">Отмена</button>
        `;
        }

        function setupAddProductHandlers() {
            document.getElementById('productSelect')?.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption?.value) {
                    const maxQuantity = parseInt(selectedOption.dataset.quantity);
                    document.getElementById('productQuantity').max = maxQuantity;
                    document.getElementById('productQuantity').value = Math.min(1, maxQuantity);
                    document.getElementById('productNameDisplay').value = selectedOption.dataset.name || '';
                    document.getElementById('productWholesalePrice').value = selectedOption.dataset.wholesalePrice || '';
                    document.getElementById('productRetailPrice').value = selectedOption.dataset.retailPrice || '';
                } else {
                    document.getElementById('productNameDisplay').value = '';
                    document.getElementById('productWholesalePrice').value = '';
                    document.getElementById('productRetailPrice').value = '';
                }
            });

            document.querySelector('.btn-add-product')?.addEventListener('click', function() {
                document.getElementById('addProductForm').style.display = 'block';
                this.style.display = 'none';
            });

            document.getElementById('cancelAddProduct')?.addEventListener('click', function() {
                document.getElementById('addProductForm').style.display = 'none';
                document.querySelector('.btn-add-product').style.display = 'block';
            });

            document.getElementById('submitAddProduct')?.addEventListener('click', async function() {
                await addProductToAppointment();
            });

            // Обработчик удаления товара
            document.querySelectorAll('.btn-delete-product').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const saleId = this.dataset.saleId;
                    await deleteProductFromAppointment(saleId);
                });
            });
            document.getElementById('saveAppointmentChanges')?.addEventListener('click', async function() {
                // Здесь можно добавить логику для сохранения всех изменений
                showNotification('Изменения сохранены');
                closeViewAppointmentModal();
                // При необходимости обновите данные на странице
            });

        }


        async function deleteProductFromAppointment() {
            try {
                const appointmentId = document.getElementById('appointmentId')?.value;
                if (!appointmentId) {
                    showNotification('Не удалось определить запись', 'error');
                    return;
                }

                // Если это временный товар (еще не сохраненный)
                if (currentDeleteIndex !== null) {
                    temporaryProducts.splice(currentDeleteIndex, 1);
                    updateProductsList();
                    updateTotalAmount();
                    showNotification('Товар удален');
                    return;
                }

                // Если товар уже сохранен в базе
                if (currentDeleteProductId) {
                    const response = await fetch(`/appointments/${appointmentId}/remove-product`, { // Измените URL на правильный
                        method: 'POST', // Используйте POST вместо DELETE, если нужно
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ product_id: currentDeleteProductId })
                    });

                    const data = await response.json();
                    if (!data.success) throw new Error(data.message || 'Ошибка удаления с сервера');

                    // После успешного удаления перезагружаем данные записи
                    await viewAppointment(appointmentId);
                    showNotification('Товар успешно удален');
                }
            } catch (err) {
                console.error(err);
                showNotification(err.message || 'Ошибка при удалении товара', 'error');
            } finally {
                currentDeleteProductId = null;
                currentDeleteIndex = null;
            }
        }

        // Функции для работы с клиентами
        function searchClients(inputElement) {
            const searchTerm = inputElement.value.trim().toLowerCase();
            const dropdown = inputElement.nextElementSibling;
            const dropdownList = dropdown.querySelector('.client-dropdown-list');

            if (searchTerm.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            const filteredClients = allClients.filter(client => {
                const nameMatch = client.name?.toLowerCase().includes(searchTerm) || false;
                const instagramMatch = client.instagram?.toLowerCase().includes(searchTerm) || false;
                const emailMatch = client.email?.toLowerCase().includes(searchTerm) || false;
                const phoneMatch = client.phone?.includes(searchTerm) || false;

                return nameMatch || instagramMatch || emailMatch || phoneMatch;
            });

            if (filteredClients.length > 0) {
                dropdownList.innerHTML = filteredClients.map(client => {
                    const name = escapeHtml(client.name || '');
                    const instagram = client.instagram ? `(@${escapeHtml(client.instagram)})` : '';
                    const phone = client.phone ? ` - ${escapeHtml(client.phone)}` : '';

                    return `
                    <div class="client-dropdown-item"
                         data-client-id="${client.id}"
                         onclick="selectClient(this, '${client.id}', '${name} ${instagram}')">
                        ${name} ${instagram} ${phone}
                    </div>
                `;
                }).join('');
                dropdown.style.display = 'block';
            } else {
                dropdownList.innerHTML = '<div class="client-dropdown-item no-results">Клиенты не найдены</div>';
                dropdown.style.display = 'block';
            }
        }

        function selectClient(element, clientId, clientName) {
            const container = element.closest('.client-search-container');
            const input = container.querySelector('.client-search-input');
            const select = container.querySelector('.client-select');
            const dropdown = container.querySelector('.client-dropdown');

            input.value = clientName.trim();
            select.value = clientId;
            dropdown.style.display = 'none';
        }

        // Функции для работы с записями
        async function editAppointment(event, id) {
            event.preventDefault();
            currentAppointmentId = id;
            toggleModal('editAppointmentModal');
            const modalBody = document.getElementById('editAppointmentModalBody');
            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

            try {
                const response = await fetch(`/appointments/${id}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    renderEditAppointmentForm(data.appointment);
                } else {
                    throw new Error(data.message || 'Ошибка загрузки данных');
                }
            } catch (error) {
                console.error('Error:', error);
                modalBody.innerHTML = `
                <div class="alert alert-danger">
                    Ошибка: ${escapeHtml(error.message)}
                </div>
                <button class="btn-cancel" onclick="toggleModal('editAppointmentModal', false)">Закрыть</button>
            `;
            }
        }

        function renderEditAppointmentForm(appointment) {
            const modalBody = document.getElementById('editAppointmentModalBody');
            modalBody.innerHTML = `
            <form id="editAppointmentForm">
                @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>Дата *</label>
                    <input type="date" name="date" value="${formatDateForInput(appointment.date)}" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Время *</label>
                        <input type="time" name="time" value="${escapeHtml(appointment.time)}" required class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Клиент *</label>
                        <select name="client_id" class="form-control" required>
                            <option value="">Выберите клиента</option>
                            ${allClients.map(client => `
                                <option value="${client.id}" ${client.id == appointment.client_id ? 'selected' : ''}>
                                    ${escapeHtml(client.name)}
                                    ${client.instagram ? `(${escapeHtml(client.instagram)})` : ''}
                                    ${client.phone ? ` - ${escapeHtml(client.phone)}` : ''}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Процедура *</label>
                        <select name="service_id" class="form-control" required>
                            <option value="">Выберите процедуру</option>
                            ${allServices.map(service => `
                                <option value="${service.id}"
                                        data-price="${service.price}"
                                        ${service.id == appointment.service_id ? 'selected' : ''}>
                                    ${escapeHtml(service.name)}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Стоимость (Грн)</label>
                    <input type="number" step="0.01" name="price" value="${parseFloat(appointment.price).toFixed(2)}" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label>Примечания</label>
                    <textarea name="notes" rows="2" class="form-control">${escapeHtml(appointment.notes || '')}</textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditAppointmentModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить изменения</button>
                </div>
            </form>
        `;

            // Обработчик изменения выбора процедуры
            document.querySelector('#editAppointmentModal [name="service_id"]')?.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const priceInput = document.querySelector('#editAppointmentModal [name="price"]');
                if (selectedOption?.dataset.price) {
                    priceInput.value = selectedOption.dataset.price;
                }
            });

            // Обработчик формы редактирования
            document.getElementById('editAppointmentForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();
                await submitEditAppointmentForm(this, currentAppointmentId);
            });
        }

        async function submitEditAppointmentForm(form, appointmentId) {
            clearErrors('editAppointmentForm');
            const formData = new FormData(form);

            try {
                // Add the _method field for Laravel to recognize it as PUT
                formData.append('_method', 'PUT');

                const response = await fetch(`/appointments/${appointmentId}`, {
                    method: 'POST', // Still use POST as the HTTP method
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showNotification('Запись успешно обновлена');
                    closeEditAppointmentModal();
                    updateAppointmentRow(data.appointment);
                } else if (data.errors) {
                    displayErrors(data.errors, 'editAppointmentForm');
                } else {
                    throw new Error(data.message || 'Ошибка при обновлении записи');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification(error.message || 'Ошибка при обновлении записи', 'error');
            }
        }

        function updateAppointmentRow(appointmentData) {
            try {
                const row = document.querySelector(`tr[data-appointment-id="${appointmentData.id}"]`);
                if (!row) return;

                // Обновляем дату
                const dateCell = row.querySelector('td:nth-child(1)');
                if (dateCell && appointmentData.date) {
                    dateCell.textContent = new Date(appointmentData.date).toLocaleDateString('ru-RU');
                }

                // Обновляем время
                const timeCell = row.querySelector('td:nth-child(2)');
                if (timeCell && appointmentData.time) {
                    timeCell.textContent = appointmentData.time;
                }

                // Обновляем клиента
                const clientCell = row.querySelector('td:nth-child(3)');
                if (clientCell && appointmentData.client) {
                    const client = appointmentData.client;
                    let clientHtml = escapeHtml(client.name);
                    if (client.instagram) {
                        clientHtml += ` (<a href="https://instagram.com/${escapeHtml(client.instagram)}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                        <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                        </svg>
                        ${escapeHtml(client.instagram)}
                    </a>)`;
                    }
                    clientCell.innerHTML = clientHtml;
                }

                // Обновляем процедуру
                const serviceCell = row.querySelector('td:nth-child(4)');
                if (serviceCell && appointmentData.service) {
                    serviceCell.textContent = appointmentData.service.name;
                }

                // Обновляем стоимость
                const priceCell = row.querySelector('td:nth-child(5)');
                if (priceCell && appointmentData.price !== undefined) {
                    priceCell.textContent = parseFloat(appointmentData.price).toFixed(2) + ' грн';
                }
            } catch (error) {
                console.error('Ошибка при обновлении строки записи:', error);
            }
        }

        async function submitAppointmentForm(form) {
            clearErrors('appointmentForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('/appointments', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showNotification('Запись успешно создана');
                    closeAppointmentModal();
                    
                    // Добавляем новую запись в таблицу без перезагрузки
                    const appointment = data.appointment;
                    const tbody = document.querySelector('#appointmentsTable tbody');
                    
                    if (tbody) {
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-appointment-id', appointment.id);
                        
                        newRow.innerHTML = `
                            <td>${new Date(appointment.date).toLocaleDateString('ru-RU')}</td>
                            <td>${escapeHtml(appointment.time)}</td>
                            <td>
                                ${escapeHtml(appointment.client.name)}
                                ${appointment.client.instagram ? `
                                    (<a href="https://instagram.com/${escapeHtml(appointment.client.instagram)}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                                        <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                                    </svg>
                                    ${escapeHtml(appointment.client.instagram)}
                                </a>)` 
                            : ''}
                        </td>
                        <td>${escapeHtml(appointment.service.name)}</td>
                        <td>${parseFloat(appointment.price).toFixed(2)} грн</td>
                        <td>
                            <div class="appointment-actions actions-cell">
                                <button class="btn-view" data-appointment-id="${appointment.id}" title="Просмотр">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Просмотр
                                </button>
                                <button class="btn-edit" data-appointment-id="${appointment.id}" title="Редактировать">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete" data-appointment-id="${appointment.id}" title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Удалить
                                </button>
                            </div>
                        </td>
                    `;
                    
                    tbody.insertBefore(newRow, tbody.firstChild);
                }
            } else if (data.errors) {
                displayErrors(data.errors, 'appointmentForm');
            } else {
                throw new Error(data.message || 'Ошибка при создании записи');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message || 'Ошибка при создании записи', 'error');
        }
    }

        function confirmDeleteAppointment(event, id) {
            event.preventDefault();
            currentDeleteId = id;
            isDeletingAppointment = true; // Добавляем флаг
            toggleModal('confirmationModal');
        }

        async function deleteAppointment(id) {
            try {
                const response = await fetch(`/appointments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    showNotification('Запись успешно удалена');
                    document.querySelector(`tr[data-appointment-id="${id}"]`)?.remove();

                    // Закрываем модальное окно просмотра, если оно открыто
                    if (currentAppointmentId === id) {
                        closeViewAppointmentModal();
                    }
                } else {
                    throw new Error(data.message || 'Ошибка при удалении записи');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification(error.message || 'Ошибка при удалении записи', 'error');
            }
        }


        // Вспомогательные функции
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDateForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function clearErrors(formId) {
            const form = document.getElementById(formId);
            if (!form) return;

            const errorElements = form.querySelectorAll('.error-message');
            errorElements.forEach(el => el.remove());

            const errorInputs = form.querySelectorAll('.is-invalid');
            errorInputs.forEach(el => el.classList.remove('is-invalid'));
        }

        function displayErrors(errors, formId) {
            clearErrors(formId);
            const form = document.getElementById(formId);
            if (!form) return;

            for (const [field, messages] of Object.entries(errors)) {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    errorElement.textContent = messages.join(', ');
                    input.parentNode.insertBefore(errorElement, input.nextSibling);
                }
            }
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Обработчики кнопок
            document.getElementById('addAppointmentBtn')?.addEventListener('click', () => toggleModal('appointmentModal'));
            document.getElementById('cancelDelete')?.addEventListener('click', () => toggleModal('confirmationModal', false));
            document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
                if (isDeletingAppointment) {
                    await deleteAppointment(currentDeleteId);
                } else {
                    await deleteProductFromAppointment();
                }
                toggleModal('confirmationModal', false);
            });


            // Обработчик формы добавления записи
            document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                submitAppointmentForm(this);
            });

            // Обработчик изменения выбора процедуры
            document.querySelector('[name="service_id"]')?.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const priceInput = document.querySelector('[name="price"]');
                if (selectedOption?.dataset.price) {
                    priceInput.value = selectedOption.dataset.price;
                }
            });

            // Обработчик переключения между видами
            document.querySelectorAll('.btn-view-switch').forEach(btn => {
                btn.addEventListener('click', function() {
                    window.location.href = `/appointments?view=${this.dataset.view}`;
                });
            });

            // Поиск по таблице
            document.getElementById('searchInput')?.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('#appointmentsTable tbody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
                });
            });

            // Делегирование событий
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-view')) {
                    viewAppointment(e.target.closest('.btn-view').dataset.appointmentId);
                }
                if (e.target.closest('.btn-edit')) {
                    editAppointment(e, e.target.closest('.btn-edit').dataset.appointmentId);
                }
                if (e.target.closest('.btn-delete')) {
                    confirmDeleteAppointment(e, e.target.closest('.btn-delete').dataset.appointmentId);
                }
                if (e.target == document.getElementById('appointmentModal')) {
                    toggleModal('appointmentModal', false);
                }
                if (e.target == document.getElementById('editAppointmentModal')) {
                    toggleModal('editAppointmentModal', false);
                }
                if (e.target == document.getElementById('viewAppointmentModal')) {
                    toggleModal('viewAppointmentModal', false);
                }
                if (e.target == document.getElementById('confirmationModal')) {
                    toggleModal('confirmationModal', false);
                }
                if (e.target && e.target.id === 'saveAppointmentChanges') {
                    saveAppointmentChanges();
                }
            });
        });
        async function saveAppointmentChanges() {
            const modal = document.getElementById('viewAppointmentModal');
            const appointmentId = modal.querySelector('#appointmentId')?.value;

            if (!appointmentId) {
                showNotification('ID записи не найден', 'error');
                return;
            }

            // Get basic appointment data
            const dateText = modal.querySelector('.detail-row:nth-child(1) .detail-value')?.textContent;
            const time = modal.querySelector('.detail-row:nth-child(2) .detail-value')?.textContent;
            const clientName = modal.querySelector('.detail-row:nth-child(3) .detail-value')?.textContent;
            const serviceName = modal.querySelector('.service-item span:first-child')?.textContent;
            const priceText = modal.querySelector('.service-item span:nth-child(2)')?.textContent;
            const price = parseFloat(priceText?.replace('грн', '').trim()) || 0;

            // Find client and service IDs
            const client = allClients.find(c => c.name === clientName);
            const service = allServices.find(s => s.name === serviceName);

            // Format date
            let formattedDate = '';
            if (dateText) {
                const [day, month, year] = dateText.split('.');
                formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }

            if (!client || !client.id) {
                showNotification('Не удалось определить клиента', 'error');
                return;
            }

            if (!service || !service.id) {
                showNotification('Не удалось определить услугу', 'error');
                return;
            }

            // Prepare products data
            const productsPayload = temporaryProducts.map(p => {
                const product = allProducts.find(prod => prod.id == p.product_id) || {};
                return {
                    product_id: p.product_id,
                    quantity: parseInt(p.quantity) || 1,
                    price: parseFloat(p.price) || 0,
                    wholesale_price: parseFloat(p.purchase_price) ||
                        parseFloat(product.warehouse?.purchase_price) || 0
                };
            });

            const payload = {
                service_id: service.id,
                client_id: client.id,
                date: formattedDate,
                time: time,
                price: price,
                products: productsPayload
            };

            try {
                const response = await fetch(`/appointments/${appointmentId}`, {
                    method: 'POST', // Laravel needs POST for PUT method with _method parameter
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ...payload,
                        _method: 'PUT' // Laravel way to simulate PUT request
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || `Ошибка сервера: ${response.status}`);
                }

                if (data.success) {
                    showNotification('Изменения сохранены');
                    toggleModal('viewAppointmentModal', false);
                    window.location.reload();
                } else {
                    showNotification(data.message || 'Ошибка сохранения', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Ошибка при сохранении: ' + error.message, 'error');
            }
        }



        function formatDateForPayload(dateString) {
            if (!dateString) return '';
            // Try both possible date formats (from table view and modal view)
            if (dateString.includes('.')) {
                // Format: dd.mm.yyyy
                const [day, month, year] = dateString.split('.');
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            } else {
                // Format: yyyy-mm-dd (already correct)
                return dateString;
            }
        }

    </script>
@endsection