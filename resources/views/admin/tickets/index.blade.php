@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Тикеты поддержки</h1>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Тема</th>
                        <th>Клиент</th>
                        <th>Проект</th>
                        <th>Статус</th>
                        <th class="text-end">Дата создания</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tickets as $ticket)
                    <tr class="ticket-row" data-ticket-id="{{ $ticket->id }}" data-ticket-subject="{{ $ticket->subject }}" style="cursor:pointer;">
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ $ticket->user->name ?? '—' }}</td>
                        <td>{{ $ticket->project->project_name ?? $ticket->project->name ?? '—' }}</td>
                        <td>
                            <select class="form-select ticket-status-select" data-ticket-id="{{ $ticket->id }}">
                                <option value="open" @if($ticket->status === 'open') selected @endif>Открыт</option>
                                <option value="pending" @if($ticket->status === 'pending') selected @endif>Ожидается</option>
                                <option value="closed" @if($ticket->status === 'closed') selected @endif>Завершён</option>
                            </select>
                        </td>
                        <td class="text-end">{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.tickets.destroy', $ticket->id) }}" style="display:inline;" onsubmit="return confirm('Удалить этот тикет?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Тикетов пока нет</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $tickets->links() }}</div>
    </div>
</div>
<!-- Модальное окно чата тикета -->
<div id="ticketChatModal" class="modal" style="display:none;">
    <div class="modal-content chat-modal-centered">
        <div class="modal-header">
            <h2 id="chatModalTitle">Тикет</h2>
            <span class="close" onclick="closeTicketChatModal()">&times;</span>
        </div>
        <div class="modal-body" style="padding:0;">
            <div class="chat-messages" id="modalChatMessages" style="height: 340px; overflow-y: auto; background: #f9fafb; border-radius: 10px; padding: 24px; margin-bottom: 0; border-bottom: 1px solid #e5e7eb;"></div>
            <form id="modalChatForm" class="chat-form d-flex gap-2" style="padding: 16px; border-top: 1px solid #e5e7eb; background: #fff;">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="Введите сообщение..." required>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
    if (!chatMessages.children.length) {
        chatMessages.innerHTML = '<div class="chat-loading">Загрузка...</div>';
    }
    try {
        const res = await fetch(`/panel/tickets/${currentChatTicketId}/messages`, {
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
    } else {
        messages.forEach(msg => {
            const isAdmin = msg.is_admin;
            const msgDiv = document.createElement('div');
            msgDiv.className = 'chat-message' + (isAdmin ? ' chat-message-admin' : ' chat-message-client');
            msgDiv.innerHTML = `
                <div class="chat-msg-meta${isAdmin ? ' chat-msg-meta-admin' : ''}">
                    <span class="chat-msg-author">${isAdmin ? 'Вы' : 'Клиент'}</span>
                    <span class="chat-msg-date">${new Date(msg.created_at).toLocaleString('ru-RU', {hour: '2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'2-digit'})}</span>
                </div>
                <div class="chat-msg-text">${escapeHtml(msg.message)}</div>
            `;
            chatMessages.appendChild(msgDiv);
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}
function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];
    });
}
document.querySelectorAll('.ticket-row').forEach(row => {
    row.addEventListener('click', function(e) {
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
        const res = await fetch(`/panel/tickets/${currentChatTicketId}/messages`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: new URLSearchParams({ message: msg, is_admin: 1 })
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
document.querySelectorAll('.ticket-status-select').forEach(function(select) {
    select.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    select.addEventListener('change', function(e) {
        const ticketId = this.dataset.ticketId;
        const status = this.value;
        fetch(`/panel/tickets/${ticketId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: new URLSearchParams({ status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.showNotification && window.showNotification('success', 'Статус обновлён');
            } else {
                window.showNotification && window.showNotification('error', 'Ошибка обновления статуса');
            }
        })
        .catch(() => {
            window.showNotification && window.showNotification('error', 'Ошибка сети');
        });
    });
});
</script>
<style>
.ticket-row { transition: background 0.18s, box-shadow 0.18s; cursor: pointer; }
.ticket-row td, .ticket-row th { cursor: pointer; }
#ticketChatModal .close { cursor: pointer; user-select: none; }
.ticket-row:hover { background: #e8f0fe; box-shadow: 0 2px 8px rgba(59,130,246,0.07); border-radius: 6px; }
.chat-messages { min-height: 200px; background: #f9fafb; }
.chat-message { margin-bottom: 18px; padding: 10px 16px; border-radius: 10px; max-width: 80%; word-break: break-word; position: relative; }
.chat-message-admin {
    background: #e5e7eb;
    color: #222;
    margin-left: auto;
    margin-right: 0;
    text-align: right;
}
.chat-message-client {
    background: #f3f4f6;
    color: #222;
    margin-right: auto;
    margin-left: 0;
    text-align: left;
}
.chat-msg-meta, .chat-msg-meta-admin {
    color: #64748b;
}
.chat-msg-author { font-weight: 600; }
.chat-msg-text { font-size: 15px; }
.chat-form { margin-top: 8px; }
.chat-empty, .chat-loading, .chat-error { color: #888; text-align: center; margin: 40px 0; }
.chat-modal-centered {
    position: fixed;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 700px;
    max-width: 98vw;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
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
.ticket-status-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 16px;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.02em;
    color: #fff;
}
.ticket-status-badge.open {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff;
}
.ticket-status-badge.closed {
    background: linear-gradient(135deg, #64748b, #334155);
    color: #fff;
}
</style>
@endpush 