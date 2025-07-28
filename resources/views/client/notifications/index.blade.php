@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="natification-header" style="display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 24px;">
        <h1 class="mb-0">{{ __('messages.notifications') }}</h1>
        <div class="header-actions">
            <button id="markAllReadBtn" class="btn-add-product" onclick="markAllAsRead()" style="display: none;">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                {{ __('messages.mark_all_as_read') }}
            </button>
            
            <!-- Фильтры -->
            <div class="filters-row" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                <div class="filter-group">
                    <select id="typeFilter" onchange="applyFilters()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: #fff; min-width: 150px;">
                        <option value="">{{ __('messages.all_types') }}</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}">
                                @if($type === 'web_booking')
                                    {{ __('messages.web_booking') }}
                                @else
                                    {{ ucfirst($type) }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="statusFilter" onchange="applyFilters()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: #fff; min-width: 150px;">
                        <option value="">{{ __('messages.all_statuses') }}</option>
                        <option value="unread">{{ __('messages.unread') }}</option>
                        <option value="read">{{ __('messages.read') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="natification-table table-striped">
            <thead>
                <tr>
                    <th>{{ __('messages.type') }}</th>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody id="notificationsTableBody">
            @foreach($notifications as $notification)
                <tr id="notification-{{ $notification->id }}" @if(!$notification->is_read) style="font-weight:bold;" @endif>
                    <td>
                        <div class="client-info">
                            <div class="client-details">
                                <div class="client-name">
                                    @if($notification->type === 'web_booking')
                                        {{ __('messages.web_booking') }}
                                    @else
                                        {{ ucfirst($notification->type) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $notification->title }}</td>
                    <td>{{ $notification->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($notification->is_read)
                            <span class="badge status-success">{{ __('messages.read') }}</span>
                        @else
                            <span class="badge status-warning text-dark">{{ __('messages.unread') }}</span>
                        @endif
                    </td>
                    <td class="actions-cell">
                        @if(!$notification->is_read && $notification->url)
                            <form method="POST" action="{{ route('client.notifications.read', $notification->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-add-client btn-sm">{{ __('messages.open') }}</button>
                            </form>
                        @endif
                        <button class="btn-delete" title="{{ __('messages.delete') }}" data-notification-id="{{ $notification->id }}">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        
        <!-- Пагинация будет добавлена через JavaScript -->
        <div id="notificationsPagination"></div>
    </div>
</div>

<!-- Модальное окно подтверждения -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-content">
        <h3 id="confirmationTitle">{{ __('messages.confirmation') }}</h3>
        <p id="confirmationMessage">{{ __('messages.confirm_mark_all_as_read') }}</p>
        <div class="confirmation-buttons">
            <button id="cancelAction" class="cancel-btn">{{ __('messages.cancel') }}</button>
            <button id="confirmAction" class="confirm-btn">{{ __('messages.confirm') }}</button>
        </div>
    </div>
</div>
@endsection 

<style>
.natification-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 24px;
}

.header-actions {
    display: flex;
    gap: 16px;
    align-items: center;
}

.filter-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Стили для пагинации (точно как в товарах) */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 20px;
    padding: 20px 0;
}

.page-btn {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    background: #fff;
    color: #374151;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s ease;
    min-width: 40px;
    text-align: center;
}

.page-btn:hover:not(:disabled) {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.page-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-ellipsis {
    padding: 8px 12px;
    color: #6b7280;
    font-size: 14px;
}

/* Стили для модального окна подтверждения */
.confirmation-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.confirmation-modal .confirmation-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    border-radius: 8px;
    text-align: center;
}

.confirmation-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.confirm-btn {
    background-color: #dc3545;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.confirm-btn:hover {
    background-color: #c82333;
}

.cancel-btn {
    background-color: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.cancel-btn:hover {
    background-color: #5a6268;
}
</style>

@push('scripts')
<script>
        // AJAX-пагинация с фильтрами (без поиска)
        let currentPage = 1;
        let typeFilter = '';
        let statusFilter = '';
        let currentAction = null;
        let currentNotificationId = null;

        function loadPage(page, type = '', status = '') {
            currentPage = page;
            typeFilter = type;
            statusFilter = status;
            
            const params = new URLSearchParams();
            if (page > 1) params.append('page', page);
            if (type) params.append('type', type);
            if (status) params.append('status', status);
            
            fetch(`{{ route('client.notifications.index') }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('{{ __('messages.error_loading_data') }}');
                }
                return response.json();
            })
            .then(data => {
                updateTable(data.data);
                renderPagination(data.meta);
                updateTypeFilter(data.types);
            })
            .catch(error => {
                console.error('Ошибка загрузки уведомлений:', error);
            });
        }

        function updateTable(notifications) {
            const tbody = document.getElementById('notificationsTableBody');
            tbody.innerHTML = '';

            let hasUnreadNotifications = false;

            notifications.forEach(notification => {
                const row = document.createElement('tr');
                row.id = `notification-${notification.id}`;
                if (!notification.is_read) {
                    row.style.fontWeight = 'bold';
                    hasUnreadNotifications = true;
                }
                
                const statusBadge = notification.is_read 
                    ? '<span class="badge status-success">{{ __('messages.read') }}</span>'
                    : '<span class="badge status-warning text-dark">{{ __('messages.unread') }}</span>';
                    
                const actionButton = (!notification.is_read && notification.url) 
                    ? `<form method="POST" action="{{ route('client.notifications.read', '') }}/${notification.id}" style="display:inline;">
                         @csrf
                         <button type="submit" class="btn-add-client btn-sm">{{ __('messages.open') }}</button>
                       </form>`
                    : '';
                
                // Определяем правильное отображение типа уведомления
                let typeDisplay = notification.type.charAt(0).toUpperCase() + notification.type.slice(1);
                if (notification.type === 'web_booking') {
                    typeDisplay = '{{ __('messages.web_booking') }}';
                }
                
                row.innerHTML = `
                    <td>
                        <div class="client-info">
                            <div class="client-details">
                                <div class="client-name">${typeDisplay}</div>
                            </div>
                        </div>
                    </td>
                    <td>${notification.title}</td>
                    <td>${formatDate(notification.created_at)}</td>
                    <td>${statusBadge}</td>
                    <td class="actions-cell">
                        ${actionButton}
                        <button class="btn-delete" title="{{ __('messages.delete') }}" data-notification-id="${notification.id}">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });

            // Показываем или скрываем кнопку в зависимости от наличия непрочитанных уведомлений
            updateMarkAllReadButton(hasUnreadNotifications);
        }

        function updateTypeFilter(types) {
            const typeFilter = document.getElementById('typeFilter');
            const currentValue = typeFilter.value;
            
            // Сохраняем текущий выбор
            typeFilter.innerHTML = '<option value="">{{ __('messages.all_types') }}</option>';
            
            types.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                if (type === 'web_booking') {
                    option.textContent = '{{ __('messages.web_booking') }}';
                } else {
                    option.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                }
                if (type === currentValue) {
                    option.selected = true;
                }
                typeFilter.appendChild(option);
            });
        }

        function updateMarkAllReadButton(hasUnreadNotifications) {
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            if (hasUnreadNotifications) {
                markAllReadBtn.style.display = 'flex';
            } else {
                markAllReadBtn.style.display = 'none';
            }
        }

        function checkUnreadNotifications() {
            // Проверяем непрочитанные уведомления в текущем списке
            const unreadRows = document.querySelectorAll('tr[id^="notification-"]:not([style*="font-weight:normal"])');
            const hasUnread = unreadRows.length > 0;
            updateMarkAllReadButton(hasUnread);
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
            let pagContainer = document.getElementById('notificationsPagination');
            if (!pagContainer) {
                pagContainer = document.createElement('div');
                pagContainer.id = 'notificationsPagination';
                document.querySelector('.table-wrapper').appendChild(pagContainer);
            }
            pagContainer.innerHTML = paginationHtml;

            // Навешиваем обработчики
            document.querySelectorAll('.page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (!isNaN(page) && !this.disabled) {
                        loadPage(page, typeFilter, statusFilter);
                    }
                });
            });
        }

        function applyFilters() {
            const typeFilterEl = document.getElementById('typeFilter');
            const statusFilterEl = document.getElementById('statusFilter');
            
            // Сбрасываем на первую страницу при изменении фильтров
            loadPage(1, typeFilterEl.value, statusFilterEl.value);
        }

        function markAllAsRead() {
            // Показываем модальное окно подтверждения для отметки всех как прочитанных
            currentAction = 'markAllAsRead';
            document.getElementById('confirmationTitle').textContent = '{{ __('messages.confirmation') }}';
            document.getElementById('confirmationMessage').textContent = '{{ __('messages.confirm_mark_all_as_read') }}';
            document.getElementById('confirmationModal').style.display = 'block';
        }

        function deleteNotification(notificationId) {
            // Показываем модальное окно подтверждения для удаления
            currentAction = 'deleteNotification';
            currentNotificationId = notificationId;
            document.getElementById('confirmationTitle').textContent = '{{ __('messages.confirmation_delete') }}';
            document.getElementById('confirmationMessage').textContent = '{{ __('messages.are_you_sure_you_want_to_delete_this_notification') }}';
            document.getElementById('confirmationModal').style.display = 'block';
        }

        function performMarkAllAsRead() {
            const formData = new FormData();
            if (typeFilter) formData.append('type', typeFilter);
            if (statusFilter) formData.append('status', statusFilter);

            fetch('{{ route('client.notifications.mark-all-read') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Перезагружаем текущую страницу
                    loadPage(currentPage, typeFilter, statusFilter);
                }
            })
            .catch(error => {
                console.error('Ошибка при отметке всех как прочитанных:', error);
            });
        }

        function performDeleteNotification() {
            fetch(`{{ route('client.notifications.destroy', '') }}/${currentNotificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Перезагружаем текущую страницу
                    loadPage(currentPage, typeFilter, statusFilter);
                }
            })
            .catch(error => {
                console.error('Ошибка при удалении уведомления:', error);
            });
        }

        // Функция форматирования даты
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${day}.${month}.${year} ${hours}:${minutes}`;
        }

        // Инициализация первой загрузки
        document.addEventListener('DOMContentLoaded', function() {
            // Проверяем непрочитанные уведомления при загрузке страницы
            checkUnreadNotifications();
            loadPage(1);
            
            // Обработчики для модального окна подтверждения
            document.getElementById('confirmAction').addEventListener('click', function() {
                if (currentAction === 'markAllAsRead') {
                    performMarkAllAsRead();
                } else if (currentAction === 'deleteNotification') {
                    performDeleteNotification();
                }
                document.getElementById('confirmationModal').style.display = 'none';
                currentAction = null;
                currentNotificationId = null;
            });

            document.getElementById('cancelAction').addEventListener('click', function() {
                document.getElementById('confirmationModal').style.display = 'none';
                currentAction = null;
                currentNotificationId = null;
            });

            // Закрытие модального окна при клике вне его
            window.onclick = function(event) {
                if (event.target == document.getElementById('confirmationModal')) {
                    document.getElementById('confirmationModal').style.display = 'none';
                    currentAction = null;
                    currentNotificationId = null;
                }
            }

            // Обработчик для кнопок удаления
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-delete')) {
                    const notificationId = e.target.closest('.btn-delete').getAttribute('data-notification-id');
                    deleteNotification(notificationId);
                }
            });
        });
</script>
@endpush 