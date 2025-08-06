// Функции для работы с модальным окном
function openModal() {
    const modal = document.getElementById('addServiceModal');
    modal.style.display = 'block';
    
    // Добавляем обработчик для предотвращения закрытия при клике на модальное окно
    modal.addEventListener('click', function(e) {
        e.stopPropagation();
    });
}

function closeModal() {
    document.getElementById('addServiceModal').style.display = 'none';
    clearClientTypeErrors();
}

function closeEditModal() {
    document.getElementById('editServiceModal').style.display = 'none';
    clearClientTypeErrors('editServiceForm');
}

// Функция для перевода названий типов клиентов
function getTranslatedClientTypeName(typeName) {
    const translations = {
        'Новый клиент': 'Новый клиент',
        'Постоянный клиент': 'Постоянный клиент'
    };
    return translations[typeName] || typeName;
}

// Функция для очистки ошибок
function clearClientTypeErrors(formId = 'addServiceForm') {
    const form = document.getElementById(formId);
    if (form) {
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        form.querySelectorAll('.has-error').forEach(el => {
            el.classList.remove('has-error');
        });
    }
}

// Функция для отображения ошибок
function showClientTypeErrors(errors, formId = 'addServiceForm') {
    clearClientTypeErrors(formId);

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

// Глобальные переменные для удаления
let currentDeleteRow = null;
let currentDeleteId = null;

// Функция для показа модального окна подтверждения удаления
function showDeleteConfirmation(clientTypeId) {

    
    // Находим строку и карточку для удаления
    const row = document.getElementById('client-type-' + clientTypeId);
    const card = document.getElementById('client-type-card-' + clientTypeId);
    

    
    currentDeleteRow = row;
    currentDeleteId = clientTypeId;
    

    
    document.getElementById('confirmationModal').style.display = 'block';
}

// Функция для удаления типа клиента
function deleteClientType(rowOrId, clientTypeId) {

    
    let row;
    let card;
    
    if (typeof rowOrId === 'object' && rowOrId !== null && 'classList' in rowOrId) {
        // Вызов с двумя аргументами: (row, clientTypeId)
        row = rowOrId;
    } else {
        // Вызов с одним аргументом: (clientTypeId)
        clientTypeId = rowOrId;
        row = document.getElementById('client-type-' + clientTypeId);
        card = document.getElementById('client-type-card-' + clientTypeId);
    }
    

    
    if (row) row.classList.add('row-deleting');
    if (card) card.classList.add('row-deleting');



    fetch(`/client-types/${clientTypeId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
    
            if (!response.ok) {
                return response.json().then(err => Promise.reject({status: response.status, ...err}));
            }
            return response.json();
        })
        .then(data => {
    
            if (data.success) {
                setTimeout(() => {
                    if (row) row.remove();
                    if (card) card.remove();
                    showNotification('success', 'Тип клиента успешно удален');
                }, 300);
            }
        })
        .catch(error => {
            console.error('Ошибка при удалении:', error);
            if (row) row.classList.remove('row-deleting');
            if (card) card.classList.remove('row-deleting');
            if (error.status === 403 && error.message) {
                showNotification('error', error.message);
            } else {
                showNotification('error', 'Не удалось удалить тип клиента');
            }
        });
}

// Функции для работы с модальным окном редактирования
function openEditModal(clientTypeId) {
    fetch(`/client-types/${clientTypeId}/edit`)
        .then(response => response.json())
        .then(clientType => {
            document.getElementById('editServiceId').value = clientType.id;
            document.getElementById('editServiceName').value = clientType.name;
            document.getElementById('editServiceDescription').value = clientType.description || '';
            document.getElementById('editServiceDiscount').value = clientType.discount || '';
            document.getElementById('editServiceStatus').value = clientType.status ? '1' : '0';

            const modal = document.getElementById('editServiceModal');
            modal.style.display = 'block';
            
            // Добавляем обработчик для предотвращения закрытия при клике на модальное окно
            modal.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        })
        .catch(error => {
            showNotification('error', 'Не удалось загрузить данные типа клиента');
        });
}

// Функция для обновления строки типа клиента в таблице
function updateClientTypeRow(clientType) {
    const row = document.getElementById(`client-type-${clientType.id}`);
    if (!row) return;

    const cells = row.querySelectorAll('td');
    if (cells.length >= 4) {
        cells[0].textContent = getTranslatedClientTypeName(clientType.name);
        cells[1].textContent = clientType.description ?? '—';
        cells[2].textContent = clientType.discount !== null ? (Number(parseFloat(clientType.discount)) % 1 === 0 ? Number(parseFloat(clientType.discount)) : parseFloat(clientType.discount).toFixed(2)) + '%' : '—';

        // Обновляем статус
        const statusBadge = cells[3].querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${clientType.status ? 'active' : 'inactive'}`;
            statusBadge.textContent = clientType.status ? 'Активный' : 'Неактивный';
        }
    }
    
    // Обновляем карточку типа клиента в мобильной версии
    const card = document.getElementById(`client-type-card-${clientType.id}`);
    if (card) {
        // Обновляем название
        const nameElement = card.querySelector('.client-type-name');
        if (nameElement) {
            nameElement.textContent = getTranslatedClientTypeName(clientType.name);
        }
        
        // Обновляем статус
        const statusBadge = card.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${clientType.status ? 'active' : 'inactive'}`;
            statusBadge.textContent = clientType.status ? 'Активный' : 'Неактивный';
        }
        
        // Обновляем описание
        const descriptionElement = card.querySelector('.client-type-info-item:nth-child(1) .client-type-info-value');
        if (descriptionElement) {
            descriptionElement.textContent = clientType.description ?? '—';
        }
        
        // Обновляем скидку
        const discountElement = card.querySelector('.client-type-info-item:nth-child(2) .client-type-info-value');
        if (discountElement) {
            discountElement.textContent = clientType.discount !== null ? (Number(parseFloat(clientType.discount)) % 1 === 0 ? Number(parseFloat(clientType.discount)) : parseFloat(clientType.discount).toFixed(2)) + '%' : '—';
        }
    }
}

// Функция для загрузки типов клиентов
function loadClientTypes() {
    fetch('/client-types?ajax=1')
        .then(response => response.json())
        .then(data => {
            updateTable(data.data);
            renderPagination(data.meta);
        })
        .catch(error => {
            console.error('Ошибка загрузки типов клиентов:', error);
        });
}

// Функция для обновления таблицы и карточек
function updateTable(clientTypes) {
    const tbody = document.getElementById('servicesTableBody');
    const clientTypesCards = document.getElementById('clientTypesCards');
    
    tbody.innerHTML = '';
    clientTypesCards.innerHTML = '';

    clientTypes.forEach(clientType => {
        // Создаем строку для десктопной таблицы
        const row = document.createElement('tr');
        row.id = `client-type-${clientType.id}`;
        
        const discountText = clientType.discount !== null ? 
            (Number(parseFloat(clientType.discount)) % 1 === 0 ? 
                Number(parseFloat(clientType.discount)) : 
                parseFloat(clientType.discount).toFixed(2)) + '%' : '—';
        
        row.innerHTML = `
            <td>${getTranslatedClientTypeName(clientType.name)}</td>
            <td>${clientType.description ?? '—'}</td>
            <td>${discountText}</td>
            <td>
                <span class="status-badge ${clientType.status ? 'active' : 'inactive'}">
                    ${clientType.status ? 'Активный' : 'Неактивный'}
                </span>
            </td>
            <td class="actions-cell">
                ${!clientType.is_global ? `
                    <button class="btn-edit" onclick="openEditModal(${clientType.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Ред.
                    </button>
                    <button class="btn-delete" onclick="showDeleteConfirmation(${clientType.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Удалить
                    </button>
                ` : `
                    <span class="text-muted">Системный</span>
                `}
            </td>
        `;
        tbody.appendChild(row);

        // Создаем карточку для мобильной версии
        const card = document.createElement('div');
        card.className = 'client-type-card';
        card.id = `client-type-card-${clientType.id}`;
        
        card.innerHTML = `
            <div class="client-type-card-header">
                <div class="client-type-main-info">
                    <h3 class="client-type-name">${getTranslatedClientTypeName(clientType.name)}</h3>
                    <span class="status-badge ${clientType.status ? 'active' : 'inactive'}">
                        ${clientType.status ? 'Активный' : 'Неактивный'}
                    </span>
                </div>
            </div>
            <div class="client-type-info">
                <div class="client-type-info-item">
                    <span class="client-type-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Описание
                    </span>
                    <span class="client-type-info-value">${clientType.description ?? '—'}</span>
                </div>
                <div class="client-type-info-item">
                    <span class="client-type-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                        </svg>
                        Скидка
                    </span>
                    <span class="client-type-info-value">${discountText}</span>
                </div>
            </div>
            ${!clientType.is_global ? `
                <div class="client-type-actions">
                    <button class="btn-edit" title="Редактировать" onclick="openEditModal(${clientType.id})">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Редактировать
                    </button>
                    <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${clientType.id})">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Удалить
                    </button>
                </div>
            ` : ''}
        `;
        
        clientTypesCards.appendChild(card);
    });
}

// Функция для рендеринга пагинации
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
    
    // Обновляем мобильную пагинацию
    let mobilePagContainer = document.getElementById('mobileClientTypesPagination');
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

// Функция для переключения между десктопной и мобильной версией
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const clientTypesCards = document.getElementById('clientTypesCards');
    const mobileClientTypesPagination = document.getElementById('mobileClientTypesPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) {
            tableWrapper.style.display = 'none';
        }
        if (clientTypesCards) {
            clientTypesCards.style.display = 'block';
        }
        if (mobileClientTypesPagination) mobileClientTypesPagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) {
            tableWrapper.style.display = 'block';
        }
        if (clientTypesCards) {
            clientTypesCards.style.display = 'none';
        }
        if (mobileClientTypesPagination) mobileClientTypesPagination.style.display = 'none';
    }
}

// Функция для поиска
function handleSearch() {
    const searchInput = document.getElementById('searchInput');
    const query = searchInput.value.trim();
    searchQuery = query;
    loadPage(1, query);
}

// Функция для загрузки страницы
function loadPage(page, search = '') {
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (search) params.append('search', search);
    
    fetch(`/client-types?${params.toString()}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            updateTable(data.data);
            renderPagination(data.meta);
        })
        .catch(error => {
            console.error('Ошибка загрузки данных:', error);
        });
}

