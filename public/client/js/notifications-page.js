// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ УВЕДОМЛЕНИЙ =====

// Функция для переключения модальных окон
function toggleModal(modalId, show = true) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = show ? 'block' : 'none';
    }
}

// AJAX-пагинация с фильтрами (без поиска)
let currentPage = 1;
let typeFilter = '';
let statusFilter = '';
let currentAction = null;
let currentNotificationId = null;

// Функция для переключения между десктопной и мобильной версией
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const notificationsCards = document.getElementById('notificationsCards');
    const notificationsPagination = document.getElementById('notificationsPagination');
    const mobileNotificationsPagination = document.getElementById('mobileNotificationsPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (notificationsCards) notificationsCards.style.display = 'block';
        if (notificationsPagination) notificationsPagination.style.display = 'none';
        if (mobileNotificationsPagination) mobileNotificationsPagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (notificationsCards) notificationsCards.style.display = 'none';
        if (notificationsPagination) notificationsPagination.style.display = 'block';
        if (mobileNotificationsPagination) mobileNotificationsPagination.style.display = 'none';
    }
}

function loadPage(page, type = '', status = '') {
    currentPage = page;
    typeFilter = type;
    statusFilter = status;
    
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (type) params.append('type', type);
    if (status) params.append('status', status);
    
    fetch(`/notifications?${params.toString()}`, {
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
        updateTable(data.data);
        updateMobileCards(data.data);
        renderPagination(data.meta);
        renderMobilePagination(data.meta);
        updateTypeFilter(data.types);
    })
    .catch(error => {
        // Ошибка загрузки уведомлений
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
            ? '<span class="badge status-success">Прочитано</span>'
            : '<span class="badge status-warning text-dark">Не прочитано</span>';
            
        const actionButton = (!notification.is_read && notification.url) 
            ? `<form method="POST" action="/notifications/${notification.id}/read" style="display:inline;">
                 <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                 <button type="submit" class="btn-add-client btn-sm">Открыт</button>
               </form>`
            : '';
        
        // Определяем правильное отображение типа уведомления
        let typeDisplay = notification.type.charAt(0).toUpperCase() + notification.type.slice(1);
        if (notification.type === 'web_booking') {
            typeDisplay = 'Запись';
        } else if (notification.type === 'ticket') {
            typeDisplay = 'Сообщение';
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
                <button class="btn-delete" title="Удалить" data-notification-id="${notification.id}">
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

function updateMobileCards(notifications) {
    const cardsContainer = document.getElementById('notificationsCards');
    cardsContainer.innerHTML = '';

    let hasUnreadNotifications = false;

    notifications.forEach(notification => {
        if (!notification.is_read) {
            hasUnreadNotifications = true;
        }

        const card = document.createElement('div');
        card.className = 'notification-card';
        card.id = `notification-card-${notification.id}`;
        if (!notification.is_read) {
            card.style.fontWeight = 'bold';
        }

        // Определяем правильное отображение типа уведомления
        let typeDisplay = notification.type.charAt(0).toUpperCase() + notification.type.slice(1);
        if (notification.type === 'web_booking') {
            typeDisplay = 'Запись';
        } else if (notification.type === 'ticket') {
            typeDisplay = 'Сообщение';
        }

        const statusBadge = notification.is_read 
            ? '<span class="status-badge read">Прочитано</span>'
            : '<span class="status-badge unread">Не прочитано</span>';

        const actionButton = (!notification.is_read && notification.url) 
            ? `<form method="POST" action="/notifications/${notification.id}/read" style="display:inline;">
                 <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                 <button type="submit" class="btn-open">
                     <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                         <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                     </svg>
                     Открыт
                 </button>
               </form>`
            : '';

        card.innerHTML = `
            <div class="notification-card-header">
                <div class="notification-main-info">
                    <div class="notification-content">
                        <h3 class="notification-title">${notification.title}</h3>
                        <div class="notification-type">${typeDisplay}</div>
                    </div>
                    <div class="notification-status">
                        ${statusBadge}
                    </div>
                </div>
            </div>
            <div class="notification-info">
                <div class="notification-info-item">
                    <span class="notification-info-label">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Дата
                    </span>
                    <span class="notification-info-value">${formatDate(notification.created_at)}</span>
                </div>
            </div>
            <div class="notification-actions">
                ${actionButton}
                <button class="btn-delete" data-notification-id="${notification.id}">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Удалить
                </button>
            </div>
        `;

        cardsContainer.appendChild(card);
    });

    // Показываем или скрываем кнопку в зависимости от наличия непрочитанных уведомлений
    updateMarkAllReadButton(hasUnreadNotifications);
}

function updateTypeFilter(types) {
    const typeFilter = document.getElementById('typeFilter');
    const currentValue = typeFilter.value;
    
    // Сохраняем текущий выбор
    typeFilter.innerHTML = '<option value="">Все типы</option>';
    
    types.forEach(type => {
        const option = document.createElement('option');
        option.value = type;
        if (type === 'web_booking') {
            option.textContent = 'Запись';
        } else if (type === 'ticket') {
            option.textContent = 'Сообщение';
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
    let pagContainer = document.getElementById('mobileNotificationsPagination');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'mobileNotificationsPagination';
        document.querySelector('.dashboard-container').appendChild(pagContainer);
    }
    pagContainer.innerHTML = paginationHtml;

    // Навешиваем обработчики
    document.querySelectorAll('#mobileNotificationsPagination .page-btn').forEach(btn => {
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
    document.getElementById('confirmationTitle').textContent = 'Подтверждение';
    document.getElementById('confirmationMessage').textContent = 'Вы уверены, что хотите отметить все уведомления как прочитанные?';
    toggleModal('confirmationModal');
}

function deleteNotification(notificationId) {
    // Показываем модальное окно подтверждения для удаления
    currentAction = 'deleteNotification';
    currentNotificationId = notificationId;
    document.getElementById('confirmationTitle').textContent = 'Подтверждение удаления';
    document.getElementById('confirmationMessage').textContent = 'Вы уверены, что хотите удалить это уведомление?';
    toggleModal('confirmationModal');
}

function closeConfirmationModal() {
    toggleModal('confirmationModal', false);
    currentAction = null;
    currentNotificationId = null;
}

function performMarkAllAsRead() {
    const formData = new FormData();
    if (typeFilter) formData.append('type', typeFilter);
    if (statusFilter) formData.append('status', statusFilter);

    fetch('/notifications/mark-all-read', {
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
            // Закрываем модальное окно только после успешного выполнения
            closeConfirmationModal();
            // Перезагружаем текущую страницу
            loadPage(currentPage, typeFilter, statusFilter);
        }
    })
    .catch(error => {
        // Ошибка при отметке всех как прочитанных
    });
}

function performDeleteNotification() {
    fetch(`/notifications/${currentNotificationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Закрываем модальное окно только после успешного выполнения
            closeConfirmationModal();
            // Перезагружаем текущую страницу
            loadPage(currentPage, typeFilter, statusFilter);
        }
    })
    .catch(error => {
        // Ошибка при удалении уведомления
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

// ===== ИНИЦИАЛИЗАЦИЯ =====
document.addEventListener('DOMContentLoaded', function() {
    // Проверяем непрочитанные уведомления при загрузке страницы
    checkUnreadNotifications();
    loadPage(1);
    toggleMobileView(); // Переключаем на правильную версию
    
    // Обработчики для модального окна подтверждения
    document.getElementById('confirmAction').addEventListener('click', function() {
        if (currentAction === 'markAllAsRead') {
            performMarkAllAsRead();
        } else if (currentAction === 'deleteNotification') {
            performDeleteNotification();
        }
        // Модальное окно закроется только после успешного выполнения действия
    });

    document.getElementById('cancelAction').addEventListener('click', function() {
        closeConfirmationModal();
    });

    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        if (event.target == document.getElementById('confirmationModal')) {
            closeConfirmationModal();
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

// Обработчик изменения размера окна
window.addEventListener('resize', function() {
    toggleMobileView();
}); 