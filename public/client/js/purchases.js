// ===== ФАЙЛ СКРИПТОВ ДЛЯ ЗАКУПОК - ЧАСТЬ 1 =====

// Глобальные переменные
let currentDeleteId = null;
let itemCounter = 1;
let allProducts = window.allProducts || [];

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

// Используем универсальную функцию уведомлений из notifications.js

// Функции для работы с модальными окнами
function openPurchaseModal() {
    document.getElementById('purchaseForm').reset();
    document.getElementById('purchaseModal').style.display = 'block';
    
    // Устанавливаем сегодняшнюю дату в поле даты
    const dateInput = document.querySelector('#purchaseForm [name="date"]');
    if (dateInput && typeof setTodayDate === 'function') {
        setTodayDate(dateInput);
    }
}

function closePurchaseModal(force = false) {
    if (force) {
        document.getElementById('purchaseModal').style.display = 'none';
        clearErrors('purchaseForm');
        resetPurchaseForm();
        return;
    }

    const form = document.getElementById('purchaseForm');
    const supplier = form.querySelector('[name="supplier_id"]').value;
    const notes = form.querySelector('[name="notes"]').value;

    // Проверяем, есть ли данные в первом ряду товара, который не является шаблоном
    const firstItemRow = form.querySelector('.item-row:not(.template)');
    let firstProduct = '';
    if (firstItemRow) {
        const productInput = firstItemRow.querySelector('[name*="[product_id]"]');
        if (productInput) {
            firstProduct = productInput.value;
        }
    }
    
    const otherRows = form.querySelectorAll('.item-row:not(.template)').length > 1;

    if (supplier || notes || firstProduct || otherRows) {
        document.getElementById('cancelPurchaseModal').style.display = 'block';
    } else {
        document.getElementById('purchaseModal').style.display = 'none';
        clearErrors('purchaseForm');
        resetPurchaseForm();
    }
}

function closeEditPurchaseModal() {
    document.getElementById('editPurchaseModal').style.display = 'none';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    currentDeleteId = null;
}

// Закрытие модальных окон при клике вне их
window.onclick = function(event) {
    if (event.target == document.getElementById('editPurchaseModal')) {
        closeEditPurchaseModal();
    }
    if (event.target == document.getElementById('confirmationModal')) {
        closeConfirmationModal();
    }
    if (event.target == document.getElementById('cancelPurchaseModal')) {
        document.getElementById('cancelPurchaseModal').style.display = 'none';
    }
}

// Функции для работы с товарами в закупке
function addItemRow(containerId = 'itemsContainer') {
    const container = document.getElementById(containerId);
    if (!container) {
        return;
    }

    // Шаблон всегда находится в модальном окне добавления
    const template = document.querySelector('#itemsContainer .template');
    if (!template) {
        return;
    }

    const newRow = template.cloneNode(true);
    newRow.style.display = 'block';
    newRow.classList.remove('template');

    // Новый индекс — это количество существующих строк товаров
    const newIndex = container.querySelectorAll('.item-row:not(.template)').length;

    newRow.querySelectorAll('input, select').forEach(input => {
        if (input.name) {
            input.name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
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

    // Для режима редактирования: обновить имена всех полей
    if (containerId === 'editItemsContainer') {
        newRow.querySelectorAll('input, select').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/items\[\d+\]/, `items[${newIndex}]`);
            }
        });
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
    const tableRow = document.getElementById(`table-row-${id}`);
    
    if (detailsRow) {
        detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
    }
    
    if (tableRow) {
        tableRow.style.display = tableRow.style.display === 'none' ? 'table-row' : 'none';
    }
}

