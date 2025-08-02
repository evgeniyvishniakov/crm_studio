// Функции для работы с категориями товаров

// Функции для работы с модальными окнами
function openModal() {
    document.getElementById('addServiceModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('addServiceModal').style.display = 'none';
    clearCategoryErrors();
}

function closeEditModal() {
    document.getElementById('editServiceModal').style.display = 'none';
    clearCategoryErrors('editServiceForm');
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
        window.currentDeleteId = null;
    }
}

// Функция для очистки ошибок
function clearCategoryErrors(formId = 'addServiceForm') {
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
function showCategoryErrors(errors, formId = 'addServiceForm') {
    console.log('showCategoryErrors вызвана с:', { errors, formId });
    clearCategoryErrors(formId);

    Object.entries(errors).forEach(([field, messages]) => {
        console.log('Обрабатываем поле:', field, 'сообщения:', messages);
        const input = document.querySelector(`#${formId} [name="${field}"]`);
        console.log('Найденный input:', input);
        if (input) {
            const inputGroup = input.closest('.form-group');
            console.log('Найденная группа:', inputGroup);
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
            console.log('Ошибка добавлена в DOM');
        } else {
            console.log('Input не найден для поля:', field);
        }
    });
}

// Функция для переключения между десктопной и мобильной версией
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const categoriesCards = document.getElementById('categoriesCards');
    const categoriesPagination = document.getElementById('categoriesPagination');
    const mobileCategoriesPagination = document.getElementById('mobileCategoriesPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (categoriesCards) categoriesCards.style.display = 'block';
        if (categoriesPagination) categoriesPagination.style.display = 'none';
        if (mobileCategoriesPagination) mobileCategoriesPagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (categoriesCards) categoriesCards.style.display = 'none';
        if (categoriesPagination) categoriesPagination.style.display = 'block';
        if (mobileCategoriesPagination) mobileCategoriesPagination.style.display = 'none';
    }
}

// Функция для показа модального окна подтверждения удаления
function showDeleteConfirmation(categoryId) {
    window.currentDeleteId = categoryId;
    document.getElementById('confirmationModal').style.display = 'block';
}

// Функция для удаления категории
function deleteCategory(categoryId) {
    const row = document.getElementById('category-' + categoryId);
    const card = document.getElementById('category-card-' + categoryId);
    
    if (row) row.classList.add('row-deleting');
    if (card) card.classList.add('row-deleting');

    fetch(`/product-categories/${categoryId}`, {
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
                window.showNotification('success', 'Категория успешно удалена');
                
                // Сдвигающая пагинация - обновляем текущую страницу
                const pag = document.querySelector('.pagination .page-btn.active');
                let currentPage = pag ? parseInt(pag.textContent) : 1;
                const searchValue = document.querySelector('#searchInput') ? document.querySelector('#searchInput').value.trim() : '';
                loadCategories(currentPage, searchValue);
            }, 300);
        }
    })
    .catch(error => {
        if (row) row.classList.remove('row-deleting');
        if (card) card.classList.remove('row-deleting');
        window.showNotification('error', 'Не удалось удалить категорию');
    });
}

// Функции для работы с модальным окном редактирования
function openEditModal(categoryId) {
    fetch(`/product-categories/${categoryId}/edit`)
        .then(response => response.json())
        .then(category => {
            const form = document.getElementById('editServiceForm');
            form.querySelector('#editServiceId').value = category.id;
            form.querySelector('#editServiceName').value = category.name;
            form.querySelector('#editServiceDescription').value = category.description || '';
            form.querySelector('#editServiceStatus').value = category.status ? '1' : '0';

            document.getElementById('editServiceModal').style.display = 'block';
        })
        .catch(error => {
            window.showNotification('error', 'Ошибка при загрузке данных категории');
        });
}

