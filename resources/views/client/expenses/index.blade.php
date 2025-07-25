@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="expenses-container">
        <div class="expenses-header">
            <h1>{{ __('messages.expenses') }}</h1>
            <div id="notification" class="notification">
                <!-- Уведомления будут появляться здесь -->
            </div>
            <div class="header-actions">
                <button class="btn-add-expense" onclick="openExpenseModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    {{ __('messages.add_expense') }}
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" placeholder="{{ __('messages.search') }}..." id="searchInput" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="expenses-list table-wrapper" id="expensesList">
            <table class="table-striped expenses-table" id="expensesTable">
                <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.comment') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th>{{ __('messages.actions') }}</th>
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
                <h2 id="modalTitle">{{ __('messages.add_expense') }}</h2>
                <span class="close" onclick="closeExpenseModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    @csrf
                    <input type="hidden" name="expense_id" id="expenseId">
                    <div class="form-group">
                        <label>{{ __('messages.date') }} *</label>
                        <input type="date" name="date" required class="form-control" data-locale="{{ app()->getLocale() }}"
                               data-locale="{{ app()->getLocale() }}"
                               data-month-names="{{ json_encode([
                                   __('messages.january'), __('messages.february'), __('messages.march'),
                                   __('messages.april'), __('messages.may'), __('messages.june'),
                                   __('messages.july'), __('messages.august'), __('messages.september'),
                                   __('messages.october'), __('messages.november'), __('messages.december')
                               ]) }}"
                               data-day-names="{{ json_encode([
                                   __('messages.sun'), __('messages.mon'), __('messages.tue'),
                                   __('messages.wed'), __('messages.thu'), __('messages.fri'), __('messages.sat')
                               ]) }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.category') }} *</label>
                        <select name="category" required class="form-control">
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.comment') }} *</label>
                        <textarea name="comment" required class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.amount') }} *</label>
                        <input type="number" step="0.01" name="amount" required class="form-control" min="0">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeExpenseModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.delete_confirmation') }}</h2>
                <span class="close" onclick="closeConfirmationModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.confirm_delete_expense') }}</p>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeConfirmationModal()">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn-delete" onclick="deleteExpense()">{{ __('messages.delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
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
            document.getElementById('modalTitle').textContent = '{{ __('messages.add_expense') }}';
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
                window.showNotification('{{ __('messages.error_loading_data') }}', 'error');
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
            document.getElementById('modalTitle').textContent = '{{ __('messages.edit_expense') }}';
            document.getElementById('expenseId').value = id;
            document.getElementById('expenseModal').style.display = 'block';
            
            // Устанавливаем данные из таблицы
            const dateInput = document.querySelector('#expenseForm [name="date"]');
            const categoryInput = document.querySelector('#expenseForm [name="category"]');
            const commentInput = document.querySelector('#expenseForm [name="comment"]');
            const amountInput = document.querySelector('#expenseForm [name="amount"]');

            dateInput.value = dateValue;
            categoryInput.value = category === '{{ __('messages.not_specified') }}' ? '' : category;
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
                    window.showNotification('success', '{{ __('messages.expense_deleted') }}');
                    // Перезагружаем текущую страницу
                    loadExpenses(currentPage);
                } else {
                    window.showNotification('error', data.message || '{{ __('messages.error_deleting_expense') }}');
                }
            } catch (error) {
                window.showNotification('error', '{{ __('messages.error_deleting_expense') }}');
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
                window.showNotification('success', data.message || '{{ __('messages.expense_saved') }}');
                document.getElementById('expenseForm').reset();
                closeExpenseModal();
                // Перезагружаем текущую страницу для отображения изменений
                loadExpenses(currentPage);
            } else {
                window.showNotification('error', data.message || '{{ __('messages.error_saving_expense') }}');
            }
        });



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
                    <td>${escapeHtml(expense.category || '{{ __('messages.not_specified') }}')}</td>
                    <td>${escapeHtml(expense.comment || '')}</td>
                    <td class="currency-amount" data-amount="${expense.amount}">${formatCurrency(expense.amount)}</td>
                    <td>
                        <div class="expense-actions">
                            <button class="btn-edit" onclick="editExpense(event, ${expense.id})" title="{{ __('messages.edit') }}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                {{ __('messages.edit_short') }}
                            </button>
                            <button class="btn-delete" onclick="confirmDeleteExpense(event, ${expense.id})" title="{{ __('messages.delete') }}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.delete') }}
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
                    window.showNotification('{{ __('messages.error_loading_data') }}', 'error');
                }
            })
            .catch(error => {
                window.showNotification('{{ __('messages.error_loading_data') }}', 'error');
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
