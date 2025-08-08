// Функция форматирования валюты
function formatCurrency(value) {
    if (window.CurrencyManager) {
        return window.CurrencyManager.formatAmount(value);
    } else {
        value = parseFloat(value);
        if (isNaN(value)) return '0';
        const symbol = '₽'; // Заменяем Blade синтаксис на статичное значение
        return (value % 1 === 0 ? Math.floor(value) : value.toFixed(2)) + ' ' + symbol;
    }
}

// Основные функции управления модальными окнами
function openModal() {
    document.getElementById('addProductModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('addProductModal').style.display = 'none';
    clearErrors();
}

function closeEditModal() {
    document.getElementById('editProductModal').style.display = 'none';
}

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    if (event.target == document.getElementById('addProductModal')) {
        closeModal();
    }
    if (event.target == document.getElementById('editProductModal')) {
        closeEditModal();
    }
    if (event.target == document.getElementById('confirmationModal')) {
        document.getElementById('confirmationModal').style.display = 'none';
        currentDeleteRow = null;
        currentDeleteId = null;
    }
}

// Функция для очистки ошибок
function clearErrors(formId = null) {
    const form = formId ? document.getElementById(formId) : document.getElementById('addProductForm');
    if (form) {
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        form.querySelectorAll('.has-error').forEach(el => {
            el.classList.remove('has-error');
        });
    }
}

// Функция для отображения ошибок
function showErrors(errors, formId = 'addProductForm') {
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

// Глобальные переменные для удаления
let currentDeleteRow = null;
let currentDeleteId = null;

// Исправляю обработчик клика по кнопке удаления
// Теперь только открываем модальное окно, а удаление происходит после подтверждения

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-delete')) {
        const row = e.target.closest('tr');
        const productId = row.id.split('-')[1];
        currentDeleteRow = row;
        currentDeleteId = productId;
        document.getElementById('confirmationModal').style.display = 'block';
    }
});

// Кнопка подтверждения удаления
// (уже реализовано, но оставляю для ясности)
document.getElementById('confirmDelete').addEventListener('click', function() {
    if (currentDeleteId) {
        deleteProduct(currentDeleteId);
    }
    document.getElementById('confirmationModal').style.display = 'none';
    currentDeleteRow = null;
    currentDeleteId = null;
});

// Кнопка отмены удаления
document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('confirmationModal').style.display = 'none';
    currentDeleteRow = null;
    currentDeleteId = null;
});

// Кнопка подтверждения удаления всех товаров
document.getElementById('confirmDeleteAll').addEventListener('click', function() {
    forceDeleteAllProducts();
    document.getElementById('confirmationDeleteAllModal').style.display = 'none';
});

// Кнопка отмены удаления всех товаров
document.getElementById('cancelDeleteAll').addEventListener('click', function() {
    document.getElementById('confirmationDeleteAllModal').style.display = 'none';
});

// Кнопка подтверждения принудительного удаления товара
document.getElementById('confirmForceDelete').addEventListener('click', function() {
    const productId = document.getElementById('confirmationForceDeleteModal').dataset.productId;
    forceDeleteProduct(productId);
    document.getElementById('confirmationForceDeleteModal').style.display = 'none';
});

// Кнопка отмены принудительного удаления товара
document.getElementById('cancelForceDelete').addEventListener('click', function() {
    document.getElementById('confirmationForceDeleteModal').style.display = 'none';
});

// Функция для показа модального окна подтверждения удаления
function showDeleteConfirmation(productId) {
    currentDeleteRow = null;
    currentDeleteId = productId;
    document.getElementById('confirmationModal').style.display = 'block';
}

