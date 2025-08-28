// ===== РАСХОДЫ =====

let currentExpenseId = null;

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

// Функции для работы с модальными окнами
function openExpenseModal() {
    document.getElementById('modalTitle').textContent = 'Добавить расход';
    document.getElementById('expenseId').value = '';
    document.getElementById('expenseForm').reset();
    document.getElementById('expenseModal').style.display = 'block';
    
    // Устанавливаем сегодняшнюю дату в поле даты только для добавления
    const dateInput = document.querySelector('#expenseForm [name="date"]');
    if (dateInput && typeof setTodayDate === 'function') {
        setTodayDate(dateInput);
    }
}

function closeExpenseModal() {
    document.getElementById('expenseModal').style.display = 'none';
    // Не сбрасываем форму при закрытии, чтобы сохранить данные для редактирования
    currentExpenseId = null;
}

function openConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'block';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    currentExpenseId = null;
}

// Функции для работы с расходами
function editExpense(event, id) {
    event.preventDefault();
    currentExpenseId = id;

    // Находим строку таблицы с данным ID
    const row = document.querySelector(`tr[data-expense-id="${id}"]`);
    if (!row) {
        window.showNotification('Ошибка загрузки данных', 'error');
        return;
    }

    // Получаем данные из ячеек таблицы
    const cells = row.querySelectorAll('td');
    const dateCell = cells[0];
    const categoryCell = cells[1];
    const commentCell = cells[2];
    const amountCell = cells[3];

    // Извлекаем данные
    const dateText = dateCell.textContent.trim();
    const category = categoryCell.textContent.trim();
    const comment = commentCell.textContent.trim();
    const amount = amountCell.getAttribute('data-amount');

    // Конвертируем дату из формата DD.MM.YYYY в YYYY-MM-DD
    let dateValue = '';
    if (dateText && dateText !== '—') {
        const dateParts = dateText.split('.');
        if (dateParts.length === 3) {
            dateValue = `${dateParts[2]}-${dateParts[1].padStart(2, '0')}-${dateParts[0].padStart(2, '0')}`;
        }
    }

    // Открываем модальное окно
    document.getElementById('modalTitle').textContent = 'Редактировать расход';
    document.getElementById('expenseId').value = id;
    document.getElementById('expenseModal').style.display = 'block';
    
    // Устанавливаем данные из таблицы
    const dateInput = document.querySelector('#expenseForm [name="date"]');
    const categoryInput = document.querySelector('#expenseForm [name="category"]');
    const commentInput = document.querySelector('#expenseForm [name="comment"]');
    const amountInput = document.querySelector('#expenseForm [name="amount"]');

    dateInput.value = dateValue;
    categoryInput.value = category === 'Не указано' ? '' : category;
    commentInput.value = comment;
    amountInput.value = amount;

    // Принудительно обновляем поле даты
    dateInput.setAttribute('value', dateValue);
    
    // Если используется Flatpickr, обновляем через его API
    if (dateInput.hasAttribute('data-flatpickr-initialized')) {
        const flatpickrInstance = dateInput._flatpickr;
        if (flatpickrInstance) {
            flatpickrInstance.setDate(dateValue, false);
        }
    }
    
    // Принудительно обновляем визуальное отображение
    dateInput.dispatchEvent(new Event('input', { bubbles: true }));
    dateInput.dispatchEvent(new Event('change', { bubbles: true }));
}

// Функция для обновления карточки расхода
function updateExpenseCard(expense) {
    const card = document.getElementById(`expense-card-${expense.id}`);
    if (!card) return;

    // Обновляем категорию
    const categoryElement = card.querySelector('.expense-category');
    if (categoryElement) {
        categoryElement.textContent = expense.category || 'Не указано';
    }

    // Обновляем сумму
    const amountElement = card.querySelector('.expense-amount');
    if (amountElement) {
        amountElement.textContent = formatCurrency(expense.amount);
    }

    // Обновляем дату
    const dateElement = card.querySelector('.expense-info-value');
    if (dateElement) {
        dateElement.textContent = expense.date ? new Date(expense.date).toLocaleDateString('ru-RU') : '—';
    }

    // Обновляем комментарий
    const commentElements = card.querySelectorAll('.expense-info-value');
    if (commentElements.length > 1) {
        commentElements[1].textContent = expense.comment || '—';
    }
}

function confirmDeleteExpense(event, id) {
    event.preventDefault();
    currentExpenseId = id;
    openConfirmationModal();
}

