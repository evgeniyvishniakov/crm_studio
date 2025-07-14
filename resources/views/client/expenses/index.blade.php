@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="expenses-container">
        <div class="expenses-header">
            <h1>Расходы</h1>
            <div id="notification" class="notification">
                <!-- Уведомления будут появляться здесь -->
            </div>
            <div class="header-actions">
                <button class="btn-add-expense" onclick="openExpenseModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить расход
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" placeholder="Поиск..." id="searchInput" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="expenses-list table-wrapper" id="expensesList">
            <table class="table-striped expenses-table" id="expensesTable">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Комментарий</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody id="expensesTableBody">
                <!-- Данные будут загружаться через AJAX -->
                </tbody>
            </table>
        </div>
        <div id="expensesPagination"></div>
    </div>

    <!-- Модальное окно добавления/редактирования расхода -->
    <div id="expenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Добавить расход</h2>
                <span class="close" onclick="closeExpenseModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    @csrf
                    <input type="hidden" name="expense_id" id="expenseId">
                    <div class="form-group">
                        <label>Дата *</label>
                        <input type="date" name="date" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Комментарий *</label>
                        <textarea name="comment" required class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Сумма *</label>
                        <input type="number" step="0.01" name="amount" required class="form-control" min="0">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeExpenseModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Подтверждение удаления</h2>
                <span class="close" onclick="closeConfirmationModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этот расход?</p>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeConfirmationModal()">Отмена</button>
                    <button type="button" class="btn-delete" onclick="deleteExpense()">Удалить</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentExpenseId = null;

        // Функции для работы с модальными окнами
        function openExpenseModal() {
            document.getElementById('modalTitle').textContent = 'Добавить расход';
            document.getElementById('expenseId').value = '';
            document.getElementById('expenseForm').reset();
            // Устанавливаем текущую дату
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('#expenseForm [name="date"]').value = today;
            document.getElementById('expenseModal').style.display = 'block';
        }

        function closeExpenseModal() {
            document.getElementById('expenseModal').style.display = 'none';
            document.getElementById('expenseForm').reset();
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
        async function editExpense(event, id) {
            event.preventDefault();
            currentExpenseId = id;

            try {
                const response = await fetch(`/expenses/${id}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const expense = data.expense;
                    document.getElementById('modalTitle').textContent = 'Редактировать расход';
                    document.getElementById('expenseId').value = expense.id;
                    document.querySelector('#expenseForm [name="date"]').value = expense.date;
                    document.querySelector('#expenseForm [name="comment"]').value = expense.comment;
                    document.querySelector('#expenseForm [name="amount"]').value = expense.amount;
                    document.getElementById('expenseModal').style.display = 'block';
                } else {
                    showNotification(data.message || 'Ошибка загрузки данных', 'error');
                }
            } catch (error) {
                showNotification('Ошибка при загрузке данных', 'error');
            }
        }

        function confirmDeleteExpense(event, id) {
            event.preventDefault();
            currentExpenseId = id;
            openConfirmationModal();
        }

        async function deleteExpense() {
            if (!currentExpenseId) return;

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
                    showNotification('Расход успешно удален');
                    // Перезагружаем текущую страницу
                    loadExpenses(currentPage);
                } else {
                    showNotification(data.message || 'Ошибка при удалении', 'error');
                }
            } catch (error) {
                showNotification('Ошибка при удалении', 'error');
            }

            closeConfirmationModal();
        }

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
                showNotification(data.message || 'Расход успешно сохранен');
                closeExpenseModal();
                // Перезагружаем текущую страницу для отображения изменений
                loadExpenses(currentPage);
            } else {
                showNotification(data.message || 'Ошибка при сохранении', 'error');
            }
        });



        // Вспомогательные функции
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            if (!notification) return;

            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';

            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

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
                const formattedAmount = amount % 1 === 0 ? amount.toString() : amount.toFixed(2);
                
                row.innerHTML = `
                    <td>${expense.date ? new Date(expense.date).toLocaleDateString('ru-RU') : '—'}</td>
                    <td>${escapeHtml(expense.comment || '')}</td>
                    <td>${formattedAmount} грн</td>
                    <td>
                        <div class="expense-actions">
                            <button class="btn-edit" onclick="editExpense(event, ${expense.id})" title="Редактировать">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Ред.
                            </button>
                            <button class="btn-delete" onclick="confirmDeleteExpense(event, ${expense.id})" title="Удалить">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Удалить
                            </button>
                        </div>
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
            fetch(`/expenses?search=${encodeURIComponent(searchValue)}&page=${page}`, {
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
                    renderPagination(data.meta);
                } else {
                    showNotification('Ошибка при загрузке данных', 'error');
                }
            })
            .catch(error => {
                showNotification('Ошибка при загрузке данных', 'error');
            });
        }

        // Поиск с пагинацией
        document.getElementById('searchInput').addEventListener('input', function() {
            loadExpenses(1, this.value.trim());
        });

        // Инициализация первой загрузки
        loadExpenses(1);

    </script>
</div>
@endsection 
