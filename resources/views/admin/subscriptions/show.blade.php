@extends('admin.layouts.app')

@section('title', 'Детали подписки')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Главная</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index') }}">Подписки</a></li>
                        <li class="breadcrumb-item active">Детали подписки</li>
                    </ol>
                </div>
                <h4 class="page-title">Детали подписки</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Информация о подписке</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Проект:</strong> {{ $subscription->project->name }}</p>
                            <p><strong>Email:</strong> {{ $subscription->project->email }}</p>
                            <p><strong>Тариф:</strong> 
                                <span class="badge bg-primary">{{ $subscription->plan_type ?? 'Не указан' }}</span>
                            </p>
                            <p><strong>Статус:</strong> 
                                @if($subscription->status === 'trial')
                                    <span class="badge bg-warning">Пробный</span>
                                @elseif($subscription->status === 'active')
                                    <span class="badge bg-success">Активный</span>
                                @elseif($subscription->status === 'expired')
                                    <span class="badge bg-danger">Истек</span>
                                @else
                                    <span class="badge bg-secondary">{{ $subscription->status }}</span>
                                @endif
                            </p>
                        </div>
                                                        <div class="col-md-6">
                                    <p><strong>Дата начала:</strong> {{ $subscription->starts_at ? $subscription->starts_at->format('d.m.Y H:i') : 'Не указано' }}</p>
                                    <p><strong>Пробный период до:</strong> {{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('d.m.Y H:i') : 'Не указано' }}</p>
                                    <p><strong>Дата окончания подписки:</strong> {{ $subscription->expires_at ? $subscription->expires_at->format('d.m.Y H:i') : 'Не указано' }}</p>
                                    <p><strong>Дата оплаты:</strong> {{ $subscription->paid_at ? $subscription->paid_at->format('d.m.Y H:i') : 'Не указано' }}</p>
                                    <p><strong>Стоимость:</strong> {{ $subscription->amount ?? 0 }} {{ $subscription->currency ?? 'USD' }}</p>
                                </div>
                    </div>
                    @if($subscription->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Заметки:</strong> {{ $subscription->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Статистика проекта</h4>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-primary">{{ $subscription->project->clients->count() }}</h3>
                        <p class="text-muted">Клиентов</p>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h3 class="text-success">{{ $subscription->project->appointments->count() }}</h3>
                        <p class="text-muted">Записей</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Действия</h4>
                </div>
                <div class="card-body">
                    @if($subscription->status !== 'cancelled')
                    <button class="btn btn-success me-2" onclick="extendSubscription({{ $subscription->id }})">
                        <i class="fas fa-plus"></i> Продлить подписку
                    </button>
                    <button class="btn btn-danger" onclick="cancelSubscription({{ $subscription->id }})">
                        <i class="fas fa-times"></i> Отменить подписку
                    </button>
                    @else
                    <p class="text-muted">Подписка отменена. Действия недоступны.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для продления подписки -->
<div class="modal fade" id="extendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Продлить подписку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите продлить подписку для этого проекта на месяц?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-success" id="confirmExtend">Продлить</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для отмены подписки -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Отменить подписку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите отменить подписку для этого проекта?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">Отменить</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSubscriptionId = null;

function extendSubscription(subscriptionId) {
    currentSubscriptionId = subscriptionId;
    $('#extendModal').modal('show');
}

function cancelSubscription(subscriptionId) {
    currentSubscriptionId = subscriptionId;
    $('#cancelModal').modal('show');
}

$('#confirmExtend').click(function() {
    if (!currentSubscriptionId) return;
    
    $.ajax({
        url: `/panel/subscriptions/${currentSubscriptionId}/extend`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Произошла ошибка');
        }
    });
    
    $('#extendModal').modal('hide');
});

$('#confirmCancel').click(function() {
    if (!currentSubscriptionId) return;
    
    $.ajax({
        url: `/panel/subscriptions/${currentSubscriptionId}/cancel`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Произошла ошибка');
        }
    });
    
    $('#cancelModal').modal('hide');
});
</script>
@endpush