// Функция для обновления строки категории в таблице
function updateCategoryRow(category) {
    const row = document.getElementById(`category-${category.id}`);
    if (!row) return;

    const cells = row.querySelectorAll('td');
    if (cells.length >= 3) {
        cells[0].textContent = category.name;
        cells[1].textContent = category.description ?? '—';

        const statusBadge = cells[2].querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${category.status ? 'active' : 'inactive'}`;
            statusBadge.textContent = category.status ? 'Активна' : 'Неактивна';
        }
    }
    
    // Обновляем карточку категории в мобильной версии
    const card = document.getElementById(`category-card-${category.id}`);
    if (card) {
        // Обновляем название
        const nameElement = card.querySelector('.category-name');
        if (nameElement) {
            nameElement.textContent = category.name;
        }
        
        // Обновляем статус
        const statusBadge = card.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${category.status ? 'active' : 'inactive'}`;
            statusBadge.textContent = category.status ? 'Активна' : 'Неактивна';
        }
        
        // Обновляем описание
        const descriptionElement = card.querySelector('.category-info-value');
        if (descriptionElement) {
            descriptionElement.textContent = category.description ?? '—';
        }
    }
}

// Функция для рендеринга категорий
function renderCategories(categories) {
    const tableBody = document.getElementById('servicesTableBody');
    const categoriesCards = document.getElementById('categoriesCards');
    
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    if (categoriesCards) categoriesCards.innerHTML = '';

    categories.forEach(category => {
        // Создаем строку для десктопной таблицы
        const row = document.createElement('tr');
        row.id = `category-${category.id}`;
        row.innerHTML = `
            <td>${category.name}</td>
            <td>${category.description ?? '—'}</td>
            <td>
                <span class="status-badge ${category.status ? 'active' : 'inactive'}">
                    ${category.status ? 'Активна' : 'Неактивна'}
                </span>
            </td>
            <td class="actions-cell">
                <button class="btn-edit" onclick="openEditModal(${category.id})">
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
        tableBody.appendChild(row);

        // Создаем карточку для мобильной версии
        if (categoriesCards) {
            const card = document.createElement('div');
            card.className = 'category-card';
            card.id = `category-card-${category.id}`;
            
            card.innerHTML = `
                <div class="category-card-header">
                    <div class="category-main-info">
                        <h3 class="category-name">${category.name}</h3>
                        <span class="status-badge ${category.status ? 'active' : 'inactive'}">
                            ${category.status ? 'Активна' : 'Неактивна'}
                        </span>
                    </div>
                </div>
                <div class="category-info">
                    <div class="category-info-item">
                        <span class="category-info-label">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            Описание
                        </span>
                        <span class="category-info-value">${category.description ?? '—'}</span>
                    </div>
                </div>
                <div class="category-actions">
                    <button class="btn-edit" title="Редактировать" onclick="openEditModal(${category.id})">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Изменить
                    </button>
                    <button class="btn-delete" title="Удалить" onclick="showDeleteConfirmation(${category.id})">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Удалить
                    </button>
                </div>
            `;
            
            categoriesCards.appendChild(card);
        }
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
    let pagContainer = document.getElementById('categoriesPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'categoriesPagination';
        document.querySelector('.table-wrapper').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    // Обновляем мобильную пагинацию
    let mobilePagContainer = document.getElementById('mobileCategoriesPagination');
    if (mobilePagContainer) {
        mobilePagContainer.innerHTML = paginationHtml;
    }

    // Навешиваем обработчики
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadCategories(page, window.currentSearchQuery || '');
            }
        });
    });
}

// Функция для загрузки категорий
function loadCategories(page = 1, search = '') {
    window.currentSearchQuery = search;
    const searchValue = search !== undefined ? search : document.querySelector('#searchInput').value.trim();
    fetch(`/product-categories?search=${encodeURIComponent(searchValue)}&page=${page}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        renderCategories(data.data);
        renderPagination(data.meta);
    })
    .catch(error => {
        console.error('Ошибка при загрузке категорий:', error);
        window.showNotification('error', 'Ошибка при загрузке категорий');
    });
}

