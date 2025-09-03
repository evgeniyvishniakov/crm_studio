// ===== СКРИПТЫ ДЛЯ РАЗДЕЛА "ПРОДАЖИ" =====
// Использует общие функции из common.js для:
// - escapeHtml() - экранирование HTML
// - formatPrice() - форматирование цен
// - toggleMobileView() - переключение мобильного вида
// - openImageModal() / closeImageModal() - работа с изображениями
// - clearErrors() / showErrors() - обработка ошибок

// ===== ЧАСТЬ 1: Глобальные переменные и инициализация =====
let currentDeleteId = null;
let itemCounter = 1;
let allClients = [];
let allProducts = [];
let currentDeleteSaleId = null;
let currentDeleteItemId = null;
let currentPage = 1;

// ===== ЧАСТЬ 2: Вспомогательные функции =====
function setTodayDateInSales() {
    
    
    try {
        // Попробуем разные селекторы
        let dateInput = document.querySelector('#saleForm input[name="date"]');

        
        if (!dateInput) {
            dateInput = document.querySelector('input[name="date"]');

        }
        
        if (!dateInput) {
            dateInput = document.querySelector('#saleForm input[type="date"]');

        }
        
        if (!dateInput) {
            const allDateInputs = document.querySelectorAll('input[type="date"]');
            if (allDateInputs.length > 0) {
                dateInput = allDateInputs[0];
            }
        }
        
        if (dateInput) {
            // Используем глобальную функцию setTodayDate
            if (typeof window.setTodayDate === 'function') {

                window.setTodayDate(dateInput);
            } else {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const todayString = `${year}-${month}-${day}`;
                dateInput.value = todayString;
                
                // Попробуем также вызвать событие change
                dateInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        } else {

            
            const saleForm = document.getElementById('saleForm');

        }
    } catch (error) {
        // Ошибка в setTodayDateInSales()
    }
    

}

// ===== ЧАСТЬ 3: Функции поиска и выбора клиентов =====
function searchClients(input) {
    const searchTerm = input.value.toLowerCase();
    const dropdown = input.nextElementSibling;
    const dropdownList = dropdown.querySelector('.client-dropdown-list');

    if (searchTerm.length === 0) {
        showFirstClients(dropdownList);
        dropdown.style.display = 'block';
        return;
    }

    const filteredClients = allClients.filter(client => {
        const name = client.name ? client.name.toLowerCase() : '';
        const instagram = client.instagram ? client.instagram.toLowerCase() : '';
        const phone = client.phone ? client.phone.toLowerCase() : '';
        return name.includes(searchTerm) || instagram.includes(searchTerm) || phone.includes(searchTerm);
    }).slice(0, 5);

    if (filteredClients.length === 0) {
        dropdownList.innerHTML = '<div class="client-dropdown-item">Клиенты не найдены</div>';
    } else {
        dropdownList.innerHTML = filteredClients.map(client => `
            <div class="client-dropdown-item"
                 onclick="selectClient(this, '${escapeHtml(client.name)}', ${client.id})">
                ${escapeHtml(client.name)}
                ${client.instagram ? ` (@${escapeHtml(client.instagram)})` : ''}
                ${client.phone ? ` - ${escapeHtml(client.phone)}` : ''}
            </div>
        `).join('');
    }

    dropdown.style.display = 'block';
}

function showFirstClients(dropdownList) {
    const firstClients = allClients.slice(0, 5);
    dropdownList.innerHTML = firstClients.map(client => `
        <div class="client-dropdown-item"
             onclick="selectClient(this, '${escapeHtml(client.name)}', ${client.id})">
            ${escapeHtml(client.name)}
            ${client.instagram ? ` (@${escapeHtml(client.instagram)})` : ''}
            ${client.phone ? ` - ${escapeHtml(client.phone)}` : ''}
        </div>
    `).join('');
}

function showClientDropdown(input) {
    const dropdown = input.nextElementSibling;
    const dropdownList = dropdown.querySelector('.client-dropdown-list');
    showFirstClients(dropdownList);
    dropdown.style.display = 'block';
}

function selectClient(element, clientName, clientId) {
    const container = element.closest('.client-search-container');
    const input = container.querySelector('.client-search-input');
    const select = container.querySelector('.client-select');
    const dropdown = container.querySelector('.client-dropdown');

    input.value = clientName;
    if (select) {
        select.value = clientId;
        select.dispatchEvent(new Event('change'));
        select.style.display = 'block';
        select.style.position = 'absolute';
        select.style.left = '-9999px';
    }
    dropdown.style.display = 'none';

    container.querySelectorAll('.client-dropdown-item').forEach(item => {
        item.classList.remove('selected');
    });

    element.classList.add('selected');
}

// ===== ЧАСТЬ 3: Функции поиска и выбора продуктов =====
function searchProducts(input) {
    const searchTerm = input.value.toLowerCase();
    const dropdown = input.nextElementSibling;
    const dropdownList = dropdown.querySelector('.product-dropdown-list');

    if (searchTerm.length === 0) {
        showFirstProducts(dropdownList);
        dropdown.style.display = 'block';
        return;
    }

    const filteredProducts = allProducts.filter(product => {
        return product.name.toLowerCase().includes(searchTerm);
    }).slice(0, 5);

    if (filteredProducts.length === 0) {
        dropdownList.innerHTML = '<div class="product-dropdown-item">Товары не найдены</div>';
    } else {
        dropdownList.innerHTML = filteredProducts.map(product => `
            <div class="product-dropdown-item"
                 onclick="selectProduct(this, '${escapeHtml(product.name)}', ${product.id})">
                ${escapeHtml(product.name)}
            </div>
        `).join('');
    }

    dropdown.style.display = 'block';
}

function showFirstProducts(dropdownList) {
    const firstProducts = allProducts.slice(0, 5);
    dropdownList.innerHTML = firstProducts.map(product => `
        <div class="product-dropdown-item"
             onclick="selectProduct(this, '${escapeHtml(product.name)}', ${product.id})">
            ${escapeHtml(product.name)}
        </div>
    `).join('');
}

function showProductDropdown(input) {
    const dropdown = input.nextElementSibling;
    const dropdownList = dropdown.querySelector('.product-dropdown-list');
    showFirstProducts(dropdownList);
    dropdown.style.display = 'block';
}

function selectProduct(element, productName, productId) {
    const container = element.closest('.product-search-container');
    const input = container.querySelector('.product-search-input');
    const select = container.querySelector('.product-select');
    const dropdown = container.querySelector('.product-dropdown');
    const hiddenInput = container.querySelector('input[name*="product_id"]');

    input.value = productName;
    if (select) {
        select.value = productId;
        select.dispatchEvent(new Event('change'));
        select.style.display = 'block';
        select.style.position = 'absolute';
        select.style.left = '-9999px';
    }
    
    // Обновляем скрытое поле product_id
    if (hiddenInput) {
        hiddenInput.value = productId;
    }
    
    dropdown.style.display = 'none';

    const product = allProducts.find(p => String(p.id) === String(productId));
    if (product) {
        const row = container.closest('.item-row');
        if (row) {
            const wholesalePriceInput = row.querySelector('input[name*="wholesale_price"]');
            const retailPriceInput = row.querySelector('input[name*="retail_price"]');
            const wholesaleValue = parseFloat(product.wholesale_price) || 0;
            const retailValue = parseFloat(product.retail_price) || 0;
            
            if (wholesalePriceInput) wholesalePriceInput.value = wholesaleValue;
            if (retailPriceInput) retailPriceInput.value = retailValue;
            
            // Также вызываем updateProductPrices для совместимости
            if (select) {
                updateProductPrices(select);
            }
        }
    }

    container.querySelectorAll('.product-dropdown-item').forEach(item => {
        item.classList.remove('selected');
    });

    element.classList.add('selected');
}

// Закрытие выпадающих списков при клике вне их
document.addEventListener('click', function(e) {
    if (!e.target.closest('.client-search-container')) {
        document.querySelectorAll('.client-dropdown').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
    if (!e.target.closest('.product-search-container')) {
        document.querySelectorAll('.product-dropdown').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
});

// Локальная функция для экранирования HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ===== ЧАСТЬ 4: Функции модальных окон =====
function openSaleModal() {

    
    const saleForm = document.getElementById('saleForm');
    const saleModal = document.getElementById('saleModal');
    

    
    if (saleForm) {
        saleForm.reset();

    }
    
    if (saleModal) {
        saleModal.style.display = 'block';

        setTodayDateInSales();
    }
    
    clearErrors('saleForm');
    
    // Также попробуем установить дату с задержкой на всякий случай
    setTimeout(() => {

        setTodayDateInSales();
    }, 100);
}

function closeSaleModal() {
    document.getElementById('saleModal').style.display = 'none';
    clearErrors('saleForm');
    resetSaleForm();
}

function openEditSaleModal(id) {

    
    fetch(`/sales/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const sale = data.sale;

                const modalBody = document.getElementById('editSaleModalBody');

                // Создаем форму редактирования
                const formHtml = `
                    <form id="editSaleForm">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id" value="${sale.id}">
                        <div class="form-row date-client-row">
                            <div class="form-group">
                                <label>${window.messages.client} *</label>
                                <select name="client_id" required class="form-control">
                                    <option value="">${window.messages.select_client}</option>
                                    ${window.allClients ? window.allClients.map(client => 
                                        `<option value="${client.id}" ${sale.client_id == client.id ? 'selected' : ''}>${client.name}</option>`
                                    ).join('') : ''}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>${window.messages.date} *</label>
                                <input type="date" name="date" required class="form-control" 
                                       value="${typeof window.formatDateForInput === 'function' ? window.formatDateForInput(sale.date) : sale.date}"
                                       data-locale="${document.querySelector('input[name="date"]')?.getAttribute('data-locale') || 'ru'}"
                                       data-month-names="${document.querySelector('input[name="date"]')?.getAttribute('data-month-names') || '[]'}"
                                       data-day-names="${document.querySelector('input[name="date"]')?.getAttribute('data-day-names') || '[]'}">
                            </div>
                            <div class="form-group">
                                <label>${window.messages.employee_master} *</label>
                                <select name="employee_id" required class="form-control">
                                    <option value="">${window.messages.select_employee}</option>
                                    ${window.allEmployees ? window.allEmployees.map(employee => 
                                        `<option value="${employee.id}" ${sale.employee_id == employee.id ? 'selected' : ''}>${employee.name}</option>`
                                    ).join('') : ''}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>${window.messages.notes}</label>
                            <textarea name="notes" rows="2" class="form-control">${sale.notes || ''}</textarea>
                        </div>
                        <div class="items-container" id="editItemsContainer">
                            <h3>${window.messages.products}</h3>
                            <!-- Шаблон для новых товаров -->
                            <div class="item-row template" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group product-field">
                                        <label>${window.messages.product} *</label>
                                        <div class="product-search-container">
                                            <input type="text" class="product-search-input form-control"
                                                   placeholder="${window.messages.start_typing_product_name}"
                                                   oninput="searchProducts(this)"
                                                   onfocus="showProductDropdown(this)" autocomplete="off">
                                            <div class="product-dropdown" style="display: none;">
                                                <div class="product-dropdown-list"></div>
                                            </div>
                                            <select class="form-control product-select" style="display: none;"
                                                    onchange="updateProductPrices(this)">
                                                <option value="">${window.messages.select_product}</option>
                                                ${window.allProducts ? window.allProducts.map(product => 
                                                    `<option value="${product.id}"
                                                            data-wholesale="${product.wholesale_price || 0}"
                                                            data-retail="${product.retail_price || 0}"
                                                            data-quantity="${product.available_quantity || 0}">
                                                        ${product.name}
                                                    </option>`
                                                ).join('') : ''}
                                            </select>
                                            <!-- Скрытое поле для сохранения product_id -->
                                            <input type="hidden" name="items[0][product_id]" value="">
                                        </div>
                                    </div>
                                    <div class="form-group price-field">
                                        <label>
                                            <span class="desktop-label">${window.messages.wholesale_price}</span>
                                            <span class="mobile-label">${window.messages.wholesale_price_mobile}</span>
                                            *
                                        </label>
                                        <input type="number" step="0.01" name="items[0][wholesale_price]" required
                                               class="form-control wholesale-price" min="0" value="0">
                                    </div>
                                    <div class="form-group price-field">
                                        <label>
                                            <span class="desktop-label">${window.messages.retail_price}</span>
                                            <span class="mobile-label">${window.messages.retail_price_mobile}</span>
                                            *
                                        </label>
                                        <input type="number" step="0.01" name="items[0][retail_price]" required
                                               class="form-control retail-price" min="0" value="0">
                                    </div>
                                    <div class="form-group quantity-field">
                                        <label>
                                            <span class="desktop-label">${window.messages.quantity}</span>
                                            <span class="mobile-label">${window.messages.quantity_mobile}</span>
                                            *
                                        </label>
                                        <input type="number" name="items[0][quantity]" required
                                               class="form-control quantity" min="1" value="1"
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
                            ${sale.items.map((item, index) => `
                                <div class="item-row">
                                    <div class="form-row">
                                        <div class="form-group product-field">
                                            <label>${window.messages.product} *</label>
                                            <div class="product-search-container">
                                                <input type="text"
                                                       id="product-search-edit-${index}"
                                                       class="product-search-input form-control"
                                                       placeholder="${window.messages.start_typing_product_name}"
                                                       oninput="searchProducts(this)"
                                                       onfocus="showProductDropdown(this)"
                                                       value="${item.product ? item.product.name : ''}"
                                                       autocomplete="off">
                                                <div class="product-dropdown" style="display: none;">
                                                    <div class="product-dropdown-list"></div>
                                                </div>
                                                <select class="form-control product-select" style="display: none;" onchange="updateProductPrices(this)">
                                                    <option value="">${window.messages.select_product}</option>
                                                    ${window.allProducts ? window.allProducts.map(product => 
                                                        `<option value="${product.id}"
                                                                data-wholesale="${product.wholesale_price || 0}"
                                                                data-retail="${product.retail_price || 0}"
                                                                data-quantity="${product.available_quantity || 0}"
                                                                ${product.id == item.product_id ? 'selected' : ''}>
                                                            ${product.name}
                                                        </option>`
                                                    ).join('') : ''}
                                                </select>
                                                <!-- Скрытое поле для сохранения product_id -->
                                                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                            </div>
                                        </div>
                                        <div class="form-group price-field">
                                            <label>
                                                <span class="desktop-label">${window.messages.wholesale_price}</span>
                                                <span class="mobile-label">${window.messages.wholesale_price_mobile}</span>
                                                *
                                            </label>
                                            <input type="number" step="0.01" name="items[${index}][wholesale_price]" required class="form-control" value="${formatPriceForInput(item.wholesale_price)}">
                                        </div>
                                        <div class="form-group price-field">
                                            <label>
                                                <span class="desktop-label">${window.messages.retail_price}</span>
                                                <span class="mobile-label">${window.messages.retail_price_mobile}</span>
                                                *
                                            </label>
                                            <input type="number" step="0.01" name="items[${index}][retail_price]" required class="form-control" value="${formatPriceForInput(item.retail_price)}">
                                        </div>
                                        <div class="form-group quantity-field">
                                            <label>
                                                <span class="desktop-label">${window.messages.quantity}</span>
                                                <span class="mobile-label">${window.messages.quantity_mobile}</span>
                                                *
                                            </label>
                                            <input type="number" name="items[${index}][quantity]" required class="form-control" value="${item.quantity}" min="1" oninput="validateQuantity(this)">
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
                            `).join('')}
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-add-item" onclick="addItemRow('editItemsContainer')">
                                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                </svg>
                                ${window.translations?.add_product || 'Добавить товар'}
                            </button>
                            <button type="button" class="btn-cancel" onclick="closeEditSaleModal()">${window.translations?.cancel || 'Отмена'}</button>
                            <button type="submit" class="btn-submit">${window.translations?.save_changes || 'Сохранить изменения'}</button>
                        </div>
                    </form>
                `;

                modalBody.innerHTML = formHtml;
                document.getElementById('editSaleModal').style.display = 'block';

                // Инициализируем календарь для поля даты с небольшой задержкой
                setTimeout(() => {
                    const dateInput = document.querySelector('#editSaleForm input[name="date"]');

                    
                    if (dateInput && typeof initializeDatePicker === 'function') {
                        initializeDatePicker(dateInput);

                    }
                    
                    // Убираем установку максимального количества для полей количества
                    // const quantityInputs = document.querySelectorAll('#editSaleForm input[name*="quantity"]');
                    // quantityInputs.forEach((input, index) => {
                    //     const productSelect = document.querySelector(`#editSaleForm select[name="items[${index}][product_id]"]`);
                    //     if (productSelect && productSelect.selectedOptions[0]) {
                    //         const maxQuantity = productSelect.selectedOptions[0].dataset.quantity || 1;
                    //         input.max = maxQuantity;
                    //     }
                    // });
                }, 100);

                // Удаляем старый обработчик, если он есть
                const oldForm = document.getElementById('editSaleForm');
                if (oldForm) {
                    const newForm = oldForm.cloneNode(true);
                    oldForm.parentNode.replaceChild(newForm, oldForm);
                }

                // Добавляем обработчик отправки формы
                document.getElementById('editSaleForm').addEventListener('submit', submitEditSaleForm);
            } else {
                window.showNotification('error', data.message || 'Ошибка загрузки данных продажи');
            }
        })
        .catch(error => {
            // Ошибка загрузки
            window.showNotification('error', 'Ошибка загрузки данных продажи');
        });
}

