@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Тикет: {{ $ticket->subject }}</h1>
    <div class="mb-3">
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">← К списку тикетов</a>
    </div>
    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <div class="card-body p-0">
            <div class="chat-messages" id="adminChatMessages" style="height: 400px; overflow-y: auto; background: #f9fafb; border-radius: 10px; padding: 24px; margin-bottom: 0; border-bottom: 1px solid #e5e7eb;"></div>
        </div>
        <div class="card-footer bg-white">
            <form id="adminChatForm" class="chat-form d-flex gap-2">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="Введите сообщение..." required>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const ticketId = {{ $ticket->id }};
const chatMessages = document.getElementById('adminChatMessages');
const chatForm = document.getElementById('adminChatForm');
let chatPollingInterval = null;

function renderMessages(messages) {
    chatMessages.innerHTML = '';
    if (!messages.length) {
        chatMessages.innerHTML = '<div class="chat-empty">Сообщений пока нет</div>';
        return;
    }
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

function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];
    });
}

async function loadMessages(scrollToEnd = false) {
    try {
        const res = await fetch(`/panel/tickets/${ticketId}/messages`, {
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

chatForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = chatForm.querySelector('input[name="message"]');
    const msg = input.value.trim();
    if (!msg) return;
    const btn = chatForm.querySelector('button[type="submit"]');
    btn.disabled = true;
    try {
        const res = await fetch(`/panel/tickets/${ticketId}/messages`, {
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
chatPollingInterval = setInterval(() => loadMessages(), 5000);

// Первая загрузка
loadMessages(true);
</script>
<style>
.chat-messages { min-height: 200px; background: #f9fafb; }
.chat-message { margin-bottom: 18px; padding: 10px 16px; border-radius: 10px; max-width: 80%; word-break: break-word; position: relative; }
.chat-message-admin { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: #fff; margin-left: auto; text-align: right; }
.chat-message-client { background: #e5e7eb; color: #222; margin-right: auto; text-align: left; }
.chat-msg-meta { font-size: 12px; color: #64748b; margin-bottom: 4px; display: flex; justify-content: space-between; }
.chat-msg-meta-admin { color: #fff; }
.chat-msg-author { font-weight: 600; }
.chat-msg-text { font-size: 15px; }
.chat-form { margin-top: 8px; }
.chat-empty, .chat-loading, .chat-error { color: #888; text-align: center; margin: 40px 0; }
</style>
@endpush
@endsection 