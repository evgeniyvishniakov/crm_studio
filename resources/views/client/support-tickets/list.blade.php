@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="clients-header">
        <h1>{{ __('messages.support') }}</h1>
        <div class="header-actions">
            <button class="btn-add-client" onclick="openTicketModal()">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('messages.create_ticket') }}
            </button>
        </div>
    </div>
    <!-- Десктопная таблица -->
    <div class="table-wrapper">
        <table class="clients-table">
            <thead>
                <tr>
                    <th>{{ __('messages.subject') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr class="ticket-row{{ in_array($ticket->status, ['closed','pending']) ? ' ticket-row-closed' : '' }}" data-ticket-id="{{ $ticket->id }}" data-ticket-subject="{{ $ticket->subject }}" data-ticket-status="{{ $ticket->status }}" style="cursor:{{ in_array($ticket->status, ['pending','closed']) ? 'not-allowed' : 'pointer' }};">
                        <td>{{ $ticket->subject }}</td>
                        <td>
                            <span class="status-badge {{ $ticket->status === 'open' ? 'status-completed' : ($ticket->status === 'pending' ? 'status-pending' : 'status-cancelled') }}">
                                {{ $ticket->status === 'open' ? __('messages.open') : ($ticket->status === 'pending' ? __('messages.pending') : __('messages.closed')) }}
                            </span>
                        </td>
                        <td>{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">{{ __('messages.no_tickets_yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $tickets->links() }}</div>
    </div>

    <!-- Мобильные карточки тикетов -->
    <div class="tickets-cards" id="ticketsCards" style="display: none;">
        @forelse($tickets as $ticket)
            <div class="ticket-card{{ in_array($ticket->status, ['closed','pending']) ? ' ticket-card-closed' : '' }}" 
                 data-ticket-id="{{ $ticket->id }}" 
                 data-ticket-subject="{{ $ticket->subject }}" 
                 data-ticket-status="{{ $ticket->status }}"
                 style="cursor:{{ in_array($ticket->status, ['pending','closed']) ? 'not-allowed' : 'pointer' }};">
                <div class="ticket-card-header">
                    <div class="ticket-main-info">
                        <div class="ticket-icon">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ticket-subject">{{ $ticket->subject }}</div>
                    </div>
                    <div class="ticket-status">
                        <span class="status-badge {{ $ticket->status === 'open' ? 'status-completed' : ($ticket->status === 'pending' ? 'status-pending' : 'status-cancelled') }}">
                            {{ $ticket->status === 'open' ? __('messages.open') : ($ticket->status === 'pending' ? __('messages.pending') : __('messages.closed')) }}
                        </span>
                    </div>
                </div>
                <div class="ticket-info">
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.created_date') }}:
                        </div>
                        <div class="ticket-info-value">{{ $ticket->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
                @if(!in_array($ticket->status, ['pending','closed']))
                <div class="ticket-actions">
                    <button class="btn-chat" title="{{ __('messages.open_chat') }}" onclick="openTicketChatModalFromCard({{ $ticket->id }}, '{{ $ticket->subject }}')">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                        </svg>
                        {{ __('messages.open_chat') }}
                    </button>
                </div>
                @endif
            </div>
        @empty
            <div class="no-tickets-message">
                <div class="no-tickets-icon">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="no-tickets-text">{{ __('messages.no_tickets_yet') }}</div>
            </div>
        @endforelse
    </div>

    <!-- Мобильная пагинация -->
    <div id="mobileTicketsPagination" class="mobile-pagination" style="display: none;">
        <!-- Пагинация будет добавлена через JavaScript -->
    </div>
</div>

<!-- Модальное окно создания тикета -->
<div id="createTicketModal" class="modal" style="display:none;">
    <div class="modal-content" style="width: 600px; max-width: 98vw;">
        <div class="modal-header">
            <h2>{{ __('messages.create_ticket') }}</h2>
            <span class="close" onclick="closeTicketModal()">&times;</span>
        </div>
        <form method="POST" action="{{ route('support-tickets.store') }}" id="createTicketForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>{{ __('messages.subject') }} *</label>
                    <input type="text" name="subject" class="form-control" required maxlength="255">
                </div>
                <div class="form-group">
                    <label>{{ __('messages.message') }} *</label>
                    <textarea name="message" class="form-control" required rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer form-actions">
                <button type="button" class="btn-cancel" onclick="closeTicketModal()">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn-submit">{{ __('messages.send') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно чата тикета -->
<div id="ticketChatModal" class="modal" style="display:none;">
    <div class="modal-content chat-modal-centered">
        <div class="modal-header">
            <h2 id="chatModalTitle">{{ __('messages.ticket') }}</h2>
            <span class="close" onclick="closeTicketChatModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="chat-messages" id="modalChatMessages" style="height: 350px; overflow-y: auto; background: #f9fafb; border-radius: 10px; padding: 24px; margin-bottom: 0; border-bottom: 1px solid #e5e7eb;"></div>
            <form id="modalChatForm" class="chat-form" style="display: flex; gap: 12px; padding: 16px; border-top: 1px solid #e5e7eb; background: #fff;">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="{{ __('messages.enter_message') }}..." required style="flex:1;">
                <button type="submit" class="btn-submit">{{ __('messages.send') }}</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Функции для мобильной версии
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

// Функция для открытия модального окна чата из карточки
function openTicketChatModalFromCard(ticketId, subject) {
    openTicketChatModal(ticketId, subject);
}
function openTicketModal() {
    document.getElementById('createTicketModal').style.display = 'block';
}
function closeTicketModal() {
    document.getElementById('createTicketModal').style.display = 'none';
    document.getElementById('createTicketForm').reset();
}

// Используем глобальную функцию уведомлений

// AJAX отправка формы создания тикета

document.addEventListener('DOMContentLoaded', function() {
    // Инициализация мобильной версии
    toggleMobileView();
    
    // Обработчик изменения размера окна
    window.addEventListener('resize', toggleMobileView);
    
    const form = document.getElementById('createTicketForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('.btn-submit');
            submitBtn.disabled = true;
            submitBtn.textContent = '{{ __('messages.sending') }}...';
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
                    window.showNotification('success', '{{ __('messages.ticket_successfully_created') }}');
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
                        <td><span class="status-badge status-completed">{{ __('messages.open') }}</span></td>
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
                                <span class="status-badge status-completed">{{ __('messages.open') }}</span>
                            </div>
                        </div>
                        <div class="ticket-info">
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('messages.created_date') }}:
                                </div>
                                <div class="ticket-info-value">${dateStr}</div>
                            </div>
                        </div>
                        <div class="ticket-actions">
                            <button class="btn-chat" title="{{ __('messages.open_chat') }}" onclick="openTicketChatModalFromCard(${ticket.id}, '${ticket.subject}')">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                </svg>
                                {{ __('messages.open_chat') }}
                            </button>
                        </div>
                    `;
                    ticketsCards.prepend(newCard);
                    
                    // Удаляем строку 'Тикетов пока нет', если есть
                    const emptyRow = tbody.querySelector('tr td[colspan]');
                    if (emptyRow && emptyRow.textContent.includes('{{ __('messages.no_tickets_yet') }}')) {
                        emptyRow.parentElement.remove();
                    }
                    
                    // Навешиваем обработчик клика на новую строку
                    newRow.addEventListener('click', function(e) {
                        if (e.target.closest('button, a, .no-chat-open')) return;
                        openTicketChatModal(this.dataset.ticketId, this.dataset.ticketSubject);
                    });
                    
                    setTimeout(() => { 
                        newRow.style.transition = 'opacity 0.5s'; 
                        newRow.style.opacity = 1; 
                        newCard.style.transition = 'opacity 0.5s'; 
                        newCard.style.opacity = 1; 
                    }, 10);
                } else {
                    let msg = '{{ __('messages.error_creating_ticket') }}';
                    try {
                        const data = await response.json();
                        if (data.errors) {
                            msg = Object.values(data.errors).flat().join(' ');
                        } else if (data.message) {
                            msg = data.message;
                        }
                    } catch {}
                    window.showNotification('error', msg);
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = '{{ __('messages.send') }}';
                window.showNotification('error', '{{ __('messages.network_error') }}');
            });
        });
    }
});

// Модальное окно чата тикета
let currentChatTicketId = null;
let chatPollingInterval = null;

function openTicketChatModal(ticketId, subject) {
    currentChatTicketId = ticketId;
    document.getElementById('chatModalTitle').textContent = '{{ __('messages.ticket') }}: ' + subject;
    document.getElementById('ticketChatModal').style.display = 'block';
    loadModalChatMessages(true);
    if (chatPollingInterval) clearInterval(chatPollingInterval);
    chatPollingInterval = setInterval(() => loadModalChatMessages(), 5000);
    // Проверяем статус тикета
    const row = document.querySelector(`.ticket-row[data-ticket-id='${ticketId}']`);
    if (row && ['pending','closed'].includes(row.dataset.ticketStatus)) {
        window.showNotification('error', '{{ __('messages.chat_unavailable_for_status') }}');
        return;
    }
}
function closeTicketChatModal() {
    document.getElementById('ticketChatModal').style.display = 'none';
    document.getElementById('modalChatMessages').innerHTML = '';
    if (chatPollingInterval) clearInterval(chatPollingInterval);
}

async function loadModalChatMessages(scrollToEnd = false) {
    if (!currentChatTicketId) return;
    const chatMessages = document.getElementById('modalChatMessages');
    // Показываем лоадер только если сообщений ещё нет
    if (!chatMessages.children.length) {
        chatMessages.innerHTML = '<div class="chat-loading">{{ __('messages.loading') }}...</div>';
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
        chatMessages.innerHTML = '<div class="chat-error">{{ __('messages.error_loading_messages') }}</div>';
    }
}
function renderModalChatMessages(messages) {
    const chatMessages = document.getElementById('modalChatMessages');
    chatMessages.innerHTML = '';
    if (!messages.length) {
        chatMessages.innerHTML = '<div class="chat-empty">{{ __('messages.no_messages_yet') }}</div>';
        return;
    }
    messages.forEach(msg => {
        const isMe = msg.is_admin ? false : true;
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message' + (isMe ? ' chat-message-me' : ' chat-message-admin');
        msgDiv.innerHTML = `
            <div class="chat-msg-meta">
                <span class="chat-msg-author">${isMe ? '{{ __('messages.you') }}' : '{{ __('messages.admin') }}'}</span>
                <span class="chat-msg-date">${new Date(msg.created_at).toLocaleString('ru-RU', {hour: '2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'2-digit'})}</span>
            </div>
            <div class="chat-msg-text">${escapeHtml(msg.message)}</div>
        `;
        chatMessages.appendChild(msgDiv);
    });
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];
    });
}
document.querySelectorAll('.ticket-row').forEach(row => {
    row.addEventListener('click', function(e) {
        // Не открывать чат только если клик по элементу управления внутри строки (например, кнопка)
        if (e.target.closest('button, a, .no-chat-open')) return;
        if (['pending','closed'].includes(this.dataset.ticketStatus)) return; // Блокируем открытие чата
        openTicketChatModal(this.dataset.ticketId, this.dataset.ticketSubject);
    });
});
document.getElementById('modalChatForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = this.querySelector('input[name="message"]');
    const msg = input.value.trim();
    if (!msg) return;
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    // Блокируем отправку, если тикет не открыт
    const row = document.querySelector(`.ticket-row[data-ticket-id='${currentChatTicketId}']`);
    if (row && ['pending','closed'].includes(row.dataset.ticketStatus)) {
        window.showNotification('error', '{{ __('messages.cannot_send_messages_for_status') }}');
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
            window.showNotification('success', '{{ __('messages.message_sent') }}');
        } else {
            window.showNotification('error', '{{ __('messages.error_sending') }}');
        }
    } catch (e) {
        window.showNotification('error', '{{ __('messages.network_error') }}');
    } finally {
        btn.disabled = false;
    }
});
</script>
<style>
.chat-link { color: #2563eb; text-decoration: underline; cursor: pointer; }
.chat-link:hover { color: #1d4ed8; }
.chat-messages { min-height: 200px; background: #f9fafb; }
.chat-message { margin-bottom: 18px; padding: 10px 16px; border-radius: 10px; max-width: 80%; word-break: break-word; position: relative; }
.chat-message-me {
    background: #e5e7eb;
    color: #222;
    margin-left: auto;
    margin-right: 0;
    text-align: right;
}
.chat-message-admin {
    background: #f3f4f6;
    color: #222;
    margin-right: auto;
    margin-left: 0;
    text-align: left;
}
.chat-msg-meta { font-size: 12px; color: #64748b; margin-bottom: 4px; display: flex; justify-content: space-between; }
.chat-msg-author { font-weight: 600; }
.chat-msg-text { font-size: 15px; }
.chat-form { margin-top: 8px; }
.chat-empty, .chat-loading, .chat-error { color: #888; text-align: center; margin: 40px 0; }
.ticket-row { transition: background 0.18s, box-shadow 0.18s; cursor: pointer; }
.ticket-row:hover {
    background: #e8f0fe;
    box-shadow: 0 2px 8px rgba(59,130,246,0.07);
    border-radius: 6px;
}
.chat-message-me .chat-msg-meta { color: #64748b; }
.chat-msg-meta, .chat-msg-meta-admin {
    color: #64748b;
}
#ticketChatModal .modal-content.chat-modal-centered {
    position: fixed;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 800px;
    max-width: 98vw;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
}
#ticketChatModal .modal-content.chat-modal-centered .modal-body {
    border-radius: 0 0 16px 16px;
    overflow: hidden;
}
#ticketChatModal .chat-messages {
    height: 540px !important;
    min-height: 300px;
    max-height: 75vh;
    background: #f9fafb;
    border-radius: 10px;
    padding: 24px;
    margin-bottom: 0;
    border-bottom: 1px solid #e5e7eb;
}
.ticket-row-disabled {
    opacity: 0.6;
    pointer-events: none;
    cursor: not-allowed !important;
}
.status-badge.status-completed {
    background: linear-gradient(135deg, #458dd7 60%, #5fbbd7 100%);
    color: #fff;
}
.status-badge.status-pending {
    background: linear-gradient(135deg, #eba70e 60%, #f3c138 100%);
    color: #fff;
}
.status-badge.status-cancelled {
    background: linear-gradient(135deg, #05990b 60%, #56bb93 100%);
    color: #fff;
}
.ticket-row-closed {
    background: #f3f4f6 !important;
    color: #888 !important;
}
</style>
@endpush
@endsection 