// ===== СКРИПТЫ ДЛЯ РАЗДЕЛА "ИНВЕНТАРИЗАЦИЯ" =====
// Использует общие функции из common.js для:
// - escapeHtml() - экранирование HTML
// - formatPrice() - форматирование цен
// - toggleMobileView() - переключение мобильного вида
// - openImageModal() / closeImageModal() - работа с изображениями
// - clearErrors() / showErrors() - обработка ошибок

// ===== ЧАСТЬ 1: Глобальные переменные и инициализация =====
let currentDeleteId = null;
let itemCounter = 1;
let allProducts = [];

// ===== ЧАСТЬ 2: Функции для работы с модальными окнами =====
function openInventoryModal() {
    // Полный сброс формы
    resetInventoryForm();
    document.getElementById('inventoryModal').style.display = 'block';
    
    // Устанавливаем сегодняшнюю дату в поле даты
    const dateInput = document.querySelector('#inventoryForm [name="date"]');
    if (dateInput && typeof setTodayDate === 'function') {
        setTodayDate(dateInput);
    }
}

function closeInventoryModal(force = false) {
    if (force) {
        resetInventoryModal();
        return;
    }
    // Проверяем, есть ли введенные данные
    const hasData = Array.from(document.querySelectorAll('#inventoryForm input, #inventoryForm select, #inventoryForm textarea'))
        .some(el => {
            if (el.name === 'items[0][product_id]' || el.name === 'items[0][actual_qty]') {
                return el.value !== '' && el.value !== '0';
            }
            return el.value !== '';
        });

    if (hasData) {
        document.getElementById('cancelInventoryModal').style.display = 'block';
    } else {
        resetInventoryModal();
    }
}

function resetInventoryModal() {
    document.getElementById('inventoryModal').style.display = 'none';
    clearErrors('inventoryForm');
    resetInventoryForm();
}

function openAnalysisModal() {
    document.getElementById('analysisModal').style.display = 'block';
}

function closeAnalysisModal() {
    document.getElementById('analysisModal').style.display = 'none';
}

function closeEditInventoryModal(force = false) {
    if (force) {
        document.getElementById('editInventoryModal').style.display = 'none';
        // Возвращаем исходный заголовок
        const modalHeader = document.querySelector('#editInventoryModal .modal-header h2');
        modalHeader.textContent = window.messages.edit_inventory || 'Редактировать инвентаризацию';
        return;
    }
    // Проверяем, есть ли введенные данные
    const hasData = Array.from(document.querySelectorAll('#editInventoryForm input, #editInventoryForm select, #editInventoryForm textarea'))
        .some(el => el.value && el.value !== '' && el.value !== '0');
    if (hasData) {
        document.getElementById('cancelEditInventoryModal').style.display = 'block';
    } else {
        document.getElementById('editInventoryModal').style.display = 'none';
    }
}

function closeViewAllItemsModal() {
    document.getElementById('viewAllItemsModal').style.display = 'none';
}

// Закрытие модальных окон при клике вне их
window.onclick = function(event) {
    if (event.target == document.getElementById('analysisModal')) {
        closeAnalysisModal();
    }
    if (event.target == document.getElementById('editInventoryModal')) {
        closeEditInventoryModal();
    }
    if (event.target == document.getElementById('viewAllItemsModal')) {
        closeViewAllItemsModal();
    }
    if (event.target == document.getElementById('confirmationModal')) {
        closeConfirmationModal();
    }
    if (event.target == document.getElementById('cancelInventoryModal')) {
        closeCancelInventoryModal();
    }
    if (event.target == document.getElementById('cancelEditInventoryModal')) {
        closeCancelEditInventoryModal();
    }
    if (event.target == document.getElementById('zoomImageModal')) {
        document.getElementById('zoomImageModal').style.display = 'none';
        document.getElementById('zoomedImage').src = '';
    }
}

// Функции закрытия модальных окон
function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    currentDeleteId = null;
}

function closeCancelInventoryModal() {
    document.getElementById('cancelInventoryModal').style.display = 'none';
}

function closeCancelEditInventoryModal() {
    document.getElementById('cancelEditInventoryModal').style.display = 'none';
}

// Логика для модального окна отмены инвентаризации
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cancelCancelInventory').addEventListener('click', function() {
        closeCancelInventoryModal();
    });
    
    document.getElementById('confirmCancelInventoryBtn').addEventListener('click', function() {
        closeCancelInventoryModal();
        resetInventoryModal();
        window.showNotification('error', window.messages.inventory_cancelled || 'Инвентаризация отменена');
    });

    // Логика для модального окна подтверждения отмены редактирования инвентаризации
    document.getElementById('cancelCancelEditInventory').addEventListener('click', function() {
        closeCancelEditInventoryModal();
    });
    
    document.getElementById('confirmCancelEditInventoryBtn').addEventListener('click', function() {
        closeCancelEditInventoryModal();
        document.getElementById('editInventoryModal').style.display = 'none';
        // Возвращаем исходный заголовок
        const modalHeader = document.querySelector('#editInventoryModal .modal-header h2');
        modalHeader.textContent = window.messages.edit_inventory || 'Редактировать инвентаризацию';
        window.showNotification('error', window.messages.edit_cancelled || 'Редактирование отменено');
    });
});

