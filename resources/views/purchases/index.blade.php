@extends('layouts.app')

@section('content')
    <div class="purchases-container">
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

        <div class="purchases-list" id="purchasesList">
            @foreach($purchases as $purchase)
                <div class="purchase-item" id="purchase-{{ $purchase->id }}">
                    <div class="purchase-header" onclick="togglePurchaseDetails({{ $purchase->id }})">
                        <div class="purchase-info">
                            <span class="purchase-date">{{ $purchase->formatted_date }}</span>
                            <span class="purchase-supplier">{{ $purchase->supplier->name ?? '—' }}</span>
                            <span class="purchase-total">{{ number_format($purchase->total_amount, 2) }} грн</span>
                        </div>
                        <div class="purchase-actions">
                            <button class="btn-edit" onclick="editPurchase(event, {{ $purchase->id }})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Ред.
                            </button>
                            <button class="btn-delete" onclick="confirmDeletePurchase(event, {{ $purchase->id }})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Удалить
                            </button>
                        </div>
                    </div>
                    <div class="purchase-details" id="details-{{ $purchase->id }}" style="display: none;">
                        <div class="purchase-notes">{{ $purchase->notes }}</div>
                        <table class="table-striped purchase-table">
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
                                    <td>{{ number_format($item->purchase_price, 2) }} грн</td>
                                    <td>{{ number_format($item->retail_price, 2) }} грн</td>
                                    <td>{{ $item->quantity }} шт</td>
                                    <td>{{ number_format($item->total, 2) }} грн</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
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
        let itemCounter = 1;

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

            container.insertBefore(newRow, container.querySelector('.form-actions'));
            itemCounter++;
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
            const rows = document.querySelectorAll('.item-row:not(.template)');
            rows.forEach((row, index) => {
                if (index > 0) {
                    row.remove();
                }
            });

            itemCounter = 1;
        }

        // Функция для показа/скрытия деталей закупки
        function togglePurchaseDetails(id) {
            const details = document.getElementById(`details-${id}`);
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
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
                                                    <label>Товар </label>
                                                    <select name="items[${index}][product_id]" required class="form-control">
                                                        <option value="">Выберите товар</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" ${item.product_id == {{ $product->id }} ? 'selected' : ''}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Закупочная цена </label>
                                                    <input type="number" step="0.01" name="items[${index}][purchase_price]" required class="form-control" value="${item.purchase_price}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Розничная цена </label>
                                                    <input type="number" step="0.01" name="items[${index}][retail_price]" required class="form-control" value="${item.retail_price}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Количество </label>
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
                                    const purchaseElement = document.getElementById(`purchase-${id}`);
                                    if (purchaseElement) {
                                        const purchaseInfo = purchaseElement.querySelector('.purchase-info');
                                        purchaseInfo.innerHTML = `
                                            <span class="purchase-date">${data.purchase.formatted_date}</span>
                                            <span class="purchase-supplier">${data.purchase.supplier?.name ?? '—'}</span>
                                            <span class="purchase-total">${Number(data.purchase.total_amount).toFixed(2)} грн</span>
                                        `;
                                    }
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
                        showNotification('Закупка успешно удалена', 'success');
                        document.getElementById(`purchase-${id}`).remove();
                    } else {
                        showNotification('Ошибка при удалении закупки', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Ошибка при удалении закупки', 'error');
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
            document.querySelectorAll('.item-row:not(.template)').forEach((row, index) => {
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
                        showNotification('Закупка успешно добавлена', 'success');
                        closePurchaseModal();
                        addPurchaseToDOM(data.purchase);
                        resetPurchaseForm();
                    } else {
                        if (data.errors) {
                            displayErrors(data.errors, 'purchaseForm');
                        } else {
                            showNotification(data.message || 'Ошибка при добавлении закупки', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.errors) {
                        displayErrors(error.errors, 'purchaseForm');
                    } else {
                        showNotification(error.message || 'Ошибка при добавлении закупки', 'error');
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

            // Генерируем HTML для товаров
            const itemsHTML = purchase.items.map(item => {
                // Преобразуем цены и количество в числа
                const purchasePrice = parseFloat(item.purchase_price);
                const retailPrice = parseFloat(item.retail_price);
                const quantity = parseInt(item.quantity);
                const total = purchasePrice * quantity;

                return `
        <tr>
            <td>
                ${item.product.photo
                    ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">`
                    : `<div class="no-photo">Нет фото</div>`
                }
            </td>
            <td>${item.product.name}</td>
            <td>${purchasePrice.toFixed(2)} грн</td>
            <td>${retailPrice.toFixed(2)} грн</td>
            <td>${quantity} шт</td>
            <td>${total.toFixed(2)} грн</td>
        </tr>
        `;
            }).join('');

            // Создаём HTML закупки
            const purchaseHTML = `
    <div class="purchase-item" id="purchase-${purchase.id}">
        <div class="purchase-header" onclick="togglePurchaseDetails(${purchase.id})">
            <div class="purchase-info">
                <span class="purchase-date">${formattedDate}</span>
                <span class="purchase-supplier">${purchase.supplier}</span>
                <span class="purchase-total">${parseFloat(purchase.total_amount).toFixed(2)} грн</span>
            </div>
            <div class="purchase-actions">
                <button class="btn-edit" onclick="editPurchase(event, ${purchase.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    Ред.
                </button>
                <button class="btn-delete" onclick="confirmDeletePurchase(event, ${purchase.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Удалить
                </button>
            </div>
        </div>
        <div class="purchase-details" id="details-${purchase.id}" style="display: none;">
            <div class="purchase-notes">${purchase.notes || ''}</div>
            <table class="purchase-table">
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
    </div>
    `;

            // Вставляем в DOM
            const purchasesList = document.getElementById('purchasesList');
            purchasesList.insertAdjacentHTML('afterbegin', purchaseHTML);
        }

        function updatePurchaseInDOM(purchase) {
            const formattedDate = new Date(purchase.date).toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).replace(/\./g, '.');

            const purchaseElement = document.getElementById(`purchase-${purchase.id}`);
            if (purchaseElement) {
                // Преобразуем все числовые значения в числа
                const itemsWithNumbers = purchase.items.map(item => ({
                    ...item,
                    purchase_price: parseFloat(item.purchase_price),
                    retail_price: parseFloat(item.retail_price),
                    quantity: parseInt(item.quantity),
                    total: parseFloat(item.purchase_price) * parseInt(item.quantity)
                }));

                purchaseElement.innerHTML = `
            <div class="purchase-header" onclick="togglePurchaseDetails(${purchase.id})">
                <div class="purchase-info">
                    <span class="purchase-date">${formattedDate}</span>
                    <span class="purchase-supplier">${purchase.supplier}</span>
                    <span class="purchase-total">${parseFloat(purchase.total_amount).toFixed(2)} грн</span>
                </div>
                <div class="purchase-actions">
                    <button class="btn-edit" onclick="editPurchase(event, ${purchase.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        Ред.
                    </button>
                    <button class="btn-delete" onclick="confirmDeletePurchase(event, ${purchase.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Удалить
                    </button>
                </div>
            </div>
            <div class="purchase-details" id="details-${purchase.id}" style="display: none;">
                <div class="purchase-notes">${purchase.notes || ''}</div>
                <table class="purchase-table">
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
                        ${itemsWithNumbers.map(item => `
                            <tr>
                                <td>
                                    ${item.product.photo ?
                    `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">` :
                    `<div class="no-photo">Нет фото</div>`}
                                </td>
                                <td>${item.product.name}</td>
                                <td>${item.purchase_price.toFixed(2)} грн</td>
                                <td>${item.retail_price.toFixed(2)} грн</td>
                                <td>${item.quantity} шт</td>
                                <td>${item.total.toFixed(2)} грн</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
            }
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
            const purchases = document.querySelectorAll('.purchase-item');

            purchases.forEach(purchase => {
                const header = purchase.querySelector('.purchase-header');
                const textContent = header.textContent.toLowerCase();
                if (textContent.includes(searchTerm)) {
                    purchase.style.display = 'block';
                } else {
                    purchase.style.display = 'none';
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
@endsection
