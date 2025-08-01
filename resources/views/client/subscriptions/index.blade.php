@extends('client.layouts.app')

@section('title', __('messages.subscriptions'))

@section('content')
<div class="dashboard-container">
    <div class="subscriptions-container">
        <!-- Заголовок страницы -->
        <div class="subscriptions-header">
            <h1>{{ __('messages.subscriptions') }}</h1>
            <div id="notification"></div>
        </div>

        <!-- Основной контент -->
        <div class="subscriptions-content">
            <!-- Текущая подписка -->
            <div class="subscription-card">
                <div class="subscription-header">
                    <h2>{{ __('messages.current_subscription') }}</h2>
                    @if($subscription['status'] === 'active')
                        <span class="status-badge active">{{ __('messages.active') }}</span>
                    @elseif($subscription['status'] === 'expired')
                        <span class="status-badge expired">{{ __('messages.expired') }}</span>
                    @else
                        <span class="status-badge pending">{{ __('messages.pending') }}</span>
                    @endif
                </div>
                
                <div class="subscription-details">
                    <div class="subscription-info">
                        <div class="info-item">
                            <span class="label">{{ __('messages.plan') }}:</span>
                            <span class="value">{{ $subscription['plan'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">{{ __('messages.end_date') }}:</span>
                            <span class="value">{{ $subscription['end_date'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">{{ __('messages.days_left') }}:</span>
                            <span class="value {{ $subscription['days_left'] <= 7 ? 'warning' : '' }}">
                                {{ $subscription['days_left'] }} {{ __('messages.days') }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">{{ __('messages.price') }}:</span>
                            <span class="value">{{ $subscription['price'] }} {{ $subscription['currency'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">{{ __('messages.auto_renewal') }}:</span>
                            <span class="value">
                                @if($subscription['auto_renewal'])
                                    <i class="fa fa-check text-success"></i> {{ __('messages.enabled') }}
                                @else
                                    <i class="fa fa-times text-danger"></i> {{ __('messages.disabled') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="subscription-actions">
                        @if($subscription['status'] === 'active')
                            @if($subscription['days_left'] <= 7)
                                <button class="btn btn-warning" onclick="renewSubscription()">
                                    <i class="fa fa-refresh"></i> {{ __('messages.renew_subscription') }}
                                </button>
                            @endif
                            <button class="btn btn-primary" onclick="changePlan()">
                                <i class="fa fa-exchange"></i> {{ __('messages.change_plan') }}
                            </button>
                            @if($subscription['auto_renewal'])
                                <button class="btn btn-outline-danger" onclick="cancelAutoRenewal()">
                                    <i class="fa fa-stop"></i> {{ __('messages.cancel_auto_renewal') }}
                                </button>
                            @endif
                        @elseif($subscription['status'] === 'expired')
                            <button class="btn btn-success" onclick="renewSubscription()">
                                <i class="fa fa-refresh"></i> {{ __('messages.renew_subscription') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Уведомления -->
            @if($subscription['status'] === 'active' && $subscription['days_left'] <= 7)
                <div class="alert alert-warning subscription-alert">
                    <i class="fa fa-exclamation-triangle"></i>
                    @if($subscription['days_left'] <= 3)
                        {{ __('messages.subscription_expires_soon') }}: {{ $subscription['days_left'] }} {{ __('messages.days') }}
                    @else
                        {{ __('messages.subscription_expires_in') }}: {{ $subscription['days_left'] }} {{ __('messages.days') }}
                    @endif
                </div>
            @elseif($subscription['status'] === 'expired')
                <div class="alert alert-danger subscription-alert">
                    <i class="fa fa-times-circle"></i>
                    {{ __('messages.subscription_expired') }}
                </div>
            @endif

            <!-- История платежей -->
            <div class="payment-history-card">
                <h2>{{ __('messages.payment_history') }}</h2>
                <div class="payment-history-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentHistory as $payment)
                            <tr>
                                <td>{{ $payment['date'] }}</td>
                                <td>{{ $payment['description'] }}</td>
                                <td>{{ $payment['amount'] }} {{ $payment['currency'] }}</td>
                                <td>
                                    @if($payment['status'] === 'completed')
                                        <span class="badge badge-success">{{ __('messages.completed') }}</span>
                                    @elseif($payment['status'] === 'pending')
                                        <span class="badge badge-warning">{{ __('messages.pending') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('messages.failed') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Доступные планы -->
            <div class="available-plans-card">
                <h2>{{ __('messages.available_plans') }}</h2>
                <div class="plans-grid">
                    @foreach($availablePlans as $plan)
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3>{{ $plan['name'] }}</h3>
                            <div class="plan-price">
                                <span class="price">{{ $plan['price'] }}</span>
                                <span class="currency">{{ $plan['currency'] }}</span>
                                <span class="period">/{{ __('messages.month') }}</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                @foreach($plan['features'] as $feature)
                                <li><i class="fa fa-check"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="plan-actions">
                            <button class="btn btn-outline-primary" onclick="selectPlan('{{ $plan['id'] }}')">
                                {{ __('messages.select_plan') }}
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Переменные для функций подписок
const confirmRenewSubscriptionMessage = '{{ __("messages.confirm_renew_subscription") }}';
const confirmChangePlanMessage = '{{ __("messages.confirm_change_plan") }}';
const confirmCancelAutoRenewalMessage = '{{ __("messages.confirm_cancel_auto_renewal") }}';
const renewSubscriptionUrl = '{{ route("client.subscriptions.renew") }}';
const changePlanUrl = '{{ route("client.subscriptions.change-plan") }}';
const cancelSubscriptionUrl = '{{ route("client.subscriptions.cancel") }}';
const csrfTokenValue = '{{ csrf_token() }}';
</script>
<script src="{{ asset('client/js/subscriptions.js') }}"></script>
@endsection 