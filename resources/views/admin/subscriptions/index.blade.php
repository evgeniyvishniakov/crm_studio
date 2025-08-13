@extends('admin.layouts.app')

@section('title', 'Управление подписками')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-title-box">
        <div class="page-title-right">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель управления</a></li>
                <li class="breadcrumb-item active">Подписки</li>
            </ol>
        </div>
        <h4 class="page-title">Управление подписками</h4>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_subscriptions'] }}</h4>
                            <p class="mb-0">Всего подписок</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-credit-card fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active_subscriptions'] }}</h4>
                            <p class="mb-0">Активные подписки</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['trial_subscriptions'] }}</h4>
                            <p class="mb-0">Пробные подписки</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['expired_subscriptions'] }}</h4>
                            <p class="mb-0">Просроченные</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Дополнительная статистика -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['revenue_this_month'] }} USD</h4>
                            <p class="mb-0">Доход за месяц</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['trial_conversion_rate'] }}%</h4>
                            <p class="mb-0">Конверсия пробных в платные</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_subscriptions'] }}</h4>
                            <p class="mb-0">Всего подписок</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-credit-card fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Табы для разных типов подписок -->
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-bordered" id="subscriptionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="trial-tab" data-bs-toggle="tab" data-bs-target="#trial" type="button" role="tab">
                        <i class="fas fa-clock me-2"></i>Пробные подписки
                        <span class="badge bg-warning ms-2">{{ $trialSubscriptions->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                        <i class="fas fa-check-circle me-2"></i>Активные подписки
                        <span class="badge bg-success ms-2">{{ $activeSubscriptions->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired" type="button" role="tab">
                        <i class="fas fa-exclamation-triangle me-2"></i>Просроченные подписки
                        <span class="badge bg-danger ms-2">{{ $expiredSubscriptions->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
                        <i class="fas fa-times-circle me-2"></i>Отмененные подписки
                        <span class="badge bg-secondary ms-2">{{ $cancelledSubscriptions->count() }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3" id="subscriptionTabsContent">
                <!-- Пробные подписки -->
                <div class="tab-pane fade show active" id="trial" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Проект</th>
                                    <th>Email</th>
                                    <th>Дата начала пробного периода</th>
                                    <th>Пробный период до</th>
                                    <th>Осталось дней</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trialSubscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-warning rounded-circle">
                                                    {{ substr($subscription->project->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $subscription->project->name }}</h6>
                                                <small class="text-muted">Проект ID: {{ $subscription->project->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $subscription->project->email }}</td>
                                    <td>{{ $subscription->starts_at ? $subscription->starts_at->format('d.m.Y') : 'Не указано' }}</td>
                                    <td>{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('d.m.Y') : 'Не указано' }}</td>
                                    <td>
                                        @if($subscription->trial_ends_at)
                                            @php
                                                $daysLeft = $subscription->getDaysUntilTrialEnd();
                                            @endphp
                                            @if($daysLeft > 0)
                                                <span class="badge bg-warning">{{ $daysLeft }} дней</span>
                                            @else
                                                <span class="badge bg-danger">Истекла</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Не указано</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-success" onclick="extendSubscription({{ $subscription->id }})">
                                            <i class="fas fa-plus"></i> Продлить
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>Нет пробных подписок</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Активные подписки -->
                <div class="tab-pane fade" id="active" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Проект</th>
                                    <th>Email</th>
                                    <th>Тариф</th>
                                    <th>Дата окончания</th>
                                    <th>Стоимость</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeSubscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-success rounded-circle">
                                                    {{ substr($subscription->project->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $subscription->project->name }}</h6>
                                                <small class="text-muted">Проект ID: {{ $subscription->project->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $subscription->project->email }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $subscription->plan_type ?? 'Стандарт' }}</span>
                                    </td>
                                    <td>{{ $subscription->expires_at ? $subscription->expires_at->format('d.m.Y') : 'Не указано' }}</td>
                                    <td>{{ $subscription->amount ?? 0 }} {{ $subscription->currency ?? 'USD' }}</td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" onclick="extendSubscription({{ $subscription->id }})">
                                            <i class="fas fa-plus"></i> Продлить
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>Нет активных подписок</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Просроченные подписки -->
                <div class="tab-pane fade" id="expired" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Проект</th>
                                    <th>Email</th>
                                    <th>Дата истечения</th>
                                    <th>Дней просрочки</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiredSubscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-danger rounded-circle">
                                                    {{ substr($subscription->project->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $subscription->project->name }}</h6>
                                                <small class="text-muted">Проект ID: {{ $subscription->project->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $subscription->project->email }}</td>
                                    <td>{{ $subscription->expires_at ? $subscription->expires_at->format('d.m.Y') : 'Не указано' }}</td>
                                    <td>
                                        @if($subscription->expires_at)
                                            @php
                                                $daysOverdue = now()->diffInDays($subscription->expires_at);
                                            @endphp
                                            <span class="badge bg-danger">{{ $daysOverdue }} дней</span>
                                        @else
                                            <span class="badge bg-secondary">Не указано</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-success" onclick="extendSubscription({{ $subscription->id }})">
                                            <i class="fas fa-plus"></i> Восстановить
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>Нет просроченных подписок</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Отмененные подписки -->
                <div class="tab-pane fade" id="cancelled" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Проект</th>
                                    <th>Email</th>
                                    <th>Дата отмены</th>
                                    <th>Причина</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cancelledSubscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-secondary rounded-circle">
                                                    {{ substr($subscription->project->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $subscription->project->name }}</h6>
                                                <small class="text-muted">Проект ID: {{ $subscription->project->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $subscription->project->email }}</td>
                                    <td>{{ $subscription->updated_at->format('d.m.Y') }}</td>
                                    <td>{{ $subscription->notes ?? 'Не указано' }}</td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>Нет отмененных подписок</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
@endsection

@push('scripts')
<script>
let currentSubscriptionId = null;

function extendSubscription(subscriptionId) {
    currentSubscriptionId = subscriptionId;
    $('#extendModal').modal('show');
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
</script>
@endpush
