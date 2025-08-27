@extends('admin.layouts.app')

@section('title', 'Настройки платежных систем')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Настройки платежных систем</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- LiqPay -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fab fa-cc-visa me-2 text-primary"></i>
                        LiqPay
                    </h5>
                    <div class="form-check form-switch">
                        <form method="POST" action="{{ route('admin.payment-settings.toggle', 'liqpay') }}" class="d-inline">
                            @csrf
                            <x-switch name="is_active" :checked="$liqpay && $liqpay->is_active" :id="'liqpay_is_active'" />
                        </form>
                    </div>
                </div>
                                 <div class="card-body">
                     <form method="POST" action="{{ route('admin.payment-settings.liqpay') }}">
                         @csrf
                         <div class="mb-3">
                             <label for="liqpay_public_key" class="form-label">Публичный ключ</label>
                             <input type="text" class="form-control" id="liqpay_public_key" 
                                    name="liqpay_public_key" 
                                    value="{{ $liqpay ? $liqpay->settings['public_key'] ?? '' : '' }}" 
                                    placeholder="Введите публичный ключ LiqPay">
                         </div>
                         <div class="mb-3">
                             <label for="liqpay_private_key" class="form-label">Приватный ключ</label>
                             <input type="password" class="form-control" id="liqpay_private_key" 
                                    name="liqpay_private_key" 
                                    value="{{ $liqpay ? $liqpay->settings['private_key'] ?? '' : '' }}" 
                                    placeholder="Введите приватный ключ LiqPay">
                         </div>
                         <button type="submit" class="btn btn-primary">
                             <i class="fas fa-save me-2"></i>Сохранить настройки LiqPay
                         </button>
                     </form>
                 </div>
            </div>
        </div>

        <!-- Stripe -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fab fa-stripe me-2 text-success"></i>
                        Stripe
                    </h5>
                    <div class="form-check form-switch">
                        <form method="POST" action="{{ route('admin.payment-settings.toggle', 'stripe') }}" class="d-inline">
                            @csrf
                            <x-switch name="is_active" :checked="$stripe && $stripe->is_active" :id="'stripe_is_active'" />
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payment-settings.stripe') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="stripe_public_key" class="form-label">Публичный ключ</label>
                            <input type="text" class="form-control" id="stripe_public_key" 
                                   name="stripe_public_key" 
                                   value="{{ $stripe ? $stripe->settings['public_key'] ?? '' : '' }}" 
                                   placeholder="Введите публичный ключ Stripe">
                        </div>
                        <div class="mb-3">
                            <label for="stripe_secret_key" class="form-label">Секретный ключ</label>
                            <input type="password" class="form-control" id="stripe_secret_key" 
                                   name="stripe_secret_key" 
                                   value="{{ $stripe ? $stripe->settings['secret_key'] ?? '' : '' }}" 
                                   placeholder="Введите секретный ключ Stripe">
                        </div>
                        <div class="mb-3">
                            <x-switch name="stripe_sandbox" :checked="$stripe && ($stripe->settings['sandbox'] ?? false)" :id="'stripe_sandbox'" :label="'Тестовый режим (Sandbox)'" />
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Сохранить настройки Stripe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- PayPal -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fab fa-paypal me-2 text-info"></i>
                        PayPal
                    </h5>
                    <div class="form-check form-switch">
                        <form method="POST" action="{{ route('admin.payment-settings.toggle', 'paypal') }}" class="d-inline">
                            @csrf
                            <x-switch name="is_active" :checked="$paypal && $paypal->is_active" :id="'paypal_is_active'" />
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payment-settings.paypal') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="paypal_client_id" class="form-label">Client ID</label>
                            <input type="text" class="form-control" id="paypal_client_id" 
                                   name="paypal_client_id" 
                                   value="{{ $paypal ? $paypal->settings['client_id'] ?? '' : '' }}" 
                                   placeholder="Введите Client ID PayPal">
                        </div>
                        <div class="mb-3">
                            <label for="paypal_secret" class="form-label">Secret</label>
                            <input type="password" class="form-control" id="paypal_secret" 
                                   name="paypal_secret" 
                                   value="{{ $paypal ? $paypal->settings['secret'] ?? '' : '' }}" 
                                   placeholder="Введите Secret PayPal">
                        </div>
                        <div class="mb-3">
                            <x-switch name="paypal_sandbox" :checked="$paypal && ($paypal->settings['sandbox'] ?? false)" :id="'paypal_sandbox'" :label="'Тестовый режим (Sandbox)'" />
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save me-2"></i>Сохранить настройки PayPal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Информация -->
        <div class="col-lg-6">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Информация
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Как получить ключи:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>LiqPay:</strong> 
                            <a href="https://www.liqpay.ua/" target="_blank" class="text-decoration-none">
                                Зарегистрируйтесь на liqpay.ua
                            </a>
                        </li>
                        <li class="mb-2">
                            <strong>Stripe:</strong> 
                            <a href="https://stripe.com/" target="_blank" class="text-decoration-none">
                                Зарегистрируйтесь на stripe.com
                            </a>
                        </li>
                        <li class="mb-2">
                            <strong>PayPal:</strong> 
                            <a href="https://developer.paypal.com/" target="_blank" class="text-decoration-none">
                                Создайте приложение на developer.paypal.com
                            </a>
                        </li>
                    </ul>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Важно:</strong> Включайте только одну платежную систему одновременно!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2"></i>
            <strong class="me-auto">Успех!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert">
        <div class="toast-header bg-danger text-white">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong class="me-auto">Ошибка!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            {{ session('error') }}
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Автоматически скрывать уведомления через 5 секунд
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var toasts = document.querySelectorAll('.toast');
        toasts.forEach(function(toast) {
            var bsToast = new bootstrap.Toast(toast);
            bsToast.hide();
        });
    }, 5000);
});
</script>
@endpush
