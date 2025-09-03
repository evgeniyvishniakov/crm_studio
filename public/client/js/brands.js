// Функции для работы с брендами товаров

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
    clearBrandErrors();
}

function closeEditModal() {
    document.getElementById('editServiceModal').style.display = 'none';
    clearBrandErrors('editServiceForm');
}

// Функция для очистки ошибок
function clearBrandErrors(formId = 'addServiceForm') {
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
function showBrandErrors(errors, formId = 'addServiceForm') {
    clearBrandErrors(formId);

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
    const brandsCards = document.getElementById('brandsCards');
    const brandsPagination = document.getElementById('brandsPagination');
    const mobileBrandsPagination = document.getElementById('mobileBrandsPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (brandsCards) brandsCards.style.display = 'block';
        if (brandsPagination) brandsPagination.style.display = 'none';
        if (mobileBrandsPagination) mobileBrandsPagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (brandsCards) brandsCards.style.display = 'none';
        if (brandsPagination) brandsPagination.style.display = 'block';
        if (mobileBrandsPagination) mobileBrandsPagination.style.display = 'none';
    }
}

// Функция для показа модального окна подтверждения удаления
function showDeleteConfirmation(brandId) {
    window.currentDeleteId = brandId;
    document.getElementById('confirmationModal').style.display = 'block';
}

// Функция для удаления бренда
function deleteBrand(brandId) {
    const row = document.getElementById('brand-' + brandId);
    const card = document.getElementById('brand-card-' + brandId);
    
    if (row) row.classList.add('row-deleting');
    if (card) card.classList.add('row-deleting');

    fetch(`/product-brands/${brandId}`, {
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
                window.showNotification('success', 'Бренд успешно удален');
                
                // Сдвигающая пагинация - обновляем текущую страницу
                const pag = document.querySelector('.pagination .page-btn.active');
                let currentPage = pag ? parseInt(pag.textContent) : 1;
                const searchValue = document.querySelector('#searchInput') ? document.querySelector('#searchInput').value.trim() : '';
                loadBrands(currentPage, searchValue);
            }, 300);
        }
    })
    .catch(error => {
        if (row) row.classList.remove('row-deleting');
        if (card) card.classList.remove('row-deleting');
        window.showNotification('error', 'Не удалось удалить бренд');
    });
}

// Функции для работы с модальным окном редактирования
function openEditModal(brandId) {
    fetch(`/product-brands/${brandId}/edit`)
        .then(response => response.json())
        .then(brand => {
            const form = document.getElementById('editServiceForm');
            form.querySelector('#editServiceId').value = brand.id;
            form.querySelector('#editServiceName').value = brand.name;
            form.querySelector('#editServiceCountry').value = brand.country || '';
            form.querySelector('#editServiceWebsite').value = brand.website || '';
            form.querySelector('#editServiceDescription').value = brand.description || '';
            form.querySelector('#editServiceStatus').value = brand.status ? '1' : '0';

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
            window.showNotification('error', 'Ошибка при загрузке данных бренда');
        });
}

// Функция для обновления строки бренда в таблице
function updateBrandRow(brand) {
    const row = document.getElementById(`brand-${brand.id}`);
    if (!row) return;

    const cells = row.querySelectorAll('td');
    if (cells.length >= 4) {
        cells[0].textContent = brand.name;
        cells[1].textContent = brand.country ?? '—';

        // Обновляем вебсайт
        if (brand.website) {
            cells[2].innerHTML = `<a href="${brand.website}" target="_blank">${new URL(brand.website).hostname}</a>`;
        } else {
            cells[2].textContent = '—';
        }

        // Обновляем статус
        const statusBadge = cells[3].querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${brand.status ? 'active' : 'inactive'}`;
            statusBadge.textContent = brand.status ? 'Активен' : 'Неактивен';
        }
    }
    
    // Обновляем карточку бренда в мобильной версии
    const card = document.getElementById(`brand-card-${brand.id}`);
    if (card) {
        // Обновляем название
        const nameElement = card.querySelector('.brand-name');
        if (nameElement) {
            nameElement.textContent = brand.name;
        }
        
        // Обновляем статус
        const statusBadge = card.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${brand.status ? 'active' : 'inactive'}`;
            statusBadge.textContent = brand.status ? 'Активен' : 'Неактивен';
        }
        
        // Обновляем страну
        const countryElement = card.querySelector('.brand-info-item:nth-child(1) .brand-info-value');
        if (countryElement) {
            countryElement.textContent = brand.country ?? '—';
        }
        
        // Обновляем сайт
        const websiteElement = card.querySelector('.brand-info-item:nth-child(2) .brand-info-value');
        if (websiteElement) {
            if (brand.website) {
                websiteElement.innerHTML = `<a href="${brand.website}" target="_blank">${new URL(brand.website).hostname}</a>`;
            } else {
                websiteElement.textContent = '—';
            }
        }
        
        // Обновляем описание
        const descriptionElement = card.querySelector('.brand-info-item:nth-child(3) .brand-info-value');
        if (descriptionElement) {
            descriptionElement.textContent = brand.description ?? '—';
        }
    }
}

