// Функции для работы с поставщиками

// Функции для работы с модальными окнами
function openModal() {
    const modal = document.getElementById('addServiceModal');
    modal.style.display = 'block';
    
    // Предотвращаем закрытие при клике вне модального окна
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            e.stopPropagation();
        }
    });
}

function closeModal() {
    document.getElementById('addServiceModal').style.display = 'none';
    clearSupplierErrors();
}

function closeEditModal() {
    document.getElementById('editServiceModal').style.display = 'none';
    clearSupplierErrors('editServiceForm');
}

// Функция для очистки ошибок
function clearSupplierErrors(formId = 'addServiceForm') {
    const form = document.getElementById(formId);
    if (!form) return;

    form.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('has-error');
    });

    form.querySelectorAll('.error-message').forEach(error => {
        error.remove();
    });
}

// Функция для отображения ошибок
function showSupplierErrors(errors, formId = 'addServiceForm') {
    clearSupplierErrors(formId);

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
            errorElement.style.display = 'block';
            errorElement.style.fontWeight = 'bold';

            inputGroup.appendChild(errorElement);
        }
    });
}

// Функция для переключения между десктопной и мобильной версией
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const suppliersCards = document.getElementById('suppliersCards');
    const suppliersPagination = document.getElementById('suppliersPagination');
    const mobileSuppliersPagination = document.getElementById('mobileSuppliersPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (suppliersCards) suppliersCards.style.display = 'block';
        if (suppliersPagination) suppliersPagination.style.display = 'none';
        if (mobileSuppliersPagination) mobileSuppliersPagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (suppliersCards) suppliersCards.style.display = 'none';
        if (suppliersPagination) suppliersPagination.style.display = 'block';
        if (mobileSuppliersPagination) mobileSuppliersPagination.style.display = 'none';
    }
}

// Функция для показа модального окна подтверждения удаления
function showDeleteConfirmation(supplierId) {
    window.currentDeleteId = supplierId;
    document.getElementById('confirmationModal').style.display = 'block';
}

// Функция для удаления поставщика
function deleteSupplier(supplierId) {
    const row = document.getElementById('supplier-' + supplierId);
    const card = document.getElementById('supplier-card-' + supplierId);
    
    if (row) row.classList.add('row-deleting');
    if (card) card.classList.add('row-deleting');

    fetch(`/suppliers/${supplierId}`, {
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
                window.showNotification('success', 'Поставщик успешно удален');

                // Сдвигаем пагинацию - обновляем текущую страницу
                const pag = document.querySelector('.pagination .page-btn.active');
                let currentPage = pag ? parseInt(pag.textContent) : 1;
                const searchValue = document.querySelector('#searchInput') ? document.querySelector('#searchInput').value.trim() : '';
                loadSuppliers(currentPage, searchValue);
            }, 300);
        }
    })
    .catch(error => {
        if (row) row.classList.remove('row-deleting');
        if (card) card.classList.remove('row-deleting');
        window.showNotification('error', 'Не удалось удалить поставщика');
    });
}

// Функции для работы с модальным окном редактирования
function openEditModal(supplierId) {
    fetch(`/suppliers/${supplierId}/edit`)
        .then(response => response.json())
        .then(supplier => {
            const form = document.getElementById('editServiceForm');
            form.querySelector('#editServiceId').value = supplier.id;
            form.querySelector('#editServiceName').value = supplier.name;
            form.querySelector('#editServiceContactPerson').value = supplier.contact_person || '';
            form.querySelector('#editServicePhone').value = supplier.phone || '';
            form.querySelector('#editServiceEmail').value = supplier.email || '';
            form.querySelector('#editServiceAddress').value = supplier.address || '';
            form.querySelector('#editServiceInstagram').value = supplier.instagram || '';
            form.querySelector('#editServiceInn').value = supplier.inn || '';
            form.querySelector('#editServiceNote').value = supplier.note || '';
            form.querySelector('#editServiceStatus').value = supplier.status ? '1' : '0';

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
            window.showNotification('error', 'Ошибка при загрузке данных поставщика');
        });
}

