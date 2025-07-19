@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="natification-header" style="display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 24px;">
        <h1 class="mb-0">Уведомления</h1>
        <form method="get" class="table-filters" style="background: #f7fafd; border-radius: 10px; padding: 0; box-shadow: 0 1px 3px rgba(59,130,246,0.04); display: flex; gap: 16px; align-items: center; margin-bottom: 0; border: none;">
            <select name="type" class="table-filter-select chosen-select" onchange="this.form.submit()">
                <option value="">Все типы</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @if(request('type') == $type) selected @endif>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <select name="status" class="table-filter-select chosen-select" onchange="this.form.submit()">
                <option value="">Все статусы</option>
                <option value="unread" @if(request('status') == 'unread') selected @endif>Непрочитанные</option>
                <option value="read" @if(request('status') == 'read') selected @endif>Прочитанные</option>
            </select>
        </form>
    </div>
    <div class="table-wrapper">
        <div class="card-body p-0">
            <table class="natification-table table-striped">
                <thead>
                    <tr>
                        <th>Тип</th>
                        <th>Заголовок</th>
                        <th>Дата</th>
                        <th>Статус</th>
                        <th class="actions-column"></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($notifications as $notification)
                    <tr @if(!$notification->is_read) style="font-weight:bold;" @endif>
                        <td>
                            <div class="client-info">
                            
                                <div class="client-details">
                                    <div class="client-name">{{ ucfirst($notification->type) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $notification->title }}</td>
                        <td>{{ $notification->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($notification->is_read)
                                <span class="badge status-success">Прочитано</span>
                            @else
                                <span class="badge status-warning text-dark">Непрочитано</span>
                            @endif
                        </td>
                        <td class="actions-cell">
                            @if(!$notification->is_read && $notification->url)
                                <form method="POST" action="{{ route('client.notifications.read', $notification->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-add-client btn-sm">Открыть</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Нет уведомлений</td></tr>
                @endforelse
                </tbody>
            </table>
    </div>
        <div class="card-footer">{{ $notifications->withQueryString()->links() }}</div>
    </div>
</div>
@endsection 

<style>
.table-filters {
    background: #f7fafd;
    border-radius: 10px;
    padding: 0;
    margin-bottom: 0;
    box-shadow: 0 1px 3px rgba(59,130,246,0.04);
    border: none !important;
}
.natification-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 24px;
}
.table-filter-select {
    padding: 8px 36px 8px 14px;
    border-radius: 8px;
    border: 1.5px solid #e0e4e9;
    background: #fff url('data:image/svg+xml;utf8,<svg fill="gray" height="16" viewBox="0 0 20 20" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>') no-repeat right 12px center/18px 18px;
    font-size: 15px;
    color: #374151;
    min-width: 160px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    box-shadow: 0 1px 2px rgba(59,130,246,0.03);
    transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
}
.table-filter-select:focus {
    color: #495057;
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
    border: 1px solid #e5e7eb;
}
.table-filter-select option {
    padding: 8px 16px;
    color: #374151;
    background: #f7fafd;
    font-size: 15px;
}
.table-filter-select option:checked, .table-filter-select option[selected] {
    background: #e0e4e9 !important;
    color: #2563eb !important;
}
</style> 
@push('scripts')
<script src="/client/js/lib/chosen/chosen.jquery.min.js"></script>
<script>$(function() { $('.chosen-select').chosen({width: '160px', disable_search: true}); });</script>
@endpush 