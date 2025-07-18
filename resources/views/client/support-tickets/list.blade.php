@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="clients-header">
        <h1>Поддержка</h1>
        <div id="notification"></div>
        <div class="header-actions">
            <button class="btn-add-client" onclick="openTicketModal()">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Создать тикет
            </button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="clients-table">
            <thead>
                <tr>
                    <th>Тема</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr class="ticket-row" data-ticket-id="{{ $ticket->id }}" data-ticket-subject="{{ $ticket->subject }}" style="cursor:pointer;">
                        <td>{{ $ticket->subject }}</td>
                        <td>
                            <span class="status-badge {{ $ticket->status === 'open' ? 'status-completed' : 'status-cancelled' }}">
                                {{ $ticket->status === 'open' ? 'Открыт' : 'Закрыт' }}
                            </span>
                        </td>
                        <td>{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">Тикетов пока нет</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $tickets->links() }}</div>
    </div>
</div>

<!-- Модальное окно создания тикета -->
<div id="createTicketModal" class="modal" style="display:none;">
    <div class="modal-content" style="width: 600px; max-width: 98vw;">
        <div class="modal-header">
            <h2>Создать тикет</h2>
            <span class="close" onclick="closeTicketModal()">&times;</span>
        </div>
        <form method="POST" action="{{ route('support-tickets.store') }}" id="createTicketForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Тема *</label>
                    <input type="text" name="subject" class="form-control" required maxlength="255">
                </div>
                <div class="form-group">
                    <label>Сообщение *</label>
                    <textarea name="message" class="form-control" required rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer form-actions">
                <button type="button" class="btn-cancel" onclick="closeTicketModal()">Отмена</button>
                <button type="submit" class="btn-submit">Отправить</button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно чата тикета -->
<div id="ticketChatModal" class="modal" style="display:none;">
    <div class="modal-content" style="width: 600px; max-width: 98vw;">
        <div class="modal-header">
            <h2 id="chatModalTitle">Тикет</h2>
            <span class="close" onclick="closeTicketChatModal()">&times;</span>
        </div>
        <div class="modal-body" style="padding:0;">
            <div class="chat-messages" id="modalChatMessages" style="height: 350px; overflow-y: auto; background: #f9fafb; border-radius: 10px; padding: 24px; margin-bottom: 0; border-bottom: 1px solid #e5e7eb;"></div>
            <form id="modalChatForm" class="chat-form" style="display: flex; gap: 12px; padding: 16px; border-top: 1px solid #e5e7eb; background: #fff;">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="Введите сообщение..." required style="flex:1;">
                <button type="submit" class="btn-submit">Отправить</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openTicketModal() {
    document.getElementById('createTicketModal').style.display = 'block';
}
function closeTicketModal() {
    document.getElementById('createTicketModal').style.display = 'none';
    document.getElementById('createTicketForm').reset();
}

// Универсальное уведомление (как в записях)
window.showNotification = function(type, message) {
    let notification = document.getElementById('notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        document.body.appendChild(notification);
    }
    notification.className = `notification ${type} show shake`;
    const icon = type === 'success'
        ? '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>'
        : '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';
    notification.innerHTML = `
        <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
            ${icon}
        </svg>
        <span class="notification-message">${message}</span>
    `;
    notification.addEventListener('animationend', function handler() {
        notification.classList.remove('shake');
        notification.removeEventListener('animationend', handler);
    });
    setTimeout(() => {
        notification.className = `notification ${type}`;
    }, 3000);
};

// AJAX отправка формы создания тикета

document.addEventListener('DOMContentLoaded', function() {
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
                    window.showNotification('success', 'Тикет успешно создан!');
                    closeTicketModal();
                    // Добавляем тикет в таблицу без перезагрузки
                    const tbody = document.querySelector('table tbody');
                    const ticket = data.ticket;
                    const createdAt = new Date(ticket.created_at);
                    const dateStr = createdAt.toLocaleDateString('ru-RU') + ' ' + createdAt.toLocaleTimeString('ru-RU', {hour: '2-digit', minute:'2-digit'});
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
                    setTimeout(() => { newRow.style.transition = 'opacity 0.5s'; newRow.style.opacity = 1; }, 10);
                } else {
                    let msg = 'Ошибка при создании тикета';
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
                submitBtn.textContent = 'Отправить';
                window.showNotification('error', 'Ошибка сети');
            });
        });
    }
});

// Модальное окно чата тикета
let currentChatTicketId = null;
let chatPollingInterval = null;

function openTicketChatModal(ticketId, subject) {
    currentChatTicketId = ticketId;
    document.getElementById('chatModalTitle').textContent = 'Тикет: ' + subject;
    document.getElementById('ticketChatModal').style.display = 'block';
    loadModalChatMessages(true);
    if (chatPollingInterval) clearInterval(chatPollingInterval);
    chatPollingInterval = setInterval(() => loadModalChatMessages(), 5000);
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
function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];
    });
}
document.querySelectorAll('.ticket-row').forEach(row => {
    row.addEventListener('click', function(e) {
        // Не открывать чат только если клик по элементу управления внутри строки (например, кнопка)
        if (e.target.closest('button, a, .no-chat-open')) return;
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
</script>
<style>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    color: #fff;
    font-size: 1rem;
    z-index: 1050;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 250px;
    text-align: center;
    transition: opacity 0.3s, transform 0.3s;
}
.notification.success {
    background: linear-gradient(135deg, #28a745, #34d399);
}
.notification.error {
    background: linear-gradient(135deg, #dc3545, #ef4444);
}
.notification.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}
.notification .notification-icon {
    width: 24px;
    height: 24px;
    vertical-align: middle;
    margin-right: 8px;
}
.notification .notification-message {
    vertical-align: middle;
}
@keyframes shake {
    10%, 90% { transform: translate3d(-2px, 0, 0); }
    20%, 80% { transform: translate3d(4px, 0, 0); }
    30%, 50%, 70% { transform: translate3d(-8px, 0, 0); }
    40%, 60% { transform: translate3d(8px, 0, 0); }
}
.notification.shake {
    animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
}
.chat-link { color: #2563eb; text-decoration: underline; cursor: pointer; }
.chat-link:hover { color: #1d4ed8; }
.chat-messages { min-height: 200px; background: #f9fafb; }
.chat-message { margin-bottom: 18px; padding: 10px 16px; border-radius: 10px; max-width: 80%; word-break: break-word; position: relative; }
.chat-message-me { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: #fff; margin-left: auto; text-align: right; }
.chat-message-admin { background: #e5e7eb; color: #222; margin-right: auto; text-align: left; }
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
.chat-message-me .chat-msg-meta { color: #fff; }
</style>
@endpush
@endsection 