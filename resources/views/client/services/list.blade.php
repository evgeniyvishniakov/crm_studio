@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>Услуги</h1>
            <div id="notification" class="notification alert alert-success" role="alert">
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <span class="notification-message">Услуга успешно добавлена!</span>
            </div>
            <div class="header-actions">
                <button class="btn-add-service" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Добавить услугу
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="Поиск..." onkeyup="handleSearch()">
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class=" table-striped services-table">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Цена</th>
                    <th class="actions-column">Действия</th>
                </tr>
                </thead>
                <tbody id="servicesTableBody">
                @foreach($services as $service)
                    <tr id="service-{{ $service->id }}">
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->price ? ($service->price == (int)$service->price ? (int)$service->price : number_format($service->price, 2, '.', '')) . ' грн' : '—' }}</td>
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
            
            <!-- Пагинация будет добавлена через JavaScript -->
            <div id="servicesPagination"></div>
        </div>
    </div>

    <!-- Модальное окно для добавления услуги -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Добавить новую услугу</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">Название *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="servicePrice">Цена (грн)</label>
                        <input type="number" id="servicePrice" name="price" min="0" step="0.01">
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
            <p>Вы уверены, что хотите удалить эту услугу?</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">Отмена</button>
                <button id="confirmDelete" class="confirm-btn">Удалить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования услуги -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Редактировать услугу</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editServiceId" name="id">
                    <div class="form-group">
                        <label for="editServiceName">Название *</label>
                        <input type="text" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editServicePrice">Цена (грн)</label>
                        <input type="number" id="editServicePrice" name="price" min="0" step="0.01">
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
        // Функции для работы с модальным окном
        function openModal() {
            document.getElementById('addServiceModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addServiceModal').style.display = 'none';
            clearErrors();
        }

        function closeEditModal() {
            document.getElementById('editServiceModal').style.display = 'none';
            clearErrors('editServiceForm');
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            if (event.target == document.getElementById('addServiceModal')) {
                closeModal();
            }
            if (event.target == document.getElementById('editServiceModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
                currentDeleteRow = null;
                currentDeleteId = null;
            }
        }

        // Функция для очистки ошибок
        function clearErrors(formId = 'addServiceForm') {
            const form = document.getElementById(formId);
            if (form) {
                form.querySelectorAll('.error-message').forEach(el => el.remove());
                form.querySelectorAll('.has-error').forEach(el => {
                    el.classList.remove('has-error');
                });
            }
        }

        // Функция для отображения ошибок
        function showErrors(errors, formId = 'addServiceForm') {
            clearErrors(formId);

            Object.entries(errors).forEach(([field, messages]) => {
                const input = document.querySelector(`#${formId} [name="${field}"]`);
                if (input) {
                    const inputGroup = input.closest('.form-group');
                    inputGroup.classList.add('has-error');

                    const errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                    errorElement.style.color = '#f44336';
                    errorElement.style.marginTop = '5px';
                    errorElement.style.fontSize = '0.85rem';

                    inputGroup.appendChild(errorElement);
                }
            });
        }

        // Добавление новой услуги
        document.getElementById('addServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const servicesTableBody = document.getElementById('servicesTableBody');

            clearErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("{{ route('services.store') }}", {
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
                    if (data.success && data.service) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `service-${data.service.id}`;

                        newRow.innerHTML = `
                            <td>${data.service.name}</td>
                            <td>${data.service.price ? (Number(parseFloat(data.service.price)) % 1 === 0 ? Number(parseFloat(data.service.price)) : parseFloat(data.service.price).toFixed(2)) + ' грн' : '—'}</td>
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
                        servicesTableBody.insertBefore(newRow, servicesTableBody.firstChild);

                        // Показываем уведомление
                        window.showNotification('success', `Услуга "${data.service.name}" успешно добавлена`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('Сервер не вернул данные услуги');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);

                    if (error.errors) {
                        showErrors(error.errors);
                        window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        window.showNotification('error', error.message || 'Произошла ошибка при добавлении услуги');
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
                const serviceId = row.id.split('-')[1];

                currentDeleteRow = row;
                currentDeleteId = serviceId;

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
                deleteService(currentDeleteRow, currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Функция для удаления услуги
        function deleteService(row, serviceId) {
            row.classList.add('row-deleting');

            fetch(`/services/${serviceId}`, {
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
                        setTimeout(() => {
                            row.remove();
                            window.showNotification('success', 'Услуга успешно удалена');
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    row.classList.remove('row-deleting');
                    window.showNotification('error', 'Не удалось удалить услугу');
                });
        }

        // Функции для работы с модальным окном редактирования
        function openEditModal(serviceId) {
            fetch(`/services/${serviceId}/edit`)
                .then(response => response.json())
                .then(service => {
                    document.getElementById('editServiceId').value = service.id;
                    document.getElementById('editServiceName').value = service.name;
                    document.getElementById('editServicePrice').value = service.price || '';

                    document.getElementById('editServiceModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Ошибка загрузки данных:', error);
                    window.showNotification('error', 'Не удалось загрузить данные услуги');
                });
        }

        // Обработчик клика по кнопке редактирования
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-edit')) {
                const row = e.target.closest('tr');
                const serviceId = row.id.split('-')[1];
                openEditModal(serviceId);
            }
        });

        // Обработчик отправки формы редактирования
        document.getElementById('editServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const serviceId = document.getElementById('editServiceId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            fetch(`/services/${serviceId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                        updateServiceRow(data.service);
                        window.showNotification('success', 'Изменения успешно сохранены');
                        closeEditModal();
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    if (error.errors) {
                        showErrors(error.errors, 'editServiceForm');
                        window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        window.showNotification('error', 'Ошибка при сохранении изменений');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Функция для обновления строки услуги в таблице
        function updateServiceRow(service) {
            const row = document.getElementById(`service-${service.id}`);
            if (!row) return;

            const cells = row.querySelectorAll('td');
            if (cells.length >= 2) {
                cells[0].textContent = service.name;
                cells[1].textContent = service.price ? (Number(parseFloat(service.price)) % 1 === 0 ? Number(parseFloat(service.price)) : parseFloat(service.price).toFixed(2)) + ' грн' : '—';
            }
        }

        // AJAX-пагинация
        let currentPage = 1;
        let searchQuery = '';

        function loadPage(page, search = '') {
            currentPage = page;
            searchQuery = search;
            
            const params = new URLSearchParams();
            if (page > 1) params.append('page', page);
            if (search) params.append('search', search);
            
            fetch(`/services?${params.toString()}`, {
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
            })
            .catch(error => {
                console.error('Ошибка:', error);
                window.showNotification('error', 'Ошибка загрузки данных');
            });
        }

        function updateTable(services) {
            const tbody = document.getElementById('servicesTableBody');
            tbody.innerHTML = '';

            services.forEach(service => {
                const row = document.createElement('tr');
                row.id = `service-${service.id}`;
                row.innerHTML = `
                    <td>${service.name}</td>
                    <td>${service.price ? (Number(parseFloat(service.price)) % 1 === 0 ? Number(parseFloat(service.price)) : parseFloat(service.price).toFixed(2)) + ' грн' : '—'}</td>
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
                tbody.appendChild(row);
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
            let pagContainer = document.getElementById('servicesPagination');
            if (!pagContainer) {
                pagContainer = document.createElement('div');
                pagContainer.id = 'servicesPagination';
                document.querySelector('.table-wrapper').appendChild(pagContainer);
            }
            pagContainer.innerHTML = paginationHtml;

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
            const query = searchInput.value.trim();
            
            // Сбрасываем на первую страницу при поиске
            loadPage(1, query);
        }

        // Обновляем функцию добавления услуги для обновления таблицы
        const originalAddServiceForm = document.getElementById('addServiceForm').addEventListener;
        document.getElementById('addServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch('/services', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
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
                    if (data.success && data.service) {
                        // Обновляем таблицу после добавления
                        loadPage(currentPage, searchQuery);
                        
                        // Показываем уведомление
                        window.showNotification('success', `Услуга "${data.service.name}" успешно добавлена`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('Сервер не вернул данные услуги');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);

                    if (error.errors) {
                        showErrors(error.errors);
                        window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        window.showNotification('error', error.message || 'Произошла ошибка при добавлении услуги');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Обновляем функцию удаления услуги для обновления таблицы
        function deleteService(row, serviceId) {
            row.classList.add('row-deleting');

            fetch(`/services/${serviceId}`, {
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
                        setTimeout(() => {
                            // Обновляем таблицу после удаления
                            loadPage(currentPage, searchQuery);
                            window.showNotification('success', 'Услуга успешно удалена');
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    row.classList.remove('row-deleting');
                    window.showNotification('error', 'Не удалось удалить услугу');
                });
        }

        // Инициализация первой загрузки
        loadPage(1);
    </script>
</div>
@endsection
