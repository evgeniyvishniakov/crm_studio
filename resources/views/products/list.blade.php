@extends('layouts.app')

@section('content')

    <div class="products-container">
        <div class="products-header">
            <h1>Товары</h1>
            <div id="notification" class="notification alert alert-success" role="alert">
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <span class="notification-message">Товар успешно добавлен!</span>
            </div>
            <div class="header-actions">
                <button class="btn-add-product" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Добавить товар
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" placeholder="Поиск...">
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="products-table">
                <thead>
                <tr>
                    <th>Фото</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Бренд</th>
                    <th class="actions-column">Действия</th>
                </tr>
                </thead>
                <tbody id="productsTableBody">
                @foreach($products as $product)
                    <tr id="product-{{ $product->id }}">
                        <td>
                            @if($product->photo)
                                <img src="{{ Storage::url($product->photo) }}" alt="{{ $product->name }}" class="product-photo">
                            @else
                                <div class="no-photo">Нет фото</div>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->brand }}</td>
                        <td class="actions-cell">
                            <button class="btn-edit">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Ред.
                            </button>
                            <button class="btn-delete">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
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

    <!-- Модальное окно для добавления товара -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Добавить новый товар</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="productName">Название *</label>
                        <input type="text" id="productName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">Категория *</label>
                        <input type="text" id="productCategory" name="category" required>
                    </div>
                    <div class="form-group">
                        <label for="productBrand">Бренд *</label>
                        <input type="text" id="productBrand" name="brand" required>
                    </div>
                    <div class="form-group">
                        <label for="productPhoto">Фото</label>
                        <input type="file" id="productPhoto" name="photo" accept="image/jpeg,image/png,image/jpg">
                        <small class="form-text text-muted">Максимальный размер: 2MB. Допустимые форматы: JPEG, PNG, JPG</small>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить этот товар?</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">Отмена</button>
                <button id="confirmDelete" class="confirm-btn">Удалить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования товара -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Редактировать товар</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editProductId" name="id">
                    <div class="form-group">
                        <label for="editProductName">Название *</label>
                        <input type="text" id="editProductName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductCategory">Категория *</label>
                        <input type="text" id="editProductCategory" name="category" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductBrand">Бренд *</label>
                        <input type="text" id="editProductBrand" name="brand" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductPhoto">Фото</label>
                        <input type="file" id="editProductPhoto" name="photo" accept="image/jpeg,image/png,image/jpg">
                        <small class="form-text text-muted">Максимальный размер: 2MB. Допустимые форматы: JPEG, PNG, JPG</small>
                        <div id="currentPhotoContainer" class="mt-2"></div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        // Добавление нового товара
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const productsTableBody = document.getElementById('productsTableBody');

            clearErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("{{ route('products.store') }}", {
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
                    if (data.success && data.product) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `product-${data.product.id}`;

                        // Форматируем цену
                        const formattedPrice = new Intl.NumberFormat('ru-RU', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data.product.price);

                        // Создаем HTML для фото
                        let photoHtml = '<div class="no-photo">Нет фото</div>';
                        if (data.product.photo) {
                            photoHtml = `<img src="/storage/${data.product.photo}" alt="${data.product.name}" class="product-photo">`;
                        }

                        // Создаем HTML для новой строки
                        newRow.innerHTML = `
                            <td>${photoHtml}</td>
                            <td>${data.product.name}</td>
                            <td>${data.product.category}</td>
                            <td>${data.product.brand}</td>
                            <td class="actions-cell">
                                <button class="btn-edit">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Удалить
                                </button>
                            </td>
                        `;

                        // Добавляем новую строку в начало таблицы
                        productsTableBody.insertBefore(newRow, productsTableBody.firstChild);

                        // Показываем уведомление
                        showNotification('success', `Товар ${data.product.name} успешно добавлен`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('Сервер не вернул данные товара');
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

        // Глобальные переменные для удаления
        let currentDeleteRow = null;
        let currentDeleteId = null;

        // Обработчик клика по кнопке удаления
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                const row = e.target.closest('tr');
                const productId = row.id.split('-')[1];

                // Сохраняем ссылку на удаляемую строку
                currentDeleteRow = row;
                currentDeleteId = productId;

                // Показываем модальное окно подтверждения
                document.getElementById('confirmationModal').style.display = 'block';
            }
        });
        // Обработчики для модального окна подтверждения
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (currentDeleteRow && currentDeleteId) {
                deleteProduct(currentDeleteRow, currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Функция для удаления товара
        function deleteProduct(row, productId) {
            // Добавляем класс для анимации
            row.classList.add('row-deleting');

            // Отправляем запрос на удаление
            fetch(`/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка при удалении');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Удаляем строку после завершения анимации
                        setTimeout(() => {
                            row.remove();
                            showNotification('success', 'Товар успешно удален');
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    row.classList.remove('row-deleting');
                    showNotification('error', 'Не удалось удалить товар');
                });
        }

        // Функции для работы с модальным окном редактирования
        function openEditModal(productId) {
            const modal = document.getElementById('editProductModal');
            const modalBody = modal.querySelector('.modal-body');

            // Показываем модальное окно и лоадер
            modal.style.display = 'block';
            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`/products/${productId}/edit`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Ошибка сети');
                    return response.json();
                })
                .then(product => {
                    // Создаем HTML формы
                    const formHtml = `
            <form id="editProductForm" enctype="multipart/form-data">
                @csrf
                    @method('PUT')
                    <input type="hidden" id="editProductId" name="id" value="${product.id}">
                <div class="form-group">
                    <label for="editProductName">Название *</label>
                    <input type="text" id="editProductName" name="name" value="${(product.name || '').replace(/"/g, '&quot;')}" required>
                </div>
                <div class="form-group">
                    <label for="editProductCategory">Категория *</label>
                    <input type="text" id="editProductCategory" name="category" value="${product.category || ''}" required>
                </div>
                <div class="form-group">
                    <label for="editProductBrand">Бренд *</label>
                    <input type="text" id="editProductBrand" name="brand" value="${product.brand || ''}" required>
                </div>
                <div class="form-group">
                    <label for="editProductPhoto">Фото</label>
                    <input type="file" id="editProductPhoto" name="photo" accept="image/jpeg,image/png,image/jpg">
                    <small class="form-text text-muted">Максимальный размер: 2MB. Допустимые форматы: JPEG, PNG, JPG</small>
                    <div id="currentPhotoContainer" class="mt-2">
                        ${product.photo ? `
                            <div>
                                <p>Текущее фото:</p>
                                <img src="/storage/${product.photo}" alt="${product.name}" class="current-photo">
                                <button type="button" class="remove-photo-btn" onclick="removePhoto(${product.id})">Удалить фото</button>
                            </div>
                        ` : ''}
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить</button>
                </div>
            </form>
        `;

                    // Вставляем форму в модальное окно
                    modalBody.innerHTML = formHtml;

                    // Назначаем обработчик события
                    document.getElementById('editProductForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        submitEditForm(this);
                    });
                })
                .catch(error => {
                    console.error('Ошибка загрузки данных:', error);
                    modalBody.innerHTML = '<p class="text-danger">Ошибка загрузки данных товара</p>';
                });
        }



        // Функция для удаления фото товара
        function removePhoto(productId) {
            if (confirm('Вы уверены, что хотите удалить фото товара?')) {
                fetch(`/products/${productId}/remove-photo`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('currentPhotoContainer').innerHTML = '';
                            showNotification('success', 'Фото товара успешно удалено');
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        showNotification('error', 'Не удалось удалить фото товара');
                    });
            }
        }

        // Обработчик клика по кнопке редактирования
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit');
            if (editBtn) {
                const row = editBtn.closest('tr');
                if (row) {
                    const productId = row.id.split('-')[1];
                    if (productId) {
                        openEditModal(productId);
                    }
                }
            }
        });

        // Обработчик отправки формы редактирования
        async function submitEditForm(form) {
            const formData = new FormData(form);
            const productId = formData.get('id');
            const submitBtn = form.querySelector('.btn-submit');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(`/products/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    throw new Error(
                        errorData?.message ||
                        `Ошибка сервера: ${response.status} ${response.statusText}`
                    );
                }

                const data = await response.json();

                if (data.success) {
                    updateProductRow(data.product);
                    showNotification('success', '✅ Изменения сохранены');
                    closeEditModal();
                } else {
                    throw new Error(data.message || 'Неизвестная ошибка сервера');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('error', `❌ ${error.message || 'Ошибка сети или сервера'}`);
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

            // Функция для обновления строки товара в таблице
            function updateProductRow(product) {
                const row = document.getElementById(`product-${product.id}`);
                if (!row) return;

                // Обновляем фото
                const photoCell = row.querySelector('td:first-child');
                if (photoCell) {
                    photoCell.innerHTML = product.photo
                        ? `<img src="/storage/${product.photo}" alt="${product.name}" class="product-photo">`
                        : '<div class="no-photo">Нет фото</div>';
                }

                // Обновляем название
                const nameCell = row.querySelector('td:nth-child(2)');
                if (nameCell) nameCell.textContent = product.name;

                // Обновляем категорию
                const categoryCell = row.querySelector('td:nth-child(3)');
                if (categoryCell) categoryCell.textContent = product.category;

                // Обновляем бренд
                const brandCell = row.querySelector('td:nth-child(4)');
                if (brandCell) brandCell.textContent = product.brand;
            }

            // Обработчик для кнопки просмотра (можно добавить функционал по необходимости)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-view')) {
                    const row = e.target.closest('tr');
                    const productId = row.id.split('-')[1];
                    // Здесь можно реализовать просмотр деталей товара
                    alert('Просмотр товара с ID: ' + productId);
                }
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
    </script>

@endsection
