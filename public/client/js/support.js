// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ ПОДДЕРЖКИ =====

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ ПОДДЕРЖКИ =====

/**
 * Переключение между мобильной и десктопной версией
 */
function toggleMobileView() {
    const tableWrapper = document.querySelector('.table-wrapper');
    const ticketsCards = document.getElementById('ticketsCards');
    const mobilePagination = document.getElementById('mobileTicketsPagination');
    
    if (window.innerWidth <= 768) {
        // Мобильная версия
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (ticketsCards) ticketsCards.style.display = 'block';
        if (mobilePagination) mobilePagination.style.display = 'block';
    } else {
        // Десктопная версия
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (ticketsCards) ticketsCards.style.display = 'none';
        if (mobilePagination) mobilePagination.style.display = 'none';
    }
}

/**
 * Открытие модального окна создания тикета
 */
function openTicketModal() {
    document.getElementById('createTicketModal').style.display = 'block';
}

/**
 * Закрытие модального окна создания тикета
 */
function closeTicketModal() {
    document.getElementById('createTicketModal').style.display = 'none';
    document.getElementById('createTicketForm').reset();
}

/**
 * Открытие модального окна чата из карточки
 * @param {number} ticketId - ID тикета
 * @param {string} subject - Тема тикета
 */
function openTicketChatModalFromCard(ticketId, subject) {
    openTicketChatModal(ticketId, subject);
}

// ===== ФУНКЦИИ ДЛЯ ЧАТА =====

// Глобальные переменные для чата
let currentChatTicketId = null;
let chatPollingInterval = null;

/**
 * Открытие модального окна чата тикета
 * @param {number} ticketId - ID тикета
 * @param {string} subject - Тема тикета
 */
function openTicketChatModal(ticketId, subject) {
    currentChatTicketId = ticketId;
    document.getElementById('chatModalTitle').textContent = 'Тикет: ' + subject;
    document.getElementById('ticketChatModal').style.display = 'block';
    loadModalChatMessages(true);
    if (chatPollingInterval) clearInterval(chatPollingInterval);
    chatPollingInterval = setInterval(() => loadModalChatMessages(), 5000);
    
    // Проверяем статус тикета
    const row = document.querySelector(`.ticket-row[data-ticket-id='${ticketId}']`);
    if (row && ['pending','closed'].includes(row.dataset.ticketStatus)) {
        window.showNotification('error', 'Чат недоступен для данного статуса тикета');
        return;
    }
}

/**
 * Закрытие модального окна чата
 */
function closeTicketChatModal() {
    document.getElementById('ticketChatModal').style.display = 'none';
    document.getElementById('modalChatMessages').innerHTML = '';
    if (chatPollingInterval) clearInterval(chatPollingInterval);
}

/**
 * Загрузка сообщений чата в модальном окне
 * @param {boolean} scrollToEnd - Прокрутить к концу чата
 */
async function loadModalChatMessages(scrollToEnd = false) {
    if (!currentChatTicketId) return;
    const chatMessages = document.getElementById('modalChatMessages');
    
    // Показываем лоадер только если сообщений ещё нет
    if (!chatMessages.children.length) {
        chatMessages.innerHTML = '<div class="chat-loading">Загрузка...</div>';
    }
    
    try {
        const res = await fetch(`/support-tickets/${currentChatTicketId}/messages`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        renderModalChatMessages(data.messages);
        if (scrollToEnd) chatMessages.scrollTop = chatMessages.scrollHeight;
    } catch (e) {
        chatMessages.innerHTML = '<div class="chat-error">Ошибка загрузки сообщений</div>';
    }
}

/**
 * Отрисовка сообщений чата в модальном окне
 * @param {Array} messages - Массив сообщений
 */
function renderModalChatMessages(messages) {
    const chatMessages = document.getElementById('modalChatMessages');
    chatMessages.innerHTML = '';
    
    if (!messages.length) {
        chatMessages.innerHTML = '<div class="chat-empty">Сообщений пока нет</div>';
        return;
    }
    
    messages.forEach(msg => {
        const isMe = msg.is_admin ? false : true;
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message' + (isMe ? ' chat-message-me' : ' chat-message-admin');
        msgDiv.innerHTML = `
            <div class="chat-msg-meta">
                <span class="chat-msg-author">${isMe ? 'Вы' : 'Админ'}</span>
                <span class="chat-msg-date">${new Date(msg.created_at).toLocaleString('ru-RU', {hour: '2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'2-digit'})}</span>
            </div>
            <div class="chat-msg-text">${escapeHtml(msg.message)}</div>
        `;
        chatMessages.appendChild(msgDiv);
    });
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

/**
 * Экранирование HTML для безопасности
 * @param {string} text - Текст для экранирования
 * @returns {string} Экранированный текст
 */
function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];
    });
}

// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ ЧАТА =====

/**
 * Отрисовка сообщений на странице чата
 * @param {Array} messages - Массив сообщений
 */
function renderMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '';
    
    if (!messages.length) {
        chatMessages.innerHTML = '<div class="chat-empty">Сообщений пока нет</div>';
        return;
    }
    
    messages.forEach(msg => {
        const isMe = msg.is_admin ? false : true;
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message' + (isMe ? ' chat-message-me' : ' chat-message-admin');
        msgDiv.innerHTML = `
            <div class="chat-msg-meta">
                <span class="chat-msg-author">${isMe ? 'Вы' : 'Админ'}</span>
                <span class="chat-msg-date">${new Date(msg.created_at).toLocaleString('ru-RU', {hour: '2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'2-digit'})}</span>
            </div>
            <div class="chat-msg-text">${escapeHtml(msg.message)}</div>
        `;
        chatMessages.appendChild(msgDiv);
    });
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

/**
 * Загрузка сообщений на странице чата
 * @param {boolean} scrollToEnd - Прокрутить к концу чата
 */
async function loadMessages(scrollToEnd = false) {
    const chatMessages = document.getElementById('chatMessages');
    const ticketId = window.ticketId; // Глобальная переменная из blade
    
    try {
        const res = await fetch(`/support-tickets/${ticketId}/messages`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        renderMessages(data.messages);
        if (scrollToEnd) chatMessages.scrollTop = chatMessages.scrollHeight;
    } catch (e) {
        chatMessages.innerHTML = '<div class="chat-error">Ошибка загрузки сообщений</div>';
    }
}

// ===== ИНИЦИАЛИЗАЦИЯ =====

document.addEventListener('DOMContentLoaded', function() {
    // Инициализация мобильной версии для списка тикетов
    if (document.querySelector('.dashboard-container')) {
        toggleMobileView();
        
        // Обработчик изменения размера окна
        window.addEventListener('resize', toggleMobileView);
        
        // Обработчик формы создания тикета
        const form = document.getElementById('createTicketForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = form.querySelector('.btn-submit');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Отправка...';
                
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(async response => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Отправить';
                    
                    if (response.ok) {
                        const data = await response.json().catch(() => ({}));
                        window.showNotification('success', 'Тикет успешно создан');
                        closeTicketModal();
                        
                        // Добавляем тикет в таблицу и создаем карточку без перезагрузки
                        const tbody = document.querySelector('table tbody');
                        const ticketsCards = document.getElementById('ticketsCards');
                        const ticket = data.ticket;
                        const createdAt = new Date(ticket.created_at);
                        const dateStr = createdAt.toLocaleDateString('ru-RU') + ' ' + createdAt.toLocaleTimeString('ru-RU', {hour: '2-digit', minute:'2-digit'});
                        
                        // Добавляем в десктопную таблицу
                        const newRow = document.createElement('tr');
                        newRow.className = 'ticket-row';
                        newRow.setAttribute('data-ticket-id', ticket.id);
                        newRow.setAttribute('data-ticket-subject', ticket.subject);
                        newRow.style.opacity = 0;
                        newRow.innerHTML = `
                            <td>${ticket.subject}</td>
                            <td><span class="status-badge status-completed">Открыт</span></td>
                            <td>${dateStr}</td>
                        `;
                        tbody.prepend(newRow);
                        
                        // Создаем мобильную карточку
                        const noTicketsMessage = ticketsCards.querySelector('.no-tickets-message');
                        if (noTicketsMessage) {
                            noTicketsMessage.remove();
                        }
                        
                        const newCard = document.createElement('div');
                        newCard.className = 'ticket-card';
                        newCard.setAttribute('data-ticket-id', ticket.id);
                        newCard.setAttribute('data-ticket-subject', ticket.subject);
                        newCard.setAttribute('data-ticket-status', 'open');
                        newCard.style.opacity = 0;
                        newCard.innerHTML = `
                            <div class="ticket-card-header">
                                <div class="ticket-main-info">
                                    <div class="ticket-icon">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ticket-subject">${ticket.subject}</div>
                                </div>
                                <div class="ticket-status">
                                    <span class="status-badge status-completed">Открыт</span>
                                </div>
                            </div>
                            <div class="ticket-info">
                                <div class="ticket-info-item">
                                    <div class="ticket-info-label">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                        </svg>
                                        Дата создания:
                                    </div>
                                    <div class="ticket-info-value">${dateStr}</div>
                                </div>
                            </div>
                            <div class="ticket-actions">
                                <button class="btn-chat" title="Открыть чат" onclick="openTicketChatModalFromCard(${ticket.id}, '${ticket.subject}')">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                    </svg>
                                    Открыть чат
                                </button>
                            </div>
                        `;
                        ticketsCards.prepend(newCard);
                        
                        // Удаляем строку 'Тикетов пока нет', если есть
                        const emptyRow = tbody.querySelector('tr td[colspan]');
                        if (emptyRow && emptyRow.textContent.includes('Тикетов пока нет')) {
                            emptyRow.parentElement.remove();
                        }
                        
                        // Навешиваем обработчик клика на новую строку
                        newRow.addEventListener('click', function(e) {
                            if (e.target.closest('button, a, .no-chat-open')) return;
                            openTicketChatModal(this.dataset.ticketId, this.dataset.ticketSubject);
                        });
                        
                        // Навешиваем обработчик клика на новую карточку
                        newCard.addEventListener('click', function(e) {
                            if (e.target.closest('button, a, .no-chat-open')) return;
                            openTicketChatModal(this.dataset.ticketId, this.dataset.ticketSubject);
                        });
                        
                        // Анимация появления
                        setTimeout(() => {
                            newRow.style.opacity = 1;
                            newCard.style.opacity = 1;
                        }, 100);
                        
                    } else {
                        window.showNotification('error', 'Ошибка создания тикета');
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Отправить';
                    window.showNotification('error', 'Ошибка сети');
                });
            });
        }
        
        // Обработчики клика по строкам тикетов
        document.querySelectorAll('.ticket-row').forEach(row => {
            row.addEventListener('click', function(e) {
                // Не открывать чат только если клик по элементу управления внутри строки
                if (e.target.closest('button, a, .no-chat-open')) return;
                if (['pending','closed'].includes(this.dataset.ticketStatus)) return; // Блокируем открытие чата
                openTicketChatModal(this.dataset.ticketId, this.dataset.ticketSubject);
            });
        });
        
        // Обработчик формы чата в модальном окне
        const modalChatForm = document.getElementById('modalChatForm');
        if (modalChatForm) {
            modalChatForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const input = this.querySelector('input[name="message"]');
                const msg = input.value.trim();
                if (!msg) return;
                
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                
                // Блокируем отправку, если тикет не открыт
                const row = document.querySelector(`.ticket-row[data-ticket-id='${currentChatTicketId}']`);
                if (row && ['pending','closed'].includes(row.dataset.ticketStatus)) {
                    window.showNotification('error', 'Нельзя отправлять сообщения для данного статуса тикета');
                    return;
                }
                
                try {
                    const res = await fetch(`/support-tickets/${currentChatTicketId}/messages`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: new URLSearchParams({ message: msg })
                    });
                    const data = await res.json();
                    if (data.success) {
                        input.value = '';
                        await loadModalChatMessages(true);
                        window.showNotification('success', 'Сообщение отправлено');
                    } else {
                        window.showNotification('error', 'Ошибка отправки');
                    }
                } catch (e) {
                    window.showNotification('error', 'Ошибка сети');
                } finally {
                    btn.disabled = false;
                }
            });
        }
    }
    
    // Инициализация для страницы чата
    const chatForm = document.getElementById('chatForm');
    if (chatForm) {
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const input = chatForm.querySelector('input[name="message"]');
            const msg = input.value.trim();
            if (!msg) return;
            
            const btn = chatForm.querySelector('button[type="submit"]');
            btn.disabled = true;
            
            try {
                const res = await fetch(`/support-tickets/${window.ticketId}/messages`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: new URLSearchParams({ message: msg })
                });
                const data = await res.json();
                if (data.success) {
                    input.value = '';
                    await loadMessages(true);
                    window.showNotification('success', 'Сообщение отправлено');
                } else {
                    window.showNotification('error', 'Ошибка отправки');
                }
            } catch (e) {
                window.showNotification('error', 'Ошибка сети');
            } finally {
                btn.disabled = false;
            }
        });
        
        // Автообновление чата (polling)
        setInterval(() => loadMessages(), 5000);
        
        // Первая загрузка
        loadMessages(true);
    }
}); 