@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    @php
    if (!function_exists('formatPrice')) {
        function formatPrice($price) {
            return (fmod($price, 1) == 0.0) ? (int)$price : number_format($price, 2, '.', '');
        }
    }
    @endphp
    <div class="sales-container">
        <div class="sales-header">
            <h1>Продажи</h1>
            <div id="notification" class="notification">
                <!-- Уведомления будут появляться здесь -->
            </div>
            <div class="header-actions">
                <button class="btn-add-sale" onclick="openSaleModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить продажу
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" placeholder="Поиск..." id="searchInput">
                </div>
            </div>
        </div>

        <div class="sales-list table-wrapper" id="salesList">
            <table class="table-striped sale-table" id="salesTable">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Клиент</th>
                    <th>Товар</th>
                    <th>Фото</th>
                    <th>Опт. цена</th>
                    <th>Розн. цена</th>
                    <th>Кол-во</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody id="salesTableBody">
                <!-- Данные будут загружаться через AJAX -->
                </tbody>
            </table>
        </div>
        <div id="salesPagination"></div>
    </div>

    <!-- Модальное окно добавления продажи -->
    <div id="saleModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Добавить продажу</h2>
                <span class="close" onclick="closeSaleModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="saleForm">
                    @csrf
                    <div class="form-row date-client-row">
                        <div class="form-group">
                            <label>Клиент *</label>
                            <div class="client-search-container">
                                <input type="text" class="client-search-input form-control"
                                       placeholder="Начните вводить имя, инстаграм или email клиента..."
                                       oninput="searchClients(this)" onfocus="showClientDropdown(this)" autocomplete="off">
                                <div class="client-dropdown" style="display: none;">
                                    <div class="client-dropdown-list"></div>
                                </div>
                                <select name="client_id" class="form-control client-select" style="display: none;" required>
                                    <option value="">Выберите клиента</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">
                                            {{ $client->name }}
                                            @if($client->instagram) (@{{ $client->instagram }}) @endif
                                            @if($client->phone) - {{ $client->phone }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Дата *</label>
                            <input type="date" name="date" required class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Примечания</label>
                        <textarea name="notes" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="items-container" id="itemsContainer">
                        <h3>Товары</h3>
                        <div class="item-row template" style="display: none;">
                            <div class="form-row">
                                <div class="form-group product-field">
                                    <label>Товар *</label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control"
                                               placeholder="Начните вводить название товара..."
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select" style="display: none;"
                                                onchange="updateProductPrices(this)">
                                            <option value="">Выберите товар</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-wholesale="{{ $product->wholesale_price }}"
                                                        data-retail="{{ $product->retail_price }}"
                                                        data-quantity="{{ $product->available_quantity }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group price-field">
                                    <label>Оптовая цена *</label>
                                    <input type="number" step="0.01" name="items[0][wholesale_price]"
                                            class="form-control wholesale-price" min="0.01" readonly>
                                </div>
                                <div class="form-group price-field">
                                    <label>Розничная цена *</label>
                                    <input type="number" step="0.01" name="items[0][retail_price]"
                                            class="form-control retail-price" min="0.01" >
                                </div>
                                <div class="form-group quantity-field">
                                    <label>Кол-во *</label>
                                    <input type="number" name="items[0][quantity]"
                                           class="form-control quantity" min="1" value="1" max="1"
                                           oninput="validateQuantity(this)">
                                </div>
                                <div class="form-group remove-field">
                                    <button type="button" class="btn-remove-item" onclick="removeItemRow(this)">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="item-row">
                            <div class="form-row">
                                <div class="form-group product-field">
                                    <label>Товар *</label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control"
                                               placeholder="Начните вводить название товара..."
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select" style="display: none;"
                                                onchange="updateProductPrices(this)">
                                            <option value="">Выберите товар</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-wholesale="{{ $product->wholesale_price }}"
                                                        data-retail="{{ $product->retail_price }}"
                                                        data-quantity="{{ $product->available_quantity }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group price-field">
                                    <label>Оптовая цена *</label>
                                    <input type="number" step="0.01" name="items[0][wholesale_price]"
                                           required class="form-control wholesale-price" min="0.01" readonly>
                                </div>
                                <div class="form-group price-field">
                                    <label>Розничная цена *</label>
                                    <input type="number" step="0.01" name="items[0][retail_price]"
                                           required class="form-control retail-price" min="0.01" >
                                </div>
                                <div class="form-group quantity-field">
                                    <label>Кол-во *</label>
                                    <input type="number" name="items[0][quantity]" required
                                           class="form-control quantity" min="1" value="1" max="1"
                                           oninput="validateQuantity(this)">
                                </div>
                                <div class="form-group remove-field">
                                    <button type="button" class="btn-remove-item" onclick="removeItemRow(this)">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-add-item" onclick="addItemRow()">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                            Добавить товар
                        </button>
                        <button type="button" class="btn-cancel" onclick="closeSaleModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить продажу</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования продажи -->
    <div id="editSaleModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Редактировать продажу</h2>
                <span class="close" onclick="closeEditSaleModal()">&times;</span>
            </div>
            <div class="modal-body" id="editSaleModalBody">
                <!-- Контент будет загружен динамически -->
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить эту продажу?</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelDelete">Отмена</button>
                <button class="confirm-btn" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>

    <script>
        // Глобальные переменные
        let currentDeleteId = null;
        let itemCounter = 1;
        let allClients = @json($clients);
        let allProducts = @json($products);
        let currentDeleteSaleId = null;
        let currentDeleteItemId = null;

        // Функции для работы с модальными окнами
        function openSaleModal() {
            document.getElementById('saleForm').reset();
            // Устанавливаем текущую дату
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('#saleForm [name="date"]').value = today;
            document.getElementById('saleModal').style.display = 'block';
        }

        function closeSaleModal() {
            document.getElementById('saleModal').style.display = 'none';
            clearErrors('saleForm');
            resetSaleForm();
        }

        function closeEditSaleModal() {
            document.getElementById('editSaleModal').style.display = 'none';
        }

        // Закрытие модальных окон при клике вне их
        window.onclick = function(event) {
            if (event.target == document.getElementById('saleModal')) {
                closeSaleModal();
            }
            if (event.target == document.getElementById('editSaleModal')) {
                closeEditSaleModal();
            }
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
            }
        }

        // Функции для работы с товарами в продаже
        function addItemRow() {
            const container = document.getElementById('itemsContainer');
            const template = container.querySelector('.template');
            const newRow = template.cloneNode(true);

            newRow.style.display = 'block';
            newRow.classList.remove('template');

            // Обновляем индексы в именах полей
            const inputs = newRow.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.name.replace(/\[\d+\]/, `[${itemCounter}]`);
                input.name = name;
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else {
                    input.value = input.name.includes('quantity') ? '1' : '';
                }
            });

            // Инициализируем поиск для нового ряда
            const searchContainer = newRow.querySelector('.product-search-container');
            if (searchContainer) {
                const searchInput = searchContainer.querySelector('.product-search-input');
                searchInput.id = `product-search-${itemCounter}`;
                searchInput.value = '';

                const select = searchContainer.querySelector('.product-select');
                select.name = `items[${itemCounter}][product_id]`;
                select.selectedIndex = 0;
            }

            // Добавляем CSS классы к полям
            const formGroups = newRow.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                if (index === 0) group.classList.add('product-field');
                else if (index === 1 || index === 2) group.classList.add('price-field');
                else if (index === 3) group.classList.add('quantity-field');
                else if (index === 4) group.classList.add('remove-field');
            });

            container.insertBefore(newRow, container.querySelector('.form-actions'));
            itemCounter++;
        }

        // Функция для обновления цен при выборе товара
        function updateProductPrices(select) {
            const row = select.closest('.item-row');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                const wholesalePrice = row.querySelector('.wholesale-price');
                const retailPrice = row.querySelector('.retail-price');
                const quantityInput = row.querySelector('.quantity');

                // Устанавливаем значения по умолчанию, но не блокируем их изменение
                wholesalePrice.value = selectedOption.dataset.wholesale;
                retailPrice.value = selectedOption.dataset.retail;
                quantityInput.max = selectedOption.dataset.quantity;

                // Проверяем, если текущее количество больше доступного
                if (parseInt(quantityInput.value) > parseInt(quantityInput.max)) {
                    quantityInput.value = quantityInput.max;
                }
            }
        }

        // Функция для валидации количества
        function validateQuantity(input) {
            const max = parseInt(input.max);
            const value = parseInt(input.value);

            if (value > max) {
                input.value = max;
                alert(`Максимально доступное количество: ${max}`);
            }
        }

        function removeItemRow(button) {
            const row = button.closest('.item-row');
            if (document.querySelectorAll('.item-row:not(.template)').length > 1) {
                row.remove();
            } else {
                // Если это последний ряд, просто очищаем его
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = input.name.includes('quantity') ? '1' : '';
                    }
                });
            }
        }

        function resetSaleForm() {
            const form = document.getElementById('saleForm');
            form.reset();

            // Удаляем все ряды товаров, кроме первого
            const rows = document.querySelectorAll('.item-row:not(.template)');
            rows.forEach((row, index) => {
                if (index > 0) {
                    row.remove();
                }
            });

            itemCounter = 1;
        }

        // Функция для редактирования продажи
        function editSale(event, id) {
            event.stopPropagation();

            const modal = document.getElementById('editSaleModal');
            const modalBody = document.getElementById('editSaleModalBody');

            modal.style.display = 'block';
            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`/sales/${id}/edit`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const clientOptions = data.clients.map(client =>
                            `<option value="${client.id}" ${data.sale.client_id == client.id ? 'selected' : ''}>
                            ${client.name}
                            ${client.instagram ? `(@${client.instagram})` : ''}
                            ${client.phone ? ` - ${client.phone}` : ''}
                        </option>`
                        ).join('');

                        const productOptions = data.products.map(product =>
                            `<option value="${product.id}">${product.name}</option>`
                        ).join('');

                        modalBody.innerHTML = `
                        <form id="editSaleForm" novalidate>
                            @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="${data.sale.id}">
                            <div class="form-row date-client-row">
                                <div class="form-group">
                                    <label>Клиент *</label>
                                    <div class="client-search-container">
                                        <input type="text" class="client-search-input form-control" placeholder="Начните вводить имя, инстаграм или email клиента..."
                                               value="${data.sale.client.name}${data.sale.client.instagram ? ` (@${data.sale.client.instagram})` : ''}"
                                               oninput="searchClients(this)"
                                               onfocus="showClientDropdown(this)" autocomplete="off">
                                        <div class="client-dropdown" style="display: none;">
                                            <div class="client-dropdown-list"></div>
                                        </div>
                                        <select name="client_id" class="form-control client-select" style="display: none;">
                                            <option value="">Выберите клиента</option>
                                            ${clientOptions}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Дата *</label>
                                    <input type="date" name="date" value="${formatDateForInput(data.sale.date)}" required class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Примечания</label>
                                <textarea name="notes" rows="2" class="form-control">${data.sale.notes || ''}</textarea>
                            </div>

                            <div class="items-container" id="editItemsContainer">
                                <h3>Товары</h3>
                                <div class="item-row template" style="display: none;">
                                    <div class="form-row">
                                        <div class="form-group product-field">
                                            <label>Товар *</label>
                                            <div class="product-search-container">
                                                <input type="text" class="product-search-input form-control" placeholder="Начните вводить название товара..."
                                                       oninput="searchProducts(this)"
                                                       onfocus="showProductDropdown(this)" autocomplete="off">
                                                <div class="product-dropdown" style="display: none;">
                                                    <div class="product-dropdown-list"></div>
                                                </div>
                                                <select name="items[0][product_id]" class="form-control product-select" style="display: none;">
                                                    <option value="">Выберите товар</option>
                                                    ${productOptions}
                                                </select>
                                                <input type="hidden" name="items[0][product_id]" class="product-id-hidden" value="">
                                            </div>
                                        </div>
                                        <div class="form-group price-field">
                                            <label>Оптовая цена *</label>
                                            <input type="number" step="0.01" name="items[0][wholesale_price]"  class="form-control wholesale-price" min="0.01" readonly>
                                        </div>
                                        <div class="form-group price-field">
                                            <label>Розничная цена *</label>
                                            <input type="number" step="0.01" name="items[0][retail_price]"  class="form-control retail-price" min="0.01">
                                        </div>
                                        <div class="form-group quantity-field">
                                            <label>Кол-во *</label>
                                            <input type="number" name="items[0][quantity]" class="form-control" min="1" value="1">
                                        </div>
                                        <div class="form-group remove-field">
                                            <button type="button" class="btn-remove-item" onclick="removeEditItemRow(this)">
                                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                ${data.sale.items.map((item, index) => `
                                    <div class="item-row">
                                        <div class="form-row">
                                            <div class="form-group product-field">
                                                <label>Товар *</label>
                                                <div class="product-search-container">
                                                    <input type="text" class="product-search-input form-control" placeholder="Начните вводить название товара..."
                                                           value="${item.product.name}"
                                                           oninput="searchProducts(this)"
                                                           onfocus="showProductDropdown(this)" autocomplete="off">
                                                    <div class="product-dropdown" style="display: none;">
                                                        <div class="product-dropdown-list"></div>
                                                    </div>
                                                    <select name="items[${index}][product_id]" class="form-control product-select" style="display: none;">
                                                        <option value="">Выберите товар</option>
                                                        ${data.products.map(product =>
                            `<option value="${product.id}" ${item.product_id == product.id ? 'selected' : ''}>${product.name}</option>`
                        ).join('')}
                                                    </select>
                                                    <input type="hidden" name="items[${index}][product_id]" class="product-id-hidden" value="${item.product_id}">
                                                </div>
                                            </div>
                                            <div class="form-group price-field">
                                                <label>Оптовая цена *</label>
                                                <input type="number" step="0.01" name="items[${index}][wholesale_price]" value="${item.wholesale_price}"  class="form-control" min="0.01" readonly>
                                            </div>
                                            <div class="form-group price-field">
                                                <label>Розничная цена *</label>
                                                <input type="number" step="0.01" name="items[${index}][retail_price]" value="${item.retail_price}"  class="form-control" min="0.01">
                                            </div>
                                            <div class="form-group quantity-field">
                                                <label>Кол-во *</label>
                                                <input type="number" name="items[${index}][quantity]" value="${item.quantity}"  class="form-control" min="1">
                                            </div>
                                            <div class="form-group remove-field">
                                                <button type="button" class="btn-remove-item" onclick="removeEditItemRow(this)">
                                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-add-item" onclick="addEditItemRow()">
                                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                    </svg>
                                    Добавить товар
                                </button>
                                <button type="button" class="btn-cancel" onclick="closeEditSaleModal()">Отмена</button>
                                <button type="submit" class="btn-submit">Сохранить изменения</button>
                            </div>
                        </form>
                    `;

                        // --- ДОБАВЛЯЕМ СИНХРОНИЗАЦИЮ SELECT И ЗНАЧЕНИЙ ДЛЯ ТОВАРОВ ---
                        document.querySelectorAll('#editItemsContainer .item-row:not(.template)').forEach((row, index) => {
                            const productSelect = row.querySelector('.product-select');
                            const productInput = row.querySelector('.product-search-input');
                            const wholesaleInput = row.querySelector('input[name*="[wholesale_price]"]');
                            const retailInput = row.querySelector('input[name*="[retail_price]"]');
                            const quantityInput = row.querySelector('input[name*="[quantity]"]');
                            const hiddenInput = row.querySelector('.product-id-hidden');
                            if (productSelect && productInput) {
                                const selectedOption = productSelect.querySelector('option[selected]');
                                if (selectedOption) {
                                    productSelect.value = selectedOption.value;
                                    if (hiddenInput) hiddenInput.value = selectedOption.value;
                                }
                                // При изменении select — обновлять hidden input
                                productSelect.addEventListener('change', function() {
                                    if (hiddenInput) hiddenInput.value = productSelect.value;
                                    // --- ДОБАВЛЯЕМ: подтягивать цены при изменении товара ---
                                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                                    if (selectedOption && selectedOption.value) {
                                        const product = allProducts.find(p => p.id == selectedOption.value);
                                        if (product) {
                                            if (wholesaleInput) wholesaleInput.value = product.wholesale_price;
                                            if (retailInput) retailInput.value = product.retail_price;
                                        }
                                    }
                                });
                            }
                            // Программно выставляем значения для остальных полей, если они есть в data.sale.items
                            if (typeof data !== 'undefined' && data.sale && data.sale.items && data.sale.items[index]) {
                                if (wholesaleInput) wholesaleInput.value = data.sale.items[index].wholesale_price;
                                if (retailInput) retailInput.value = data.sale.items[index].retail_price;
                                if (quantityInput) quantityInput.value = data.sale.items[index].quantity;
                            }
                        });
                        // --- КОНСОЛЬ-ЛОГ ДЛЯ ДИАГНОСТИКИ ---
                        console.log('itemRows:', document.querySelectorAll('#editItemsContainer .item-row:not(.template)'));
                        document.querySelectorAll('#editItemsContainer .item-row:not(.template)').forEach((row, index) => {
                            const productSelect = row.querySelector('select[name*="[product_id]"]');
                            const wholesaleInput = row.querySelector('input[name*="[wholesale_price]"]');
                            const retailInput = row.querySelector('input[name*="[retail_price]"]');
                            const quantityInput = row.querySelector('input[name*="[quantity]"]');
                            console.log(`row #${index+1}`, {
                                productSelect: productSelect ? productSelect.value : null,
                                wholesaleInput: wholesaleInput ? wholesaleInput.value : null,
                                retailInput: retailInput ? retailInput.value : null,
                                quantityInput: quantityInput ? quantityInput.value : null
                            });
                        });
                        // --- КОНЕЦ ДОБАВЛЕНИЯ ---

                        // Инициализация обработчика формы
                        document.getElementById('editSaleForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            submitEditSaleForm(this);
                        });
                    } else {
                        throw new Error(data.error || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        Ошибка загрузки данных: ${error.message}
                    </div>
                    <button class="btn-cancel" onclick="closeEditSaleModal()">Закрыть</button>
                `;
                });
        }

        function addEditItemRow() {
            const container = document.getElementById('editItemsContainer');
            const template = container.querySelector('.template');
            const newRow = template.cloneNode(true);

            newRow.style.display = 'block';
            newRow.classList.remove('template');

            // Получаем текущее количество рядов (не включая шаблон)
            const currentRows = container.querySelectorAll('.item-row:not(.template)');
            const newIndex = currentRows.length;

            // Обновляем индексы в именах полей
            const inputs = newRow.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
                input.name = name;
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else if (input.type === 'number') {
                    input.value = input.name.includes('quantity') ? '1' : '';
                } else {
                    input.value = '';
                }
            });

            // Инициализируем поиск для нового ряда
            const searchContainer = newRow.querySelector('.product-search-container');
            if (searchContainer) {
                const searchInput = searchContainer.querySelector('.product-search-input');
                searchInput.id = `product-search-edit-${newIndex}`;
                searchInput.value = '';

                const select = searchContainer.querySelector('.product-select');
                select.name = `items[${newIndex}][product_id]`;
                select.selectedIndex = 0;

                // Формируем опции с data-атрибутами для цен и количества
                select.innerHTML = `<option value="">Выберите товар</option>` +
                    allProducts.map(product =>
                        `<option value="${product.id}" data-wholesale="${product.wholesale_price}" data-retail="${product.retail_price}" data-quantity="${product.available_quantity}">${product.name}</option>`
                    ).join('');

                // --- ДОБАВЛЯЕМ: при выборе товара подтягивать цены ---
                select.addEventListener('change', function() {
                    const selectedOption = select.options[select.selectedIndex];
                    const row = select.closest('.item-row');
                    const wholesaleInput = row.querySelector('input[name*="[wholesale_price]"]');
                    const retailInput = row.querySelector('input[name*="[retail_price]"]');
                    const hiddenInput = row.querySelector('.product-id-hidden');
                    if (selectedOption && selectedOption.value) {
                        if (wholesaleInput) wholesaleInput.value = selectedOption.dataset.wholesale ?? '';
                        if (retailInput) retailInput.value = selectedOption.dataset.retail ?? '';
                        if (hiddenInput) hiddenInput.value = selectedOption.value;
                    } else {
                        if (wholesaleInput) wholesaleInput.value = '';
                        if (retailInput) retailInput.value = '';
                        if (hiddenInput) hiddenInput.value = '';
                    }
                });
            }

            // Добавляем CSS классы к полям
            const formGroups = newRow.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                if (index === 0) group.classList.add('product-field');
                else if (index === 1 || index === 2) group.classList.add('price-field');
                else if (index === 3) group.classList.add('quantity-field');
                else if (index === 4) group.classList.add('remove-field');
            });

            container.insertBefore(newRow, container.querySelector('.form-actions'));
        }

        function removeEditItemRow(button) {
            const row = button.closest('.item-row');
            if (document.querySelectorAll('#editItemsContainer .item-row:not(.template)').length > 1) {
                row.remove();
            } else {
                // Если это последний ряд, просто очищаем его
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = input.name.includes('quantity') ? '1' : '';
                    }
                });
            }
        }

        // Функции для подтверждения удаления
        function confirmDeleteSale(event, id) {
            event.stopPropagation();
            currentDeleteId = id;
            currentDeleteSaleId = null;
            currentDeleteItemId = null;
            document.getElementById('confirmationModal').style.display = 'block';
            document.querySelector('.confirmation-content p').textContent = 'Вы уверены, что хотите удалить эту продажу?';
        }

        function confirmDeleteItem(event, saleId, itemId) {
            event.stopPropagation();
            currentDeleteId = null;
            currentDeleteSaleId = saleId;
            currentDeleteItemId = itemId;
            document.getElementById('confirmationModal').style.display = 'block';
            document.querySelector('.confirmation-content p').textContent = 'Вы уверены, что хотите удалить этот товар из продажи?';
        }

        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteId = null;
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentDeleteId) {
                deleteSale(currentDeleteId);
            } else if (currentDeleteSaleId && currentDeleteItemId) {
                deleteItem(currentDeleteSaleId, currentDeleteItemId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteId = null;
            currentDeleteSaleId = null;
            currentDeleteItemId = null;
        });

        // Функция для удаления продажи
        function deleteSale(id) {
            fetch(`/sales/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Продажа успешно удалена', 'success');
                        // Удаляем все строки таблицы, относящиеся к этой продаже
                        document.querySelectorAll(`tr[data-sale-id="${id}"]`).forEach(row => {
                            row.remove();
                        });
                    } else {
                        showNotification('Ошибка при удалении продажи', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Ошибка при удалении продажи', 'error');
                });
        }

        // Обработчик формы добавления продажи
        document.getElementById('saleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitSaleForm(this);
        });

        function submitSaleForm(form) {
            const formData = {
                date: form.querySelector('[name="date"]').value,
                client_id: form.querySelector('[name="client_id"]').value,
                notes: form.querySelector('[name="notes"]').value,
                items: []
            };

            // Собираем данные о товарах
            document.querySelectorAll('.item-row:not(.template)').forEach((row, index) => {
                const item = {
                    product_id: row.querySelector('[name*="product_id"]').value,
                    wholesale_price: parseFloat(row.querySelector('[name*="wholesale_price"]').value) || 0,
                    retail_price: parseFloat(row.querySelector('[name*="retail_price"]').value) || 0,
                    quantity: parseInt(row.querySelector('[name*="quantity"]').value) || 1
                };

                // Проверка валидности цен
                if (isNaN(item.retail_price)) {
                    showNotification('Укажите корректную розничную цену', 'error');
                    return;
                }

                formData.items.push(item);
            });

            // Отправка данных
            fetch('/sales', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Продажа сохранена', 'success');
                        closeSaleModal();
                        resetSaleForm(); // Очищаем форму
                        // Перезагружаем текущую страницу для отображения новой продажи
                        loadSales(currentPage);
                    } else {
                        showNotification(data.message || 'Ошибка сохранения', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Ошибка соединения', 'error');
                });
        }

        function submitEditSaleForm(form) {
            clearErrors('editSaleForm');

            const id = form.querySelector('[name="id"]').value;
            const items = [];
            let hasError = false;

            // Собираем данные о товарах
            const itemRows = document.querySelectorAll('#editItemsContainer .item-row:not(.template)');

            // Проверяем, что есть хотя бы один товар
            if (itemRows.length === 0) {
                showNotification('Добавьте хотя бы один товар', 'error');
                return;
            }

            // Формируем массив товаров
            itemRows.forEach((row, index) => {
                // Получаем элементы формы
                const productSelect = row.querySelector('select[name*="[product_id]"]');
                const hiddenInput = row.querySelector('.product-id-hidden');
                const productId = hiddenInput ? hiddenInput.value : '';
                const retailInput = row.querySelector('input[name*="[retail_price]"]');
                const quantityInput = row.querySelector('input[name*="[quantity]"]');

                // Подробный лог для диагностики
                console.log(`row #${index+1}`, {
                    productSelect: productSelect ? productSelect.value : null,
                    hiddenInput: productId,
                    retailInput: retailInput ? retailInput.value : null,
                    quantityInput: quantityInput ? quantityInput.value : null
                });

                // Проверяем, что все поля заполнены
                if (!productId ||
                    !retailInput || !retailInput.value ||
                    !quantityInput || !quantityInput.value) {
                    showNotification('Заполните все поля для товара #' + (index + 1), 'error');
                    hasError = true;
                    return;
                }

                // Создаём объект товара
                const item = {
                    product_id: productId,
                    retail_price: parseFloat(retailInput.value),
                    quantity: parseInt(quantityInput.value)
                };

                // Проверяем валидность цен
                if (isNaN(item.retail_price)) {
                    showNotification('Некорректная розничная цена для товара #' + (index + 1), 'error');
                    hasError = true;
                    return;
                }

                items.push(item);
            });

            if (hasError) {
                return; // Не отправлять форму, если есть ошибки
            }

            if (!items.length) {
                showNotification('Добавьте хотя бы один товар', 'error');
                return;
            }

            // Формируем данные для отправки
            const formData = {
                _method: 'PUT',
                date: form.querySelector('[name="date"]').value,
                client_id: form.querySelector('[name="client_id"]').value,
                notes: form.querySelector('[name="notes"]').value,
                items: items
            };

            // Добавляем логирование перед отправкой
            console.log('Отправляемые данные:', formData);

            // Отправляем данные на сервер
            fetch(`/sales/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification('Продажа успешно обновлена', 'success');
                        closeEditSaleModal();
                        updateSaleInTable(data.sale);
                    } else {
                        showNotification(data.message || 'Ошибка при обновлении продажи', 'error');
                        if (data.errors) {
                            displayErrors(data.errors, 'editSaleForm');
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    showNotification(error.message || 'Ошибка при обновлении продажи', 'error');
                });
        }

        // Функции для работы с таблицей
        function addSaleToTable(sale) {
            const tbody = document.querySelector('#salesTable tbody');

            sale.items.forEach(item => {
                const row = document.createElement('tr');
                row.setAttribute('data-sale-id', sale.id);
                row.setAttribute('data-item-id', item.id);

                // Форматируем дату
                const saleDate = new Date(sale.date);
                const formattedDate = `${saleDate.getDate().toString().padStart(2, '0')}.${(saleDate.getMonth()+1).toString().padStart(2, '0')}.${saleDate.getFullYear()}`;

                row.innerHTML = `
            <td>${formattedDate}</td>
            <td>
                ${sale.client.name}
                ${sale.client.instagram ? `<a href="https://instagram.com/${sale.client.instagram}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                    <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                    </svg>
                    ${sale.client.instagram}
                </a>` : ''}
            </td>
            <td>${item.product.name}</td>
            <td>
                ${item.product.photo ?
                    `<img src="/storage/${item.product.photo}" class="product-photo" alt="Фото" style="height: 50px;">` :
                    '<div class="no-photo">Нет фото</div>'}
            </td>
            <td>${formatPriceJS(item.wholesale_price)} грн</td>
            <td>${formatPriceJS(item.retail_price)} грн</td>
            <td>${item.quantity}</td>
            <td>${formatPriceJS(item.retail_price * item.quantity)} грн</td>
            <td>
                <div class="sale-actions">
                    <button class="btn-edit" onclick="editSale(event, ${sale.id})" title="Редактировать">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                    </button>
                    <button class="btn-delete" onclick="confirmDeleteItem(event, ${sale.id}, ${item.id})" title="Удалить">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </td>
        `;

                // Вставляем новую строку в начало таблицы
                tbody.insertBefore(row, tbody.firstChild);
            });
            console.log('Добавляемая продажа:', sale);
        }


        function updateSaleInTable(sale) {
            // Сначала удаляем все строки, связанные с этой продажей
            document.querySelectorAll(`tr[data-sale-id="${sale.id}"]`).forEach(row => {
                row.remove();
            });

            // Затем добавляем обновленные строки
            addSaleToTable(sale);
        }

        // Вспомогательные функции
        function formatDateForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';

            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        function clearErrors(formId) {
            const form = document.getElementById(formId);
            const errorElements = form.querySelectorAll('.error-message');
            errorElements.forEach(el => el.remove());

            const errorInputs = form.querySelectorAll('.is-invalid');
            errorInputs.forEach(el => el.classList.remove('is-invalid'));
        }

        function displayErrors(errors, formId) {
            clearErrors(formId);
            const form = document.getElementById(formId);

            for (const [field, messages] of Object.entries(errors)) {
                let input;

                if (field.includes('items.')) {
                    // Обработка ошибок для элементов массива items
                    const parts = field.split('.');
                    const index = parts[1];
                    const fieldName = parts[2];

                    // Находим все ряды с товарами
                    const rows = form.querySelectorAll('.item-row:not(.template)');
                    if (index < rows.length) {
                        input = rows[index].querySelector(`[name*="${fieldName}"]`);
                    }
                } else {
                    // Обычные поля
                    input = form.querySelector(`[name="${field}"]`);
                }

                if (input) {
                    input.classList.add('is-invalid');
                    const errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    errorElement.textContent = messages.join(', ');

                    // Вставляем сообщение об ошибке после поля ввода
                    input.parentNode.insertBefore(errorElement, input.nextSibling);
                }
            }
        }

        // Поиск продаж
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#salesTable tbody tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let found = false;

                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchTerm)) {
                        found = true;
                    }
                });

                row.style.display = found ? '' : 'none';
            });
        });

        // Добавьте эту вспомогательную функцию для экранирования HTML
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Поиск клиентов (исправленная версия)
        function searchClients(input) {
            const searchTerm = input.value.toLowerCase().trim();
            const dropdown = input.nextElementSibling;
            const dropdownList = dropdown.querySelector('.client-dropdown-list');
            const select = input.parentNode.querySelector('.client-select');

            if (!searchTerm) {
                dropdown.style.display = 'none';
                return;
            }

            // Проверка наличия данных
            if (!allClients || allClients.length === 0) {
                console.error('No clients data available');
                dropdownList.innerHTML = '<div class="client-dropdown-item">Нет данных о клиентах</div>';
                dropdown.style.display = 'block';
                return;
            }

            const filteredClients = allClients.filter(client => {
                if (!client) return false;

                const name = client.name ? client.name.toLowerCase() : '';
                const instagram = client.instagram ? client.instagram.toLowerCase() : '';
                const email = client.email ? client.email.toLowerCase() : '';
                const phone = client.phone ? client.phone.toString() : '';

                return name.includes(searchTerm) ||
                    instagram.includes(searchTerm) ||
                    email.includes(searchTerm) ||
                    phone.includes(searchTerm);
            });

            if (filteredClients.length === 0) {
                dropdownList.innerHTML = '<div class="client-dropdown-item">Клиенты не найдены</div>';
            } else {
                dropdownList.innerHTML = filteredClients.map(client => `
                <div class="client-dropdown-item"
                     data-id="${client.id}"
                     onclick="selectClient(this,
                         '${escapeHtml(client.name || '')}${client.instagram ? ` (@${escapeHtml(client.instagram)})` : ''}',
                         ${client.id}, '${input.id}')">
                    ${escapeHtml(client.name || '')}
                    ${client.instagram ? `(@${escapeHtml(client.instagram)})` : ''}
                    ${client.phone ? ` - ${escapeHtml(client.phone.toString())}` : ''}
                </div>
            `).join('');
            }

            dropdown.style.display = 'block';
        }

        // Показать выпадающий список клиентов (исправленная версия)
        function showClientDropdown(input) {
            const dropdown = input.nextElementSibling;
            const dropdownList = dropdown.querySelector('.client-dropdown-list');

            if (!allClients || allClients.length === 0) {
                dropdownList.innerHTML = '<div class="client-dropdown-item">Нет данных о клиентах</div>';
            } else {
                dropdownList.innerHTML = allClients.map(client => `
                <div class="client-dropdown-item"
                     data-id="${client.id}"
                     onclick="selectClient(this,
                         '${escapeHtml(client.name || '')}${client.instagram ? ` (@${escapeHtml(client.instagram)})` : ''}',
                         ${client.id}, '${input.id}')">
                    ${escapeHtml(client.name || '')}
                    ${client.instagram ? `(@${escapeHtml(client.instagram)})` : ''}
                    ${client.phone ? ` - ${escapeHtml(client.phone.toString())}` : ''}
                </div>
            `).join('');
            }

            dropdown.style.display = 'block';
        }

        // Выбрать клиента из списка (исправленная версия)
        function selectClient(element, clientName, clientId, inputId) {
            const container = element.closest('.client-search-container');
            const input = container.querySelector('.client-search-input');
            const select = container.querySelector('.client-select');
            const dropdown = container.querySelector('.client-dropdown');

            input.value = clientName;
            select.value = clientId;
            dropdown.style.display = 'none';

            // Убираем выделение у всех элементов
            container.querySelectorAll('.client-dropdown-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Выделяем выбранный элемент
            element.classList.add('selected');
        }

        document.addEventListener('click', function(e) {
            // Закрытие выпадающего списка клиентов
            if (!e.target.closest('.client-search-container')) {
                document.querySelectorAll('.client-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }

            // Закрытие выпадающего списка товаров
            if (!e.target.closest('.product-search-container')) {
                document.querySelectorAll('.product-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });

        // Поиск товаров
        function searchProducts(input) {
            const searchTerm = input.value.toLowerCase();
            const dropdown = input.nextElementSibling;
            const dropdownList = dropdown.querySelector('.product-dropdown-list');
            const select = input.parentNode.querySelector('.product-select');

            if (searchTerm.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            const filteredProducts = allProducts.filter(product =>
                product.name.toLowerCase().includes(searchTerm)
            );

            if (filteredProducts.length === 0) {
                dropdownList.innerHTML = '<div class="product-dropdown-item">Товары не найдены</div>';
            } else {
                dropdownList.innerHTML = filteredProducts.map(product => `
                <div class="product-dropdown-item"
                     data-id="${product.id}"
                     data-wholesale="${product.wholesale_price}"
                     data-retail="${product.retail_price}"
                     data-quantity="${product.available_quantity}"
                     onclick="selectProduct(this, '${product.name}', ${product.id}, '${input.id}')">
                    ${product.name} (Доступно: ${product.available_quantity})
                </div>
            `).join('');
            }

            dropdown.style.display = 'block';
        }


        // Показать выпадающий список товаров
        function showProductDropdown(input) {
            if (input.value.length > 0) {
                searchProducts(input);
            } else {
                const dropdown = input.nextElementSibling;
                const dropdownList = dropdown.querySelector('.product-dropdown-list');
                dropdownList.innerHTML = allProducts.map(product => `
                <div class="product-dropdown-item"
                     data-id="${product.id}"
                     data-wholesale="${product.wholesale_price}"
                     data-retail="${product.retail_price}"
                     data-quantity="${product.available_quantity}"
                     onclick="selectProduct(this, '${product.name}', ${product.id}, '${input.id}')">
                    ${product.name} (Доступно: ${product.available_quantity})
                </div>
            `).join('');
                dropdown.style.display = 'block';
            }
        }

        // Выбрать товар из списка
        function selectProduct(element, productName, productId, inputId) {
            const container = element.closest('.product-search-container');
            const input = container.querySelector('.product-search-input');
            const select = container.querySelector('.product-select');
            const dropdown = container.querySelector('.product-dropdown');
            const row = container.closest('.item-row');

            input.value = productName + ' (Доступно: ' + element.dataset.quantity + ')';
            select.value = productId;
            dropdown.style.display = 'none';

            // Обновляем цены и максимальное количество
            const wholesalePrice = row.querySelector('.wholesale-price');
            const retailPrice = row.querySelector('.retail-price');
            const quantityInput = row.querySelector('.quantity');
            const hiddenInput = row.querySelector('.product-id-hidden');

            if (wholesalePrice) wholesalePrice.value = element.dataset.wholesale ?? '';
            if (retailPrice) retailPrice.value = element.dataset.retail ?? '';
            if (quantityInput) quantityInput.max = element.dataset.quantity ?? '';
            if (hiddenInput) hiddenInput.value = productId;
        }


        // Функции для подтверждения удаления
        function deleteItem(saleId, itemId) {
            fetch(`/sales/${saleId}/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Server error');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Товар успешно удален', 'success');
                        // Перезагружаем текущую страницу
                        loadSales(currentPage);
                    } else {
                        showNotification(data.message || 'Ошибка при удалении товара', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Ошибка при удалении товара', 'error');
                });
        }

        function formatPriceJS(price) {
            if (price === null || price === undefined || isNaN(price)) return '';
            price = parseFloat(price);
            return (price % 1 === 0) ? price.toString() : price.toFixed(2);
        }

        // --- AJAX пагинация ---
        let currentPage = 1;

        function renderSales(sales) {
            const tbody = document.getElementById('salesTableBody');
            tbody.innerHTML = '';
            
            // Если нет продаж, не делаем ничего
            if (!sales || sales.length === 0) {
                return;
            }
            
            sales.forEach(sale => {
                sale.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-sale-id', sale.id);
                    row.setAttribute('data-item-id', item.id);
                    
                    const instagramLink = sale.client.instagram ? 
                        `<a href="https://instagram.com/${sale.client.instagram}" class="instagram-link" target="_blank" rel="noopener noreferrer">
                            <svg class="icon instagram-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                        </svg>
                        ${sale.client.instagram}
                    </a>` : '';

                    const photoHtml = item.product.photo ? 
                        `<img src="/storage/${item.product.photo}" class="product-photo" alt="Фото" style="height: 50px;">` : 
                        '<div class="no-photo">Нет фото</div>';

                    row.innerHTML = `
                        <td>${sale.date ? new Date(sale.date).toLocaleDateString('ru-RU') : '—'}</td>
                        <td>
                            ${sale.client.name}
                            ${instagramLink}
                        </td>
                        <td>${item.product.name}</td>
                        <td>${photoHtml}</td>
                        <td>${item.wholesale_price !== null ? formatPriceJS(item.wholesale_price) : '—'} грн</td>
                        <td>${item.retail_price !== null ? formatPriceJS(item.retail_price) : '—'} грн</td>
                        <td>${item.quantity}</td>
                        <td>${formatPriceJS(item.retail_price * item.quantity)} грн</td>
                        <td>
                            <div class="sale-actions">
                                <button class="btn-edit" onclick="editSale(event, ${sale.id})" title="Редактировать">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                </button>
                                <button class="btn-delete" onclick="confirmDeleteItem(event, ${sale.id}, ${item.id})" title="Удалить">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
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
            let pagContainer = document.getElementById('salesPagination');
            if (!pagContainer) {
                pagContainer = document.createElement('div');
                pagContainer.id = 'salesPagination';
                document.querySelector('.sales-container').appendChild(pagContainer);
            }
            pagContainer.innerHTML = paginationHtml;

            // Навешиваем обработчики
            document.querySelectorAll('.page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (!isNaN(page) && !this.disabled) {
                        loadSales(page);
                    }
                });
            });
        }

        function loadSales(page = 1, search = '') {
            currentPage = page;
            const searchValue = search !== undefined ? search : document.getElementById('searchInput').value.trim();
            fetch(`/sales?search=${encodeURIComponent(searchValue)}&page=${page}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Обновляем allProducts и allClients для поиска в модальных окнах
                if (data.products) {
                    allProducts = data.products;
                }
                if (data.clients) {
                    allClients = data.clients;
                }
                
                renderSales(data.data);
                renderPagination(data.meta);
            })
            .catch(error => {
                console.error('Ошибка при загрузке данных:', error);
            });
        }

        // Поиск с пагинацией
        document.getElementById('searchInput').addEventListener('input', function() {
            loadSales(1, this.value.trim());
        });

        // Инициализация первой загрузки
        loadSales(1);

    </script>