// Функция для обновления строки поставщика в таблице
function updateSupplierRow(supplier) {
    const row = document.getElementById(`supplier-${supplier.id}`);
    if (!row) return;

    const cells = row.querySelectorAll('td');
    if (cells.length >= 5) {
        cells[0].textContent = supplier.name;
        cells[1].textContent = supplier.contact_person ?? '—';

        // Обновляем телефон
        if (supplier.phone) {
            cells[2].innerHTML = `<a href="tel:${supplier.phone}">${supplier.phone}</a>`;
        } else {
            cells[2].textContent = '—';
        }

        // Обновляем email
        if (supplier.email) {
            cells[3].innerHTML = `<a href="mailto:${supplier.email}">${supplier.email}</a>`;
        } else {
            cells[3].textContent = '—';
        }

        // Обновляем статус
        const statusBadge = cells[4].querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${supplier.status ? 'active' : 'inactive'}`;
            statusBadge.textContent = supplier.status ? 'Активен' : 'Неактивен';
        }
    }

    // Также обновляем мобильную карточку
    updateSupplierCard(supplier);
}

// Функция для обновления карточки поставщика
function updateSupplierCard(supplier) {
    const card = document.getElementById(`supplier-card-${supplier.id}`);
    if (!card) return;

    // Обновляем название
    const nameElement = card.querySelector('.supplier-name');
    if (nameElement) {
        nameElement.textContent = supplier.name;
    }

    // Обновляем статус
    const statusBadge = card.querySelector('.status-badge');
    if (statusBadge) {
        statusBadge.className = `status-badge ${supplier.status ? 'active' : 'inactive'}`;
        statusBadge.textContent = supplier.status ? 'Активен' : 'Неактивен';
    }

    // Обновляем контактное лицо
    const contactElement = card.querySelector('.supplier-info-item:nth-child(1) .supplier-info-value');
    if (contactElement) {
        contactElement.textContent = supplier.contact_person ?? '—';
    }

    // Обновляем телефон
    const phoneElement = card.querySelector('.supplier-info-item:nth-child(2) .supplier-info-value');
    if (phoneElement) {
        if (supplier.phone) {
            phoneElement.innerHTML = `<a href="tel:${supplier.phone}">${supplier.phone}</a>`;
        } else {
            phoneElement.textContent = '—';
        }
    }

    // Обновляем email
    const emailElement = card.querySelector('.supplier-info-item:nth-child(3) .supplier-info-value');
    if (emailElement) {
        if (supplier.email) {
            emailElement.innerHTML = `<a href="mailto:${supplier.email}">${supplier.email}</a>`;
        } else {
            emailElement.textContent = '—';
        }
    }
}

// Функция для рендеринга поставщиков
function renderSuppliers(suppliers) {
    const tableBody = document.getElementById('servicesTableBody');
    const suppliersCards = document.getElementById('suppliersCards');

    if (!tableBody || !suppliersCards) return;

    // Очищаем контейнеры
    tableBody.innerHTML = '';
    suppliersCards.innerHTML = '';

    suppliers.forEach(supplier => {
        // Создаем строку для десктопной таблицы
        const row = document.createElement('tr');
        row.id = `supplier-${supplier.id}`;
        
        const phoneHtml = supplier.phone 
            ? `<a href="tel:${supplier.phone}">${supplier.phone}</a>`
            : '—';
        
        const emailHtml = supplier.email 
            ? `<a href="mailto:${supplier.email}">${supplier.email}</a>`
            : '—';
        
        row.innerHTML = `
            <td>${supplier.name}</td>
            <td>${supplier.contact_person ?? '—'}</td>
            <td>${phoneHtml}</td>
            <td>${emailHtml}</td>
            <td>
                <span class="status-badge ${supplier.status ? 'active' : 'inactive'}">
                    ${supplier.status ? 'Активен' : 'Неактивен'}
                </span>
            </td>
            <td class="actions-cell">
                <button class="btn-edit" onclick="openEditModal(${supplier.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Ред.
                </button>
                <button class="btn-delete" onclick="showDeleteConfirmation(${supplier.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Удалить
                </button>
            </td>
        `;
        tableBody.appendChild(row);

        // Создаем карточку для мобильной версии
        const card = document.createElement('div');
        card.className = 'supplier-card';
        card.id = `supplier-card-${supplier.id}`;
        
        const phoneValue = supplier.phone 
            ? `<a href="tel:${supplier.phone}">${supplier.phone}</a>`
            : '—';
        
        const emailValue = supplier.email 
            ? `<a href="mailto:${supplier.email}">${supplier.email}</a>`
            : '—';
        
        card.innerHTML = `
            <div class="supplier-card-header">
                <div class="supplier-main-info">
                    <h3 class="supplier-name">${supplier.name}</h3>
                    <span class="status-badge ${supplier.status ? 'active' : 'inactive'}">
                        ${supplier.status ? 'Активен' : 'Неактивен'}
                    </span>
                </div>
            </div>
            <div class="supplier-info">
                <div class="supplier-info-item">
                    <span class="supplier-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Контакт
                    </span>
                    <span class="supplier-info-value">${supplier.contact_person ?? '—'}</span>
                </div>
                <div class="supplier-info-item">
                    <span class="supplier-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                        Телефон
                    </span>
                    <span class="supplier-info-value">${phoneValue}</span>
                </div>
                <div class="supplier-info-item">
                    <span class="supplier-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        Email
                    </span>
                    <span class="supplier-info-value">${emailValue}</span>
                </div>
            </div>
            <div class="supplier-actions">
                <button class="btn-edit" title="Редактировать" onclick="openEditModal(${supplier.id})">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Изменить
                </button>
                <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${supplier.id})">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Удалить
                </button>
            </div>
        `;
        
        suppliersCards.appendChild(card);
    });

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
    let pagContainer = document.getElementById('suppliersPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'suppliersPagination';
        document.querySelector('.table-wrapper').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    // Обновляем мобильную пагинацию
    let mobilePagContainer = document.getElementById('mobileSuppliersPagination');
    if (mobilePagContainer) {
        mobilePagContainer.innerHTML = paginationHtml;
    }

    // Навешиваем обработчики
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                const searchValue = document.querySelector('#searchInput') ? document.querySelector('#searchInput').value.trim() : '';
                loadSuppliers(page, searchValue);
            }
        });
    });
}

// Функция для загрузки поставщиков
function loadSuppliers(page = 1, search = '') {
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (search) params.append('search', search);
    
    fetch(`/suppliers?${params.toString()}`, {
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
        renderSuppliers(data.data);
        renderPagination(data.meta);
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка загрузки данных');
    });
}

// Функция для обработки поиска
function handleSearch() {
    const searchInput = document.getElementById('searchInput');
    const query = searchInput.value.trim();
    
    // Сбрасываем на первую страницу при поиске
    loadSuppliers(1, query);
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация первой загрузки
    loadSuppliers(1);
    
    // Обработчик изменения размера окна
    window.addEventListener('resize', function() {
        toggleMobileView();
    });
    
    // Обработчики для модального окна подтверждения удаления
    document.getElementById('cancelDelete').addEventListener('click', function() {
        document.getElementById('confirmationModal').style.display = 'none';
        window.currentDeleteId = null;
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (window.currentDeleteId) {
            deleteSupplier(window.currentDeleteId);
        }
        document.getElementById('confirmationModal').style.display = 'none';
    });
    
    // Обработчик клика по кнопке удаления
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete')) {
            const element = e.target.closest('tr') || e.target.closest('.supplier-card');
            let supplierId;
            
            if (element.classList.contains('supplier-card')) {
                supplierId = element.id.split('-')[2]; // supplier-card-{id}
            } else {
                supplierId = element.id.split('-')[1]; // supplier-{id}
            }

            window.currentDeleteId = supplierId;
            document.getElementById('confirmationModal').style.display = 'block';
        }
    });
    
    // Обработчик клика по кнопке редактирования
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-edit')) {
            const element = e.target.closest('tr') || e.target.closest('.supplier-card');
            let supplierId;
            
            if (element.classList.contains('supplier-card')) {
                supplierId = element.id.split('-')[2]; // supplier-card-{id}
            } else {
                supplierId = element.id.split('-')[1]; // supplier-{id}
            }
            openEditModal(supplierId);
        }
    });
    
    // Обработчик отправки формы добавления
    const addForm = document.getElementById('addServiceForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            clearSupplierErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("/suppliers", {
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
                if (data.success && data.supplier) {
                    // Показываем уведомление
                    window.showNotification('success', `Поставщик "${data.supplier.name}" успешно добавлен`);

                    // Закрываем модальное окно и очищаем форму
                    closeModal();
                    this.reset();

                    // Перезагружаем данные
                    const searchValue = document.querySelector('#searchInput') ? document.querySelector('#searchInput').value.trim() : '';
                    loadSuppliers(1, searchValue);
                } else {
                    throw new Error('Ошибка добавления поставщика');
                }
            })
            .catch(error => {
                if (error.errors) {
                    showSupplierErrors(error.errors);
                    window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    window.showNotification('error', error.message || 'Ошибка добавления поставщика');
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
            const supplierId = document.getElementById('editServiceId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            fetch(`/suppliers/${supplierId}`, {
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
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateSupplierRow(data.supplier);
                    window.showNotification('success', 'Поставщик успешно обновлен');
                    closeEditModal();
                }
            })
            .catch(error => {
                if (error.errors) {
                    showSupplierErrors(error.errors, 'editServiceForm');
                    window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    window.showNotification('error', 'Ошибка обновления поставщика');
                }
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }
}); 