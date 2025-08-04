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
<script src="{{ asset('client/js/support.js') }}"></script>
@endpush
@endsection 