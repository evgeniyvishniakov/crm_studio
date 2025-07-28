@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="natification-header" style="display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 24px;">
        <h1 class="mb-0">{{ __('messages.notifications') }}</h1>
        <div class="header-actions">
            <div class="search-box">
                <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                <input type="text" id="searchInput" placeholder="{{ __('messages.search') }}..." onkeyup="handleSearch()">
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
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        
        <!-- Пагинация будет добавлена через JavaScript -->
        <div id="notificationsPagination"></div>
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

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 12px;
    width: 16px;
    height: 16px;
    color: #6b7280;
}

#searchInput {
    padding: 8px 12px 8px 36px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    width: 250px;
    background: #fff;
}

#searchInput:focus {
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
</style>

@push('scripts')
<script>
        // AJAX-пагинация (точно как в товарах)
        let currentPage = 1;
        let searchQuery = '';

        function loadPage(page, search = '') {
            currentPage = page;
            searchQuery = search;
            
            const params = new URLSearchParams();
            if (page > 1) params.append('page', page);
            if (search) params.append('search', search);
            
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
            })
            .catch(error => {
                console.error('Ошибка загрузки уведомлений:', error);
            });
        }

        function updateTable(notifications) {
            const tbody = document.getElementById('notificationsTableBody');
            tbody.innerHTML = '';

            notifications.forEach(notification => {
                const row = document.createElement('tr');
                row.id = `notification-${notification.id}`;
                if (!notification.is_read) {
                    row.style.fontWeight = 'bold';
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
                    <td class="actions-cell">${actionButton}</td>
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
            loadPage(1);
        });
</script>
@endpush 