// ===== ЧАСТЬ 3: Функции для работы с товарами и формами =====
function addItemRow(containerId = 'itemsContainer') {
    const container = document.getElementById(containerId);
    const template = container.querySelector('.template');
    const newRow = template.cloneNode(true);

    newRow.style.display = 'block';
    newRow.classList.remove('template');

    // Определяем текущий индекс
    let index = itemCounter;
    if (containerId === 'editItemsContainer') {
        // Для редактирования считаем количество уже добавленных рядов
        index = container.querySelectorAll('.item-row:not(.template)').length;
    }

    // Обновляем индексы в именах полей
    const inputs = newRow.querySelectorAll('input, select');
    inputs.forEach(input => {
        const name = input.name.replace(/\[\d+\]/, `[${index}]`);
        input.name = name;
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else {
            input.value = input.name.includes('actual_qty') ? '0' : '';
        }
    });

    // Инициализируем поиск для нового ряда
    const searchContainer = newRow.querySelector('.product-search-container');
    if (searchContainer) {
        const searchInput = searchContainer.querySelector('.product-search-input');
        searchInput.id = `product-search-${index}`;
        searchInput.value = '';

        const select = searchContainer.querySelector('.product-select');
        select.name = `items[${index}][product_id]`;
        select.selectedIndex = 0;
    }

    container.insertBefore(newRow, container.querySelector('.form-actions'));
    if (containerId === 'itemsContainer') itemCounter++;
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
                input.value = input.name.includes('actual_qty') ? '0' : '';
            }
        });
    }
}

function resetInventoryForm() {
    itemCounter = 1;
    const form = document.getElementById('inventoryForm');
    form.reset();

    // Удаляем все ряды товаров, кроме шаблона
    const container = document.getElementById('itemsContainer');
    const rows = container.querySelectorAll('.item-row:not(.template)');
    rows.forEach(row => row.remove());

    // Добавляем первый пустой ряд
    addItemRow();
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

    const filteredProducts = window.allProducts.filter(product =>
        product.name.toLowerCase().includes(searchTerm)
    );

    if (filteredProducts.length === 0) {
        dropdownList.innerHTML = '<div class="product-dropdown-item">' + (window.messages.products_not_found || 'Товары не найдены') + '</div>';
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
        dropdownList.innerHTML = window.allProducts.map(product => `
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

    // Автоматически подставлять остаток на складе в warehouse_qty
    const warehouseQtyInput = container.closest('.item-row').querySelector('[name*="warehouse_qty"]');
    if (warehouseQtyInput && window.allProducts) {
        const pid = Number(productId);
        const product = window.allProducts.find(p => Number(p.id) === pid);
        let stock = 0;
        if (product && product.stock !== undefined && !isNaN(parseInt(product.stock))) {
            stock = parseInt(product.stock);
        }
        warehouseQtyInput.value = stock;
        warehouseQtyInput.dispatchEvent(new Event('input', { bubbles: true }));
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

// Автоочистка поля при фокусе и возврат 0 при blur
document.addEventListener('focusin', function(e) {
    if (e.target.matches('input[name*="actual_qty"], input[name*="warehouse_qty"]')) {
        if (e.target.value === '0') {
            e.target.value = '';
        }
    }
});

document.addEventListener('focusout', function(e) {
    if (e.target.matches('input[name*="actual_qty"], input[name*="warehouse_qty"]')) {
        if (e.target.value === '' || e.target.value === null) {
            e.target.value = '0';
        }
    }
});

// ===== ЧАСТЬ 4: Функции для анализа, сохранения и управления инвентаризацией =====
function analyzeInventory() {
    clearErrors('inventoryForm');
    const formData = {
        date: document.querySelector('#inventoryForm [name="date"]').value,
        user_id: document.querySelector('#inventoryForm [name="user_id"]').value,
        notes: document.querySelector('#inventoryForm [name="notes"]').value,
        items: []
    };

    // Проверка обязательных полей
    if (!formData.date || !formData.user_id) {
        window.showNotification('error', window.messages.fill_all_required_fields || 'Заполните все обязательные поля');
        return;
    }

    const seenProducts = new Set();
    let hasErrors = false;

    document.querySelectorAll('#itemsContainer .item-row:not(.template)').forEach(row => {
        const productId = row.querySelector('[name*="product_id"]').value;
        const actualQty = row.querySelector('[name*="actual_qty"]').value;
        const productName = row.querySelector('.product-search-input').value;

        if (!productId) {
            showError(row.querySelector('[name*="product_id"]'), window.messages.select_product || 'Выберите товар');
            hasErrors = true;
            return;
        }
        if (seenProducts.has(productId)) {
            showError(row.querySelector('[name*="product_id"]'), window.messages.product_already_added || 'Товар уже добавлен');
            hasErrors = true;
            return;
        }
        seenProducts.add(productId);

        let warehouseQty = 0;
        let pid = Number(productId);
        let foundProduct = null;
        if (window.allProducts) {
            foundProduct = window.allProducts.find(p => Number(p.id) === pid);
            if (foundProduct && foundProduct.stock !== undefined && !isNaN(parseInt(foundProduct.stock))) {
                warehouseQty = parseInt(foundProduct.stock);
            }
        }

        formData.items.push({
            product_id: productId,
            product_name: productName,
            warehouse_qty: warehouseQty,
            actual_qty: parseInt(actualQty) || 0,
            difference: (parseInt(actualQty) || 0) - warehouseQty
        });
    });

    if (hasErrors) return;

    if (formData.items.length === 0) {
        window.showNotification('error', window.messages.add_at_least_one_product || 'Добавьте хотя бы один товар');
        return;
    }

    // Сохраняем данные в data-атрибут модалки анализа
    document.getElementById('analysisModal').dataset.inventory = JSON.stringify(formData);

    updateAnalysisTable(formData);
    closeInventoryModal(true);
    openAnalysisModal();
}

function updateAnalysisTable(data) {
    const tableBody = document.getElementById('analysisTableBody');
    tableBody.innerHTML = '';

    const totalItems = data.items.length;
    const matchedItems = data.items.filter(item => item.difference === 0).length;
    const shortageItems = data.items.filter(item => item.difference < 0).length;
    const overageItems = data.items.filter(item => item.difference > 0).length;

    data.items.forEach(item => {
        const status = item.difference == 0 ? (window.messages.matches_status || 'Совпадает') :
            item.difference > 0 ? (window.messages.overage_status || 'Избыток') : (window.messages.shortage_status || 'Недостача');
        let product = null;
        if (window.allProducts) {
            product = window.allProducts.find(p => p.id == item.product_id);
        }
        let photoHtml = `<div class="no-photo">${window.messages?.no_photo || 'Нет фото'}</div>`;
        if (product && product.photo) {
            photoHtml = `<a href="#" class="zoomable-image" data-img="/storage/${product.photo}">
                <img src="/storage/${product.photo}" alt="${item.product_name}" class="product-photo">
            </a>`;
        }
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${photoHtml}</td>
            <td>${item.product_name}</td>
            <td class="small-col">${item.warehouse_qty} ${window.messages.units || 'pcs'}</td>
            <td class="small-col">${item.actual_qty} ${window.messages.units || 'pcs'}</td>
            <td class="${item.difference > 0 ? 'text-success' : 'text-danger'}">
                ${item.difference > 0 ? '+' : ''}${item.difference} ${window.messages.units || 'pcs'}
            </td>
            <td>${status}</td>
        `;
        tableBody.appendChild(row);
    });

    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('matchedItems').textContent = matchedItems;
    document.getElementById('shortageItems').textContent = shortageItems;
    document.getElementById('overageItems').textContent = overageItems;
}