// Функция для рендеринга брендов
function renderBrands(brands) {
    const tableBody = document.getElementById('servicesTableBody');
    const brandsCards = document.getElementById('brandsCards');
    
    if (!tableBody || !brandsCards) return;
    
    // Очищаем контейнеры
    tableBody.innerHTML = '';
    brandsCards.innerHTML = '';
    
    brands.forEach(brand => {
        // Создаем строку для десктопной таблицы
        const row = document.createElement('tr');
        row.id = `brand-${brand.id}`;
        
        const websiteHtml = brand.website 
            ? `<a href="${brand.website}" target="_blank">${new URL(brand.website).hostname}</a>`
            : '—';
        
        row.innerHTML = `
            <td>${brand.name}</td>
            <td>${brand.country ?? '—'}</td>
            <td>${websiteHtml}</td>
            <td>
                <span class="status-badge ${brand.status ? 'active' : 'inactive'}">
                    ${brand.status ? (window.translations?.brand_active || 'Активен') : (window.translations?.brand_inactive || 'Неактивен')}
                </span>
            </td>
            <td class="actions-cell">
                <button class="btn-edit" onclick="openEditModal(${brand.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    ${window.translations?.edit || 'Редактировать'}
                </button>
                <button class="btn-delete" onclick="showDeleteConfirmation(${brand.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    ${window.translations?.delete || 'Удалить'}
                </button>
            </td>
        `;
        tableBody.appendChild(row);

        // Создаем карточку для мобильной версии
        const card = document.createElement('div');
        card.className = 'brand-card';
        card.id = `brand-card-${brand.id}`;
        
        const websiteValue = brand.website 
            ? `<a href="${brand.website}" target="_blank">${new URL(brand.website).hostname}</a>`
            : '—';
        
        card.innerHTML = `
            <div class="brand-card-header">
                <div class="brand-main-info">
                    <h3 class="brand-name">${brand.name}</h3>
                    <span class="status-badge ${brand.status ? 'active' : 'inactive'}">
                        ${brand.status ? (window.translations?.brand_active || 'Активен') : (window.translations?.brand_inactive || 'Неактивен')}
                    </span>
                </div>
            </div>
            <div class="brand-info">
                <div class="brand-info-item">
                    <span class="brand-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        ${window.translations?.brand_country || 'Страна'}
                    </span>
                    <span class="brand-info-value">${brand.country ?? '—'}</span>
                </div>
                <div class="brand-info-item">
                    <span class="brand-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                            <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                        </svg>
                        ${window.translations?.website || 'Сайт'}
                    </span>
                    <span class="brand-info-value">${websiteValue}</span>
                </div>
                <div class="brand-info-item">
                    <span class="brand-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        ${window.translations?.description || 'Описание'}
                    </span>
                    <span class="brand-info-value">${brand.description ?? '—'}</span>
                </div>
            </div>
            <div class="brand-actions">
                <button class="btn-edit" title="Редактировать" onclick="openEditModal(${brand.id})">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    ${window.translations?.edit || 'Редактировать'}
                </button>
                <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${brand.id})">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    ${window.translations?.delete || 'Удалить'}
                </button>
            </div>
        `;
        
        brandsCards.appendChild(card);
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
    let pagContainer = document.getElementById('brandsPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'brandsPagination';
        document.querySelector('.table-wrapper').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    // Обновляем мобильную пагинацию
    let mobilePagContainer = document.getElementById('mobileBrandsPagination');
    if (mobilePagContainer) {
        mobilePagContainer.innerHTML = paginationHtml;
    }

    // Навешиваем обработчики
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                const searchValue = document.querySelector('#searchInput') ? document.querySelector('#searchInput').value.trim() : '';
                loadBrands(page, searchValue);
            }
        });
    });
}

// Функция для загрузки брендов
function loadBrands(page = 1, search = '') {
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (search) params.append('search', search);
    
    fetch(`/product-brands?${params.toString()}`, {
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
        renderBrands(data.data);
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
    loadBrands(1, query);
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация первой загрузки
    loadBrands(1);
    
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
            deleteBrand(window.currentDeleteId);
        }
        document.getElementById('confirmationModal').style.display = 'none';
    });
    
    // Обработчик клика по кнопке удаления
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete')) {
            const element = e.target.closest('tr') || e.target.closest('.brand-card');
            let brandId;
            
            if (element.classList.contains('brand-card')) {
                brandId = element.id.split('-')[2]; // brand-card-{id}
            } else {
                brandId = element.id.split('-')[1]; // brand-{id}
            }

            window.currentDeleteId = brandId;
            document.getElementById('confirmationModal').style.display = 'block';
        }
    });
    
    // Обработчик клика по кнопке редактирования
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-edit')) {
            const element = e.target.closest('tr') || e.target.closest('.brand-card');
            let brandId;
            
            if (element.classList.contains('brand-card')) {
                brandId = element.id.split('-')[2]; // brand-card-{id}
            } else {
                brandId = element.id.split('-')[1]; // brand-{id}
            }
            openEditModal(brandId);
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

            clearBrandErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("/product-brands", {
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
                if (data.success && data.brand) {
                    // Показываем уведомление
                    window.showNotification('success', `Бренд "${data.brand.name}" успешно добавлен`);

                    // Закрываем модальное окно и очищаем форму
                    closeModal();
                    this.reset();
                    
                    // Перезагружаем данные
                    const searchValue = document.querySelector('#searchInput') ? document.querySelector('#searchInput').value.trim() : '';
                    loadBrands(1, searchValue);
                } else {
                    throw new Error('Ошибка добавления бренда');
                }
            })
            .catch(error => {
                if (error.errors) {
                    showBrandErrors(error.errors);
                    window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    window.showNotification('error', error.message || 'Ошибка добавления бренда');
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
            const brandId = document.getElementById('editServiceId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
            submitBtn.disabled = true;

            fetch(`/product-brands/${brandId}`, {
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
                    updateBrandRow(data.brand);
                    window.showNotification('success', window.translations?.brand_successfully_updated || 'Бренд успешно обновлен');
                    closeEditModal();
                }
            })
            .catch(error => {
                if (error.errors) {
                    showBrandErrors(error.errors, 'editServiceForm');
                    window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    window.showNotification('error', 'Ошибка обновления бренда');
                }
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }
}); 