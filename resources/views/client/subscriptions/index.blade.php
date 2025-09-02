@extends('client.layouts.app')

@section('title', __('messages.subscriptions'))

@section('content')
<div class="dashboard-container">
    <div class="subscriptions-container">
        <!-- Заголовок страницы -->
        <div class="subscriptions-header">
            <h1>{{ __('messages.subscriptions') }}</h1>
        
        </div>

        <!-- Основной контент -->
        <div class="subscriptions-content">
            <!-- Текущая подписка -->
            <div class="subscription-card">
                <div class="subscription-header">
                    <h2>{{ __('messages.current_subscription') }}</h2>
                    @if($subscriptionData['status'] === 'active')
                        <span class="status-badge active">{{ __('messages.active') }}</span>
                    @elseif($subscriptionData['status'] === 'expired')
                        <span class="status-badge expired">{{ __('messages.expired') }}</span>
                    @elseif($subscriptionData['status'] === 'pending')
                        <span class="status-badge pending">{{ __('messages.pending') }}</span>
                    @elseif($subscriptionData['status'] === 'trial')
                        <span class="status-badge trial">{{ __('messages.trial') }}</span>
                    @elseif($subscriptionData['status'] === 'no_subscription')
                        <span class="status-badge no-subscription">{{ __('messages.no_subscription') }}</span>
                    @else
                        <span class="status-badge {{ $subscriptionData['status'] }}">{{ $subscriptionData['status'] }}</span>
                    @endif
                </div>
                
                <div class="subscription-details">
                    <div class="subscription-info">
                        <div class="info-item">
                            <span class="label">{{ __('messages.plan') }}:</span>
                            <span class="value">{{ $subscriptionData['plan'] }}</span>
                        </div>
                        @if($subscriptionData['starts_at'] !== __('messages.not_specified') && $subscriptionData['status'] !== 'trial')
                        <div class="info-item">
                            <span class="label">{{ __('messages.start_date') }}:</span>
                            <span class="value">{{ $subscriptionData['starts_at'] }}</span>
                        </div>
                        @endif
                        @if($subscriptionData['end_date'] !== __('messages.not_specified'))
                        <div class="info-item">
                            <span class="label">{{ __('messages.end_date') }}:</span>
                            <span class="value">{{ $subscriptionData['end_date'] }}</span>
                        </div>
                        @endif
                        @if($subscriptionData['trial_ends_at'] && $subscriptionData['status'] === 'trial')
                        <div class="info-item">
                            <span class="label">{{ __('messages.trial_period_until') }}:</span>
                            <span class="value">{{ $subscriptionData['trial_ends_at'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">{{ __('messages.trial_days_left') }}:</span>
                            <span class="value {{ $subscriptionData['trial_days_left'] <= 3 ? 'warning' : '' }}">
                                {{ $subscriptionData['trial_days_left'] }} {{ __('messages.days') }}
                            </span>
                        </div>
                        @endif
                        @if($subscriptionData['days_left'] > 0)
                        <div class="info-item">
                            <span class="label">{{ __('messages.days_left') }}:</span>
                            <span class="value {{ $subscriptionData['days_left'] <= 7 ? 'warning' : '' }}">
                                {{ $subscriptionData['days_left'] }} {{ __('messages.days') }}
                            </span>
                        </div>
                        @endif
                        @if($subscriptionData['status'] === 'trial')
                        <div class="info-item">
                            <span class="label">{{ __('messages.price') }}:</span>
                            <span class="value text-success">{{ __('messages.free_trial') }}</span>
                        </div>
                        @elseif($subscriptionData['price'] > 0)
                        <div class="info-item">
                            <span class="label">{{ __('messages.price') }}:</span>
                            <span class="value">{{ $subscriptionData['price'] }} {{ $subscriptionData['currency'] }}</span>
                        </div>
                        @endif
                        @if($subscriptionData['status'] !== 'trial')
                        <div class="info-item">
                            <span class="label">{{ __('messages.payment_status') }}:</span>
                            <span class="value">
                                @if($subscriptionData['payment_status'] === 'paid')
                                    <i class="fa fa-check text-success"></i> {{ __('messages.paid') }}
                                @elseif($subscriptionData['payment_status'] === 'pending')
                                    <i class="fa fa-clock text-warning"></i> {{ __('messages.payment_pending') }}
                                @elseif($subscriptionData['payment_status'] === 'failed')
                                    <i class="fa fa-times text-danger"></i> {{ __('messages.payment_failed') }}
                                @else
                                    <i class="fa fa-question text-muted"></i> {{ $subscriptionData['payment_status'] }}
                                @endif
                            </span>
                        </div>
                        @endif
                        @if($subscriptionData['status'] !== 'trial')
                        <div class="info-item">
                            <span class="label">{{ __('messages.auto_renewal') }}:</span>
                            <span class="value">
                                @if($subscriptionData['auto_renewal'])
                                    <i class="fa fa-check text-success"></i> {{ __('messages.enabled') }}
                                @else
                                    <i class="fa fa-times text-danger"></i> {{ __('messages.disabled') }}
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    
                                        <div class="subscription-actions">
                        @if($subscriptionData['status'] === 'active')
                            <button class="btn btn-success" onclick="renewSubscription()">
                                <i class="fa fa-refresh"></i> {{ __('messages.renew_subscription_btn') }}
                            </button>
                            <a href="{{ \App\Helpers\DomainHelper::url('landing', 'pricing') }}" class="btn btn-primary">
                                <i class="fa fa-exchange"></i> {{ __('messages.change_plan_btn') }}
                            </a>
                            @if($subscriptionData['auto_renewal'])
                                <button class="btn btn-outline-danger" onclick="cancelAutoRenewal()">
                                    <i class="fa fa-stop"></i> {{ __('messages.cancel_auto_renewal') }}
                                </button>
                            @endif
                        @elseif($subscriptionData['status'] === 'expired')
                            <button class="btn btn-success" onclick="renewSubscription()">
                                <i class="fa fa-refresh"></i> {{ __('messages.renew_subscription') }}
                            </button>

                        @elseif($subscriptionData['status'] === 'trial')
                            <a href="{{ \App\Helpers\DomainHelper::url('landing', 'pricing') }}" class="btn btn-success">
                                <i class="fa fa-credit-card"></i> {{ __('messages.select_paid_plan') }}
                            </a>
                        @elseif($subscriptionData['status'] === 'no_subscription')
                            <a href="{{ \App\Helpers\DomainHelper::url('landing', 'pricing') }}" class="btn btn-success">
                                <i class="fa fa-credit-card"></i> {{ __('messages.select_plan_btn') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Уведомления -->
            @if($subscriptionData['status'] === 'active' && $subscriptionData['days_left'] <= 7)
                <div class="alert alert-warning subscription-alert">
                    <i class="fa fa-exclamation-triangle"></i>
                    @if($subscriptionData['days_left'] <= 3)
                        {{ __('messages.subscription_expires_soon') }}: {{ $subscriptionData['days_left'] }} {{ __('messages.days') }}
                    @else
                        {{ __('messages.subscription_expires_in') }}: {{ $subscriptionData['days_left'] }} {{ __('messages.days') }}
                    @endif
                </div>
            @elseif($subscriptionData['status'] === 'expired')
                <div class="alert alert-danger subscription-alert">
                    <i class="fa fa-times-circle"></i>
                    {{ __('messages.subscription_expired') }}
                </div>

            @elseif($subscriptionData['status'] === 'trial')
                <div class="alert alert-success subscription-alert">
                    <i class="fa fa-gift"></i>
                    {{ __('messages.trial_period_active') }}
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


        </div>
    </div>
</div>

@push('scripts')
<script>
// Переменные для функций подписок
const confirmRenewSubscriptionMessage = '{{ __("messages.confirm_renew_subscription") }}';
const confirmCancelAutoRenewalMessage = '{{ __("messages.confirm_cancel_auto_renewal") }}';
const renewSubscriptionUrl = '{{ route("client.subscriptions.renew") }}';
const cancelSubscriptionUrl = '{{ route("client.subscriptions.cancel") }}';
const csrfTokenValue = '{{ csrf_token() }}';
</script>
<script src="{{ asset('client/js/subscriptions.js') }}"></script>
@endpush
@endsection 