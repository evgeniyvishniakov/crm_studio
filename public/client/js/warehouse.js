// ===== СКРИПТЫ ДЛЯ РАЗДЕЛА "СКЛАД" =====
// Использует общие функции из common.js для:
// - escapeHtml() - экранирование HTML
// - formatPrice() - форматирование цен
// - toggleMobileView() - переключение мобильного вида
// - openImageModal() / closeImageModal() - работа с изображениями
// - clearErrors() / showErrors() - обработка ошибок

// ===== ЧАСТЬ 1: Глобальные переменные и инициализация =====
let allProducts = [];
let currentPage = 1;
let currentDeleteId = null;

// ===== ЧАСТЬ 2: Функции поиска и выбора продуктов =====
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

    input.value = productName;
    if (select) {
        select.value = productId;
        select.dispatchEvent(new Event('change'));
        // Убеждаемся, что select не скрыт для отправки формы
        select.style.display = 'block';
        select.style.position = 'absolute';
        select.style.left = '-9999px';
    }
    dropdown.style.display = 'none';

    const product = allProducts.find(p => String(p.id) === String(productId));
    if (product) {
        const form = container.closest('form');
        if (form && form.id === 'addForm') {
            const purchaseInput = form.querySelector('input[name="purchase_price"]');
            const retailInput = form.querySelector('input[name="retail_price"]');
            if (purchaseInput) purchaseInput.value = product.purchase_price ?? 0;
            if (retailInput) retailInput.value = product.retail_price ?? 0;
        }
    }

    container.querySelectorAll('.product-dropdown-item').forEach(item => {
        item.classList.remove('selected');
    });

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

// Локальная функция для экранирования HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ===== ЧАСТЬ 3: Функции модальных окон =====
function openModal() {
    document.getElementById('addModal').style.display = 'block';
    clearErrors();
}

function closeModal() {
    document.getElementById('addModal').style.display = 'none';
    clearErrors();
    document.getElementById('addForm').reset();
}