// Инициализация
let searchQuery = '';
let isInitialized = false;

document.addEventListener('DOMContentLoaded', function() {
    if (!isInitialized) {
        isInitialized = true;
        toggleMobileView(); // Переключаем на правильную версию
    }
});

// Обработчик изменения размера окна
window.addEventListener('resize', function() {
    toggleMobileView();
});

// Добавление нового типа клиента
document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.getElementById('addServiceForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const servicesTableBody = document.getElementById('servicesTableBody');

            clearClientTypeErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("/client-types", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                    if (data.success && data.clientType) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `client-type-${data.clientType.id}`;

                        newRow.innerHTML = `
                            <td>${getTranslatedClientTypeName(data.clientType.name)}</td>
                            <td>${data.clientType.description ?? '—'}</td>
                            <td>
                                ${data.clientType.discount !== null ? (Number(parseFloat(data.clientType.discount)) % 1 === 0 ? Number(parseFloat(data.clientType.discount)) : parseFloat(data.clientType.discount).toFixed(2)) + '%' : '—'}
                            </td>
                            <td>
                                <span class="status-badge ${data.clientType.status ? 'active' : 'inactive'}">
                                    ${data.clientType.status ? 'Активный' : 'Неактивный'}
                                </span>
                            </td>
                            <td class="actions-cell">
                                <button class="btn-edit" onclick="openEditModal(${data.clientType.id})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete" onclick="showDeleteConfirmation(${data.clientType.id})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Удалить
                                </button>
                            </td>
                        `;

                        // Добавляем новую строку в начало таблицы
                        servicesTableBody.insertBefore(newRow, servicesTableBody.firstChild);

                        // Создаем новую карточку для мобильной версии
                        const clientTypesCards = document.getElementById('clientTypesCards');
                        const newCard = document.createElement('div');
                        newCard.className = 'client-type-card';
                        newCard.id = `client-type-card-${data.clientType.id}`;
                        
                        const discountText = data.clientType.discount !== null ? 
                            (Number(parseFloat(data.clientType.discount)) % 1 === 0 ? 
                                Number(parseFloat(data.clientType.discount)) : 
                                parseFloat(data.clientType.discount).toFixed(2)) + '%' : '—';
                        
                        newCard.innerHTML = `
                            <div class="client-type-card-header">
                                <div class="client-type-main-info">
                                    <h3 class="client-type-name">${getTranslatedClientTypeName(data.clientType.name)}</h3>
                                    <span class="status-badge ${data.clientType.status ? 'active' : 'inactive'}">
                                        ${data.clientType.status ? 'Активный' : 'Неактивный'}
                                    </span>
                                </div>
                            </div>
                            <div class="client-type-info">
                                <div class="client-type-info-item">
                                    <span class="client-type-info-label">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        Описание
                                    </span>
                                    <span class="client-type-info-value">${data.clientType.description ?? '—'}</span>
                                </div>
                                <div class="client-type-info-item">
                                    <span class="client-type-info-label">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                                        </svg>
                                        Скидка
                                    </span>
                                    <span class="client-type-info-value">${discountText}</span>
                                </div>
                            </div>
                            <div class="client-type-actions">
                                <button class="btn-edit" title="Редактировать" onclick="openEditModal(${data.clientType.id})">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Редактировать
                                </button>
                                <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${data.clientType.id})">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Удалить
                                </button>
                            </div>
                        `;

                        // Добавляем новую карточку в начало мобильного списка
                        if (clientTypesCards) {
                            clientTypesCards.insertBefore(newCard, clientTypesCards.firstChild);
                        }

                        // Показываем уведомление
                        showNotification('success', `Тип клиента "${getTranslatedClientTypeName(data.clientType.name)}" успешно добавлен`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('Сервер не вернул данные типа клиента');
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showClientTypeErrors(error.errors);
                        showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        showNotification('error', error.message || 'Ошибка добавления типа клиента');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });
    }

    // Обработчик отправки формы редактирования
    const editForm = document.getElementById('editServiceForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const clientTypeId = document.getElementById('editServiceId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            fetch(`/client-types/${clientTypeId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject({status: response.status, ...err}));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateClientTypeRow(data.clientType);
                        showNotification('success', 'Изменения успешно сохранены');
                        closeEditModal();
                    }
                })
                .catch(error => {
                    if (error.status === 403 && error.message) {
                        showNotification('error', error.message);
                    } else if (error.errors) {
                        showClientTypeErrors(error.errors, 'editServiceForm');
                        showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        showNotification('error', 'Ошибка сохранения изменений');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });
    }

    // Обработчики для модального окна подтверждения
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });
    }

    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            
            
            if (currentDeleteId) {
            
                deleteClientType(currentDeleteId);
            } else {
                console.error('currentDeleteId не установлен!');
            }
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });
    }
}); 