// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ УСЛУГ =====

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

// Функция для форматирования длительности
function formatDuration(duration) {
    if (!duration || duration <= 0) return '—';
    const hours = Math.floor(duration / 60);
    const minutes = duration % 60;
    let result = '';
    if (hours > 0) result += hours + ' ' + (window.translations?.service_duration_hours_short || 'h') + ' ';
    if (minutes > 0) result += minutes + ' ' + (window.translations?.service_duration_minutes_short || 'm');
    return result.trim();
}

// Функции для работы с модальным окном
function openServiceModal() {
    const modal = document.getElementById('addServiceModal');
    if (modal) {
        modal.style.display = 'block';
        // Предотвращаем закрытие при клике вне модального окна
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                e.stopPropagation();
            }
        });
    }
}

function closeServiceModal() {
    const modal = document.getElementById('addServiceModal');
    if (modal) {
        modal.style.display = 'none';
    }
    clearErrors();
}

function closeEditServiceModal() {
    const modal = document.getElementById('editServiceModal');
    if (modal) {
        modal.style.display = 'none';
    }
    clearErrors('editServiceForm');
}

// Функция для создания мобильных карточек услуг
function createMobileCards(services = null) {
    const servicesCards = document.getElementById('servicesCards');
    
    if (!servicesCards) return;
    
    // Очищаем контейнер карточек
    servicesCards.innerHTML = '';
    
    // Если services не переданы, пытаемся получить из таблицы
    if (!services) {
        const tableBody = document.getElementById('servicesTableBody');
        if (!tableBody) return;
        
        const rows = tableBody.querySelectorAll('tr');
        if (rows.length === 0) return; // Если таблица пустая, не создаем карточки
        
        rows.forEach(row => {
            const serviceId = row.id.split('-')[1];
            const cells = row.querySelectorAll('td');
            
            if (cells.length >= 5) {
                const name = cells[0].textContent;
                const price = cells[1].textContent;
                const duration = cells[2].textContent;
                const status = cells[3].textContent;
                
                const card = document.createElement('div');
                card.className = 'service-card';
                card.id = `service-card-${serviceId}`;
                
                card.innerHTML = `
                    <div class="service-card-header">
                        <h3 class="service-name">${name}</h3>
                    </div>
                    <div class="service-card-info">
                        <div class="service-info-item">
                            <span class="service-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Цена:
                            </span>
                            <span class="service-info-value">${price}</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Длительность:
                            </span>
                            <span class="service-info-value">${duration}</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">
                                <i class="fas fa-circle ${status.includes('Активная') ? 'active' : 'inactive'}"></i>
                                Статус:
                            </span>
                            <span class="service-info-value">
                                <span class="status-badge ${status.includes('Активная') ? 'active' : 'inactive'}">${status}</span>
                            </span>
                        </div>
                    </div>
                    <div class="service-card-actions">
                        <button class="btn-edit" onclick="openEditModal(${serviceId})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Ред.
                        </button>
                        <button class="btn-delete" onclick="showDeleteConfirmation(${serviceId})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Удалить
                        </button>
                    </div>
                `;
                
                servicesCards.appendChild(card);
            }
        });
    } else {
        // Создаем карточки на основе переданных данных
        services.forEach(service => {
            // Форматируем цену
            const price = service.price ? 
                (service.price == parseInt(service.price) ? 
                    parseInt(service.price) : 
                    parseFloat(service.price).toFixed(2)) + ' грн' : '—';
            
            // Форматируем длительность
            const duration = service.duration || 0;
            const hours = Math.floor(duration / 60);
            const minutes = duration % 60;
            let durationText = '';
            if (duration > 0) {
                if (hours > 0) durationText += hours + ' ч ';
                if (minutes > 0) durationText += minutes + ' мин';
            } else {
                durationText = '—';
            }
            
            const card = document.createElement('div');
            card.className = 'service-card';
            card.id = `service-card-${service.id}`;
            
            card.innerHTML = `
                <div class="service-card-header">
                    <h3 class="service-name">${service.name}</h3>
                </div>
                                    <div class="service-card-info">
                        <div class="service-info-item">
                            <span class="service-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Цена:
                            </span>
                            <span class="service-info-value">${price}</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Длительность:
                            </span>
                            <span class="service-info-value">${durationText}</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">
                                <i class="fas fa-circle ${service.status ? 'active' : 'inactive'}"></i>
                                ${window.translations?.status || 'Status'}:
                            </span>
                            <span class="service-info-value">
                                <span class="status-badge ${service.status ? 'active' : 'inactive'}">${service.status ? (window.translations?.active || 'Active') : (window.translations?.inactive || 'Inactive')}</span>
                            </span>
                        </div>
                    </div>
                <div class="service-card-actions">
                    <button class="btn-edit" onclick="openEditModal(${service.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        ${window.translations?.edit || 'Edit'}
                    </button>
                    <button class="btn-delete" onclick="showDeleteConfirmation(${service.id})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        ${window.translations?.delete || 'Delete'}
                        </button>
                </div>
            `;
            
            servicesCards.appendChild(card);
        });
    }
}