function saveInventory() {
    // Берём данные из data-атрибута модалки анализа
    const data = document.getElementById('analysisModal').dataset.inventory;
    const inventoryData = data ? JSON.parse(data) : null;

    if (!inventoryData) {
        window.showNotification('error', window.messages.error_loading_data || 'Ошибка загрузки данных');
        return;
    }
    if (!inventoryData.items || inventoryData.items.length === 0) {
        window.showNotification('error', window.messages.add_at_least_one_product || 'Добавьте хотя бы один товар');
        return;
    }
    if (!inventoryData.date || !inventoryData.user_id) {
        window.showNotification('error', window.messages.fill_all_required_fields || 'Заполните все обязательные поля');
        return;
    }

    const saveBtn = document.querySelector('#analysisModal .btn-submit');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';
    saveBtn.disabled = true;
    
    fetch('/inventories', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(inventoryData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.showNotification('success', window.messages.inventory_successfully_saved || 'Инвентаризация успешно сохранена');
            closeAnalysisModal();
            addInventoryToDOM(data.inventory);
            resetInventoryForm();
            // Очищаем data-атрибут
            document.getElementById('analysisModal').dataset.inventory = '';
        } else {
            window.showNotification('error', data.message || (window.messages.error_saving_inventory || 'Ошибка сохранения инвентаризации'));
        }
    })
    .catch(error => {
        window.showNotification('error', error.message || (window.messages.error_saving_inventory || 'Ошибка сохранения инвентаризации'));
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Функция для редактирования инвентаризации
function editInventory(event, id) {
    event.stopPropagation();
    fetch(`/inventories/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const inventory = data.inventory;
                const modalBody = document.getElementById('editInventoryModalBody');
                
                // Обновляем заголовок модального окна с датой
                const modalHeader = document.querySelector('#editInventoryModal .modal-header h2');
                const formattedDate = new Date(inventory.date).toLocaleDateString('ru-RU', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });
                modalHeader.textContent = `${window.messages.edit_inventory || 'Редактировать инвентаризацию'} - ${formattedDate}`;

                // Создаем форму редактирования
                const formHtml = `
                    <form id="editInventoryForm" data-project-id="${inventory.project_id}">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="form-row">
                            <div class="form-group">
                                <label>${window.messages.date || 'Дата'}</label>
                                <input type="date" name="date" required class="form-control" value="${inventory.date}"
                                       data-locale="ru-RU">
                            </div>
                            <div class="form-group">
                                <label>${window.messages.responsible || 'Ответственный'}</label>
                                <select name="user_id" required class="form-control">
                                    <option value="">${window.messages.select_responsible || 'Выберите ответственного'}</option>
                                    ${window.allUsers ? window.allUsers.map(user => 
                                        `<option value="${user.id}" ${inventory.user_id == user.id ? 'selected' : ''}>
                                            ${user.name}
                                        </option>`
                                    ).join('') : ''}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>${window.messages.notes || 'Заметки'}</label>
                            <textarea name="notes" rows="2" class="form-control">${inventory.notes || ''}</textarea>
                        </div>
                        <div class="items-container" id="editItemsContainer">
                            <h3>${window.messages.products || 'Товары'}</h3>
                            <div class="item-row template" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group product-group large-col">
                                        <label>${window.messages.product || 'Товар'}</label>
                                        <div class="product-search-container">
                                            <input type="text" class="product-search-input form-control large-col"
                                                   placeholder="${window.messages.start_typing_product_name || 'Начните вводить название товара'}"
                                                   oninput="searchProducts(this)"
                                                   onfocus="showProductDropdown(this)" autocomplete="off">
                                            <div class="product-dropdown" style="display: none;">
                                                <div class="product-dropdown-list"></div>
                                            </div>
                                            <select name="items[0][product_id]" class="form-control product-select large-col" style="display: none;">
                                                <option value="">${window.messages.select_product || 'Выберите товар'}</option>
                                                ${window.allProducts ? window.allProducts.map(product => 
                                                    `<option value="${product.id}" data-stock="${product.stock || 0}">
                                                        ${product.name}
                                                    </option>`
                                                ).join('') : ''}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group small-col">
                                        <label>${window.messages.warehouse_short || 'Склад'}</label>
                                        <input type="number" name="items[0][warehouse_qty]" class="form-control small-col" value="0" readonly>
                                    </div>
                                    <div class="form-group small-col">
                                        <label>${window.messages.quantity_short || 'Кол-во'}</label>
                                        <input type="number" name="items[0][actual_qty]" required class="form-control small-col" min="0" value="0">
                                    </div>
                                    <div class="form-group small-col">
                                        <button type="button" class="btn-remove-item" onclick="removeItemRow(this)">
                                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            ${inventory.items.map((item, index) => `
                                <div class="item-row">
                                    <div class="form-row">
                                        <div class="form-group product-group large-col">
                                            <label>${window.messages.product || 'Товар'}</label>
                                            <div class="product-search-container">
                                                <input type="text" class="product-search-input form-control large-col" value="${item.product.name}" readonly>
                                                <select name="items[${index}][product_id]" class="form-control product-select large-col" style="display: none;">
                                                    <option value="${item.product_id}" selected>${item.product.name}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group small-col">
                                            <label>${window.messages.warehouse_short || 'Склад'}</label>
                                            <input type="number" name="items[${index}][warehouse_qty]" class="form-control small-col" value="${item.warehouse_qty}" readonly>
                                        </div>
                                        <div class="form-group small-col">
                                            <label>${window.messages.quantity_short || 'Кол-во'}</label>
                                            <input type="number" name="items[${index}][actual_qty]" required class="form-control small-col" min="0" value="${item.actual_qty}">
                                        </div>
                                        <div class="form-group small-col">
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
                                ${window.messages.add_product || 'Добавить товар'}
                            </button>
                            <button type="button" class="btn-cancel" onclick="closeEditInventoryModal()">${window.messages.cancel || 'Отмена'}</button>
                            <button type="button" class="btn-submit" onclick="analyzeEditInventory(${inventory.id})">${window.messages.conduct_inventory || 'Провести инвентаризацию'}</button>
                        </div>
                    </form>
                `;

                modalBody.innerHTML = formHtml;
                document.getElementById('editInventoryModal').style.display = 'block';
                
                // Инициализация календаря для поля даты
                const dateInput = document.querySelector('#editInventoryForm input[name="date"]');
                if (dateInput && typeof initializeDatePicker === 'function') {
                    initializeDatePicker(dateInput);
                }
            } else {
                window.showNotification('error', data.message || (window.messages.error_loading_data || 'Ошибка загрузки данных'));
            }
        })
        .catch(error => {
            window.showNotification('error', window.messages.error_loading_data || 'Ошибка загрузки данных');
        });
}

// Функция для анализа при редактировании инвентаризации
function analyzeEditInventory(id) {
    clearErrors('editInventoryForm');

    // Собираем данные из формы
    const form = document.getElementById('editInventoryForm');
    const date = form.querySelector('[name="date"]').value;
    const user_id = form.querySelector('[name="user_id"]').value;
    const notes = form.querySelector('[name="notes"]').value;

    const items = [];
    const seenProducts = new Set();
    let hasErrors = false;

    form.querySelectorAll('#editItemsContainer .item-row').forEach(row => {
        if (row.classList.contains('template')) return;
        const productId = row.querySelector('[name*="product_id"]').value;
        const actualQty = row.querySelector('[name*="actual_qty"]').value;
        const warehouseQty = row.querySelector('[name*="warehouse_qty"]').value || 0;
        const productName = row.querySelector('.product-search-input').value;

        if (!productId) {
            showError(row.querySelector('[name*="product_id"]'), window.messages.select_product || 'Выберите товар');
            hasErrors = true;
            return;
        }
        if (seenProducts.has(productId)) {
            showError(row.querySelector('[name*="product_id"]'), window.messages.product_already_added || 'Товар уже добавлен');
            hasErrors = true;
            return;
        }
        seenProducts.add(productId);

        items.push({
            product_id: productId,
            product_name: productName,
            warehouse_qty: parseInt(warehouseQty) || 0,
            actual_qty: parseInt(actualQty) || 0,
            difference: (parseInt(actualQty) || 0) - (parseInt(warehouseQty) || 0)
        });
    });

    if (hasErrors) return;
    if (items.length === 0) {
        window.showNotification('error', window.messages.add_at_least_one_product || 'Добавьте хотя бы один товар');
        return;
    }
    if (!date || !user_id) {
        window.showNotification('error', window.messages.fill_all_required_fields || 'Заполните все обязательные поля');
        return;
    }

    // Формируем объект для отправки
    const data = {
        _method: 'PUT',
        date: date,
        user_id: user_id,
        notes: notes,
        items: items
    };

    // Отправляем на сервер
    fetch(`/inventories/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', window.messages.inventory_successfully_updated || 'Инвентаризация успешно обновлена');
            closeEditInventoryModal(true);
            updateInventoryInDOM(data.inventory);
        } else {
            window.showNotification('error', data.message || (window.messages.error_updating_inventory || 'Ошибка обновления инвентаризации'));
        }
    })
    .catch(error => {
        window.showNotification('error', window.messages.error_updating_inventory || 'Ошибка обновления инвентаризации');
    });
}

// Функции для подтверждения удаления
function confirmDeleteInventory(event, id) {
    event.stopPropagation();
    currentDeleteId = id;
    document.getElementById('confirmationModal').style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cancelDelete').addEventListener('click', function() {
        closeConfirmationModal();
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentDeleteId) {
            deleteInventory(currentDeleteId);
        }
        closeConfirmationModal();
    });
});

// Функция для удаления инвентаризации
function deleteInventory(id) {
    fetch(`/inventories/${id}`, {
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
                window.showNotification('success', window.messages.inventory_successfully_deleted || 'Инвентаризация успешно удалена');
                const summaryRow = document.getElementById(`inventory-row-${id}`);
                const detailsRow = document.getElementById(`details-row-${id}`);
                if (summaryRow) summaryRow.remove();
                if (detailsRow) detailsRow.remove();
                
                // Удаляем мобильную карточку
                removeInventoryCardFromDOM(id);
            } else {
                window.showNotification('error', data.message || (window.messages.error_deleting_inventory || 'Ошибка удаления инвентаризации'));
            }
        })
        .catch(error => {
            window.showNotification('error', window.messages.error_deleting_inventory || 'Ошибка удаления инвентаризации');
        });
}

// Функция для просмотра всех товаров инвентаризации
function viewAllInventoryItems(id) {
    fetch(`/inventories/${id}/items`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modalBody = document.getElementById('viewAllItemsModalBody');

                let html = `
                    <table class="table-striped analysis-table products-table">
                        <thead>
                            <tr>
                                <th>${window.messages.photo || 'Фото'}</th>
                                <th class="large-col">${window.messages.product || 'Товар'}</th>
                                <th class="small-col">${window.messages.warehouse_short || 'Склад'}</th>
                                <th class="small-col">${window.messages.quantity || 'Количество'}</th>
                                <th>${window.messages.difference || 'Разница'}</th>
                                <th>${window.messages.status || 'Статус'}</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                data.items.forEach(item => {
                    const status = item.difference == 0 ? (window.messages.matches_status || 'Совпадает') :
                        item.difference > 0 ? (window.messages.overage_status || 'Избыток') : (window.messages.shortage_status || 'Недостача');

                    html += `
                        <tr>
                            <td>
                                ${item.product.photo ?
                                    `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">` :
                                    `<div class="no-photo">${window.messages.no_photo || 'Нет фото'}</div>`}
                            </td>
                            <td class="large-col">${item.product.name}</td>
                            <td class="small-col">${item.warehouse_qty} ${window.messages.units || 'pcs'}</td>
                            <td class="small-col">${item.actual_qty} ${window.messages.units || 'pcs'}</td>
                            <td class="${item.difference > 0 ? 'text-success' : (item.difference < 0 ? 'text-danger' : '')}">
                                ${item.difference > 0 ? '+' : ''}${item.difference} ${window.messages.units || 'pcs'}
                            </td>
                            <td>${status}</td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                `;

                modalBody.innerHTML = html;
                document.getElementById('viewAllItemsModal').style.display = 'block';
            } else {
                window.showNotification('error', data.message || (window.messages.error_loading_data || 'Ошибка загрузки данных'));
            }
        })
        .catch(error => {
            window.showNotification('error', window.messages.error_loading_data || 'Ошибка загрузки данных');
        });
}

// Функция для показа/скрытия деталей инвентаризации
function toggleInventoryDetailsRow(id) {
    const detailsRow = document.getElementById(`details-row-${id}`);
    if (detailsRow) {
        detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
    }
}

// Функция для скачивания PDF с расхождениями
function downloadInventoryPdf(event, id) {
    event.stopPropagation();
    const link = document.createElement('a');
    link.href = `/inventories/${id}/pdf`;
    link.target = '_blank';
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}



// Подтверждение закрытия анализа инвентаризации
function confirmCloseAnalysisModal() {
    const data = document.getElementById('analysisModal').dataset.inventory;
    const inventoryData = data ? JSON.parse(data) : null;
    if (inventoryData && inventoryData.items && inventoryData.items.length > 0) {
        document.getElementById('cancelInventoryModal').style.display = 'block';
        // Обновляем обработчик подтверждения отмены
        document.getElementById('confirmCancelInventoryBtn').onclick = function() {
            document.getElementById('cancelInventoryModal').style.display = 'none';
            closeAnalysisModal();
            resetInventoryModal();
            // Очищаем data-атрибут
            document.getElementById('analysisModal').dataset.inventory = '';
            window.showNotification('info', window.messages.changes_not_saved || 'Изменения не сохранены');
        };
    } else {
        closeAnalysisModal();
        resetInventoryModal();
        document.getElementById('analysisModal').dataset.inventory = '';
    }
}

// Увеличение фото товара при клике
document.addEventListener('click', function(e) {
    if (e.target.closest('.zoomable-image')) {
        e.preventDefault();
        const imgSrc = e.target.closest('.zoomable-image').getAttribute('data-img');
        const zoomModal = document.getElementById('zoomImageModal');
        const zoomedImage = document.getElementById('zoomedImage');
        zoomedImage.src = imgSrc;
        zoomModal.style.display = 'block';
    }
    if (e.target.id === 'closeZoomImageModal' || (e.target.id === 'zoomImageModal' && e.target === document.getElementById('zoomImageModal'))) {
        document.getElementById('zoomImageModal').style.display = 'none';
        document.getElementById('zoomedImage').src = '';
    }
});

// Обработчики для изображений товаров с классом product-photo
function initInventoryImageHandlers() {
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

// Поиск инвентаризаций
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const inventories = document.querySelectorAll('.inventory-summary-row');

            inventories.forEach(inventory => {
                const header = inventory.querySelector('.inventory-header');
                const textContent = header.textContent.toLowerCase();
                if (textContent.includes(searchTerm)) {
                    inventory.style.display = 'table-row';
                } else {
                    inventory.style.display = 'none';
                }
            });
        });
    }
});

// Вспомогательные функции
function showError(input, message) {
    input.classList.add('is-invalid');
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.textContent = message;
    input.parentNode.insertBefore(errorElement, input.nextSibling);
}

// Универсальная функция для очистки ошибок формы
function clearErrors(formId = null) {
    let form;
    if (formId) {
        form = document.getElementById(formId);
    } else {
        form = document;
    }
    if (!form) return;
    // Удаляем все сообщения об ошибках
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    // Убираем класс is-invalid у всех полей
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

// ===== ЧАСТЬ 5: Функции для работы с DOM и вспомогательные функции =====

// Функция для добавления новой инвентаризации в DOM
function addInventoryToDOM(inventory) {
    // Форматируем дату
    const formattedDate = inventory.date
        ? new Date(inventory.date).toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }).replace(/\./g, '.')
        : 'Нет даты';

    // Генерируем HTML для товаров с расхождениями
    const itemsWithDiscrepancies = inventory.items.filter(item => item.difference !== 0);

    const itemsHTML = itemsWithDiscrepancies.map(item => {
        const status = item.difference == 0 ? (window.messages.matches_status || 'Совпадает') :
            item.difference > 0 ? (window.messages.overage_status || 'Избыток') : (window.messages.shortage_status || 'Недостача');

        return `
            <tr>
                <td>
                    ${item.product.photo
                        ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">`
                        : `<div class="no-photo">${window.messages.no_photo || 'Нет фото'}</div>`
                    }
                </td>
                <td>${item.product.name}</td>
                <td class="small-col">${item.warehouse_qty} ${window.messages.units || 'pcs'}</td>
                <td class="small-col">${item.actual_qty} ${window.messages.units || 'pcs'}</td>
                <td class="${item.difference > 0 ? 'text-success' : 'text-danger'}">
                    ${item.difference > 0 ? '+' : ''}${item.difference} ${window.messages.units || 'pcs'}
                </td>
                <td>${status}</td>
            </tr>
        `;
    }).join('');

    // Создаём HTML инвентаризации
    const inventoryHTML = `
        <tr class="inventory-summary-row" id="inventory-row-${inventory.id}" onclick="toggleInventoryDetailsRow(${inventory.id})">
            <td>${formattedDate}</td>
            <td>${inventory.user.name}</td>
            <td>${inventory.discrepancies_count}
                ${inventory.shortages_count > 0 ? `(${inventory.shortages_count} ${window.messages.shortage || 'Недостача'})` : ''}
                ${inventory.overages_count > 0 ? `(${inventory.overages_count} ${window.messages.overage || 'Избыток'})` : ''}
            </td>
            <td title="${inventory.notes}">${inventory.notes ? (inventory.notes.length > 30 ? inventory.notes.substring(0, 30) + '…' : inventory.notes) : '—'}</td>
            <td>
                <div class="inventory-actions">
                    <button class="btn-edit" onclick="editInventory(event, ${inventory.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        ${window.messages.edit_short || 'Ред'}
                    </button>
                    <button class="btn-delete" onclick="confirmDeleteInventory(event, ${inventory.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        ${window.messages.delete || 'Удалить'}
                    </button>
                    <button class="btn-pdf" onclick="downloadInventoryPdf(event, ${inventory.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L13 3.586A2 2 0 0011.586 3H6zm2 2h3v3a1 1 0 001 1h3v9a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1zm5 3.414V8h-2V6h.586L13 5.414zM8 10a1 1 0 100 2h4a1 1 0 100-2H8zm0 4a1 1 0 100 2h4a1 1 0 100-2H8z"/>
                        </svg>
                        PDF
                    </button>
                </div>
            </td>
        </tr>
        <tr class="inventory-details-row" id="details-row-${inventory.id}" style="display: none;">
            <td colspan="5">
                <div class="inventory-notes">${inventory.notes || ''}</div>
                <table class="table-striped analysis-table products-table">
                    <thead>
                        <tr>
                            <th>${window.messages.photo || 'Фото'}</th>
                            <th class="large-col">${window.messages.product || 'Товар'}</th>
                            <th class="small-col">${window.messages.warehouse_short || 'Склад'}</th>
                            <th class="small-col">${window.messages.quantity || 'Количество'}</th>
                            <th>${window.messages.difference || 'Разница'}</th>
                            <th>${window.messages.status || 'Статус'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHTML}
                    </tbody>
                </table>
                <div class="view-all-items">
                    <button class="btn-view-all" onclick="viewAllInventoryItems(${inventory.id})">
                        ${window.messages.view_all_list || 'Просмотреть весь список'}
                    </button>
                </div>
            </td>
        </tr>
    `;

    // Вставляем в DOM
    const inventoriesListBody = document.getElementById('inventoriesListBody');
    inventoriesListBody.insertAdjacentHTML('beforeend', inventoryHTML);
}

// Функция для обновления инвентаризации в DOM
function updateInventoryInDOM(inventory) {
    const formattedDate = new Date(inventory.date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).replace(/\./g, '.');

    // Создаём новую основную строку
    const summaryRow = document.createElement('tr');
    summaryRow.className = 'inventory-summary-row';
    summaryRow.id = `inventory-row-${inventory.id}`;
    summaryRow.setAttribute('onclick', `toggleInventoryDetailsRow(${inventory.id})`);
    
    const discrepanciesCell =
        inventory.discrepancies_count > 0
            ? `<span
                ${inventory.overages_count > 0 && inventory.shortages_count == 0
                    ? 'style="color: #b78e15;"'
                    : inventory.shortages_count > 0 && inventory.overages_count == 0
                        ? 'class="text-danger"'
                        : ''}
            >${inventory.discrepancies_count}
                ${inventory.shortages_count > 0 ? `(${inventory.shortages_count} ${window.messages.shortage || 'Недостача'})` : ''}
                ${inventory.overages_count > 0 ? `(${inventory.overages_count} ${window.messages.overage || 'Избыток'})` : ''}
            </span>`
            : `<span class="text-success">${window.messages.matches || 'Совпадает'}</span>`;
    
    summaryRow.innerHTML = `
        <td>${formattedDate}</td>
        <td>${inventory.user.name}</td>
        <td>${discrepanciesCell}</td>
        <td title="${inventory.notes}">${inventory.notes ? (inventory.notes.length > 30 ? inventory.notes.substring(0, 30) + '…' : inventory.notes) : '—'}</td>
        <td>
            <div class="inventory-actions">
                <button class="btn-edit" onclick="editInventory(event, ${inventory.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    Ред.
                </button>
                <button class="btn-delete" onclick="confirmDeleteInventory(event, ${inventory.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Удалить
                </button>
                <button class="btn-pdf" onclick="downloadInventoryPdf(event, ${inventory.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L13 3.586A2 2 0 0011.586 3H6zm2 2h3v3a1 1 0 001 1h3v9a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1zm5 3.414V8h-2V6h.586L13 5.414zM8 10a1 1 0 100 2h4a1 1 0 100-2H8zm0 4a1 1 0 100 2h4a1 1 0 100-2H8z"/>
                    </svg>
                    PDF
                </button>
            </div>
        </td>
    `;

    // Создаём новую детальную строку
    const detailsRow = document.createElement('tr');
    detailsRow.className = 'inventory-details-row';
    detailsRow.id = `details-row-${inventory.id}`;
    detailsRow.style.display = 'none';
    
    const itemsWithDiscrepancies = inventory.items.filter(item => item.difference !== 0);
    const itemsHTML = itemsWithDiscrepancies.map(item => {
        const status = item.difference == 0 ? (window.messages.matches_status || 'Совпадает') :
            item.difference > 0 ? (window.messages.overage_status || 'Избыток') : (window.messages.shortage_status || 'Недостача');
        return `
            <tr>
                <td>
                    ${item.product.photo
                        ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">`
                        : `<div class="no-photo">${window.messages.no_photo || 'Нет фото'}</div>`
                    }
                </td>
                <td>${item.product.name}</td>
                <td class="small-col">${item.warehouse_qty} ${window.messages.units || 'pcs'}</td>
                <td class="small-col">${item.actual_qty} ${window.messages.units || 'pcs'}</td>
                <td class="${item.difference > 0 ? 'text-success' : 'text-danger'}">
                    ${item.difference > 0 ? '+' : ''}${item.difference} ${window.messages.units || 'pcs'}
                </td>
                <td>${status}</td>
            </tr>
        `;
    }).join('');
    
    detailsRow.innerHTML = `
        <td colspan="5">
            <div class="inventory-notes">${inventory.notes || ''}</div>
            <table class="table-striped analysis-table products-table">
                <thead>
                    <tr>
                        <th>${window.messages.photo || 'Фото'}</th>
                        <th class="large-col">${window.messages.product || 'Товар'}</th>
                        <th class="small-col">${window.messages.warehouse_short || 'Склад'}</th>
                        <th class="small-col">${window.messages.quantity || 'Количество'}</th>
                        <th>${window.messages.difference || 'Разница'}</th>
                        <th>${window.messages.status || 'Статус'}</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHTML}
                </tbody>
            </table>
            <div class="view-all-items">
                <button class="btn-view-all" onclick="viewAllInventoryItems(${inventory.id})">
                    Просмотреть весь список
                </button>
            </div>
        </td>
    `;

    // Заменяем обе строки в DOM
    const oldSummaryRow = document.getElementById(`inventory-row-${inventory.id}`);
    const oldDetailsRow = document.getElementById(`details-row-${inventory.id}`);
    if (oldSummaryRow && oldDetailsRow) {
        oldSummaryRow.parentNode.replaceChild(summaryRow, oldSummaryRow);
        oldDetailsRow.parentNode.replaceChild(detailsRow, oldDetailsRow);
    }
} 

// ===== ЧАСТЬ 7: Функции для мобильных карточек и пагинации =====

// Функция для переключения между таблицей и карточками
function toggleMobileView() {
    const tableWrapper = document.querySelector('.inventories-list.table-wrapper');
    const cardsContainer = document.getElementById('inventoriesCards');
    const tablePagination = document.getElementById('inventoriesPagination');
    const mobilePagination = document.getElementById('mobileInventoriesPagination');
    
    if (window.innerWidth <= 768) {
        // На мобильных показываем карточки
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (cardsContainer) cardsContainer.style.display = 'block';
        if (tablePagination) tablePagination.style.display = 'none';
        if (mobilePagination) mobilePagination.style.display = 'block';
        
        // Загружаем карточки если их нет
        if (cardsContainer && cardsContainer.children.length === 0) {
            loadMobileCards();
        }
    } else {
        // На десктопе показываем таблицу
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (cardsContainer) cardsContainer.style.display = 'none';
        if (tablePagination) tablePagination.style.display = 'block';
        if (mobilePagination) mobilePagination.style.display = 'none';
    }
}

// Функция для загрузки мобильных карточек
function loadMobileCards() {
    const cardsContainer = document.getElementById('inventoriesCards');
    if (!cardsContainer) return;
    
    // Получаем данные из таблицы
    const inventoryRows = document.querySelectorAll('.inventory-summary-row');
    cardsContainer.innerHTML = '';
    
    inventoryRows.forEach(row => {
        const inventoryId = row.id.replace('inventory-row-', '');
        const date = row.cells[0].textContent;
        const responsible = row.cells[1].textContent;
        const discrepancies = row.cells[2].innerHTML;
        const notes = row.cells[3].getAttribute('title') || row.cells[3].textContent;
        
        const card = createInventoryCard(inventoryId, date, responsible, discrepancies, notes);
        cardsContainer.appendChild(card);
    });
}

// Функция для создания карточки инвентаризации
function createInventoryCard(id, date, responsible, discrepancies, notes) {
    const card = document.createElement('div');
    card.className = 'inventory-card';
    card.id = `inventory-card-${id}`;
    
    card.innerHTML = `
        <div class="inventory-card-header">
            <div class="inventory-main-info">
                <div class="inventory-date">${date}</div>
            </div>
        </div>
        <div class="inventory-info">
            <div class="inventory-info-item">
                <div class="inventory-info-label">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    ${window.messages.responsible || 'Ответственный'}
                </div>
                <div class="inventory-info-value">${responsible}</div>
            </div>
            <div class="inventory-info-item">
                <div class="inventory-info-label">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    ${window.messages.discrepancies || 'Расхождения'}
                </div>
                <div class="inventory-info-value">${discrepancies}</div>
            </div>
            <div class="inventory-info-item">
                <div class="inventory-info-label">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    ${window.messages.notes || 'Примечания'}
                </div>
                <div class="inventory-info-value">${notes || '—'}</div>
            </div>
        </div>
        <div class="inventory-actions">
            <button class="btn-edit" onclick="editInventory(event, ${id})">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                ${window.messages.edit_short || 'Ред'}
            </button>
            <button class="btn-delete" onclick="confirmDeleteInventory(event, ${id})">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                ${window.messages.delete || 'Удалить'}
            </button>
            <button class="btn-pdf" onclick="downloadInventoryPdf(event, ${id})">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L13 3.586A2 2 0 0011.586 3H6zm2 2h3v3a1 1 0 001 1h3v9a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1zm5 3.414V8h-2V6h.586L13 5.414zM8 10a1 1 0 100 2h4a1 1 0 100-2H8zm0 4a1 1 0 100 2h4a1 1 0 100-2H8z"/>
                </svg>
                PDF
            </button>
        </div>
    `;
    
    // Добавляем обработчик клика для показа деталей
    card.addEventListener('click', function(e) {
        if (!e.target.closest('.inventory-actions')) {
            toggleInventoryDetailsRow(id);
        }
    });
    
    return card;
}

// Функция для обновления карточки в мобильном виде
function updateInventoryCardInDOM(inventory) {
    const card = document.getElementById(`inventory-card-${inventory.id}`);
    if (!card) return;
    
    const formattedDate = new Date(inventory.date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).replace(/\./g, '.');
    
    const discrepanciesCell = inventory.discrepancies_count > 0
        ? `<span
            ${inventory.overages_count > 0 && inventory.shortages_count == 0
                ? 'style="color: #b78e15;"'
                : inventory.shortages_count > 0 && inventory.overages_count == 0
                    ? 'class="text-danger"'
                    : ''}
        >${inventory.discrepancies_count}
            ${inventory.shortages_count > 0 ? `(${inventory.shortages_count} ${window.messages.shortage || 'Недостача'})` : ''}
            ${inventory.overages_count > 0 ? `(${inventory.overages_count} ${window.messages.overage || 'Избыток'})` : ''}
        </span>`
        : `<span class="text-success">${window.messages.matches || 'Совпадает'}</span>`;
    
    const newCard = createInventoryCard(
        inventory.id,
        formattedDate,
        inventory.user.name,
        discrepanciesCell,
        inventory.notes
    );
    
    card.parentNode.replaceChild(newCard, card);
}

// Функция для добавления карточки в мобильном виде
function addInventoryCardToDOM(inventory) {
    const cardsContainer = document.getElementById('inventoriesCards');
    if (!cardsContainer) return;
    
    const formattedDate = inventory.date
        ? new Date(inventory.date).toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }).replace(/\./g, '.')
        : 'Нет даты';
    
    const discrepanciesCell = inventory.discrepancies_count > 0
        ? `<span
            ${inventory.overages_count > 0 && inventory.shortages_count == 0
                ? 'style="color: #b78e15;"'
                : inventory.shortages_count > 0 && inventory.overages_count == 0
                    ? 'class="text-danger"'
                    : ''}
        >${inventory.discrepancies_count}
            ${inventory.shortages_count > 0 ? `(${inventory.shortages_count} ${window.messages.shortage || 'Недостача'})` : ''}
            ${inventory.overages_count > 0 ? `(${inventory.overages_count} ${window.messages.overage || 'Избыток'})` : ''}
        </span>`
        : `<span class="text-success">${window.messages.matches || 'Совпадает'}</span>`;
    
    const card = createInventoryCard(
        inventory.id,
        formattedDate,
        inventory.user.name,
        discrepanciesCell,
        inventory.notes
    );
    
    cardsContainer.insertBefore(card, cardsContainer.firstChild);
}

// Функция для удаления карточки в мобильном виде
function removeInventoryCardFromDOM(id) {
    const card = document.getElementById(`inventory-card-${id}`);
    if (card) {
        card.classList.add('row-deleting');
        setTimeout(() => {
            card.remove();
        }, 300);
    }
}

// Функция для поиска в мобильных карточках
function searchMobileInventories(searchTerm) {
    const cards = document.querySelectorAll('.inventory-card');
    const mobileSearchInput = document.getElementById('searchInputMobile');
    
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            
            cards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(term)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

// Инициализация мобильного вида при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем мобильный вид
    toggleMobileView();
    
    // Обработчик изменения размера окна
    window.addEventListener('resize', function() {
        toggleMobileView();
    });
    
    // Инициализируем поиск для мобильных карточек
    searchMobileInventories();
    
    // Инициализируем обработчики изображений
    setTimeout(() => {
        initInventoryImageHandlers();
    }, 500);
    
    // Обработчик для мобильного поиска
    const mobileSearchInput = document.getElementById('searchInputMobile');
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.inventory-card');
            
            cards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});

 