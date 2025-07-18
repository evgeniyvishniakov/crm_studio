@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="clients-header">
        <h1>Тикет: {{ $ticket->subject }}</h1>
        <a href="{{ route('client.support-tickets.index') }}" class="btn-cancel" style="margin-left: 16px;">← К списку тикетов</a>
    </div>
    <div class="chat-wrapper" id="chatWrapper">
        <div class="chat-messages" id="chatMessages" style="height: 400px; overflow-y: auto; background: #f9fafb; border-radius: 10px; padding: 24px; margin-bottom: 16px; border: 1px solid #e5e7eb;">
            <div class="chat-loading">Загрузка...</div>
        </div>
        <form id="chatForm" class="chat-form" style="display: flex; gap: 12px;">
            @csrf
            <input type="text" name="message" class="form-control" placeholder="Введите сообщение..." required style="flex:1;">
            <button type="submit" class="btn-submit">Отправить</button>
        </form>
    </div>
</div>
<div id="notification"></div>
@push('scripts')
<script>
const ticketId = {{ $ticket->id }};
const chatMessages = document.getElementById('chatMessages');
const chatForm = document.getElementById('chatForm');
let lastMessageId = null;

function renderMessages(messages) {
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

async function loadMessages(scrollToEnd = false) {
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

chatForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = chatForm.querySelector('input[name="message"]');
    const msg = input.value.trim();
    if (!msg) return;
    const btn = chatForm.querySelector('button[type="submit"]');
    btn.disabled = true;
    try {
        const res = await fetch(`/support-tickets/${ticketId}/messages`, {
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
</script>
<style>
.chat-wrapper { max-width: 600px; margin: 0 auto; }
.chat-messages { min-height: 200px; background: #f9fafb; }
.chat-message { margin-bottom: 18px; padding: 10px 16px; border-radius: 10px; max-width: 80%; word-break: break-word; position: relative; }
.chat-message-me { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: #fff; margin-left: auto; text-align: right; }
.chat-message-admin { background: #e5e7eb; color: #222; margin-right: auto; text-align: left; }
.chat-msg-meta { font-size: 12px; color: #64748b; margin-bottom: 4px; display: flex; justify-content: space-between; }
.chat-msg-author { font-weight: 600; }
.chat-msg-text { font-size: 15px; }
.chat-form { margin-top: 8px; }
.chat-empty, .chat-loading, .chat-error { color: #888; text-align: center; margin: 40px 0; }
</style>
@endpush
@endsection 