// Функция для переключения между десктопной и мобильной версией
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const servicesCards = document.getElementById('servicesCards');
    const servicesPagination = document.getElementById('servicesPagination');
    const mobileServicesPagination = document.getElementById('mobileServicesPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (servicesCards) {
            servicesCards.style.display = 'block';
            // Карточки будут созданы в renderServices при загрузке данных
        }
        if (servicesPagination) servicesPagination.style.display = 'none';
        if (mobileServicesPagination) mobileServicesPagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (servicesCards) servicesCards.style.display = 'none';
        if (servicesPagination) servicesPagination.style.display = 'block';
        if (mobileServicesPagination) mobileServicesPagination.style.display = 'none';
    }
}

// Функция для показа модального окна подтверждения удаления
function showDeleteConfirmation(serviceId) {
    window.currentDeleteId = serviceId;
    openModal('confirmationModal');
}

// Функция для удаления услуги
function deleteService(serviceId) {
    const row = document.getElementById('service-' + serviceId);
    const card = document.getElementById('service-card-' + serviceId);
    
    if (row) row.classList.add('row-deleting');
    if (card) card.classList.add('row-deleting');

    fetch(`/services/${serviceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                if (row) row.remove();
                if (card) card.remove();
                window.showNotification('success', 'Услуга успешно удалена');
                
                // Сдвигающая пагинация - обновляем текущую страницу
                const pag = document.querySelector('.pagination .page-btn.active');
                let currentPage = pag ? parseInt(pag.textContent) : 1;
                const searchValue = document.querySelector('#searchInput') ? document.querySelector('#searchInput').value.trim() : '';
                loadServices(currentPage, searchValue);
            }, 300);
        }
    })
    .catch(error => {
        if (row) row.classList.remove('row-deleting');
        if (card) card.classList.remove('row-deleting');
        window.showNotification('error', 'Не удалось удалить услугу');
    });
}

// Функции для работы с модальным окном редактирования
function openEditModal(serviceId) {
    fetch(`/services/${serviceId}/edit`)
        .then(response => response.json())
        .then(service => {
            const form = document.getElementById('editServiceForm');
            form.querySelector('#editServiceId').value = service.id;
            form.querySelector('#editServiceName').value = service.name;
            // Форматируем цену для отображения в поле
            let displayPrice = '';
            if (service.price) {
                const price = parseFloat(service.price);
                if (price % 1 === 0) {
                    // Если цена целая, показываем без копеек
                    displayPrice = price.toString();
                } else {
                    // Если есть копейки, показываем с двумя знаками после запятой
                    displayPrice = price.toFixed(2);
                }
            }
            form.querySelector('#editServicePrice').value = displayPrice;

            // Вычисляем и устанавливаем длительность
            const duration = service.duration || 0;
            const hours = Math.floor(duration / 60);
            const minutes = duration % 60;
            form.querySelector('[name="duration_hours"]').value = hours;
            form.querySelector('[name="duration_minutes"]').value = minutes;
            
            // Устанавливаем статус
            form.querySelector('#editServiceStatus').value = service.status ? '1' : '0';

            const modal = document.getElementById('editServiceModal');
            if (modal) {
                modal.style.display = 'block';
                // Предотвращаем закрытие при клике вне модального окна
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        e.stopPropagation();
                    }
                });
            }
        })
        .catch(error => {
            window.showNotification('error', 'Ошибка при загрузке данных услуги');
        });
}

// Функция для очистки полей длительности при фокусе
function clearDurationFieldOnFocus() {
    const durationInputs = document.querySelectorAll('input[name="duration_hours"], input[name="duration_minutes"]');
    
    durationInputs.forEach(input => {
        input.addEventListener('focus', function() {
            if (this.value === '0') {
                this.value = '';
            }
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '' || this.value === null) {
                this.value = '0';
            }
        });
    });
}

// Функция для рендеринга услуг в таблице
function renderServices(services) {
    const tableBody = document.getElementById('servicesTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    services.forEach(service => {
        const row = document.createElement('tr');
        row.id = `service-${service.id}`;
        
        // Форматируем цену
        const price = service.price ? 
            (service.price == parseInt(service.price) ? 
                parseInt(service.price) : 
                parseFloat(service.price).toFixed(2)) + ' грн' : '—';
        
        // Форматируем длительность
        const duration = service.duration || 0;
        const durationText = duration > 0 ? formatDuration(duration) : '—';
        
                // Форматируем статус
        const statusText = service.status ? (window.translations?.active || 'Active') : (window.translations?.inactive || 'Inactive');
        const statusClass = service.status ? 'active' : 'inactive';
        
        row.innerHTML = `
            <td>${service.name}</td>
        <td class="currency-amount" data-amount="${service.price || ''}">${price}</td>
        <td>${durationText}</td>
        <td class="status-cell">
            <span class="status-badge ${statusClass}">${statusText}</span>
        </td>
            <td class="actions-cell">
                <button class="btn-edit" onclick="openEditModal(${service.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    ${window.translations?.edit || 'Edit'}
                </button>
                <button class="btn-delete" onclick="showDeleteConfirmation(${service.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    ${window.translations?.delete || 'Delete'}
                </button>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    // Обновляем мобильные карточки если нужно
    if (window.innerWidth <= 768) {
        createMobileCards(services);
    }
    
    // Переключаем вид в зависимости от размера экрана
    toggleMobileView();
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
    
    // Пагинация для десктопа (в таблице)
    let pagContainer = document.getElementById('servicesPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'servicesPagination';
        document.querySelector('.table-wrapper').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;
    
    // Пагинация для мобильных устройств (в карточках)
    let mobilePagContainer = document.getElementById('mobileServicesPagination');
    if (!mobilePagContainer) {
        mobilePagContainer = document.createElement('div');
        mobilePagContainer.id = 'mobileServicesPagination';
        document.querySelector('.services-cards').appendChild(mobilePagContainer);
    }
    mobilePagContainer.innerHTML = paginationHtml;

    // Навешиваем обработчики для всех кнопок пагинации
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadServices(page);
            }
        });
    });
}

