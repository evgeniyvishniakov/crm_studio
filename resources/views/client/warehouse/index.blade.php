@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Модальное окно для увеличенного изображения -->
    <div id="imageModal" class="modal image-modal" onclick="closeImageModal()">
        <img id="modalImage" class="modal-image-content" onclick="event.stopPropagation()">
    </div>

    <div class="warehouse-container">
        <div class="warehouse-header">
        <h1>{{ __('messages.warehouse') }}</h1>
        <div id="notification" class="notification">
            <!-- Уведомления будут появляться здесь -->
        </div>
        <div class="header-actions">
            <button class="btn-add-product" onclick="openModal()">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                {{ __('messages.add_to_warehouse') }}
            </button>
            <div class="search-box">
                <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" placeholder="{{ __('messages.search') }}..." id="searchInput">
            </div>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table-striped warehouse-table">
            <thead>
            <tr>
                <th>{{ __('messages.photo') }}</th>
                <th>{{ __('messages.product') }}</th>
                <th>{{ __('messages.purchase_price') }}</th>
                <th>{{ __('messages.retail_price') }}</th>
                <th>{{ __('messages.stock') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
            </thead>
            <tbody id="warehouseTableBody">
            <!-- Данные будут загружаться через AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Карточки для мобильной версии -->
    <div class="warehouse-cards" id="warehouseCards" style="display: none;">
        <!-- Карточки будут загружаться через AJAX -->
    </div>

    <!-- Пагинация -->
    <div id="warehousePagination"></div>
    
    <!-- Пагинация для мобильных карточек -->
    <div id="mobileWarehousePagination" style="display: none;"></div>

    <!-- Модальное окно добавления -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_product_to_warehouse') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('messages.product') }} *</label>
                        <div class="product-search-container">
                            <input type="text" class="product-search-input form-control"
                                   placeholder="{{ __('messages.start_typing_product_name') }}..."
                                   oninput="searchProducts(this)"
                                   onfocus="showProductDropdown(this)"
                                   autocomplete="off">
                            <div class="product-dropdown" style="display: none;">
                                <div class="product-dropdown-list"></div>
                            </div>
                            <select id="productSelect" name="product_id" class="form-control product-select" style="display: none;" required>
                                <option value="">{{ __('messages.select_product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-purchase="{{ $product->purchase_price }}" data-retail="{{ $product->retail_price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.purchase_price') }} *</label>
                        <input type="number" step="0.01" name="purchase_price" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.retail_price') }} *</label>
                        <input type="number" step="0.01" name="retail_price" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.quantity') }} *</label>
                        <input type="number" name="quantity" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_product_on_warehouse') }}</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editItemId" name="id">
                    <div class="form-group">
                        <label>{{ __('messages.product') }}</label>
                        <input type="text" id="editProductName" readonly>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.purchase_price') }} *</label>
                        <input type="number" step="0.01" id="editPurchasePrice" name="purchase_price" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.retail_price') }} *</label>
                        <input type="number" step="0.01" id="editRetailPrice" name="retail_price" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.quantity') }} *</label>
                        <input type="number" id="editQuantity" name="quantity" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>{{ __('messages.delete_confirmation') }}</h3>
            <p>{{ __('messages.confirm_delete_product_from_warehouse') }}</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelDelete">{{ __('messages.cancel') }}</button>
                <button class="confirm-btn" id="confirmDeleteBtn">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <script>
        let allProducts = @json($products);

        function searchProducts(input) {
            const searchTerm = input.value.toLowerCase();
            const dropdown = input.nextElementSibling;
            const dropdownList = dropdown.querySelector('.product-dropdown-list');

            if (searchTerm.length === 0) {
                // Показываем первые 5 товаров при пустом поиске
                showFirstProducts(dropdownList);
                dropdown.style.display = 'block';
                return;
            }

            const filteredProducts = allProducts.filter(product => {
                return product.name.toLowerCase().includes(searchTerm);
            }).slice(0, 5); // Ограничиваем результаты первыми 5 товарами

            if (filteredProducts.length === 0) {
                dropdownList.innerHTML = '<div class="product-dropdown-item">{{ __('messages.products_not_found') }}</div>';
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

            // Подставляем цены из allProducts при добавлении на склад
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

        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        // Modal control functions
        function openModal() {
            document.getElementById('addForm').reset();
            // Устанавливаем количество по умолчанию = 1
            document.querySelector('#addForm input[name="quantity"]').value = 1;
            document.getElementById('addModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
            clearErrors();
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('addModal')) {
                closeModal();
            }
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
            }
        }

        // Edit warehouse item
        function openEditModal(id) {
            const modal = document.getElementById('editModal');
            const modalBody = modal.querySelector('.modal-body');

            // Show loading state
            modal.style.display = 'block';
            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`/warehouses/${id}/edit`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        modalBody.innerHTML = `
                <form id="editForm">
                    @csrf
                        @method('PUT')
                        <input type="hidden" id="editItemId" name="id" value="${data.warehouse.id}">
                    <div class="form-group">
                        <label>{{ __('messages.product') }}</label>
                        <input type="text" class="form-control" value="${data.warehouse.product_name}" readonly>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.purchase_price') }} *</label>
                        <input type="number" step="0.01" id="editPurchasePrice" name="purchase_price"
                               value="${data.warehouse.purchase_price}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.retail_price') }} *</label>
                        <input type="number" step="0.01" id="editRetailPrice" name="retail_price"
                               value="${data.warehouse.retail_price}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.quantity') }} *</label>
                        <input type="number" id="editQuantity" name="quantity"
                               value="${data.warehouse.quantity}" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                    </div>
                </form>
            `;

                        // Initialize form submission handler
                        document.getElementById('editForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            submitEditForm(this);
                        });
                    } else {
                        throw new Error(data.error || 'Unknown error');
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = `
            <div class="alert alert-danger">
                {{ __('messages.error_loading_data') }}: ${error.message || '{{ __('messages.an_error_occurred') }}'}
            </div>
            <button class="btn-cancel" onclick="closeEditModal()">{{ __('messages.close') }}</button>
        `;
                });
        }

        // Submit edit form
        function submitEditForm(form) {
            const formData = new FormData(form);
            const id = formData.get('id');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> {{ __('messages.saving') }}...';
            submitBtn.disabled = true;

            fetch(`/warehouses/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
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
                        // Update the table row
                        const row = document.getElementById(`warehouse-${id}`);
                        if (row) {
                            const purchasePriceElement = row.querySelector('.purchase-price');
                            const retailPriceElement = row.querySelector('.retail-price');
                            
                            purchasePriceElement.className = 'purchase-price currency-amount';
                            retailPriceElement.className = 'retail-price currency-amount';
                            
                            purchasePriceElement.setAttribute('data-amount', data.warehouse.purchase_price);
                            retailPriceElement.setAttribute('data-amount', data.warehouse.retail_price);
                            
                            purchasePriceElement.textContent = formatPrice(data.warehouse.purchase_price);
                            retailPriceElement.textContent = formatPrice(data.warehouse.retail_price);
                            row.querySelector('.quantity').textContent = data.warehouse.quantity + ' {{ __('messages.units') }}';
                        }

                        // Update the mobile card
                        const card = document.getElementById(`warehouse-card-${id}`);
                        if (card) {
                            // Update quantity (first warehouse-info-item)
                            const quantityCardElement = card.querySelector('.warehouse-info-item:nth-child(1) .warehouse-info-value');
                            if (quantityCardElement) {
                                quantityCardElement.textContent = data.warehouse.quantity + ' {{ __('messages.units') }}';
                            }

                            // Update purchase price (second warehouse-info-item)
                            const purchasePriceCardElement = card.querySelector('.warehouse-info-item:nth-child(2) .warehouse-info-value');
                            if (purchasePriceCardElement) {
                                purchasePriceCardElement.textContent = formatPrice(data.warehouse.purchase_price);
                            }

                            // Update retail price (third warehouse-info-item)
                            const retailPriceCardElement = card.querySelector('.warehouse-info-item:nth-child(3) .warehouse-info-value');
                            if (retailPriceCardElement) {
                                retailPriceCardElement.textContent = formatPrice(data.warehouse.retail_price);
                            }
                        }

                        closeEditModal();
                        window.showNotification('success', '{{ __('messages.changes_successfully_saved') }}');
                    }
                })
                .catch(error => {
                    window.showNotification('error', error.message || '{{ __('messages.error_saving_changes') }}');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }


        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            if (event.target == document.getElementById('addModal')) {
                closeModal();
            }
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
            }
        }

        // Функция для очистки ошибок
        function clearErrors(formId = null) {
            const form = formId ? document.getElementById(formId) : document.getElementById('addForm');
            if (form) {
                form.querySelectorAll('.error-message').forEach(el => el.remove());
                form.querySelectorAll('.has-error').forEach(el => {
                    el.classList.remove('has-error');
                });
            }
        }

        // Функция для отображения ошибок
        function showErrors(errors, formId = 'addForm') {
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
        let currentDeleteId = null;

        function confirmDelete(id) {
            currentDeleteId = id;
            document.getElementById('confirmationModal').style.display = 'block';
        }

        // Обработчики для модального окна подтверждения
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteId = null;
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentDeleteId) {
                deleteItem(currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Функция для удаления товара
        function deleteItem(id) {
            const row = document.getElementById(`warehouse-${id}`);
            const card = document.getElementById(`warehouse-card-${id}`);
            
            if (row) row.classList.add('row-deleting');
            if (card) card.classList.add('row-deleting');

            fetch(`/warehouses/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('{{ __('messages.error_deleting') }}');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.showNotification('success', '{{ __('messages.product_successfully_deleted_from_warehouse') }}');
                        // Перезагружаем текущую страницу
                        loadWarehouseItems(currentPage);
                    }
                })
                .catch(error => {
                    window.showNotification('error', '{{ __('messages.failed_to_delete_product') }}');
                });
        }

        // Очистка поиска при закрытии модальных окон
        function clearSearch() {
            document.getElementById('searchInput').value = '';
            loadWarehouseItems(1, '');
        }

        // Обработчик формы добавления
        document.getElementById('addForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> {{ __('messages.adding') }}...';
            submitBtn.disabled = true;

            fetch("/warehouses", {
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
                    if (data.success && data.warehouse) {
                        closeModal();
                        this.reset();
                        window.showNotification('success', '{{ __('messages.product_successfully_added_to_warehouse') }}');
                        // Перезагружаем текущую страницу для отображения нового товара
                        loadWarehouseItems(currentPage);
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors);
                        window.showNotification('error', '{{ __('messages.please_fix_form_errors') }}');
                    } else {
                        window.showNotification('error', error.message || '{{ __('messages.error_adding_product') }}');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Обработчик формы редактирования
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const id = formData.get('id');
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> {{ __('messages.saving') }}...';
            submitBtn.disabled = true;

            fetch(`/warehouses/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'PUT',
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
                    if (data.success && data.warehouse) {
                        const row = document.getElementById(`warehouse-${data.warehouse.id}`);
                        if (row) {
                            row.querySelector('.purchase-price').textContent = parseFloat(data.warehouse.purchase_price).toFixed(2);
                            row.querySelector('.retail-price').textContent = parseFloat(data.warehouse.retail_price).toFixed(2);
                            row.querySelector('.quantity').textContent = data.warehouse.quantity;
                        }
                        closeEditModal();
                        window.showNotification('success', '{{ __('messages.changes_successfully_saved') }}');
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors, 'editForm');
                        window.showNotification('error', '{{ __('messages.please_fix_form_errors') }}');
                    } else {
                        window.showNotification('error', error.message || '{{ __('messages.error_saving_changes') }}');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Функция форматирования цены (без .00 для целых)
        function formatPrice(value) {
            if (window.CurrencyManager) {
                return window.CurrencyManager.formatAmount(value);
            } else {
                value = parseFloat(value);
                if (isNaN(value)) return '0';
                return (value % 1 === 0 ? value.toFixed(0) : value.toFixed(2)) + ' грн';
            }
        }

        // --- AJAX пагинация ---
        let currentPage = 1;

        function renderWarehouseItems(items) {
            const tbody = document.getElementById('warehouseTableBody');
            const cardsContainer = document.getElementById('warehouseCards');
            tbody.innerHTML = '';
            cardsContainer.innerHTML = '';
            
            // Если нет товаров, не делаем ничего
            if (!items || items.length === 0) {
                return;
            }
            
            items.forEach(item => {
                const photoHtml = item.product && item.product.photo ?
                    `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product ? item.product.name : 'Товар не найден'}" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>Нет фото</div>'">` :
                    '<div class="no-photo">{{ __('messages.no_photo') }}</div>';
                
                // Рендерим строку таблицы
                const row = document.createElement('tr');
                row.id = `warehouse-${item.id}`;
                row.innerHTML = `
                    <td>${photoHtml}</td>
                    <td>${item.product ? item.product.name : 'Товар не найден'}</td>
                    <td class="purchase-price currency-amount" data-amount="${item.purchase_price}">${formatPrice(item.purchase_price)}</td>
                    <td class="retail-price currency-amount" data-amount="${item.retail_price}">${formatPrice(item.retail_price)}</td>
                    <td class="quantity">${item.quantity} {{ __('messages.units') }}</td>
                    <td class="actions-cell">
                        <button class="btn-edit" onclick="openEditModal(${item.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            {{ __('messages.edit') }}
                        </button>
                        <button class="btn-delete" onclick="confirmDelete(${item.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.delete') }}
                        </button>
                    </td>
                `;
                tbody.appendChild(row);

                // Рендерим карточку для мобильной версии
                const card = document.createElement('div');
                card.className = 'warehouse-card';
                card.id = `warehouse-card-${item.id}`;
                card.innerHTML = `
                    <div class="warehouse-card-header">
                        <div class="warehouse-photo-container">
                            ${photoHtml}
                        </div>
                        <div class="warehouse-main-info">
                            <div class="warehouse-product-name">${item.product ? item.product.name : 'Товар не найден'}</div>
                        </div>
                    </div>
                    <div class="warehouse-info">
                        <div class="warehouse-info-item">
                            <div class="warehouse-info-label">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                {{ __('messages.quantity') }}
                            </div>
                            <div class="warehouse-info-value">${item.quantity} {{ __('messages.units') }}</div>
                        </div>
                        <div class="warehouse-info-item">
                            <div class="warehouse-info-label">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.purchase_price') }}
                            </div>
                            <div class="warehouse-info-value">${formatPrice(item.purchase_price)}</div>
                        </div>
                        <div class="warehouse-info-item">
                            <div class="warehouse-info-label">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.retail_price') }}
                            </div>
                            <div class="warehouse-info-value">${formatPrice(item.retail_price)}</div>
                        </div>
                    </div>
                    <div class="warehouse-actions">
                        <button class="btn-edit" onclick="openEditModal(${item.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            {{ __('messages.edit') }}
                        </button>
                        <button class="btn-delete" onclick="confirmDelete(${item.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.delete') }}
                        </button>
                    </div>
                `;
                cardsContainer.appendChild(card);
            });

            // Добавляем обработчики для изображений
            const productImages = document.querySelectorAll('.product-photo');
            productImages.forEach(img => {
                img.onclick = function() {
                    openImageModal(this);
                };
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
            
            // Рендерим пагинацию для десктопа
            let pagContainer = document.getElementById('warehousePagination');
            if (!pagContainer) {
                pagContainer = document.createElement('div');
                pagContainer.id = 'warehousePagination';
                document.querySelector('.table-wrapper').appendChild(pagContainer);
            }
            pagContainer.innerHTML = paginationHtml;

            // Рендерим пагинацию для мобильных карточек
            let mobilePagContainer = document.getElementById('mobileWarehousePagination');
            if (!mobilePagContainer) {
                mobilePagContainer = document.createElement('div');
                mobilePagContainer.id = 'mobileWarehousePagination';
                document.querySelector('.warehouse-cards').appendChild(mobilePagContainer);
            }
            mobilePagContainer.innerHTML = paginationHtml;

            // Навешиваем обработчики для десктопа
            document.querySelectorAll('#warehousePagination .page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (!isNaN(page) && !this.disabled) {
                        loadWarehouseItems(page);
                    }
                });
            });

            // Навешиваем обработчики для мобильных карточек
            document.querySelectorAll('#mobileWarehousePagination .page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (!isNaN(page) && !this.disabled) {
                        loadWarehouseItems(page);
                    }
                });
            });
        }

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
                // Обновляем allProducts для поиска в модальных окнах
                if (data.products) {
                    allProducts = data.products;
                }
                
                renderWarehouseItems(data.data);
                renderPagination(data.meta);
            })
            .catch(error => {
                console.error('Ошибка при загрузке данных:', error);
            });
        }

        // Поиск с пагинацией
        document.getElementById('searchInput').addEventListener('input', function() {
            loadWarehouseItems(1, this.value.trim());
        });

        // Инициализация первой загрузки
        loadWarehouseItems(1);

        // Функция для переключения между таблицей и карточками на мобильных устройствах
        function toggleMobileView() {
            const tableWrapper = document.querySelector('.table-wrapper');
            const warehouseCards = document.getElementById('warehouseCards');
            const warehousePagination = document.getElementById('warehousePagination');
            const mobileWarehousePagination = document.getElementById('mobileWarehousePagination');

            if (window.innerWidth <= 768) {
                // На мобильных устройствах показываем карточки
                if (tableWrapper) tableWrapper.style.display = 'none';
                if (warehousePagination) warehousePagination.style.display = 'none';
                if (warehouseCards) warehouseCards.style.display = 'block';
                if (mobileWarehousePagination) mobileWarehousePagination.style.display = 'block';
            } else {
                // На десктопе показываем таблицу
                if (tableWrapper) tableWrapper.style.display = 'block';
                if (warehousePagination) warehousePagination.style.display = 'block';
                if (warehouseCards) warehouseCards.style.display = 'none';
                if (mobileWarehousePagination) mobileWarehousePagination.style.display = 'none';
            }
        }

        // Вызываем функцию при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            toggleMobileView();
        });

        // Вызываем функцию при изменении размера окна
        window.addEventListener('resize', function() {
            toggleMobileView();
        });
    </script>

        </div>
    </div>

<script>
function openImageModal(imgElement) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modalImg.src = imgElement.src;
    modal.style.display = "block";
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = "none";
}

// Добавляем обработчики для всех изображений товаров
document.addEventListener('DOMContentLoaded', function() {
    const productImages = document.querySelectorAll('.product-photo');
    productImages.forEach(img => {
        img.onclick = function() {
            openImageModal(this);
        };
    });
});
</script>
@endsection