function closeEditSaleModal() {
    document.getElementById('editSaleModal').style.display = 'none';
    clearErrors('editSaleForm');
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
        closeConfirmationModal();
    }
}

// ===== ЧАСТЬ 5: Функции работы с товарами в продаже =====
function addItemRow(containerId = 'itemsContainer') {
    const container = document.getElementById(containerId);
    if (!container) {

        return;
    }

    // Ищем шаблон в текущем контейнере
    const template = container.querySelector('.template');
    if (!template) {

        return;
    }

    const newRow = template.cloneNode(true);
    newRow.style.display = 'block';
    newRow.classList.remove('template');

    // Новый индекс — это количество существующих строк товаров
    const newIndex = container.querySelectorAll('.item-row:not(.template)').length;

    // Обновляем индексы в именах полей
    const inputs = newRow.querySelectorAll('input, select');
    inputs.forEach(input => {
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
    const searchContainer = newRow.querySelector('.product-search-container');
    if (searchContainer) {
        const searchInput = searchContainer.querySelector('.product-search-input');
        searchInput.id = `product-search-${containerId}-${newIndex}`;
        searchInput.value = '';

        const select = searchContainer.querySelector('.product-select');
        select.selectedIndex = 0;
        
        // Обновляем имя скрытого поля product_id
        const hiddenInput = searchContainer.querySelector('input[name*="product_id"]');
        if (hiddenInput) {
            hiddenInput.name = `items[${newIndex}][product_id]`;
            hiddenInput.value = '';
        }
        
        // Добавляем обработчик события change для автоматического заполнения цен
        select.addEventListener('change', function() {
            updateProductPrices(this);
        });
    }

    // Для режима редактирования: обновить имена всех полей
    if (containerId === 'editItemsContainer') {
        newRow.querySelectorAll('input, select').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/items\[\d+\]/, `items[${newIndex}]`);
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

    const formActions = container.querySelector('.form-actions');
    if (formActions) {
        container.insertBefore(newRow, formActions);
    } else {
        container.appendChild(newRow); // Fallback
    }
}

function removeItemRow(button) {
    const row = button.closest('.item-row');
    if (row && !row.classList.contains('template')) {
        row.remove();
    }
}

function updateProductPrices(select) {
    const row = select.closest('.item-row');
    const selectedOption = select.options[select.selectedIndex];

    if (selectedOption.value) {
        const wholesalePrice = row.querySelector('input[name*="wholesale_price"]');
        const retailPrice = row.querySelector('input[name*="retail_price"]');
        const quantityInput = row.querySelector('input[name*="quantity"]');

        const wholesaleValue = parseFloat(selectedOption.dataset.wholesale) || 0;
        const retailValue = parseFloat(selectedOption.dataset.retail) || 0;

        if (wholesalePrice) wholesalePrice.value = wholesaleValue;
        if (retailPrice) retailPrice.value = retailValue;
        if (quantityInput) {
            quantityInput.value = '1';
            // Убираем ограничение на максимальное количество
            // quantityInput.max = selectedOption.dataset.quantity || 1;
        }
    }
}

function validateQuantity(input) {
    const value = parseInt(input.value);
    
    if (value < 1) {
        input.value = 1;
    }
    // Убираем проверку на максимальное количество
    // else if (value > max) {
    //     input.value = max;
    //     // Показываем уведомление
    //     if (window.showNotification) {
    //         const message = window.messages && window.messages.max_available_quantity 
    //             ? window.messages.max_available_quantity.replace(':quantity', max)
    //             : `Максимальное доступное количество: ${max}`;
    //         window.showNotification('warning', message);
    //     }
    // }
}

function resetSaleForm() {
    const container = document.getElementById('itemsContainer');
    const rows = container.querySelectorAll('.item-row:not(.template)');
    rows.forEach(row => row.remove());
    
    // Добавляем один пустой ряд
    addItemRow('itemsContainer');
    itemCounter = 1;
    
    // Устанавливаем сегодняшнюю дату в поле даты
    setTimeout(() => {
        setTodayDateInSales();
    }, 100);
}

// ===== ЧАСТЬ 6: Функции форм и валидации =====
function submitSaleForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = 'Добавление...';
    submitBtn.disabled = true;

    fetch('/sales', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeSaleModal();
            window.showNotification('success', 'Продажа успешно добавлена');
            loadSales(1);
        } else if (data.errors) {
            showErrors(data.errors, 'saleForm');
        } else {
            window.showNotification('error', data.message || 'Ошибка при добавлении продажи');
        }
    })
    .catch(error => {
        // Ошибка при добавлении
        window.showNotification('error', 'Ошибка при добавлении продажи');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function submitEditSaleForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    
    // Собираем данные о товарах
    const items = [];
    const itemRows = form.querySelectorAll('.item-row:not(.template)');
    itemRows.forEach((row, index) => {
        const productInput = row.querySelector(`[name*="[product_id]"]`);
        const wholesaleInput = row.querySelector(`[name*="[wholesale_price]"]`);
        const retailInput = row.querySelector(`[name*="[retail_price]"]`);
        const quantityInput = row.querySelector(`[name*="[quantity]"]`);
        
        items.push({
            product_id: productInput ? productInput.value : '',
            wholesale_price: wholesaleInput ? wholesaleInput.value : '',
            retail_price: retailInput ? retailInput.value : '',
            quantity: quantityInput ? quantityInput.value : ''
        });
    });
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = window.translations?.saving || 'Сохранение...';
    submitBtn.disabled = true;

    fetch(`/sales/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            _method: 'PUT',
            date: formData.get('date'),
            client_id: formData.get('client_id'),
            employee_id: formData.get('employee_id'),
            notes: formData.get('notes'),
            items: items
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditSaleModal();
            window.showNotification('success', window.translations?.sale_successfully_updated || 'Продажа успешно обновлена');
            loadSales(currentPage);
        } else if (data.errors) {
            showErrors(data.errors, 'editSaleForm');
        } else {
            window.showNotification('error', data.message || 'Ошибка при обновлении продажи');
        }
    })
    .catch(error => {
        // Ошибка при обновлении
        window.showNotification('error', 'Ошибка при обновлении продажи');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Используем общие функции для работы с ошибками из common.js
function clearErrors(formId = null) {
    const form = document.getElementById(formId || 'saleForm');
    if (form) {
        form.querySelectorAll('.error-message').forEach(error => error.remove());
        form.querySelectorAll('.form-control').forEach(input => {
            input.classList.remove('error');
        });
    }
}

function showErrors(errors, formId = 'saleForm') {
    const form = document.getElementById(formId);
    if (!form) return;
    
    clearErrors(formId);
    
    Object.keys(errors).forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('error');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = errors[field][0];
            input.parentNode.appendChild(errorDiv);
        }
    });
}

// ===== ЧАСТЬ 7: Функции удаления =====
function confirmDelete(id) {
    currentDeleteId = id;
    document.getElementById('confirmationModal').style.display = 'block';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    currentDeleteId = null;
}

function deleteSale() {
    if (!currentDeleteId) return;

    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = 'Удаление...';
    deleteBtn.disabled = true;

    fetch(`/sales/${currentDeleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeConfirmationModal();
            window.showNotification('success', 'Продажа успешно удалена');
            loadSales(currentPage);
        } else {
            window.showNotification('error', data.message || 'Ошибка при удалении продажи');
        }
    })
    .catch(error => {
        // Ошибка при удалении
        window.showNotification('error', 'Ошибка при удалении продажи');
    })
    .finally(() => {
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    });
}