// Функция для поиска категорий
function handleSearch() {
    loadCategories(1, document.querySelector('#searchInput').value.trim());
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Настройка обработчиков для кнопок удаления и редактирования
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete')) {
            const row = e.target.closest('tr');
            const card = e.target.closest('.category-card');
            
            let categoryId = null;
            
            if (row && row.id) {
                // Десктопная версия - получаем ID из строки таблицы
                categoryId = row.id.split('-')[1];
            } else if (card && card.id) {
                // Мобильная версия - получаем ID из карточки
                categoryId = card.id.split('-')[2]; // category-card-{id}
            }
            
            if (categoryId) {
                showDeleteConfirmation(categoryId);
            }
        }
        
        // Обработчик для кнопок редактирования
        if (e.target.closest('.btn-edit')) {
            const row = e.target.closest('tr');
            const card = e.target.closest('.category-card');
            
            let categoryId = null;
            
            if (row && row.id) {
                // Десктопная версия - получаем ID из строки таблицы
                categoryId = row.id.split('-')[1];
            } else if (card && card.id) {
                // Мобильная версия - получаем ID из карточки
                categoryId = card.id.split('-')[2]; // category-card-{id}
            }
            
            if (categoryId) {
                openEditModal(categoryId);
            }
        }
    });

    // Обработчики для модального окна подтверждения
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            window.currentDeleteId = null;
        });
    }
    
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (window.currentDeleteId) {
                deleteCategory(window.currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
            window.currentDeleteId = null;
        });
    }

    // Обработчик формы добавления категории
    const addServiceForm = document.getElementById('addServiceForm');
    if (addServiceForm) {
        addServiceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this; // Сохраняем ссылку на форму
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Очищаем предыдущие ошибки
            clearCategoryErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Отправка...';
            submitBtn.disabled = true;

            fetch('/product-categories', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
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
                    // Успешное добавление
                    window.showNotification('success', 'Категория успешно добавлена');
                    closeModal();
                    form.reset();
                    
                    // Перезагружаем данные с правильной пагинацией
                    // Новая категория будет на первой странице (самая новая)
                    loadCategories(1);
                } else {
                    window.showNotification('error', data.message || 'Ошибка при добавлении категории');
                }
            })
            .catch(error => {
                console.log('Ошибка в categories.js:', error);
                if (error.errors) {
                    // Показываем ошибки валидации в форме
                    console.log('Показываем ошибки валидации:', error.errors);
                    showCategoryErrors(error.errors);
                    window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    window.showNotification('error', error.message || 'Ошибка при добавлении категории');
                }
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }

    // Обработчик формы редактирования категории
    const editServiceForm = document.getElementById('editServiceForm');
    if (editServiceForm) {
        editServiceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this; // Сохраняем ссылку на форму
            const categoryId = form.querySelector('#editServiceId').value;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Очищаем предыдущие ошибки
            clearCategoryErrors('editServiceForm');

            submitBtn.innerHTML = '<span class="loader"></span> Отправка...';
            submitBtn.disabled = true;

            fetch(`/product-categories/${categoryId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
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
                    // Успешное редактирование
                    window.showNotification('success', 'Категория успешно обновлена');
                    closeEditModal();
                    
                    // Обновляем данные в таблице без перезагрузки
                    if (data.category) {
                        updateCategoryRow(data.category);
                    }
                } else {
                    window.showNotification('error', data.message || 'Ошибка при обновлении категории');
                }
            })
            .catch(error => {
                console.log('Ошибка редактирования в categories.js:', error);
                if (error.errors) {
                    // Показываем ошибки валидации в форме
                    showCategoryErrors(error.errors, 'editServiceForm');
                    window.showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                } else {
                    window.showNotification('error', error.message || 'Ошибка при обновлении категории');
                }
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }

    // Поиск с пагинацией
    const searchInput = document.querySelector('#searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            loadCategories(1, this.value.trim());
        });
    }

    // Инициализация первой загрузки
    loadCategories(1);
    
    // Переключаем на правильную версию
    toggleMobileView();
});

// Обработчик изменения размера окна
window.addEventListener('resize', function() {
    toggleMobileView();
}); 