function openEditModal(id) {
    fetch(`/warehouses/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('editModal');
            const modalBody = modal.querySelector('.modal-body');
            
            modalBody.innerHTML = `
                <form id="editForm" onsubmit="submitEditForm(event)">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editItemId" name="id" value="${data.warehouse.id || ''}">
                    <div class="form-group">
                        <label>${window.translations?.product || 'Товар'}</label>
                        <input type="text" class="form-control" value="${escapeHtml(data.warehouse.product_name || '')}" readonly>
                    </div>
                    <div class="form-group">
                        <label>${window.translations?.purchase_price || 'Цена закупки'} *</label>
                        <input type="number" step="0.01" id="editPurchasePrice" name="purchase_price"
                               value="${formatPriceForInput(data.warehouse.purchase_price)}" required>
                    </div>
                    <div class="form-group">
                        <label>${window.translations?.retail_price || 'Розничная цена'} *</label>
                        <input type="number" step="0.01" id="editRetailPrice" name="retail_price"
                               value="${formatPriceForInput(data.warehouse.retail_price)}" required>
                    </div>
                    <div class="form-group">
                        <label>${window.translations?.quantity || 'Количество'} *</label>
                        <input type="number" id="editQuantity" name="quantity"
                               value="${data.warehouse.quantity || ''}" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">${window.translations?.cancel || 'Отмена'}</button>
                        <button type="submit" class="btn-submit">${window.translations?.save || 'Сохранить'}</button>
                    </div>
                </form>
            `;
            
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Ошибка при загрузке данных:', error);
            window.showNotification('error', 'Ошибка при загрузке данных');
        });
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    clearErrors('editForm');
}

// ===== ЧАСТЬ 4: Функции удаления =====
function confirmDelete(id) {
    currentDeleteId = id;
    document.getElementById('confirmationModal').style.display = 'block';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    currentDeleteId = null;
}

function deleteItem() {
    if (!currentDeleteId) return;

    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = 'Удаление...';
    deleteBtn.disabled = true;

    fetch(`/warehouses/${currentDeleteId}`, {
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
            window.showNotification('success', 'Товар успешно удален со склада');
            loadWarehouseItems(currentPage);
        } else {
            window.showNotification('error', data.message || 'Ошибка при удалении товара');
        }
    })
    .catch(error => {
        console.error('Ошибка при удалении:', error);
        window.showNotification('error', 'Ошибка при удалении товара');
    })
    .finally(() => {
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    });
}

// ===== ЧАСТЬ 5: Функции форм и валидации =====
function submitAddForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Проверяем, что все поля заполнены
    if (!formData.get('product_id') || !formData.get('purchase_price') || !formData.get('retail_price') || !formData.get('quantity')) {
        window.showNotification('error', 'Пожалуйста, заполните все обязательные поля');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    submitBtn.innerHTML = 'Добавление...';
    submitBtn.disabled = true;

    fetch('/warehouses', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            window.showNotification('success', 'Товар успешно добавлен на склад');
            loadWarehouseItems(1);
        } else if (data.errors) {
            showErrors(data.errors);
        } else {
            window.showNotification('error', data.message || 'Ошибка при добавлении товара');
        }
    })
    .catch(error => {
        console.error('Ошибка при добавлении:', error);
        window.showNotification('error', 'Ошибка при добавлении товара');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function submitEditForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = 'Сохранение...';
    submitBtn.disabled = true;

    fetch(`/warehouses/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const purchasePriceElement = row.querySelector('.purchase-price');
                const retailPriceElement = row.querySelector('.retail-price');
                
                purchasePriceElement.className = 'purchase-price currency-amount';
                retailPriceElement.className = 'retail-price currency-amount';
                
                purchasePriceElement.setAttribute('data-amount', data.warehouse.purchase_price);
                retailPriceElement.setAttribute('data-amount', data.warehouse.retail_price);
                
                purchasePriceElement.textContent = formatPrice(data.warehouse.purchase_price);
                retailPriceElement.textContent = formatPrice(data.warehouse.retail_price);
                row.querySelector('.quantity').textContent = data.warehouse.quantity + ' шт.';
            }

            const card = document.getElementById(`warehouse-card-${id}`);
            if (card) {
                const quantityCardElement = card.querySelector('.warehouse-info-item:nth-child(1) .warehouse-info-value');
                if (quantityCardElement) {
                    quantityCardElement.textContent = data.warehouse.quantity + ' шт.';
                }

                const purchasePriceCardElement = card.querySelector('.warehouse-info-item:nth-child(2) .warehouse-info-value');
                if (purchasePriceCardElement) {
                    purchasePriceCardElement.textContent = formatPrice(data.warehouse.purchase_price);
                }

                const retailPriceCardElement = card.querySelector('.warehouse-info-item:nth-child(3) .warehouse-info-value');
                if (retailPriceCardElement) {
                    retailPriceCardElement.textContent = formatPrice(data.warehouse.retail_price);
                }
            }

            closeEditModal();
            window.showNotification('success', 'Изменения успешно сохранены');
        } else if (data.errors) {
            showErrors(data.errors, 'editForm');
        } else {
            window.showNotification('error', data.message || 'Ошибка при сохранении изменений');
        }
    })
    .catch(error => {
        console.error('Ошибка при сохранении:', error);
        window.showNotification('error', 'Ошибка при сохранении изменений');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Закрытие модальных окон при клике вне их
window.onclick = function(event) {
    if (event.target == document.getElementById('addModal')) {
        closeModal();
    }
    if (event.target == document.getElementById('editModal')) {
        closeEditModal();
    }
    if (event.target == document.getElementById('confirmationModal')) {
        closeConfirmationModal();
    }
}

// Используем общие функции для работы с ошибками из common.js
function clearErrors(formId = null) {
    const form = document.getElementById(formId || 'addForm');
    if (form) {
        // Очищаем все сообщения об ошибках
        form.querySelectorAll('.error-message').forEach(error => error.remove());
        form.querySelectorAll('.form-control').forEach(input => {
            input.classList.remove('error');
        });
    }
}

function showErrors(errors, formId = 'addForm') {
    const form = document.getElementById(formId);
    if (!form) return;
    
    // Очищаем предыдущие ошибки
    clearErrors(formId);
    
    // Показываем новые ошибки
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

// ===== ЧАСТЬ 6: Функции рендеринга данных =====
function renderWarehouseItems(items) {
    const tableBody = document.getElementById('warehouseTableBody');
    const cardsContainer = document.getElementById('warehouseCards');
    
    if (!tableBody || !cardsContainer) return;

    // Рендерим таблицу
    tableBody.innerHTML = items.map(item => {
        const photoHtml = item.product && item.product.photo ?
            `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product ? item.product.name : 'Товар не найден'}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">` :
            `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`;
        
        return `
            <tr data-id="${item.id}">
                <td class="photo-cell">
                    <div class="product-photo-container">
                        ${photoHtml}
                    </div>
                </td>
                <td class="product-name">${escapeHtml(item.product ? item.product.name : 'Товар не найден')}</td>
                <td class="purchase-price currency-amount" data-amount="${item.purchase_price}">${formatPrice(item.purchase_price)}</td>
                <td class="retail-price currency-amount" data-amount="${item.retail_price}">${formatPrice(item.retail_price)}</td>
                <td class="quantity">${item.quantity} шт.</td>
                                 <td class="actions-cell" style="vertical-align: middle;">
                     <button class="btn-edit" onclick="openEditModal(${item.id})">
                         <svg viewBox="0 0 24 24" fill="currentColor">
                             <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                         </svg>
                     </button>
                     <button class="btn-delete" onclick="confirmDelete(${item.id})">
                         <svg viewBox="0 0 24 24" fill="currentColor">
                             <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                         </svg>
                     </button>
                 </td>
            </tr>
        `;
    }).join('');

    // Рендерим карточки для мобильных устройств
    cardsContainer.innerHTML = items.map(item => {
        const photoHtml = item.product && item.product.photo ?
            `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product ? item.product.name : 'Товар не найден'}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">` :
            `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`;
        
        return `
            <div class="warehouse-card" id="warehouse-card-${item.id}">
                <div class="warehouse-card-header">
                    <div class="product-photo">
                        ${photoHtml}
                    </div>
                    <div class="card-title">${escapeHtml(item.product ? item.product.name : 'Товар не найден')}</div>
                </div>
                <div class="warehouse-card-body">
                    <div class="warehouse-info-item">
                        <div class="warehouse-info-label">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                            Количество
                        </div>
                        <div class="warehouse-info-value">${item.quantity} шт.</div>
                    </div>
                    <div class="warehouse-info-item">
                        <div class="warehouse-info-label">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                            </svg>
                            Цена закупки
                        </div>
                        <div class="warehouse-info-value">${formatPrice(item.purchase_price)}</div>
                    </div>
                    <div class="warehouse-info-item">
                        <div class="warehouse-info-label">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                            </svg>
                            Розничная цена
                        </div>
                        <div class="warehouse-info-value">${formatPrice(item.retail_price)}</div>
                    </div>
                </div>
                <div class="warehouse-card-actions">
                    <button class="btn-edit" onclick="openEditModal(${item.id})">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        <span class="btn-text">Редактировать</span>
                    </button>
                    <button class="btn-delete" onclick="confirmDelete(${item.id})">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                        <span class="btn-text">Удалить</span>
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    // Инициализируем обработчики изображений после рендеринга
    setTimeout(() => {
        initWarehouseImageHandlers();
    }, 50);
}

// Используем общую функцию formatPrice из common.js и добавляем знак валюты
function formatPrice(price) {
    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;
    
    if (typeof numericPrice !== 'number' || isNaN(numericPrice)) return '0 ₴';
    
    // Форматируем цену локально, чтобы избежать рекурсии
    let formatted;
    if (window.formatPrice && window.formatPrice !== formatPrice) {
        // Используем общую функцию только если она существует и не является этой же функцией
        formatted = window.formatPrice(numericPrice);
    } else {
        // Локальное форматирование
        formatted = numericPrice.toFixed(2);
        // Убираем .00 если число целое
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

// ===== ЧАСТЬ 7: Функции пагинации =====
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
    
    let pagContainer = document.getElementById('warehousePagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'warehousePagination';
        document.querySelector('.table-wrapper').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    let mobilePagContainer = document.getElementById('mobileWarehousePagination');
    if (!mobilePagContainer) {
        mobilePagContainer = document.createElement('div');
        mobilePagContainer.id = 'mobileWarehousePagination';
        document.querySelector('.warehouse-cards').appendChild(mobilePagContainer);
    }
    mobilePagContainer.innerHTML = paginationHtml;

    document.querySelectorAll('#warehousePagination .page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadWarehouseItems(page);
            }
        });
    });

    document.querySelectorAll('#mobileWarehousePagination .page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadWarehouseItems(page);
            }
        });
    });
}

// ===== ЧАСТЬ 8: Функции загрузки данных =====
function loadWarehouseItems(page = 1, search = '') {
    currentPage = page;
    const searchValue = search !== undefined ? search : document.getElementById('searchInput').value.trim();
    fetch(`/warehouses?search=${encodeURIComponent(searchValue)}&page=${page}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.products) {
            allProducts = data.products;
        }
        
        renderWarehouseItems(data.data);
        renderPagination(data.meta);
        
        // Инициализируем обработчики изображений после рендеринга
        setTimeout(() => {
            initWarehouseImageHandlers();
        }, 100);
    })
    .catch(error => {
        console.error('Ошибка при загрузке данных:', error);
    });
}

// ===== ЧАСТЬ 9: Функции мобильного вида =====
// Используем общую функцию toggleMobileView из common.js
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const warehouseCards = document.getElementById('warehouseCards');
    const warehousePagination = document.getElementById('warehousePagination');
    const mobileWarehousePagination = document.getElementById('mobileWarehousePagination');

    if (window.innerWidth <= 768) {
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (warehousePagination) warehousePagination.style.display = 'none';
        if (warehouseCards) warehouseCards.style.display = 'block';
        if (mobileWarehousePagination) mobileWarehousePagination.style.display = 'block';
    } else {
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (warehousePagination) warehousePagination.style.display = 'block';
        if (warehouseCards) warehouseCards.style.display = 'none';
        if (mobileWarehousePagination) mobileWarehousePagination.style.display = 'none';
    }
}

// ===== ЧАСТЬ 10: Функции модального окна изображений =====
// Используем общие функции для работы с изображениями из common.js
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
function initWarehouseImageHandlers() {
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

// ===== ЧАСТЬ 11: Инициализация и обработчики событий =====
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация глобальных переменных
    if (window.allProducts) {
        allProducts = window.allProducts;
    }
    
    // Инициализация первой загрузки
    loadWarehouseItems(1);
    
    // Переключение мобильного вида
    toggleMobileView();
    
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
    
    // Обработчик формы добавления товара
    const addForm = document.getElementById('addForm');
    if (addForm) {
        addForm.addEventListener('submit', submitAddForm);
    }
    
    // Обработчик формы редактирования товара
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', submitEditForm);
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
            loadWarehouseItems(1, this.value.trim());
        });
    }
    
    // Обработчик мобильного поиска
    const searchInputMobile = document.getElementById('searchInputMobile');
    if (searchInputMobile) {
        searchInputMobile.addEventListener('input', function() {
            loadWarehouseItems(1, this.value.trim());
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
            deleteItem();
        });
    }
}); 