// Функция для удаления товара
function deleteProduct(rowOrId, id) {
    let row;
    let productId;
    let card;
    
    if (typeof rowOrId === 'object' && rowOrId !== null && 'classList' in rowOrId) {
        // Вызов с двумя аргументами: (row, id)
        row = rowOrId;
        productId = id;
    } else {
        // Вызов с одним аргументом: (id)
        productId = rowOrId;
        row = document.getElementById('product-' + productId);
        card = document.getElementById('product-card-' + productId);
    }
    
    if (row) row.classList.add('row-deleting');
    if (card) card.classList.add('row-deleting');
    
    fetch(`/products/${productId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Ошибка удаления');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            setTimeout(() => {
                if (row) row.remove();
                if (card) card.remove();
                window.showNotification('success', data.message || 'Товар успешно удален');
                checkDeletedProducts(); // Проверяем наличие удаленных товаров после удаления
            }, 300);
        }
    })
    .catch(error => {
        if (row) row.classList.remove('row-deleting');
        if (card) card.classList.remove('row-deleting');
        window.showNotification('error', 'Не удалось удалить товар');
    });
}

// Функция для показа удаленных товаров
function showTrashedProducts() {
    document.getElementById('trashedModal').style.display = 'block';
    loadTrashedProducts();
}

// Функция для загрузки удаленных товаров
function loadTrashedProducts() {
    const container = document.getElementById('trashedProductsList');
    container.innerHTML = '<div class="loading">Загрузка...</div>';

    // Используем прямой URL без префикса
    const url = '/products/trashed';

    // Получаем CSRF токен
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json'
        },
        credentials: 'include', // Важно! Передает cookies и сессию
        mode: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                if (response.status === 302) {
                    throw new Error('Перенаправление на страницу входа - не аутентифицирован');
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const footer = document.getElementById('trashedModalFooter');
                if (data.products.length === 0) {
                    container.innerHTML = '<p class="no-data">Нет удаленных товаров</p>';
                    footer.style.display = 'none';
                } else {
                    container.innerHTML = data.products.map(product => `
                        <div class="trashed-product-item">
                            <div class="product-info">
                                <strong>${product.name}</strong>
                                <small>${product.category ? product.category.name : '—'} / ${product.brand ? product.brand.name : '—'}</small>
                                <small>Удален: ${new Date(product.deleted_at).toLocaleString()}</small>
                            </div>
                            <div class="product-actions">
                                <button onclick="restoreProduct(${product.id})" class="btn-restore">
                                    Восстановить
                                </button>
                                <button onclick="showForceDeleteConfirmation(${product.id})" class="btn-force-delete">
                                    Удалить навсегда
                                </button>
                            </div>
                        </div>
                    `).join('');
                    footer.style.display = 'block';
                }
            } else {
                container.innerHTML = '<p class="error">Ошибка загрузки удаленных товаров: ' + (data.message || '') + '</p>';
                document.getElementById('trashedModalFooter').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке:', error);
            container.innerHTML = '<p class="error">Ошибка загрузки удаленных товаров: ' + error.message + '</p>';
        });
}

// Функция для восстановления товара
function restoreProduct(productId) {
    fetch(`/products/${productId}/restore`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        mode: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', 'Товар успешно восстановлен');
            loadTrashedProducts(); // Перезагружаем список удаленных товаров
            loadPage(currentPage, searchQuery); // Обновляем основную таблицу товаров
            checkDeletedProducts(); // Проверяем наличие удаленных товаров после восстановления
        } else {
            window.showNotification('error', data.message || 'Ошибка восстановления товара');
        }
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка восстановления товара');
    });
}

// Функция для принудительного удаления товара
function forceDeleteProduct(productId) {
    fetch(`/products/${productId}/force`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        mode: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', 'Товар удален навсегда');
            loadTrashedProducts(); // Перезагружаем список удаленных товаров
            checkDeletedProducts(); // Проверяем наличие удаленных товаров после принудительного удаления
        } else {
            window.showNotification('error', data.message || 'Ошибка принудительного удаления');
        }
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка принудительного удаления');
    });
}

// Функция для показа модального окна подтверждения удаления всех товаров
function showDeleteAllConfirmation() {
    document.getElementById('confirmationDeleteAllModal').style.display = 'block';
}

// Функция для показа модального окна подтверждения принудительного удаления товара
function showForceDeleteConfirmation(productId) {
    document.getElementById('confirmationForceDeleteModal').dataset.productId = productId;
    document.getElementById('confirmationForceDeleteModal').style.display = 'block';
}

// Функция для удаления всех товаров навсегда
function forceDeleteAllProducts() {
    fetch('/products/force-delete-all', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        mode: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', 'Все товары удалены навсегда');
            loadTrashedProducts(); // Перезагружаем список удаленных товаров
            checkDeletedProducts(); // Проверяем наличие удаленных товаров после удаления всех
        } else {
            window.showNotification('error', data.message || 'Ошибка удаления всех товаров');
        }
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка удаления всех товаров');
    });
}

// Функция для закрытия модального окна удаленных товаров
function closeTrashedModal() {
    document.getElementById('trashedModal').style.display = 'none';
}

// Обработчик клика по кнопке редактирования
document.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.btn-edit');
    if (editBtn) {
        const row = editBtn.closest('tr');
        if (row) {
            const productId = row.id.split('-')[1];
            if (productId) {
                editProduct(productId);
            }
        }
    }
});

// Функция для редактирования товара
function editProduct(id) {
    fetch(`/products/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                document.getElementById('editProductId').value = product.id;
                document.getElementById('editProductName').value = product.name;
                document.getElementById('editProductCategory').value = product.category_id;
                document.getElementById('editProductBrand').value = product.brand_id;

                // Показываем текущее фото
                const photoContainer = document.getElementById('currentPhotoContainer');
                if (product.photo) {
                    photoContainer.innerHTML = `
                        <img src="/storage/${product.photo}" alt="Текущее фото" style="max-width: 200px; margin-top: 10px;">
                    `;
                } else {
                    photoContainer.innerHTML = `<p>${window.translations?.no_photo || 'Нет фото'}</p>`;
                }

                // Подставляем цены с правильным форматированием
                document.getElementById('editProductPurchasePrice').value = formatPriceForInput(product.purchase_price);
                document.getElementById('editProductRetailPrice').value = formatPriceForInput(product.retail_price);

                document.getElementById('editProductModal').style.display = 'block';
            } else {
                window.showNotification('error', data.message || 'Ошибка загрузки данных товара');
            }
        })
        .catch(error => {
            window.showNotification('error', 'Ошибка загрузки данных товара');
        });
}

