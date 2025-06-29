@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="warehouse-container">
        <!-- Модальное окно для увеличенного изображения -->
        <div id="imageModal" class="modal image-modal" onclick="closeImageModal()">
            <img id="modalImage" class="modal-image-content" onclick="event.stopPropagation()">
        </div>

        <div class="warehouse-header">
            <h1>Склад</h1>
            <div id="notification" class="notification">
                <!-- Уведомления будут появляться здесь -->
            </div>
            <div class="header-actions">
                <button class="btn-add-product" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить на склад
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" placeholder="Поиск..." id="searchInput">
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table-striped warehouse-table">
                <thead>
                <tr>
                    <th>Фото</th>
                    <th>Товар</th>
                    <th>Закупочная цена</th>
                    <th>Розничная цена</th>
                    <th>Остатки</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody id="warehouseTableBody">
                @foreach($warehouseItems as $item)
                    <tr id="warehouse-{{ $item->id }}">
                        <td>
                            @if($item->product->photo)
                                <img src="{{ Storage::url($item->product->photo) }}" class="product-photo" alt="{{ $item->product->name }}">
                            @else
                                <div class="no-photo">Нет фото</div>
                            @endif
                        </td>
                        <td>{{ $item->product->name }}</td>
                        <td class="purchase-price">{{ number_format($item->purchase_price, 2) }} грн</td>
                        <td class="retail-price">{{ number_format($item->retail_price, 2) }} грн</td>
                        <td class="quantity">{{ $item->quantity }} шт</td>
                        <td class="actions-cell">
                            <button class="btn-edit" onclick="openEditModal({{ $item->id }})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Ред.
                            </button>
                            <button class="btn-delete" onclick="confirmDelete({{ $item->id }})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Удалить
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Модальное окно добавления -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Добавить товар на склад</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    @csrf
                    <div class="form-group">
                        <label>Товар *</label>
                        <div class="product-search-container">
                            <input type="text" class="product-search-input form-control"
                                   placeholder="Начните вводить название товара..."
                                   oninput="searchProducts(this)"
                                   onfocus="showProductDropdown(this)"
                                   autocomplete="off">
                            <div class="product-dropdown" style="display: none;">
                                <div class="product-dropdown-list"></div>
                            </div>
                            <select id="productSelect" name="product_id" class="form-control product-select" style="display: none;" required>
                                <option value="">Выберите товар</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-purchase="{{ $product->purchase_price }}" data-retail="{{ $product->retail_price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Закупочная цена *</label>
                        <input type="number" step="0.01" name="purchase_price" required>
                    </div>
                    <div class="form-group">
                        <label>Розничная цена *</label>
                        <input type="number" step="0.01" name="retail_price" required>
                    </div>
                    <div class="form-group">
                        <label>Количество *</label>
                        <input type="number" name="quantity" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Редактировать товар на складе</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editItemId" name="id">
                    <div class="form-group">
                        <label>Товар</label>
                        <input type="text" id="editProductName" readonly>
                    </div>
                    <div class="form-group">
                        <label>Закупочная цена *</label>
                        <input type="number" step="0.01" id="editPurchasePrice" name="purchase_price" required>
                    </div>
                    <div class="form-group">
                        <label>Розничная цена *</label>
                        <input type="number" step="0.01" id="editRetailPrice" name="retail_price" required>
                    </div>
                    <div class="form-group">
                        <label>Количество *</label>
                        <input type="number" id="editQuantity" name="quantity" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить этот товар со склада?</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelDelete">Отмена</button>
                <button class="confirm-btn" id="confirmDeleteBtn">Удалить</button>
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

            fetch(`/warehouse/${id}/edit`, {
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
                        <label>Товар</label>
                        <input type="text" class="form-control" value="${data.warehouse.product_name}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Закупочная цена *</label>
                        <input type="number" step="0.01" id="editPurchasePrice" name="purchase_price"
                               value="${data.warehouse.purchase_price}" required>
                    </div>
                    <div class="form-group">
                        <label>Розничная цена *</label>
                        <input type="number" step="0.01" id="editRetailPrice" name="retail_price"
                               value="${data.warehouse.retail_price}" required>
                    </div>
                    <div class="form-group">
                        <label>Количество *</label>
                        <input type="number" id="editQuantity" name="quantity"
                               value="${data.warehouse.quantity}" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить</button>
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
                    console.error('Error:', error);
                    modalBody.innerHTML = `
            <div class="alert alert-danger">
                Ошибка загрузки данных: ${error.message || 'Произошла ошибка'}
            </div>
            <button class="btn-cancel" onclick="closeEditModal()">Закрыть</button>
        `;
                });
        }

        // Submit edit form
        function submitEditForm(form) {
            const formData = new FormData(form);
            const id = formData.get('id');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            fetch(`/warehouse/${id}`, {
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
                            row.querySelector('.purchase-price').textContent = parseFloat(data.warehouse.purchase_price).toFixed(2) + ' грн';
                            row.querySelector('.retail-price').textContent = parseFloat(data.warehouse.retail_price).toFixed(2) + ' грн';
                            row.querySelector('.quantity').textContent = data.warehouse.quantity + ' шт';
                        }
                        closeEditModal();
                        showNotification('success', 'Изменения успешно сохранены');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', error.message || 'Ошибка при сохранении изменений');
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

        // Функция для показа уведомлений
        function showNotification(type, message) {
            const notification = document.getElementById('notification');
            notification.className = `notification ${type} show`;

            const icon = type === 'success' ?
                '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>' :
                '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';

            notification.innerHTML = `
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    ${icon}
                </svg>
                <span class="notification-message">${message}</span>
            `;

            setTimeout(() => {
                notification.className = `notification ${type}`;
            }, 3000);
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
            if (row) row.classList.add('row-deleting');

            fetch(`/warehouse/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Ошибка при удалении');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        setTimeout(() => {
                            if (row) row.remove();
                            showNotification('success', 'Товар успешно удален со склада');
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    if (row) row.classList.remove('row-deleting');
                    showNotification('error', 'Не удалось удалить товар');
                });
        }

        // Поиск товаров
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#warehouseTableBody tr');

            rows.forEach(row => {
                const productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const purchasePrice = row.querySelector('.purchase-price').textContent.toLowerCase();
                const retailPrice = row.querySelector('.retail-price').textContent.toLowerCase();
                const quantity = row.querySelector('.quantity').textContent.toLowerCase();

                if (productName.includes(searchText) ||
                    purchasePrice.includes(searchText) ||
                    retailPrice.includes(searchText) ||
                    quantity.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Очистка поиска при закрытии модальных окон
        function clearSearch() {
            document.getElementById('searchInput').value = '';
            const rows = document.querySelectorAll('#warehouseTableBody tr');
            rows.forEach(row => row.style.display = '');
        }

        // Обработчик формы добавления
        document.getElementById('addForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("{{ route('warehouses.store') }}", {
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
                        const newRow = document.createElement('tr');
                        newRow.id = `warehouse-${data.warehouse.id}`;
                        const photoHtml = data.warehouse.product.photo ?
                            `<img src="/storage/${data.warehouse.product.photo}" class="product-photo" alt="${data.warehouse.product.name}">` :
                            '<div class="no-photo">Нет фото</div>';
                        newRow.innerHTML = `
                            <td>${photoHtml}</td>
                            <td>${data.warehouse.product.name}</td>
                            <td class="purchase-price">${formatPrice(data.warehouse.purchase_price)}</td>
                            <td class="retail-price">${formatPrice(data.warehouse.retail_price)}</td>
                            <td class="quantity">${data.warehouse.quantity} шт</td>
                            <td class="actions-cell">
                                <button class="btn-edit" onclick="openEditModal(${data.warehouse.id})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete" onclick="confirmDelete(${data.warehouse.id})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Удалить
                                </button>
                            </td>
                        `;
                        document.getElementById('warehouseTableBody').appendChild(newRow);
                        closeModal();
                        this.reset();
                        showNotification('success', 'Товар успешно добавлен на склад');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    if (error.errors) {
                        showErrors(error.errors);
                        showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        showNotification('error', error.message || 'Произошла ошибка при добавлении товара');
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

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            fetch(`/warehouse/${id}`, {
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
                        showNotification('success', 'Изменения успешно сохранены');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    if (error.errors) {
                        showErrors(error.errors, 'editForm');
                        showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        showNotification('error', error.message || 'Произошла ошибка при сохранении изменений');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Функция форматирования цены (без .00 для целых)
        function formatPrice(value) {
            value = parseFloat(value);
            if (isNaN(value)) return '0';
            return (value % 1 === 0 ? value.toFixed(0) : value.toFixed(2)) + ' грн';
        }
    </script>

    <style>
        .product-search-container {
            position: relative;
            width: 100%;
        }

        .product-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        }

        .product-dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .product-dropdown-item:hover {
            background-color: #f5f5f5;
        }

        .product-search-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .product-search-input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }
    </style>
</div>

<style>
.image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9);
    cursor: pointer;
}

.modal-image-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90vh;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    cursor: default;
}

.product-photo {
    cursor: pointer;
    transition: transform 0.2s;
}

.product-photo:hover {
    transform: scale(1.1);
}
</style>

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
