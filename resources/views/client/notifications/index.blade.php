@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="natification-header" style="display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 24px;">
        <h1 class="mb-0">{{ __('messages.notifications') }}</h1>
        <div class="header-actions">
            <button id="markAllReadBtn" class="btn-add-product" onclick="markAllAsRead()" style="display: none;">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                {{ __('messages.mark_all_as_read') }}
            </button>
            
            <!-- Фильтры -->
            <div class="filters-row" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                <div class="filter-group">
                    <select id="typeFilter" onchange="applyFilters()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: #fff; min-width: 150px;">
                        <option value="">{{ __('messages.all_types') }}</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}">
                                @if($type === 'web_booking')
                                    {{ __('messages.web_booking') }}
                                @elseif($type === 'ticket')
                                    {{ __('messages.ticket') }}
                                @else
                                    {{ ucfirst($type) }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="statusFilter" onchange="applyFilters()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: #fff; min-width: 150px;">
                        <option value="">{{ __('messages.all_statuses') }}</option>
                        <option value="unread">{{ __('messages.unread') }}</option>
                        <option value="read">{{ __('messages.read') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Десктопная таблица -->
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
                                    @elseif($notification->type === 'ticket')
                                        {{ __('messages.ticket') }}
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
                        <button class="btn-delete" title="{{ __('messages.delete') }}" data-notification-id="{{ $notification->id }}">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        
        <!-- Пагинация будет добавлена через JavaScript -->
        <div id="notificationsPagination"></div>
    </div>

    <!-- Мобильные карточки уведомлений -->
    <div class="notifications-cards" id="notificationsCards" style="display: none;">
        @foreach($notifications as $notification)
            <div class="notification-card" id="notification-card-{{ $notification->id }}" @if(!$notification->is_read) style="font-weight:bold;" @endif>
                <div class="notification-card-header">
                    <div class="notification-main-info">
                        <div class="notification-content">
                            <h3 class="notification-title">{{ $notification->title }}</h3>
                            <div class="notification-type">
                                @if($notification->type === 'web_booking')
                                    {{ __('messages.web_booking') }}
                                @elseif($notification->type === 'ticket')
                                    {{ __('messages.ticket') }}
                                @else
                                    {{ ucfirst($notification->type) }}
                                @endif
                            </div>
                        </div>
                        <div class="notification-status">
                            @if($notification->is_read)
                                <span class="status-badge read">{{ __('messages.read') }}</span>
                            @else
                                <span class="status-badge unread">{{ __('messages.unread') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="notification-info">
                    <div class="notification-info-item">
                        <span class="notification-info-label">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.date') }}
                        </span>
                        <span class="notification-info-value">{{ $notification->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
                <div class="notification-actions">
                    @if(!$notification->is_read && $notification->url)
                        <form method="POST" action="{{ route('client.notifications.read', $notification->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-open">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                {{ __('messages.open') }}
                            </button>
                        </form>
                    @endif
                                         <button class="btn-delete" data-notification-id="{{ $notification->id }}">
                         <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                             <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                         </svg>
                         {{ __('messages.delete') }}
                     </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Пагинация для мобильных карточек -->
    <div id="mobileNotificationsPagination" style="display: none;"></div>
</div>

<!-- Модальное окно подтверждения -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="confirmationTitle">{{ __('messages.confirmation') }}</h2>
            <span class="close" onclick="closeConfirmationModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p id="confirmationMessage">{{ __('messages.confirm_mark_all_as_read') }}</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="cancelAction">{{ __('messages.cancel') }}</button>
            <button class="btn-delete" id="confirmAction">{{ __('messages.confirm') }}</button>
        </div>
    </div>
</div>


@push('scripts')
<script src="{{ asset('client/js/notifications-page.js') }}"></script>
@endpush
@endsection