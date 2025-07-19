@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="natification-header">
        <h1 class="mb-4">Уведомления</h1>
    </div>
    <form method="get" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="type" class="form-control" onchange="this.form.submit()">
                <option value="">Все типы</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @if(request('type') == $type) selected @endif>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">Все статусы</option>
                <option value="unread" @if(request('status') == 'unread') selected @endif>Непрочитанные</option>
                <option value="read" @if(request('status') == 'read') selected @endif>Прочитанные</option>
            </select>
        </div>
    </form>
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