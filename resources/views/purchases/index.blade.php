@extends('layouts.app')

@section('content')
    <div class="dashboard-container">
        <div class="purchases-header">
            <h1>Закупки</h1>
            <div id="notification" class="notification">
                <!-- Уведомления будут появляться здесь -->
            </div>
            <div class="header-actions">
                <button class="btn-add-purchase" onclick="openPurchaseModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить закупку
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" placeholder="Поиск..." id="searchInput">
                </div>
            </div>
        </div>

        <table class="table purchases-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Поставщик</th>
                    <th>Оптовая сумма</th>
                    <th>Примечания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="purchasesListBody">
                @foreach($purchases as $purchase)
                    <tr class="purchase-summary-row" id="purchase-row-{{ $purchase->id }}" onclick="togglePurchaseDetailsRow({{ $purchase->id }})">
                        <td class="purchase-date">{{ $purchase->formatted_date }}</td>
                        <td class="purchase-supplier">{{ $purchase->supplier ? $purchase->supplier->name : '—' }}</td>
                        <td class="purchase-total">{{ (float)$purchase->total_amount }} грн</td>
                        <td class="purchase-notes-cell" title="{{ $purchase->notes }}">{{ $purchase->notes ?: '—' }}</td>
                        <td>
                            <div class="purchases-actions">
                                <button class="btn-edit" onclick="editPurchase(event, {{ $purchase->id }})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                    Ред.
                                </button>
                                <button class="btn-delete" onclick="confirmDeletePurchase(event, {{ $purchase->id }})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="purchases-details-row" id="details-row-{{ $purchase->id }}" style="display: none;">
                        <td colspan="5">
                            <div class="purchases-details">
                                <div class="purchases-notes">{{ $purchase->notes ?: '—' }}</div>
                                <table class="table-wrapper table-striped purchases-table">
                                    <thead>
                                    <tr>
                                        <th>Фото</th>
                                        <th>Товар</th>
                                        <th>Опт</th>
                                        <th>Розница</th>
                                        <th>Количество</th>
                                        <th>Сумма</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($purchase->items as $item)
                                        <tr>
                                            <td>
                                                @if($item->product->photo)
                                                    <img src="{{ Storage::url($item->product->photo) }}" class="product-photo" alt="{{ $item->product->name }}">
                                                @else
                                                    <div class="no-photo">Нет фото</div>
                                                @endif
                                            </td>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ (float)$item->purchase_price }} грн</td>
                                            <td>{{ (float)$item->retail_price }} грн</td>
                                            <td>{{ $item->quantity }} шт</td>
                                            <td>{{ (float)$item->total }} грн</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Модальное окно добавления закупки -->
    <div id="purchaseModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Добавить закупку</h2>
                <span class="close" onclick="closePurchaseModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="purchaseForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Дата </label>
                            <input type="date" name="date" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Поставщик </label>
                            <select name="supplier_id" required class="form-control">
                                <option value="">Выберите поставщика</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
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
                                <div class="form-group">
                                    <label>Товар </label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control" placeholder="Начните вводить название товара..."
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select" style="display: none;">
                                            <option value="">Выберите товар</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Закупочная цена </label>
                                    <input type="number"  name="items[0][purchase_price]" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label>Розничная цена </label>
                                    <input type="number" name="items[0][retail_price]" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label>Количество </label>
                                    <input type="number" name="items[0][quantity]" required class="form-control" min="1" value="1">
                                </div>
                                <div class="form-group">
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
                                <div class="form-group">
                                    <label>Товар </label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control" placeholder="Начните вводить название товара..."
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select" style="display: none;">
                                            <option value="">Выберите товар</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Закупочная цена </label>
                                    <input type="number" step="0.01" name="items[0][purchase_price]" required class="form-control" min="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Розничная цена </label>
                                    <input type="number" step="0.01" name="items[0][retail_price]" required class="form-control" min="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Количество </label>
                                    <input type="number" name="items[0][quantity]" required class="form-control" min="1" value="1">
                                </div>
                                <div class="form-group">
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
                        <button type="button" class="btn-cancel" onclick="closePurchaseModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить закупку</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования закупки -->
    <div id="editPurchaseModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Редактировать закупку</h2>
                <span class="close" onclick="closeEditPurchaseModal()">&times;</span>
            </div>
            <div class="modal-body" id="editPurchaseModalBody">
                <!-- Контент будет загружен динамически -->
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить эту закупку?</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelDelete">Отмена</button>
                <button class="confirm-btn" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>

    <script>
        // Глобальные переменные
        let currentDeleteId = null;
        let itemCounter = 1; // Этот счетчик будет заменен динамическим расчетом

        // Функции для работы с модальными окнами
        function openPurchaseModal() {
            document.getElementById('purchaseForm').reset();
            // Устанавливаем текущую дату
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('#purchaseForm [name="date"]').value = today;
            document.getElementById('purchaseModal').style.display = 'block';
        }

        function closePurchaseModal() {
            document.getElementById('purchaseModal').style.display = 'none';
            clearErrors('purchaseForm');
            resetPurchaseForm();
        }

        function closeEditPurchaseModal() {
            document.getElementById('editPurchaseModal').style.display = 'none';
        }

        // Закрытие модальных окон при клике вне их
        window.onclick = function(event) {
            if (event.target == document.getElementById('purchaseModal')) {
                closePurchaseModal();
            }
            if (event.target == document.getElementById('editPurchaseModal')) {
                closeEditPurchaseModal();
            }
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
            }
        }

        // Функции для работы с товарами в закупке
        function addItemRow(containerId = 'itemsContainer') {
            const container = document.getElementById(containerId);
            if (!container) {
                console.error(`Container with id ${containerId} not found.`);
                return;
            }

            // Шаблон всегда находится в модальном окне добавления
            const template = document.querySelector('#itemsContainer .template');
            if (!template) {
                console.error('Template row not found.');
                return;
            }

            const newRow = template.cloneNode(true);
            newRow.style.display = 'block';
            newRow.classList.remove('template');

            // Новый индекс - это количество существующих строк товаров
            const newIndex = container.querySelectorAll('.item-row:not(.template)').length;

            newRow.querySelectorAll('input, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\\d+\]/, `[${newIndex}]`);
                }
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else if (input.name && input.name.includes('quantity')) {
                    input.value = '1';
                } else {
                    input.value = '';
                }
            });

            // Инициализируем поиск для нового ряда
            const searchInput = newRow.querySelector('.product-search-input');
            if (searchInput) {
                searchInput.id = `product-search-${containerId}-${newIndex}`;
            }
            const productSelect = newRow.querySelector('.product-select');
            if (productSelect) {
                productSelect.name = `items[${newIndex}][product_id]`;
            }


            const formActions = container.querySelector('.form-actions');
            if (formActions) {
                container.insertBefore(newRow, formActions);
            } else {
                container.appendChild(newRow); // Fallback
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

        function resetPurchaseForm() {
            const form = document.getElementById('purchaseForm');
            form.reset();

            // Удаляем все ряды товаров, кроме первого
            const rows = document.querySelectorAll('#purchaseForm .item-row:not(.template)');
            rows.forEach((row, index) => {
                if (index > 0) {
                    row.remove();
                }
            });

            // Сбрасываем первый ряд
            const firstRow = document.querySelector('#purchaseForm .item-row:not(.template)');
            if (firstRow) {
                const inputs = firstRow.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    } else if (input.name && input.name.includes('quantity')) {
                        input.value = '1';
                    } else {
                        input.value = '';
                    }
                });
                firstRow.querySelector('.product-search-input').value = '';
            }

            itemCounter = 1;
        }

        // Функция для показа/скрытия деталей закупки
        function togglePurchaseDetailsRow(id) {
            const detailsRow = document.getElementById(`details-row-${id}`);
            if (detailsRow) {
                detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
            }
        }

        // Функция для редактирования закупки
        function editPurchase(event, id) {
            event.stopPropagation();
            fetch(`/purchases/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const purchase = data.purchase;
                        const modalBody = document.getElementById('editPurchaseModalBody');

                        // Создаем форму редактирования
                        const formHtml = `
                            <form id="editPurchaseForm">
                                @csrf
                                @method('PUT')
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Дата </label>
                                        <input type="date" name="date" required class="form-control" value="${purchase.date}">
                                    </div>
                                    <div class="form-group">
                                        <label>Поставщик </label>
                                        <select name="supplier_id" required class="form-control">
                                            <option value="">Выберите поставщика</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" ${purchase.supplier_id == {{ $supplier->id }} ? 'selected' : ''}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Примечания</label>
                                    <textarea name="notes" rows="2" class="form-control">${purchase.notes || ''}</textarea>
                                </div>
                                <div class="items-container" id="editItemsContainer">
                                    <h3>Товары</h3>
                                    ${purchase.items.map((item, index) => `
                                        <div class="item-row">
                                            <div class="form-row">
                                                 <div class="form-group">
                                                    <label>Товар</label>
                                                    <div class="product-search-container">
                                                        <input type="text"
                                                               id="product-search-edit-${index}"
                                                               class="product-search-input form-control"
                                                               placeholder="Начните вводить название товара..."
                                                               oninput="searchProducts(this)"
                                                               onfocus="showProductDropdown(this)"
                                                               value="${item.product_name}"
                                                               autocomplete="off">
                                                        <div class="product-dropdown" style="display: none;">
                                                            <div class="product-dropdown-list"></div>
                                                        </div>
                                                        <select name="items[${index}][product_id]" class="form-control product-select" style="display: none;">
                                                            <option value="${item.product_id}">${item.product_name}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Закупочная цена</label>
                                                    <input type="number" step="0.01" name="items[${index}][purchase_price]" required class="form-control" value="${item.purchase_price}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Розничная цена</label>
                                                    <input type="number" step="0.01" name="items[${index}][retail_price]" required class="form-control" value="${item.retail_price}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Количество</label>
                                                    <input type="number" name="items[${index}][quantity]" required class="form-control" value="${item.quantity}">
                                                </div>
                                                <div class="form-group">
                                                    <button type="button" class="btn-remove-item" onclick="removeItemRow(this)">
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
                                    <button type="button" class="btn-add-item" onclick="addItemRow('editItemsContainer')">
                                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                        </svg>
                                        Добавить товар
                                    </button>
                                    <button type="button" class="btn-cancel" onclick="closeEditPurchaseModal()">Отмена</button>
                                    <button type="submit" class="btn-submit">Сохранить изменения</button>
                                </div>
                            </form>
                        `;

                        modalBody.innerHTML = formHtml;
                        document.getElementById('editPurchaseModal').style.display = 'block';

                        // Добавляем обработчик отправки формы
                        document.getElementById('editPurchaseForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(this);
                            const items = [];

                            // Собираем данные о товарах
                            const itemRows = this.querySelectorAll('.item-row');
                            itemRows.forEach((row, index) => {
                                items.push({
                                    product_id: row.querySelector(`[name="items[${index}][product_id]"]`).value,
                                    purchase_price: row.querySelector(`[name="items[${index}][purchase_price]"]`).value,
                                    retail_price: row.querySelector(`[name="items[${index}][retail_price]"]`).value,
                                    quantity: row.querySelector(`[name="items[${index}][quantity]"]`).value
                                });
                            });

                            // Отправляем запрос на обновление
                            fetch(`/purchases/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    _method: 'PUT',
                                    date: formData.get('date'),
                                    supplier_id: formData.get('supplier_id'),
                                    notes: formData.get('notes'),
                                    items: items
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showNotification('success', 'Закупка успешно обновлена');
                                    closeEditPurchaseModal();
                                    // Обновляем данные на странице
                                    updatePurchaseRowInDOM(data.purchase);
                                } else {
                                    showNotification('error', data.message || 'Ошибка обновления закупки');
                                }
                            })
                            .catch(error => {
                                showNotification('error', 'Ошибка обновления закупки');
                            });
                        });
                    } else {
                        showNotification('error', data.message || 'Ошибка загрузки данных закупки');
                    }
                })
                .catch(error => {
                    showNotification('error', 'Ошибка загрузки данных закупки');
                });
        }

        // Функции для подтверждения удаления
        function confirmDeletePurchase(event, id) {
            event.stopPropagation();
            currentDeleteId = id;
            document.getElementById('confirmationModal').style.display = 'block';
        }

        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteId = null;
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentDeleteId) {
                deletePurchase(currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteId = null;
        });

        // Функция для удаления закупки
        function deletePurchase(id) {
            fetch(`/purchases/${id}`, {
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
                        showNotification('success', 'Закупка успешно удалена');
                        document.getElementById(`purchase-row-${id}`).remove();
                        document.getElementById(`details-row-${id}`).remove();
                    } else {
                        showNotification('error', 'Ошибка при удалении закупки');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', 'Ошибка при удалении закупки');
                });
        }

        // Обработчик формы добавления закупки
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitPurchaseForm(this);
        });

        function submitPurchaseForm(form) {
            clearErrors('purchaseForm');

            // Собираем данные о товарах
            const items = [];
            form.querySelectorAll('.item-row:not(.template)').forEach((row, index) => {
                const item = {
                    product_id: row.querySelector('[name*="product_id"]').value,
                    purchase_price: row.querySelector('[name*="purchase_price"]').value,
                    retail_price: row.querySelector('[name*="retail_price"]').value,
                    quantity: row.querySelector('[name*="quantity"]').value
                };
                items.push(item);
            });

            // Собираем основные данные формы
            const formData = {
                date: form.querySelector('[name="date"]').value,
                supplier_id: form.querySelector('[name="supplier_id"]').value,
                notes: form.querySelector('[name="notes"]').value,
                items: items
            };

            fetch('/purchases', {
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
                        showNotification('success', 'Закупка успешно добавлена');
                        closePurchaseModal();
                        addPurchaseToDOM(data.purchase);
                        resetPurchaseForm();
                    } else {
                        if (data.errors) {
                            displayErrors(data.errors, 'purchaseForm');
                        } else {
                            showNotification('error', data.message || 'Ошибка при добавлении закупки');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.errors) {
                        displayErrors(error.errors, 'purchaseForm');
                    } else {
                        showNotification('error', error.message || 'Ошибка при добавлении закупки');
                    }
                });
        }

        // Функции для работы с DOM
        function addPurchaseToDOM(purchase) {
            // Форматируем дату
            const formattedDate = purchase.date
                ? new Date(purchase.date).toLocaleDateString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                }).replace(/\./g, '.')
                : 'Нет даты';

            // Генерируем HTML для товаров в детальной строке
            const itemsHTML = purchase.items.map(item => {
                const purchasePrice = parseFloat(item.purchase_price);
                const retailPrice = parseFloat(item.retail_price);
                const quantity = parseInt(item.quantity);
                const total = purchasePrice * quantity;

                return `
                <tr>
                    <td>
                        ${item.product.photo ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">` : `<div class="no-photo">Нет фото</div>`}
                    </td>
                    <td>${item.product.name}</td>
                    <td>${Number(purchasePrice)} грн</td>
                    <td>${Number(retailPrice)} грн</td>
                    <td>${quantity} шт</td>
                    <td>${Number(total)} грн</td>
                </tr>`;
            }).join('');

            // Создаём HTML для двух строк таблицы: основной и детальной
            const newRowHTML = `
                <tr class="purchase-summary-row" id="purchase-row-${purchase.id}" onclick="togglePurchaseDetailsRow(${purchase.id})">
                    <td class="purchase-date">${formattedDate}</td>
                    <td class="purchase-supplier">${purchase.supplier ? purchase.supplier.name : '—'}</td>
                    <td class="purchase-total">${Number(purchase.total_amount)} грн</td>
                    <td class="purchase-notes-cell" title="${purchase.notes || ''}">${purchase.notes || '—'}</td>
                    <td>
                        <div class="purchases-actions">
                            <button class="btn-edit" onclick="editPurchase(event, ${purchase.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg> Ред.
                            </button>
                            <button class="btn-delete" onclick="confirmDeletePurchase(event, ${purchase.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg> Удалить
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="purchases-details-row" id="details-row-${purchase.id}" style="display: none;">
                    <td colspan="5">
                        <div class="purchases-details">
                            <div class="purchases-notes">${purchase.notes || '—'}</div>
                            <table class="table-wrapper table-striped purchases-table">
                                <thead>
                                    <tr>
                                        <th>Фото</th>
                                        <th>Товар</th>
                                        <th>Закупочная цена</th>
                                        <th>Розничная цена</th>
                                        <th>Количество</th>
                                        <th>Сумма</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHTML}
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            `;

            // Вставляем в тело таблицы
            const purchasesListBody = document.getElementById('purchasesListBody');
            purchasesListBody.insertAdjacentHTML('afterbegin', newRowHTML);
        }

        function updatePurchaseRowInDOM(purchase) {
            const purchaseRow = document.getElementById(`purchase-row-${purchase.id}`);
            const detailsRow = document.getElementById(`details-row-${purchase.id}`);

            if (!purchaseRow || !detailsRow) return;

            // 1. Обновляем основную строку
            const formattedDate = new Date(purchase.date).toLocaleDateString('ru-RU', {
                day: '2-digit', month: '2-digit', year: 'numeric'
            }).replace(/\./g, '.');

            purchaseRow.querySelector('.purchase-date').textContent = formattedDate;
            purchaseRow.querySelector('.purchase-supplier').textContent = purchase.supplier ? purchase.supplier.name : '—';
            purchaseRow.querySelector('.purchase-total').textContent = `${Number(purchase.total_amount)} грн`;
            const notesCell = purchaseRow.querySelector('.purchase-notes-cell');
            notesCell.textContent = purchase.notes || '—';
            notesCell.title = purchase.notes || '';

            // 2. Обновляем детальную строку
            const itemsHTML = purchase.items.map(item => {
                const purchasePrice = parseFloat(item.purchase_price);
                const retailPrice = parseFloat(item.retail_price);
                const quantity = parseInt(item.quantity);
                const total = purchasePrice * quantity;
                return `
                <tr>
                    <td>
                        ${item.product.photo ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">` : `<div class="no-photo">Нет фото</div>`}
                    </td>
                    <td>${item.product.name}</td>
                    <td>${Number(purchasePrice)} грн</td>
                    <td>${Number(retailPrice)} грн</td>
                    <td>${quantity} шт</td>
                    <td>${Number(total)} грн</td>
                </tr>`;
            }).join('');

            const detailsCell = detailsRow.querySelector('td');
            detailsCell.innerHTML = `
                <div class="purchases-details">
                    <div class="purchases-notes">${purchase.notes || '—'}</div>
                    <table class="table-wrapper table-striped purchases-table">
                        <thead>
                            <tr>
                                <th>Фото</th>
                                <th>Товар</th>
                                <th>Закупочная цена</th>
                                <th>Розничная цена</th>
                                <th>Количество</th>
                                <th>Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHTML}
                        </tbody>
                    </table>
                </div>
            `;
        }

        // Вспомогательные функции
        function showNotification(type, message) {
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

        // Поиск закупок
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#purchasesListBody .purchase-summary-row');

            rows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                const detailsRow = document.getElementById(row.id.replace('purchase-row-', 'details-row-'));

                if (textContent.includes(searchTerm)) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                    if (detailsRow) {
                        detailsRow.style.display = 'none'; // Также скрыть детали при поиске
                    }
                }
            });
        });

        // Глобальная переменная для хранения всех продуктов
        let allProducts = @json($products);

        // Функция для поиска товаров
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
                 onclick="selectProduct(this, '${product.name}', ${product.id}, '${input.id}')">
                ${product.name}
            </div>
        `).join('');
            }

            dropdown.style.display = 'block';
        }

        // Показать выпадающий список
        function showProductDropdown(input) {
            if (input.value.length > 0) {
                searchProducts(input);
            } else {
                const dropdown = input.nextElementSibling;
                const dropdownList = dropdown.querySelector('.product-dropdown-list');
                dropdownList.innerHTML = allProducts.map(product => `
            <div class="product-dropdown-item"
                 data-id="${product.id}"
                 onclick="selectProduct(this, '${product.name}', ${product.id}, '${input.id}')">
                ${product.name}
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

            input.value = productName;
            select.value = productId;
            dropdown.style.display = 'none';

            // Убираем выделение у всех элементов
            container.querySelectorAll('.product-dropdown-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Выделяем выбранный элемент
            element.classList.add('selected');
        }

        // Закрытие выпадающего списка при клике вне его
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.product-search-container')) {
                document.querySelectorAll('.product-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });
    </script>
    <style>
        .purchase-summary-row {
            cursor: pointer;
        }
        .purchase-summary-row td {
            vertical-align: middle;
        }
        .purchase-details-row td {
            padding: 0;
            background-color: #fdfdfd !important; /* Use important to override hover */
        }
        .purchase-details {
            padding: 20px;
        }
        .purchase-notes {
            margin-bottom: 15px;
            font-style: italic;
            color: #6c757d;
        }
        .text-right {
            text-align: right;
        }
        .purchase-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .purchase-notes-cell {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            z-index: 1050;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 250px;
            text-align: center;
        }
        .notification.success {
            background-color: #28a745;
        }
        .notification.error {
            background-color: #dc3545;
        }
    </style>
@endsection