// Функция для загрузки услуг с пагинацией
function loadServices(page = 1, search = '') {
    const searchValue = search !== undefined ? search : document.querySelector('#searchInput').value.trim();
    fetch(`/services?search=${encodeURIComponent(searchValue)}&page=${page}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        renderServices(data.data);
        renderPagination(data.meta);
    })
    .catch(error => {
        // Ошибка при загрузке услуг
        window.showNotification('error', 'Ошибка при загрузке услуг');
    });
}

// Функция для поиска услуг
function handleSearch() {
    loadServices(1, document.querySelector('#searchInput').value.trim());
}

// Выполняем переключение сразу при загрузке скрипта
toggleMobileView();

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Настройка обработчиков для кнопок удаления и редактирования
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete')) {
            const row = e.target.closest('tr');
            const card = e.target.closest('.service-card');
            
            let serviceId = null;
            
            if (row && row.id) {
                // Десктопная версия - получаем ID из строки таблицы
                serviceId = row.id.split('-')[1];
            } else if (card && card.id) {
                // Мобильная версия - получаем ID из карточки
                serviceId = card.id.split('-')[2]; // service-card-{id}
            }
            
            if (serviceId) {
                showDeleteConfirmation(serviceId);
            }
        }
        
        // Обработчик для кнопок редактирования
        if (e.target.closest('.btn-edit')) {
            const row = e.target.closest('tr');
            const card = e.target.closest('.service-card');
            
            let serviceId = null;
            
            if (row && row.id) {
                // Десктопная версия - получаем ID из строки таблицы
                serviceId = row.id.split('-')[1];
            } else if (card && card.id) {
                // Мобильная версия - получаем ID из карточки
                serviceId = card.id.split('-')[2]; // service-card-{id}
            }
            
            if (serviceId) {
                openEditModal(serviceId);
            }
        }
    });

    // Обработчики для модального окна подтверждения
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            closeModal('confirmationModal');
        });
    }
    
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (window.currentDeleteId) {
                deleteService(window.currentDeleteId);
            }
            closeModal('confirmationModal');
        });
    }

    // Обработчик формы добавления услуги
    const addServiceForm = document.getElementById('addServiceForm');
    if (addServiceForm) {
        addServiceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this; // Сохраняем ссылку на форму
            submitForm('addServiceForm', '/services', 'POST', 
                function(data) {
                    // Успешное добавление
                    window.showNotification('success', 'Услуга успешно добавлена');
                    closeServiceModal();
                    form.reset();
                    
                    // Перезагружаем данные с правильной пагинацией
                    // Новая услуга будет на первой странице (самая новая)
                    loadServices(1);
                },
                function(error) {
                    // Не показываем уведомление, если ошибки уже отображены в форме
                    if (!error.errors || Object.keys(error.errors).length === 0) {
                        window.showNotification('error', 'Ошибка при добавлении услуги');
                    }
                }
            );
        });
    }

    // Обработчик формы редактирования услуги
    const editServiceForm = document.getElementById('editServiceForm');
    if (editServiceForm) {
        editServiceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this; // Сохраняем ссылку на форму
            const serviceId = form.querySelector('#editServiceId').value;
            submitForm('editServiceForm', `/services/${serviceId}`, 'POST', 
                function(data) {
                    // Успешное редактирование
                    window.showNotification('success', window.translations?.service_successfully_updated || 'Услуга успешно обновлена');
                    closeEditServiceModal();
                    
                    // Обновляем данные в таблице без перезагрузки
                    if (data.service) {
                        const row = document.getElementById(`service-${data.service.id}`);
                        if (row) {
                            // Обновляем название услуги
                            row.cells[0].textContent = data.service.name;
                            
                            // Обновляем цену
                            const price = data.service.price ? 
                                (data.service.price == parseInt(data.service.price) ? 
                                    parseInt(data.service.price) : 
                                    parseFloat(data.service.price).toFixed(2)) + ' грн' : '—';
                            row.cells[1].textContent = price;
                            row.cells[1].setAttribute('data-amount', data.service.price || '');
                            
                            // Обновляем длительность
                            const duration = data.service.duration || 0;
                            const hours = Math.floor(duration / 60);
                            const minutes = duration % 60;
                            let durationText = '';
                            if (duration > 0) {
                                if (hours > 0) durationText += hours + ' ч ';
                                if (minutes > 0) durationText += minutes + ' мин';
                            } else {
                                durationText = '—';
                            }
                            row.cells[2].textContent = durationText;
                            
                            // Обновляем статус
                            const statusText = data.service.status ? 'Активная' : 'Неактивная';
                            const statusClass = data.service.status ? 'active' : 'inactive';
                            const statusCell = row.cells[3];
                            if (statusCell) {
                                statusCell.innerHTML = `<span class="status-badge ${statusClass}">${statusText}</span>`;
                            }
                        }
                        
                        // Также обновляем мобильную карточку
                        if (window.innerWidth <= 768) {
                            const card = document.getElementById(`service-card-${data.service.id}`);
                            if (card) {
                                const nameElement = card.querySelector('.service-name');
                                const priceElement = card.querySelector('.service-info-item:nth-child(1) .service-info-value');
                                const durationElement = card.querySelector('.service-info-item:nth-child(2) .service-info-value');
                                const statusElement = card.querySelector('.service-info-item:nth-child(3) .service-info-value');
                                
                                if (nameElement) nameElement.textContent = data.service.name;
                                if (priceElement) priceElement.textContent = price;
                                if (durationElement) durationElement.textContent = durationText;
                                if (statusElement) statusElement.textContent = statusText;
                            }
                        }
                    }
                },
                function(error) {
                    // Не показываем уведомление, если ошибки уже отображены в форме
                    if (!error.errors || Object.keys(error.errors).length === 0) {
                        window.showNotification('error', 'Ошибка при обновлении услуги');
                    }
                }
            );
        });
    }

    // Поиск с пагинацией
    const searchInput = document.querySelector('#searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            loadServices(1, this.value.trim());
        });
    }

    // Инициализация первой загрузки
    loadServices(1);

    // Инициализация очистки полей
    clearDurationFieldOnFocus();
    
    // Переключаем на правильную версию
    toggleMobileView();
});

// Обработчик изменения размера окна
window.addEventListener('resize', function() {
    toggleMobileView();
}); 