// Обработчик отправки формы редактирования
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const productId = formData.get('id');

    fetch(`/products/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-HTTP-Method-Override': 'PUT',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', 'Товар успешно обновлен');
            closeEditModal();
            // Обновляем строку в таблице
            const row = document.getElementById(`product-${productId}`);
            if (row) {
                // Обновляем фото
                const photoCell = row.querySelector('td:first-child');
                if (data.product.photo) {
                    photoCell.innerHTML = `<a href="/storage/${data.product.photo}" class="zoomable-image" data-img="/storage/${data.product.photo}"><img src="/storage/${data.product.photo}" alt="${data.product.name}" class="product-photo"></a>`;
                } else {
                    photoCell.innerHTML = `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`;
                }
                // Обновляем название
                row.querySelector('td:nth-child(2)').textContent = data.product.name;
                // Обновляем категорию
                row.querySelector('td:nth-child(3)').textContent = data.product.category?.name ?? '—';
                // Обновляем бренд
                row.querySelector('td:nth-child(4)').textContent = data.product.brand?.name ?? '—';
                // Обновляем цены
                row.querySelector('td:nth-child(5)').textContent = formatPrice(data.product.purchase_price);
                row.querySelector('td:nth-child(6)').textContent = formatPrice(data.product.retail_price);
            }
            
            // Обновляем карточку товара в мобильной версии
            const card = document.getElementById(`product-card-${productId}`);
            if (card) {
                // Обновляем фото
                const photoContainer = card.querySelector('.product-photo-container');
                if (photoContainer) {
                    if (data.product.photo) {
                        photoContainer.innerHTML = `<img src="/storage/${data.product.photo}" alt="${data.product.name}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">`;
                    } else {
                        photoContainer.innerHTML = `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`;
                    }
                }
                
                // Обновляем название
                const nameElement = card.querySelector('.product-name');
                if (nameElement) {
                    nameElement.textContent = data.product.name;
                }
                
                // Обновляем категорию
                const categoryBadge = card.querySelector('.product-category-badge');
                if (categoryBadge) {
                    if (data.product.category?.name) {
                        categoryBadge.textContent = data.product.category.name;
                        categoryBadge.style.display = 'inline-block';
                    } else {
                        categoryBadge.style.display = 'none';
                    }
                }
                
                // Обновляем бренд
                const brandBadge = card.querySelector('.product-brand-badge');
                if (brandBadge) {
                    if (data.product.brand?.name) {
                        brandBadge.textContent = data.product.brand.name;
                        brandBadge.style.display = 'inline-block';
                    } else {
                        brandBadge.style.display = 'none';
                    }
                }
                
                // Обновляем цены
                const priceElements = card.querySelectorAll('.product-info-value');
                if (priceElements.length >= 2) {
                    priceElements[0].textContent = formatPrice(data.product.purchase_price);
                    priceElements[1].textContent = formatPrice(data.product.retail_price);
                }
            }
        } else {
            window.showNotification('error', data.message || 'Ошибка обновления товара');
        }
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка обновления товара');
    });
});

// Поиск товаров
const searchInput = document.querySelector('.search-box input');
searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#productsTableBody tr');

    rows.forEach(row => {
        const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const brand = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

        if (name.includes(searchTerm) || category.includes(searchTerm) || brand.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Форматирование цены как в Blade
function formatPrice(price) {
    if (window.CurrencyManager) {
        return window.CurrencyManager.formatAmount(price);
    } else {
        const symbol = '₽'; // Заменяем Blade синтаксис на статичное значение
        if (price % 1 === 0) {
            return Math.floor(price) + ' ' + symbol;
        } else {
            return Number(price).toFixed(2) + ' ' + symbol;
        }
    }
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

// Функции для работы с модальным окном импорта
function openImportModal() {
    document.getElementById('importModal').style.display = 'block';
}

function closeImportModal() {
    document.getElementById('importModal').style.display = 'none';
    // Очищаем форму
    document.getElementById('importForm').reset();
}

// Обработчик отправки формы импорта
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.textContent = 'Импорт...';
    submitBtn.disabled = true;
    
    fetch('/products/import', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message);
            closeImportModal();
            // Перезагружаем страницу для обновления списка товаров
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            window.showNotification('error', 'Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка импорта файла');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    const importModal = document.getElementById('importModal');
    if (event.target === importModal) {
        closeImportModal();
    }
}

function openExportModal() {
    document.getElementById('exportModal').style.display = 'block';
}

function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
}

function exportProducts() {
    const category = document.getElementById('exportCategory').value;
    const brand = document.getElementById('exportBrand').value;
    const photo = document.getElementById('exportPhoto').value;
    let url = '/products/export?format=xlsx';
    if (category) url += '&category_id=' + encodeURIComponent(category);
    if (brand) url += '&brand_id=' + encodeURIComponent(brand);
    if (photo) url += '&photo=' + encodeURIComponent(photo);
    window.location.href = url;
    closeExportModal();
}

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    const exportModal = document.getElementById('exportModal');
    if (event.target === exportModal) {
        closeExportModal();
    }
}

// AJAX-пагинация
let currentPage = 1;
let searchQuery = '';
let isFirstLoad = true;

function loadPage(page, search = '') {

    currentPage = page;
    searchQuery = search;
    
    // Показываем индикатор загрузки при смене страниц (только если это не первая загрузка)
    if (!isFirstLoad) {
        const tbody = document.getElementById('productsTableBody');
        if (tbody) {
            tbody.innerHTML = `
                <tr id="loading-row">
                    <td colspan="7" class="loading-indicator">
                        <div style="text-align: center; padding: 40px;">
                            <div style="width: 32px; height: 32px; border: 3px solid #f3f4f6; border-top: 3px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div>
                            <p style="color: #6c7280; margin: 0;">Загрузка товаров...</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
    
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (search) params.append('search', search);
    
    fetch(`/products?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Ошибка загрузки данных');
        }
        return response.json();
    })
    .then(data => {

        updateTable(data.data);
        renderPagination(data.meta);
        
        // После первой загрузки устанавливаем флаг в false
        if (isFirstLoad) {
            isFirstLoad = false;
        }
    })
    .catch(error => {
        console.error('Error loading data:', error);
        window.showNotification('error', 'Ошибка загрузки данных');
    });
}

function updateTable(products) {
    const tbody = document.getElementById('productsTableBody');
    const productsCards = document.getElementById('productsCards');
    

    
    tbody.innerHTML = '';
    productsCards.innerHTML = '';
    
    // Убираем индикатор загрузки
    const loadingRow = document.getElementById('loading-row');
    if (loadingRow) {
        loadingRow.remove();
    }

    products.forEach(product => {
        // Создаем строку для десктопной таблицы
        const row = document.createElement('tr');
        row.id = `product-${product.id}`;
        
        // Правильное формирование URL для изображения (с проверкой существования файла)
        let photoHtml;
        if (product.photo) {
            const photoUrl = `/storage/${product.photo}`;
            // Добавляем обработчик ошибки загрузки изображения
            photoHtml = `<a href="${photoUrl}" class="zoomable-image" data-img="${photoUrl}"><img src="${photoUrl}" alt="${product.name}" class="product-photo" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'"></a>`;
        } else {
            photoHtml = `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`;
        }
        
        // Создаем ячейки отдельно
        const photoCell = document.createElement('td');
        photoCell.innerHTML = photoHtml;
        
        const nameCell = document.createElement('td');
        nameCell.textContent = product.name;
        
        const categoryCell = document.createElement('td');
        categoryCell.textContent = product.category?.name ?? '—';
        
        const brandCell = document.createElement('td');
        brandCell.textContent = product.brand?.name ?? '—';
        
        const purchasePriceCell = document.createElement('td');
        purchasePriceCell.className = 'currency-amount';
        purchasePriceCell.setAttribute('data-amount', product.purchase_price);
        purchasePriceCell.textContent = formatPrice(product.purchase_price);
        
        const retailPriceCell = document.createElement('td');
        retailPriceCell.className = 'currency-amount';
        retailPriceCell.setAttribute('data-amount', product.retail_price);
        retailPriceCell.textContent = formatPrice(product.retail_price);
        
        const actionsCell = document.createElement('td');
        actionsCell.className = 'actions-cell';
        actionsCell.innerHTML = `
            <button class="btn-edit" title="Редактировать">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
            </button>
            <button class="btn-delete" title="Удалить">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        `;
        
        // Добавляем ячейки в строку
        row.appendChild(photoCell);
        row.appendChild(nameCell);
        row.appendChild(categoryCell);
        row.appendChild(brandCell);
        row.appendChild(purchasePriceCell);
        row.appendChild(retailPriceCell);
        row.appendChild(actionsCell);
        tbody.appendChild(row);

        // Создаем карточку для мобильной версии
        const card = document.createElement('div');
        card.className = 'product-card';
        card.id = `product-card-${product.id}`;
        
        // Формируем фото для карточки
        let cardPhotoHtml;
        if (product.photo) {
            const photoUrl = `/storage/${product.photo}`;
            cardPhotoHtml = `<img src="${photoUrl}" alt="${product.name}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>${window.translations?.no_photo || 'Нет фото'}</div>'">`;
        } else {
            cardPhotoHtml = `<div class="no-photo">${window.translations?.no_photo || 'Нет фото'}</div>`;
        }

        card.innerHTML = `
            <div class="product-card-header">
                <div class="product-photo-container">
                    ${cardPhotoHtml}
                </div>
                <div class="product-main-info">
                    <h3 class="product-name">${product.name}</h3>
                </div>
            </div>
            <div class="product-info">
                <div class="product-info-item">
                    <span class="product-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" clip-rule="evenodd" />
                        </svg>
                        Категория
                    </span>
                    <span class="product-info-value">${product.category?.name ?? '—'}</span>
                </div>
                <div class="product-info-item">
                    <span class="product-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        Бренд
                    </span>
                    <span class="product-info-value">${product.brand?.name ?? '—'}</span>
                </div>
                <div class="product-info-item">
                    <span class="product-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        Закупочная цена
                    </span>
                    <span class="product-info-value">${formatPrice(product.purchase_price)}</span>
                </div>
                <div class="product-info-item">
                    <span class="product-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        Розничная цена
                    </span>
                    <span class="product-info-value">${formatPrice(product.retail_price)}</span>
                </div>
            </div>
            <div class="product-actions">
                <button class="btn-edit" title="Редактировать" onclick="editProduct(${product.id})">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Редактировать
                </button>
                <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${product.id})">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Удалить
                </button>
            </div>
        `;
        
        productsCards.appendChild(card);
    });
    
    // После добавления строк навешиваем обработчики
    initZoomableImages();
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
    let pagContainer = document.getElementById('productsPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'productsPagination';
        document.querySelector('.table-wrapper').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    // Обновляем мобильную пагинацию
    let mobilePagContainer = document.getElementById('mobileProductsPagination');
    if (mobilePagContainer) {
        mobilePagContainer.innerHTML = paginationHtml;
    }

    // Навешиваем обработчики
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadPage(page, searchQuery);
            }
        });
    });
}

function handleSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchInputMobile = document.getElementById('searchInputMobile');
    const query = searchInput ? searchInput.value.trim() : (searchInputMobile ? searchInputMobile.value.trim() : '');
    
    // Синхронизируем поиск между десктопной и мобильной версиями
    if (searchInput && searchInputMobile) {
        searchInputMobile.value = searchInput.value;
    }
    
    // Сбрасываем на первую страницу при поиске
    loadPage(1, query);
}

// Обновляем функцию добавления товара для обновления таблицы
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновляем таблицу после добавления
            loadPage(currentPage, searchQuery);
            
            window.showNotification('success', 'Товар успешно добавлен');
            closeModal();
            this.reset();
        } else {
            window.showNotification('error', data.message || 'Ошибка добавления товара');
        }
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка добавления товара');
    });
});

// Функция для проверки наличия удаленных товаров
function checkDeletedProducts() {
    fetch('/products/trashed', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        mode: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        const deletedProductsBtn = document.getElementById('deletedProductsBtn');
        const deletedProductsBtnMobile = document.getElementById('deletedProductsBtnMobile');
        
        if (data.success && data.products && data.products.length > 0) {
            if (deletedProductsBtn) deletedProductsBtn.style.display = 'inline-flex';
            if (deletedProductsBtnMobile) deletedProductsBtnMobile.style.display = 'inline-flex';
        } else {
            if (deletedProductsBtn) deletedProductsBtn.style.display = 'none';
            if (deletedProductsBtnMobile) deletedProductsBtnMobile.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Ошибка при проверке удаленных товаров:', error);
        const deletedProductsBtn = document.getElementById('deletedProductsBtn');
        const deletedProductsBtnMobile = document.getElementById('deletedProductsBtnMobile');
        if (deletedProductsBtn) deletedProductsBtn.style.display = 'none';
        if (deletedProductsBtnMobile) deletedProductsBtnMobile.style.display = 'none';
    });
}

