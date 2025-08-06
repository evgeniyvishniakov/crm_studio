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


@push('scripts')
<script>
// Глобальная переменная для ID тикета
window.ticketId = {{ $ticket->id }};
</script>
<script src="{{ asset('client/js/support.js') }}"></script>

@endpush
@endsection 