async function deleteExpense() {
    if (!currentExpenseId) return;

    // Добавляем анимацию удаления для карточки
    const card = document.getElementById(`expense-card-${currentExpenseId}`);
    if (card) {
        card.classList.add('row-deleting');
    }

    try {
        const response = await fetch(`/expenses/${currentExpenseId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            window.showNotification('success', 'Расход удален');
            // Перезагружаем текущую страницу
            loadExpenses(currentPage);
        } else {
            window.showNotification('error', data.message || 'Ошибка удаления расхода');
            // Убираем анимацию удаления если произошла ошибка
            if (card) {
                card.classList.remove('row-deleting');
            }
        }
    } catch (error) {
        window.showNotification('error', 'Ошибка удаления расхода');
        // Убираем анимацию удаления если произошла ошибка
        if (card) {
            card.classList.remove('row-deleting');
        }
    }

    closeConfirmationModal();
}

// Вспомогательные функции
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// --- AJAX пагинация ---
let currentPage = 1;

function renderExpenses(expenses) {
    const tbody = document.getElementById('expensesTableBody');
    tbody.innerHTML = '';
    
    // Если нет расходов, не делаем ничего
    if (!expenses || expenses.length === 0) {
        return;
    }
    
    expenses.forEach(expense => {
        const row = document.createElement('tr');
        row.setAttribute('data-expense-id', expense.id);
        
        const amount = parseFloat(expense.amount);
        row.innerHTML = `
            <td>${expense.date ? new Date(expense.date).toLocaleDateString('ru-RU') : '—'}</td>
            <td>${escapeHtml(expense.category || 'Не указано')}</td>
            <td>${escapeHtml(expense.comment || '')}</td>
            <td class="currency-amount" data-amount="${Number.isInteger(parseFloat(expense.amount)) ? parseInt(expense.amount) : parseFloat(expense.amount)}">${formatCurrency(expense.amount)}</td>
            <td>
                <div class="expense-actions">
                                         <button class="btn-edit" onclick="editExpense(event, ${expense.id})" title="${window.translations?.edit || 'Редактировать'}">
                         <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                             <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                         </svg>
                         ${window.translations?.edit_short || 'Ред.'}
                     </button>
                    <button class="btn-delete" onclick="confirmDeleteExpense(event, ${expense.id})" title="${window.translations?.delete || 'Удалить'}">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        ${window.translations?.delete || 'Удалить'}
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updateMobileCards(expenses) {
    const cardsContainer = document.getElementById('expensesCards');
    cardsContainer.innerHTML = '';

    if (!expenses || expenses.length === 0) {
        return;
    }

    expenses.forEach(expense => {
        const card = document.createElement('div');
        card.className = 'expense-card';
        card.id = `expense-card-${expense.id}`;
        
        card.innerHTML = `
            <div class="expense-card-header">
                <div class="expense-main-info">
                    <div class="expense-category">${escapeHtml(expense.category || 'Не указано')}</div>
                    <div class="expense-amount">${formatCurrency(expense.amount)}</div>
                </div>
            </div>
            <div class="expense-info">
                <div class="expense-info-item">
                    <span class="expense-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Дата
                    </span>
                    <span class="expense-info-value">${expense.date ? new Date(expense.date).toLocaleDateString('ru-RU') : '—'}</span>
                </div>
                <div class="expense-info-item">
                    <span class="expense-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        Комментарий
                    </span>
                    <span class="expense-info-value">${escapeHtml(expense.comment || '—')}</span>
                </div>
            </div>
            <div class="expense-actions">
                                 <button class="btn-edit" onclick="editExpense(event, ${expense.id})">
                     <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                         <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                     </svg>
                     ${window.translations?.edit || 'Редактировать'}
                 </button>
                <button class="btn-delete" onclick="confirmDeleteExpense(event, ${expense.id})">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    ${window.translations?.delete || 'Удалить'}
                </button>
            </div>
        `;
        
        cardsContainer.appendChild(card);
    });
}

function renderMobilePagination(meta) {
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
    
    let mobilePagContainer = document.getElementById('mobileExpensesPagination');
    if (mobilePagContainer) {
        mobilePagContainer.innerHTML = paginationHtml;
    }

    // Навешиваем обработчики для мобильной пагинации
    mobilePagContainer.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadExpenses(page);
            }
        });
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
    let pagContainer = document.getElementById('expensesPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'expensesPagination';
        document.querySelector('.expenses-container').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    // Навешиваем обработчики
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            if (!isNaN(page) && !this.disabled) {
                loadExpenses(page);
            }
        });
    });
}

function loadExpenses(page = 1, search = '') {
    currentPage = page;
    const searchValue = search !== undefined ? search : document.getElementById('searchInput').value.trim();
    const url = `/expenses?search=${encodeURIComponent(searchValue)}&page=${page}`;
    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.data && data.meta) {
            renderExpenses(data.data);
            updateMobileCards(data.data);
            renderPagination(data.meta);
            renderMobilePagination(data.meta);
        } else {
            window.showNotification('Ошибка загрузки данных', 'error');
        }
    })
    .catch(error => {
        window.showNotification('Ошибка загрузки данных', 'error');
    });
}

// Функция для переключения между десктопной и мобильной версией
function toggleMobileView() {
    const tableWrapper = document.getElementById('expensesList');
    const expensesCards = document.getElementById('expensesCards');
    const expensesPagination = document.getElementById('expensesPagination');
    const mobileExpensesPagination = document.getElementById('mobileExpensesPagination');

    if (window.innerWidth <= 768) {
        // Мобильная версия
        tableWrapper.style.display = 'none';
        expensesCards.style.display = 'block';
        expensesPagination.style.display = 'none';
        mobileExpensesPagination.style.display = 'block';
    } else {
        // Десктопная версия
        tableWrapper.style.display = 'block';
        expensesCards.style.display = 'none';
        expensesPagination.style.display = 'block';
        mobileExpensesPagination.style.display = 'none';
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик формы
    document.getElementById('expenseForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const expenseId = document.getElementById('expenseId').value;
        const url = expenseId ? `/expenses/${expenseId}` : '/expenses';
        // ВСЕГДА отправляем через POST, для обновления добавляем _method=PUT
        if (expenseId) {
            formData.append('_method', 'PUT');
        }
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            window.showNotification('success', data.message || 'Расход сохранен');
            document.getElementById('expenseForm').reset();
            closeExpenseModal();
            // Перезагружаем первую страницу, так как новый расход должен быть в начале списка
            loadExpenses(1);
        } else {
            window.showNotification('error', data.message || 'Ошибка сохранения расхода');
        }
    });

    // Поиск с пагинацией
    document.getElementById('searchInput').addEventListener('input', function() {
        loadExpenses(1, this.value.trim());
    });

    toggleMobileView();
    loadExpenses(1);
});

// Обработчик изменения размера окна
window.addEventListener('resize', toggleMobileView); 