// ===== ЧАСТЬ 8: Функции рендеринга данных =====
function renderSales(sales) {

    
    const tableBody = document.getElementById('salesTableBody');
    const cardsContainer = document.getElementById('salesCards');
    
    if (!tableBody || !cardsContainer) {
        // Не найдены элементы таблицы или карточки
        return;
    }

    // Рендерим таблицу
    let tableHtml = '';
    sales.forEach(sale => {
        // Для каждого товара в продаже создаем отдельную строку
        sale.items.forEach((saleItem, itemIndex) => {
            const product = saleItem.product;
            const photoHtml = product && product.photo ?
                `<img src="/storage/${product.photo}" class="product-photo" alt="${product ? product.name : 'Товар не найден'}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">` :
                `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`;
            
            tableHtml += `
                <tr data-id="${sale.id}" data-item-id="${saleItem.id}">
                    <td>${formatDate(sale.date)}</td>
                    <td>${escapeHtml(sale.client ? sale.client.name : 'Клиент не найден')}</td>
                    <td>${escapeHtml(product ? product.name : 'Товар не найден')}</td>
                    <td>
                        <div class="product-photo-container">
                            ${photoHtml}
                        </div>
                    </td>
                    <td>${escapeHtml(sale.employee ? sale.employee.name : (window.messages?.employee_not_specified || 'Employee not specified'))}</td>
                    <td class="retail-price currency-amount" data-amount="${saleItem.retail_price || 0}">${formatPrice(saleItem.retail_price || 0)}</td>
                    <td class="quantity">${saleItem.quantity || 0} ${window.messages?.pieces || 'pcs'}</td>
                    <td class="total-sum currency-amount" data-amount="${saleItem.total || 0}">${formatPrice(saleItem.total || 0)}</td>
                    <td class="actions-cell">
                        <button class="btn-edit" onclick="openEditSaleModal(${sale.id})">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                        </button>
                        <button class="btn-delete" onclick="confirmDelete(${sale.id})">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        });
    });
    tableBody.innerHTML = tableHtml;

    // Рендерим карточки для мобильных устройств
    let cardsHtml = '';
    sales.forEach(sale => {
        // Для каждого товара в продаже создаем отдельную карточку
        sale.items.forEach((saleItem, itemIndex) => {
            const product = saleItem.product;
            const photoHtml = product && product.photo ?
                `<img src="/storage/${product.photo}" class="product-photo" alt="${product ? product.name : 'Товар не найден'}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">` :
                `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`;
            
            cardsHtml += `
                <div class="sale-card" id="sale-card-${sale.id}-${saleItem.id}">
                    <div class="sale-card-header">
                        <div class="product-photo">
                            ${photoHtml}
                        </div>
                        <div class="card-title">${escapeHtml(product ? product.name : 'Товар не найден')}</div>
                    </div>
                    <div class="sale-card-body">
                        <div class="sale-info-item">
                            <div class="sale-info-label">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                                </svg>
                                Дата
                            </div>
                            <div class="sale-info-value">${formatDate(sale.date)}</div>
                        </div>
                        <div class="sale-info-item">
                            <div class="sale-info-label">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                Клиент
                            </div>
                            <div class="sale-info-value">${escapeHtml(sale.client ? sale.client.name : 'Клиент не найден')}</div>
                        </div>
                        <div class="sale-info-item">
                            <div class="sale-info-label">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                                Количество
                            </div>
                            <div class="sale-info-value">${saleItem.quantity || 0} ${window.messages?.pieces || 'pcs'}</div>
                        </div>
                        <div class="sale-info-item">
                            <div class="sale-info-label">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                Сотрудник
                            </div>
                            <div class="sale-info-value">${escapeHtml(sale.employee ? sale.employee.name : (window.messages?.employee_not_specified || 'Employee not specified'))}</div>
                        </div>
                        <div class="sale-info-item">
                            <div class="sale-info-label">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                                </svg>
                                Розничная цена
                            </div>
                            <div class="sale-info-value">${formatPrice(saleItem.retail_price || 0)}</div>
                        </div>
                        <div class="sale-info-item">
                            <div class="sale-info-label">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                                Сумма
                            </div>
                            <div class="sale-info-value">${formatPrice(saleItem.total || 0)}</div>
                        </div>
                    </div>
                    <div class="sale-card-actions">
                        <button class="btn-edit" onclick="openEditSaleModal(${sale.id})">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                            <span class="btn-text">${window.translations?.edit || 'Редактировать'}</span>
                        </button>
                        <button class="btn-delete" onclick="confirmDelete(${sale.id})">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                            </svg>
                            <span class="btn-text">${window.translations?.delete || 'Удалить'}</span>
                        </button>
                    </div>
                </div>
            `;
        });
    });
    cardsContainer.innerHTML = cardsHtml;
    
    // Инициализируем обработчики изображений после рендеринга
    setTimeout(() => {
        initSalesImageHandlers();
    }, 50);
}

// Используем общую функцию formatPrice из common.js и добавляем знак валюты
function formatPrice(price) {
    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;
    
    if (typeof numericPrice !== 'number' || isNaN(numericPrice)) return '0 ₴';
    
    // Форматируем цену локально, чтобы избежать рекурсии
    let formatted;
    if (window.formatPrice && window.formatPrice !== formatPrice) {
        formatted = window.formatPrice(numericPrice);
    } else {
        formatted = numericPrice.toFixed(2);
        if (formatted.endsWith('.00')) {
            formatted = formatted.slice(0, -3);
        }
    }
    
    return formatted + ' ₴';
}

// Функция для форматирования цены для полей ввода (без символа валюты)
function formatPriceForInput(price) {
    if (price === null || price === undefined || price === '') {
        return '';
    }
    const numPrice = parseFloat(price);
    if (isNaN(numPrice)) {
        return '';
    }
    // Если цена целая (без копеек), возвращаем целое число
    if (numPrice % 1 === 0) {
        return Math.floor(numPrice).toString();
    } else {
        // Если есть копейки, возвращаем с двумя знаками после запятой
        return numPrice.toFixed(2);
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU');
}

// ===== ЧАСТЬ 9: Функции пагинации =====
function renderPagination(meta) {
    let paginationHtml = '';
    if (meta.last_page > 1) {
        paginationHtml += '<div class="pagination">';
        paginationHtml += `<button class="page-btn" data-page="${meta.current_page - 1}" ${meta.current_page === 1 ? 'disabled' : ''}>&lt;</button>`;

        let pages = [];
        if (meta.last_page <= 7) {
            for (let i = 1; i <= meta.last_page; i++) pages.push(i);
        } else {
            pages.push(1);
            if (meta.current_page > 4) pages.push('...');
            let start = Math.max(2, meta.current_page - 2);
            let end = Math.min(meta.last_page - 1, meta.current_page + 2);
            for (let i = start; i <= end; i++) pages.push(i);
            if (meta.current_page < meta.last_page - 3) pages.push('...');
            pages.push(meta.last_page);
        }
        pages.forEach(p => {
            if (p === '...') {
                paginationHtml += `<span class="page-ellipsis">...</span>`;
            } else {
                paginationHtml += `<button class="page-btn${p === meta.current_page ? ' active' : ''}" data-page="${p}">${p}</button>`;
            }
        });
        paginationHtml += `<button class="page-btn" data-page="${meta.current_page + 1}" ${meta.current_page === meta.last_page ? 'disabled' : ''}>&gt;</button>`;
        paginationHtml += '</div>';
    }
    
    let pagContainer = document.getElementById('salesPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'salesPagination';
        document.querySelector('.sales-list').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    let mobilePagContainer = document.getElementById('mobileSalesPagination');
    if (!mobilePagContainer) {
        mobilePagContainer = document.createElement('div');
        mobilePagContainer.id = 'mobileSalesPagination';
        document.querySelector('.sales-cards').appendChild(mobilePagContainer);
    }
    mobilePagContainer.innerHTML = paginationHtml;

    document.querySelectorAll('#salesPagination .page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadSales(page);
            }
        });
    });

    document.querySelectorAll('#mobileSalesPagination .page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadSales(page);
            }
        });
    });
}

// ===== ЧАСТЬ 10: Функции загрузки данных =====
function loadSales(page = 1, search = '') {

    
    currentPage = page;
    const searchValue = search !== undefined ? search : document.getElementById('searchInput').value.trim();
    const url = `/sales?search=${encodeURIComponent(searchValue)}&page=${page}`;
    


    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {

        
        if (data.clients) {
            allClients = data.clients;
        }
        if (data.products) {
            allProducts = data.products;
        }
        

        renderSales(data.data);
        renderPagination(data.meta);
        
        // Инициализируем обработчики изображений после рендеринга
        setTimeout(() => {
            initSalesImageHandlers();
        }, 100);
    })
    .catch(error => {
        // Ошибка при загрузке данных
    });
}

// ===== ЧАСТЬ 11: Функции мобильного вида =====
function toggleMobileView() {
    const tableWrapper = document.querySelector('.sales-list');
    const salesCards = document.getElementById('salesCards');
    const salesPagination = document.getElementById('salesPagination');
    const mobileSalesPagination = document.getElementById('mobileSalesPagination');

    if (window.innerWidth <= 768) {
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (salesPagination) salesPagination.style.display = 'none';
        if (salesCards) salesCards.style.display = 'block';
        if (mobileSalesPagination) mobileSalesPagination.style.display = 'block';
    } else {
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (salesPagination) salesPagination.style.display = 'block';
        if (salesCards) salesCards.style.display = 'none';
        if (mobileSalesPagination) mobileSalesPagination.style.display = 'none';
    }
}

// ===== ЧАСТЬ 12: Функции модального окна изображений =====
function openImageModal(imgElement) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    if (modal && modalImg) {
        modalImg.src = imgElement.src;
        modal.style.display = "block";
    }
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.style.display = "none";
    }
}

// Функция для инициализации обработчиков изображений
function initSalesImageHandlers() {
    document.querySelectorAll('.product-photo').forEach(img => {
        img.style.cursor = 'pointer';
        img.style.transition = 'transform 0.2s ease';
        img.onclick = function() {
            openImageModal(this);
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

// ===== ЧАСТЬ 13: Инициализация и обработчики событий =====
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация глобальных переменных
    if (window.allClients) {
        allClients = window.allClients;
    }
    if (window.allProducts) {
        allProducts = window.allProducts;
    }
    
    // Инициализация первой загрузки
    loadSales(1);
    
    // Переключение мобильного вида
    toggleMobileView();
    
    // Устанавливаем сегодняшнюю дату в поле даты при загрузке страницы
    setTimeout(() => {

        setTodayDateInSales();
    }, 200);
    
    // Обработчики для изображений товаров
    const productImages = document.querySelectorAll('.product-photo');
    productImages.forEach(img => {
        img.style.cursor = 'pointer';
        img.style.transition = 'transform 0.2s ease';
        img.onclick = function() {
            openImageModal(this);
        };
        // Добавляем эффект при наведении
        img.onmouseenter = function() {
            this.style.transform = 'scale(1.05)';
        };
        img.onmouseleave = function() {
            this.style.transform = 'scale(1)';
        };
    });
    
    // Обработчик формы добавления продажи
    const saleForm = document.getElementById('saleForm');
    if (saleForm) {
        saleForm.addEventListener('submit', submitSaleForm);
    }
    
    // Обработчик формы редактирования продажи
    const editSaleForm = document.getElementById('editSaleForm');
    if (editSaleForm) {
        editSaleForm.addEventListener('submit', submitEditSaleForm);
    }
});

// Обработчик изменения размера окна
window.addEventListener('resize', function() {
    toggleMobileView();
});

// Обработчик поиска
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            loadSales(1, this.value.trim());
        });
    }
    
    // Обработчик мобильного поиска
    const searchInputMobile = document.getElementById('searchInputMobile');
    if (searchInputMobile) {
        searchInputMobile.addEventListener('input', function() {
            loadSales(1, this.value.trim());
        });
    }
    
    // Обработчик кнопки "Отмена" в модальном окне удаления
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            closeConfirmationModal();
        });
    }
    
    // Обработчик кнопки "Удалить" в модальном окне удаления
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            deleteSale();
        });
    }
}); 