// ===== ЧАСТЬ 2: ФУНКЦИИ РЕДАКТИРОВАНИЯ И УДАЛЕНИЯ =====

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
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="form-row">
                            <div class="form-group">
                                <label>${window.translations?.date || 'Дата'}</label>
                                <input type="date" name="date" required class="form-control" value="${purchase.date}">
                            </div>
                            <div class="form-group">
                                <label>${window.translations?.supplier || 'Поставщик'}</label>
                                <select name="supplier_id" required class="form-control">
                                    <option value="">${window.translations?.select_supplier || 'Выберите поставщика'}</option>
                                    ${window.suppliers ? window.suppliers.map(supplier => 
                                        `<option value="${supplier.id}" ${purchase.supplier_id == supplier.id ? 'selected' : ''}>${supplier.name}</option>`
                                    ).join('') : ''}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>${window.translations?.notes || 'Примечания'}</label>
                            <textarea name="notes" rows="2" class="form-control">${purchase.notes || ''}</textarea>
                        </div>
                        <div class="items-container" id="editItemsContainer">
                            <h3>${window.translations?.products || 'Товары'}</h3>
                            ${purchase.items.map((item, index) => `
                                <div class="item-row">
                                    <div class="form-row">
                                         <div class="form-group">
                                            <label>${window.translations?.product || 'Товар'}</label>
                                            <div class="product-search-container">
                                                <input type="text"
                                                       id="product-search-edit-${index}"
                                                       class="product-search-input form-control"
                                                       placeholder="${window.translations?.start_typing_product_name || 'Начните вводить название товара'}"
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
                                            <label>${window.translations?.purchase_price || 'Закупочная цена'}</label>
                                            <input type="number" step="0.01" name="items[${index}][purchase_price]" required class="form-control" value="${item.purchase_price}">
                                        </div>
                                        <div class="form-group">
                                            <label>${window.translations?.retail_price || 'Розничная цена'}</label>
                                            <input type="number" step="0.01" name="items[${index}][retail_price]" required class="form-control" value="${item.retail_price}">
                                        </div>
                                        <div class="form-group">
                                            <label>${window.translations?.quantity || 'Количество'}</label>
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
                                ${window.translations?.add_product || 'Добавить товар'}
                            </button>
                            <button type="button" class="btn-cancel" onclick="closeEditPurchaseModal()">${window.translations?.cancel || 'Отмена'}</button>
                            <button type="submit" class="btn-submit">${window.translations?.save_changes || 'Сохранить изменения'}</button>
                        </div>
                    </form>
                `;

                modalBody.innerHTML = formHtml;
                document.getElementById('editPurchaseModal').style.display = 'block';

                // Удаляем старый обработчик, если он есть
                const oldForm = document.getElementById('editPurchaseForm');
                if (oldForm) {
                    const newForm = oldForm.cloneNode(true);
                    oldForm.parentNode.replaceChild(newForm, oldForm);
                }

                // Добавляем обработчик отправки формы
                document.getElementById('editPurchaseForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    // Инициализация календаря для поля даты
                    const dateInput = document.querySelector('#editPurchaseForm input[name="date"]');
                    if (dateInput && typeof initializeDatePicker === 'function') {
                        initializeDatePicker(dateInput);
                    }
                    
                    const items = [];

                    // Собираем данные о товарах
                    const itemRows = this.querySelectorAll('.item-row');
                    itemRows.forEach((row, index) => {
                        const productInput = row.querySelector(`[name="items[${index}][product_id]"]`);
                        const purchaseInput = row.querySelector(`[name="items[${index}][purchase_price]"]`);
                        const retailInput = row.querySelector(`[name="items[${index}][retail_price]"]`);
                        const quantityInput = row.querySelector(`[name="items[${index}][quantity]"]`);
                        items.push({
                            product_id: productInput ? productInput.value : '',
                            purchase_price: purchaseInput ? purchaseInput.value : '',
                            retail_price: retailInput ? retailInput.value : '',
                            quantity: quantityInput ? quantityInput.value : ''
                        });
                    });

                    // Отправляем запрос на обновление
                    fetch(`/purchases/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
                            window.showNotification('success', 'Закупка успешно обновлена');
                            closeEditPurchaseModal();
                            // Обновляем данные на странице
                            updatePurchaseRowInDOM(data.purchase);
                        } else {
                            window.showNotification('error', data.message || 'Ошибка обновления закупки');
                        }
                    })
                    .catch(error => {
                        window.showNotification('error', 'Ошибка обновления закупки');
                    });
                });
            } else {
                window.showNotification('error', data.message || 'Ошибка загрузки данных закупки');
            }
        })
        .catch(error => {
            window.showNotification('error', 'Ошибка загрузки данных закупки');
        });
}

// Функции для подтверждения удаления
function confirmDeletePurchase(event, id) {
    event.stopPropagation();
    currentDeleteId = id;
    document.getElementById('confirmationModal').style.display = 'block';
}

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
                window.showNotification('success', 'Закупка успешно удалена');
                // Перезагружаем текущую страницу
                loadPurchases(currentPage);
            } else {
                window.showNotification('error', 'Ошибка удаления закупки');
            }
        })
        .catch(error => {
            window.showNotification('error', 'Ошибка удаления закупки');
        });
}

// Обработчик формы добавления закупки
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
                window.showNotification('success', 'Закупка успешно добавлена');
                closePurchaseModal(true); // Принудительно закрываем и сбрасываем форму
                // Перезагружаем текущую страницу для отображения новой закупки
                loadPurchases(currentPage);
            } else {
                if (data.errors) {
                    displayErrors(data.errors, 'purchaseForm');
                } else {
                    window.showNotification('error', data.message || 'Ошибка добавления закупки');
                }
            }
        })
        .catch(error => {
            window.showNotification('error', error.message || 'Ошибка добавления закупки');
        });
}

// ===== ЧАСТЬ 3: ФУНКЦИИ ОТОБРАЖЕНИЯ ДАННЫХ И ПОИСКА =====

// Вспомогательные функции
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

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
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
                ${item.product && item.product.photo ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product ? item.product.name : 'Товар не найден'}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">` : `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`}
            </td>
            <td>${item.product ? item.product.name : 'Товар не найден'}</td>
            <td class="currency-amount" data-amount="${purchasePrice}">${formatCurrency(purchasePrice)}</td>
            <td class="currency-amount" data-amount="${retailPrice}">${formatCurrency(retailPrice)}</td>
            <td>${quantity} шт</td>
            <td class="currency-amount" data-amount="${total}">${formatCurrency(total)}</td>
        </tr>`;
    }).join('');

    // Создаём HTML для двух строк таблицы: основной и детальной
    const newRowHTML = `
        <tr class="purchase-summary-row" id="purchase-row-${purchase.id}" onclick="togglePurchaseDetailsRow(${purchase.id})">
            <td class="purchase-date">${formattedDate}</td>
            <td class="purchase-supplier">${purchase.supplier ? purchase.supplier.name : '—'}</td>
            <td class="purchase-total currency-amount" data-amount="${purchase.total_amount}">${formatCurrency(purchase.total_amount)}</td>
            <td class="purchase-notes-cell" title="${purchase.notes || ''}">${purchase.notes || '—'}</td>
            <td>
                <div class="purchases-actions">
                    <button class="btn-edit" onclick="editPurchase(event, ${purchase.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg> ${window.translations?.edit || 'Редактировать'}
                    </button>
                    <button class="btn-delete" onclick="confirmDeletePurchase(event, ${purchase.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg> ${window.translations?.delete || 'Удалить'}
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
                                <th>${window.translations?.photo || 'Фото'}</th>
                                <th>${window.translations?.product || 'Товар'}</th>
                                <th>${window.translations?.purchase_price || 'Закупочная цена'}</th>
                                <th>${window.translations?.retail_price || 'Розничная цена'}</th>
                                <th>${window.translations?.quantity || 'Количество'}</th>
                                <th>${window.translations?.sum || 'Сумма'}</th>
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
    const totalElement = purchaseRow.querySelector('.purchase-total');
    totalElement.className = 'purchase-total currency-amount';
    totalElement.setAttribute('data-amount', purchase.total_amount);
    totalElement.textContent = formatCurrency(purchase.total_amount);
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
                ${item.product && item.product.photo ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product ? item.product.name : 'Товар не найден'}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">` : `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`}
            </td>
            <td>${item.product ? item.product.name : 'Товар не найден'}</td>
            <td class="currency-amount" data-amount="${purchasePrice}">${formatCurrency(purchasePrice)}</td>
            <td class="currency-amount" data-amount="${retailPrice}">${formatCurrency(retailPrice)}</td>
            <td>${quantity} шт</td>
            <td class="currency-amount" data-amount="${total}">${formatCurrency(total)}</td>
        </tr>`;
    }).join('');

    const detailsCell = detailsRow.querySelector('td');
    detailsCell.innerHTML = `
        <div class="purchases-details">
            <div class="purchases-notes">${purchase.notes || '—'}</div>
            <table class="table-wrapper table-striped purchases-table">
                <thead>
                    <tr>
                        <th>${window.translations?.photo || 'Фото'}</th>
                        <th>${window.translations?.product || 'Товар'}</th>
                        <th>${window.translations?.purchase_price || 'Закупочная цена'}</th>
                        <th>${window.translations?.retail_price || 'Розничная цена'}</th>
                        <th>${window.translations?.quantity || 'Количество'}</th>
                        <th>${window.translations?.sum || 'Сумма'}</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHTML}
                </tbody>
            </table>
        </div>
    `;
}

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
    if (select) {
        select.value = productId;
        select.dispatchEvent(new Event('change'));
    }
    dropdown.style.display = 'none';

    // Подставляем цены из allProducts
    const product = allProducts.find(p => p.id == productId);
    if (product) {
        const row = container.closest('.item-row');
        const purchaseInput = row.querySelector('input[name*="[purchase_price]"]');
        const retailInput = row.querySelector('input[name*="[retail_price]"]');
        if (purchaseInput) purchaseInput.value = product.purchase_price;
        if (retailInput) retailInput.value = product.retail_price;
    }

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

// Автоподстановка цен из Product при выборе товара в закупке
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        const selected = e.target.options[e.target.selectedIndex];
        const row = e.target.closest('.item-row');
        if (selected && row) {
            const purchaseInput = row.querySelector('input[name*="[purchase_price]"]');
            const retailInput = row.querySelector('input[name*="[retail_price]"]');
            if (purchaseInput && selected.dataset.purchase !== undefined) {
                purchaseInput.value = selected.dataset.purchase;
            }
            if (retailInput && selected.dataset.retail !== undefined) {
                retailInput.value = selected.dataset.retail;
            }
        }
    }
});

// ===== ЧАСТЬ 4: ФУНКЦИИ ЗАГРУЗКИ И ОТОБРАЖЕНИЯ ДАННЫХ =====

// Глобальные переменные для пагинации
let currentPage = 1;

// Функция загрузки закупок
function loadPurchases(page = 1, search = '') {
    currentPage = page;
    const searchValue = search !== undefined ? search : document.getElementById('searchInput').value.trim();
    const url = `/purchases?search=${encodeURIComponent(searchValue)}&page=${page}`;
    
    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {

        // Обновляем allProducts для поиска в модальных окнах
        if (data.products) {
            allProducts = data.products;
        }
        
        renderPurchases(data.data);
        updateMobileCards(data.data);
        renderPagination(data.meta);
        renderMobilePagination(data.meta);
        
        // Переключаем на правильную версию после загрузки данных
        toggleMobileView();
        
        // Инициализируем обработчики изображений после рендеринга
        setTimeout(() => {
            initImageHandlers();
        }, 100);
    })
    .catch(error => {
        console.error('Error loading purchases:', error);
        window.showNotification('error', 'Ошибка загрузки данных');
    });
}

// Функция отображения закупок в таблице
function renderPurchases(purchases) {
    const tbody = document.getElementById('purchasesListBody');
    tbody.innerHTML = '';
    
    // Если нет закупок, не делаем ничего
    if (!purchases || purchases.length === 0) {
        return;
    }
    
    purchases.forEach(purchase => {
        // Создаем строку закупки
        const summaryRow = document.createElement('tr');
        summaryRow.className = 'purchase-summary-row';
        summaryRow.id = `purchase-row-${purchase.id}`;
        summaryRow.onclick = () => togglePurchaseDetailsRow(purchase.id);
        
        summaryRow.innerHTML = `
            <td class="purchase-date">${purchase.date ? new Date(purchase.date).toLocaleDateString('ru-RU') : '—'}</td>
            <td class="purchase-supplier">${purchase.supplier ? purchase.supplier.name : '—'}</td>
            <td class="purchase-total currency-amount" data-amount="${purchase.total_amount}">${formatCurrency(purchase.total_amount)}</td>
            <td class="purchase-notes-cell" title="${purchase.notes || '—'}">${purchase.notes || '—'}</td>
            <td>
                <div class="purchases-actions">
                    <button class="btn-edit" onclick="editPurchase(event, ${purchase.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                        ${window.translations?.edit || 'Редактировать'}
                    </button>
                    <button class="btn-delete" onclick="confirmDeletePurchase(event, ${purchase.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        ${window.translations?.delete || 'Удалить'}
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(summaryRow);

        // Создаем строку деталей
        const detailsRow = document.createElement('tr');
        detailsRow.className = 'purchases-details-row';
        detailsRow.id = `details-row-${purchase.id}`;
        detailsRow.style.display = 'none';
        
        let itemsHtml = '';
        if (purchase.items && purchase.items.length > 0) {
            itemsHtml = purchase.items.map(item => `
                <tr>
                    <td>
                        ${item.product && item.product.photo ? 
                            `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product ? item.product.name : 'Товар не найден'}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">` : 
                            `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`
                        }
                    </td>
                    <td>${item.product ? item.product.name : 'Товар не найден'}</td>
                    <td class="currency-amount" data-amount="${item.purchase_price}">${formatCurrency(item.purchase_price)}</td>
                    <td class="currency-amount" data-amount="${item.retail_price}">${formatCurrency(item.retail_price)}</td>
                    <td>${item.quantity} ${window.translations?.pieces || 'шт'}</td>
                    <td class="currency-amount" data-amount="${item.total}">${formatCurrency(item.total)}</td>
                </tr>
            `).join('');
        }

        detailsRow.innerHTML = `
            <td colspan="5">
                <div class="purchases-details">
                    <div class="purchases-notes">${purchase.notes || '—'}</div>
                </div>
            </td>
        `;
        tbody.appendChild(detailsRow);

        // Создаем отдельную строку для таблицы товаров
        const tableRow = document.createElement('tr');
        tableRow.className = 'purchases-table-row';
        tableRow.id = `table-row-${purchase.id}`;
        tableRow.style.display = 'none';
        
        tableRow.innerHTML = `
            <td colspan="5" class="purchases-table-cell">
                <div class="table-wrapper table-striped purchases-table">
                    <table class="purchases-table">
                        <thead>
                        <tr>
                            <th>${window.translations?.photo || 'Фото'}</th>
                            <th>${window.translations?.product || 'Товар'}</th>
                            <th>${window.translations?.purchase_price || 'Закупочная цена'}</th>
                            <th>${window.translations?.retail_price || 'Розничная цена'}</th>
                            <th>${window.translations?.quantity || 'Количество'}</th>
                            <th>${window.translations?.sum || 'Сумма'}</th>
                        </tr>
                        </thead>
                        <tbody>
                        ${itemsHtml}
                        </tbody>
                    </table>
                </div>
            </td>
        `;
        tbody.appendChild(tableRow);
        tbody.appendChild(detailsRow);
    });
}

// Функция обновления мобильных карточек
function updateMobileCards(purchases) {
    const cardsContainer = document.getElementById('purchasesCards');
    cardsContainer.innerHTML = '';

    if (!purchases || purchases.length === 0) {

        return;
    }

    purchases.forEach(purchase => {
        const card = document.createElement('div');
        card.className = 'purchase-card';
        card.id = `purchase-card-${purchase.id}`;
        
        card.innerHTML = `
            <div class="purchase-card-header">
                <div class="purchase-main-info">
                    <div class="purchase-supplier">${escapeHtml(purchase.supplier ? purchase.supplier.name : 'Не указано')}</div>
                    <div class="purchase-total">${formatCurrency(purchase.total_amount)}</div>
                </div>
            </div>
            <div class="purchase-info">
                <div class="purchase-info-item">
                    <span class="purchase-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Дата
                    </span>
                    <span class="purchase-info-value">${purchase.date ? new Date(purchase.date).toLocaleDateString('ru-RU') : '—'}</span>
                </div>
                <div class="purchase-info-item">
                    <span class="purchase-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        Примечания
                    </span>
                    <span class="purchase-info-value">${escapeHtml(purchase.notes || '—')}</span>
                </div>
                <div class="purchase-info-item">
                    <span class="purchase-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd" />
                        </svg>
                        Товары
                    </span>
                    <span class="purchase-info-value">${purchase.items ? purchase.items.length : 0} товаров</span>
                </div>
            </div>
            <div class="purchase-actions">
                <button class="btn-edit" onclick="editPurchase(event, ${purchase.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    ${window.translations?.edit || 'Редактировать'}
                </button>
                <button class="btn-delete" onclick="confirmDeletePurchase(event, ${purchase.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    ${window.translations?.delete || 'Удалить'}
                </button>
            </div>
        `;
        cardsContainer.appendChild(card);
    });
}

// Функции пагинации
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
    let pagContainer = document.getElementById('purchasesPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'purchasesPagination';
        document.querySelector('.dashboard-container').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    // Навешиваем обработчики
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadPurchases(page);
            }
        });
    });
}

function renderMobilePagination(meta) {
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
    let pagContainer = document.getElementById('mobilePurchasesPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'mobilePurchasesPagination';
        document.querySelector('.dashboard-container').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    // Навешиваем обработчики
    document.querySelectorAll('#mobilePurchasesPagination .page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadPurchases(page);
            }
        });
    });
}

// Функция для переключения между десктопной и мобильной версией
function toggleMobileView() {
    const tableWrapper = document.getElementById('purchasesList');
    const purchasesCards = document.getElementById('purchasesCards');
    const purchasesPagination = document.getElementById('purchasesPagination');
    const mobilePurchasesPagination = document.getElementById('mobilePurchasesPagination');
    

    
    if (window.innerWidth <= 768) {
        // Мобильная версия

        if (tableWrapper) tableWrapper.style.setProperty('display', 'none', 'important');
        if (purchasesCards) purchasesCards.style.setProperty('display', 'block', 'important');
        if (purchasesPagination) purchasesPagination.style.setProperty('display', 'none', 'important');
        if (mobilePurchasesPagination) mobilePurchasesPagination.style.setProperty('display', 'block', 'important');
    } else {
        // Десктопная версия

        if (tableWrapper) tableWrapper.style.setProperty('display', 'block', 'important');
        if (purchasesCards) purchasesCards.style.setProperty('display', 'none', 'important');
        if (purchasesPagination) purchasesPagination.style.setProperty('display', 'block', 'important');
        if (mobilePurchasesPagination) mobilePurchasesPagination.style.setProperty('display', 'none', 'important');
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Загружаем данные при загрузке страницы
    loadPurchases(1);
    
    // Привязываем обработчики событий
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitPurchaseForm(this);
    });

    // Обработчики для модальных окон подтверждения
    document.getElementById('cancelDelete').addEventListener('click', function() {
        closeConfirmationModal();
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (currentDeleteId) {
            deletePurchase(currentDeleteId);
        }
        closeConfirmationModal();
    });

    // Логика для модального окна отмены
    document.getElementById('cancelCancelPurchase').addEventListener('click', function() {
        document.getElementById('cancelPurchaseModal').style.display = 'none';
    });

    document.getElementById('confirmCancelPurchaseBtn').addEventListener('click', function() {
        document.getElementById('cancelPurchaseModal').style.display = 'none';
        closePurchaseModal(true); // Принудительно закрыть и сбросить
        window.showNotification('error', 'Создание закупки отменено');
    });

                            // Поиск с пагинацией
                        document.getElementById('searchInput').addEventListener('input', function() {
                            loadPurchases(1, this.value.trim());
                        });
                        
                        // Мобильный поиск
                        document.getElementById('searchInputMobile').addEventListener('input', function() {
                            loadPurchases(1, this.value.trim());
                        });

    // Вызываем toggleMobileView после небольшой задержки, чтобы данные успели загрузиться
    setTimeout(() => {
        toggleMobileView();
    }, 100);
});

// Вызываем toggleMobileView при изменении размера окна
window.addEventListener('resize', function() {
    toggleMobileView();
});

// Функция для инициализации обработчиков изображений
function initImageHandlers() {
    document.querySelectorAll('.product-photo').forEach(img => {
        img.style.cursor = 'pointer';
        img.style.transition = 'transform 0.2s ease';
        img.onclick = function() {
            if (typeof window.openImageModal === 'function') {
                window.openImageModal(this);
            }
        };
        // Добавляем эффект при наведении
        img.onmouseenter = function() {
            this.style.transform = 'scale(1.05)';
        };
        img.onmouseleave = function() {
            this.style.transform = 'scale(1)';
        };
    });
}

 