</div>
</div>
@endsection

<style>
    /* Стили для выравнивания полей в модальном окне продажи */
    .form-row.date-client-row {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }
    
    .form-row.date-client-row .form-group {
        flex: 1;
        min-width: 0;
        width: 50%;
    }
    
    .form-row.date-client-row .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .form-row.date-client-row .form-group input,
    .form-row.date-client-row .form-group select,
    .form-row.date-client-row .form-group .client-search-container {
        width: 100%;
        box-sizing: border-box;
    }
    
    /* Дополнительные стили для контейнера поиска клиента */
    .form-row.date-client-row .client-search-container {
        width: 100%;
        position: relative;
    }
    
    .form-row.date-client-row .client-search-container input,
    .form-row.date-client-row .client-search-container select {
        width: 100%;
        box-sizing: border-box;
    }
    
    /* Стили для выпадающего списка клиентов */
    .form-row.date-client-row .client-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        max-height: 200px;
        overflow-y: auto;
    }
    
    /* Стили для полей товаров */
    .item-row .form-row {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .item-row .form-group {
        flex: 1;
        min-width: 0;
    }
    
    /* Поле товара - увеличиваем */
    .item-row .form-group:has(.product-search-container),
    .item-row .product-field {
        flex: 3;
    }
    
    /* Поле количества - уменьшаем */
    .item-row .form-group:has(input[name*="quantity"]),
    .item-row .quantity-field {
        flex: 0.5;
        max-width: 100px;
    }
    
    /* Поля цен - средний размер */
    .item-row .form-group:has(input[name*="price"]),
    .item-row .price-field {
        flex: 1;
    }
    
    /* Кнопка удаления - минимальный размер */
    .item-row .form-group:has(.btn-remove-item),
    .item-row .remove-field {
        flex: 0.3;
        max-width: 50px;
    }
    .sale-table td{
        padding: 25px 15px!important;
    }
</style>