// Функция для переключения между десктопной и мобильной версией
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const productsCards = document.getElementById('productsCards');
    const productsPagination = document.getElementById('productsPagination');
    const mobileProductsPagination = document.getElementById('mobileProductsPagination');
    

    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) {
            tableWrapper.style.display = 'none';
    
        }
        if (productsCards) {
            productsCards.style.display = 'block';

        }
        if (productsPagination) productsPagination.style.display = 'none';
        if (mobileProductsPagination) mobileProductsPagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) {
            tableWrapper.style.display = 'block';
    
        }
        if (productsCards) {
            productsCards.style.display = 'none';

        }
        if (productsPagination) productsPagination.style.display = 'block';
        if (mobileProductsPagination) mobileProductsPagination.style.display = 'none';
    }
}

// Инициализация первой загрузки
let isInitialized = false;

document.addEventListener('DOMContentLoaded', function() {
    if (!isInitialized) {
        isInitialized = true;
        loadPage(1);
        initZoomableImages();
        checkDeletedProducts(); // Проверяем наличие удаленных товаров
        toggleMobileView(); // Переключаем на правильную версию
    }
});

// Обработчик изменения размера окна
window.addEventListener('resize', function() {
    toggleMobileView();
});

function initZoomableImages() {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const closeBtn = document.getElementById('closeImageModal');
    document.querySelectorAll('.zoomable-image').forEach(function(link) {
        link.onclick = function(e) {
            e.preventDefault();
            modal.style.display = 'flex';
            modalImg.src = this.getAttribute('data-img');
        };
    });
    closeBtn.onclick = function() {
        modal.style.display = 'none';
        modalImg.src = '';
    };
    // Модальные окна теперь закрываются только по кнопкам
    // Убираем автоматическое закрытие при клике вне модального окна
} 