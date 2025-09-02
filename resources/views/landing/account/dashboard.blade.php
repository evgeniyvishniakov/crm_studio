@extends('landing.layouts.app')

@section('title', __('landing.account_dashboard'))

@section('styles')
<style>
    /* Стили для вкладок */
    .nav-tabs.nav-bordered {
        border-bottom: 2px solid #e9ecef;
    }
    
    .nav-tabs.nav-bordered .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        color: #6c757d;
        font-weight: 500;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    
    .nav-tabs.nav-bordered .nav-link:hover {
        border-color: #dee2e6;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .nav-tabs.nav-bordered .nav-link.active {
        border-bottom-color: #0d6efd;
        color: #0d6efd;
        background-color: transparent;
    }
    
    .nav-tabs.nav-bordered .nav-link i {
        margin-right: 8px;
    }
    
    /* Стили для карточек */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem 1.5rem;
    }
    
    .card-title {
        color: #2c3e50;
        font-weight: 600;
        margin: 0;
    }
    
    /* Стили для таблиц */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    /* Стили для форм */
    .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }
    
    /* Стили для кнопок */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    /* Адаптивность */
    @media (max-width: 768px) {
        .nav-tabs.nav-bordered .nav-link {
            padding: 10px 15px;
            font-size: 14px;
        }
        
        .nav-tabs.nav-bordered .nav-link i {
            margin-right: 6px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-3">{{ __('landing.account_dashboard') }}</h1>
                <p class="lead text-muted">{{ __('landing.account_welcome') }}, {{ $project->name }}</p>
            </div>
        </div>
    </div>

    <!-- Вкладки -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered" id="accountTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                <i class="fas fa-tachometer-alt me-2"></i>{{ __('landing.account_overview') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions" type="button" role="tab">
                                <i class="fas fa-credit-card me-2"></i>{{ __('landing.account_subscriptions') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                                <i class="fas fa-key me-2"></i>{{ __('landing.account_change_password') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>{{ __('landing.account_profile') }}
                            </button>
                        </li>

                    </ul>

                    <div class="tab-content mt-4" id="accountTabsContent">
                        <!-- Вкладка Обзор -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="row">
                                <!-- Информация о проекте -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-building me-2"></i>{{ __('landing.account_project_info') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <strong>{{ __('landing.account_name') }}:</strong>
                                                <p class="mb-0">{{ $project->name }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <strong>{{ __('landing.account_email') }}:</strong>
                                                <p class="mb-0">{{ $project->email }}</p>
                                            </div>
                                            @if($project->phone)
                                            <div class="mb-3">
                                                <strong>{{ __('landing.account_phone') }}:</strong>
                                                <p class="mb-0">{{ $project->phone }}</p>
                                            </div>
                                            @endif
                                            @if($project->website)
                                            <div class="mb-3">
                                                <strong>{{ __('landing.account_website') }}:</strong>
                                                <p class="mb-0">
                                                    <a href="{{ $project->website }}" target="_blank">{{ $project->website }}</a>
                                                </p>
                                            </div>
                                            @endif
                                            <div class="mb-3">
                                                <strong>{{ __('landing.account_registration_date') }}:</strong>
                                                <p class="mb-0">{{ $project->registered_at ? $project->registered_at->format('d.m.Y') : __('landing.account_not_specified') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Статистика -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-chart-bar me-2"></i>{{ __('landing.account_statistics') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-6 mb-3">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-users text-primary"></i>
                                                    </div>
                                                    <h4 class="mb-1">{{ $project->clients->count() }}</h4>
                                                    <small class="text-muted">{{ __('landing.account_clients') }}</small>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-calendar-check text-success"></i>
                                                    </div>
                                                    <h4 class="mb-1">{{ $project->appointments->count() }}</h4>
                                                    <small class="text-muted">{{ __('landing.account_appointments') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Подписка -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-credit-card me-2"></i>{{ __('landing.account_subscription') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if($activeSubscription)
                                                <div class="mb-3">
                                                    <strong>{{ __('landing.account_status') }}:</strong>
                                                    @if($activeSubscription->status === 'trial')
                                                        <span class="badge bg-warning">{{ __('landing.account_trial_period') }}</span>
                                                    @elseif($activeSubscription->status === 'active')
                                                        <span class="badge bg-success">{{ __('landing.account_active') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $activeSubscription->status }}</span>
                                                    @endif
                                                </div>
                                                
                                                @if($activeSubscription->status === 'trial' && $activeSubscription->trial_ends_at)
                                                    <div class="mb-3">
                                                        <strong>{{ __('landing.account_trial_ends_at') }}:</strong>
                                                        <p class="mb-0">{{ $activeSubscription->trial_ends_at->format('d.m.Y') }}</p>
                                                        @php
                                                            $daysLeft = $activeSubscription->getDaysUntilTrialEnd();
                                                        @endphp
                                                        @if($daysLeft > 0)
                                                            <small class="text-warning">{{ $daysLeft }} {{ __('landing.account_days_left') }}</small>
                                                        @else
                                                            <small class="text-danger">{{ __('landing.account_trial_expired') }}</small>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                @if($activeSubscription->status === 'active' && $activeSubscription->expires_at)
                                                    <div class="mb-3">
                                                        <strong>{{ __('landing.account_subscription_until') }}:</strong>
                                                        <p class="mb-0">{{ $activeSubscription->expires_at->format('d.m.Y') }}</p>
                                                        @php
                                                            $daysLeft = $activeSubscription->getDaysUntilExpiration();
                                                        @endphp
                                                        @if($daysLeft > 0)
                                                            <small class="text-success">{{ $daysLeft }} {{ __('landing.account_days_left') }}</small>
                                                        @else
                                                            <small class="text-danger">{{ __('landing.account_subscription_expired') }}</small>
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <p class="text-muted mb-0">{{ __('landing.account_subscription_not_found') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Переход в CRM -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body text-center py-5">
                                            <div class="mb-4">
                                                <i class="fas fa-rocket fa-3x text-primary mb-3"></i>
                                                <h3>{{ __('landing.account_ready_to_work') }}</h3>
                                                <p class="text-muted">{{ __('landing.account_go_to_crm_text') }}</p>
                                            </div>
                                            
                                                                                        <div class="d-flex justify-content-center">
                                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-external-link-alt me-2"></i>{{ __('landing.account_go_to_crm') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка Подписки -->
                        <div class="tab-pane fade" id="subscriptions" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-credit-card me-2"></i>{{ __('landing.account_subscription_history') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if($project->subscriptions->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('landing.account_period') }}</th>
                                                                <th>{{ __('landing.account_status') }}</th>
                                                                <th>{{ __('landing.account_start_date') }}</th>
                                                                <th>{{ __('landing.account_end_date') }}</th>
                                                                <th>{{ __('landing.account_cost') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($project->subscriptions->sortByDesc('created_at') as $subscription)
                                                            <tr>
                                                                <td>
                                                                    @if($subscription->status === 'trial')
                                                                        <span class="badge bg-warning">{{ __('landing.account_trial') }}</span>
                                                                    @elseif($subscription->status === 'active')
                                                                        <span class="badge bg-success">{{ __('landing.account_active') }}</span>
                                                                    @elseif($subscription->status === 'expired')
                                                                        <span class="badge bg-danger">{{ __('landing.account_expired') }}</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">{{ $subscription->status }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($subscription->status === 'trial')
                                                                        {{ __('landing.account_trial') }}
                                                                    @else
                                                                        {{ $subscription->plan_type ?? __('landing.account_plan_type') }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $subscription->starts_at ? $subscription->starts_at->format('d.m.Y') : __('landing.account_not_specified') }}</td>
                                                                <td>
                                                                    @if($subscription->status === 'trial' && $subscription->trial_ends_at)
                                                                        {{ $subscription->trial_ends_at->format('d.m.Y') }}
                                                                    @elseif($subscription->expires_at)
                                                                        {{ $subscription->expires_at->format('d.m.Y') }}
                                                                    @else
                                                                        {{ __('landing.account_not_specified') }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $subscription->amount ?? 0 }} {{ $subscription->currency ?? 'USD' }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">{{ __('landing.account_subscription_history_empty') }}</h5>
                                                    <p class="text-muted">{{ __('landing.account_no_active_subscriptions') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка Смена пароля -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-key me-2"></i>{{ __('landing.account_change_password') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Сообщение об успехе (изначально скрыто) -->
                                            <div id="password-success-message" class="alert alert-success text-center" style="display: none;">
                                                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                                                    <h5 class="mb-2">Ссылка отправлена!</h5>
                                                    <p class="mb-0">Ссылка для сброса пароля отправлена на указанный email.</p>
                                                    <p class="text-muted small mt-2">Проверьте папку "Входящие" или "Спам".</p>
                                                </div>

                                            <!-- Форма (изначально показана) -->
                                            <div id="password-form">
                                                <form id="password-reset-form">
                                                    @csrf
                                                    
                                                    <div class="mb-3">
                                                        <label for="password_email" class="form-label">Email руководителя <span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" id="password_email" name="email" value="{{ Auth::guard('client')->user()->email }}" required readonly>
                                                        <small class="form-text text-muted">Ссылка для сброса пароля будет отправлена на этот email</small>
                                                    </div>

                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-primary" id="password-submit-btn">
                                                            <i class="fas fa-paper-plane me-2"></i>Отправить ссылку для сброса
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка Профиль -->
                        <div class="tab-pane fade" id="profile" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-edit me-2"></i>Редактировать профиль
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Сообщение об успехе (изначально скрыто) -->
                                            <div id="profile-success-message" class="alert alert-success text-center" style="display: none;">
                                                <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                                                <h5 class="mb-2">Профиль обновлен!</h5>
                                                <p class="mb-0">Изменения успешно сохранены.</p>
                                            </div>

                                            <!-- Форма профиля -->
                                            <div id="profile-form">
                                                <form id="profile-update-form">
                                                @csrf
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="name" class="form-label">Название проекта <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="name" name="name" value="{{ $project->name }}" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="phone" class="form-label">Телефон</label>
                                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $project->phone }}">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email <span class="text-muted">(неизменяем)</span></label>
                                                    <input type="email" class="form-control" id="email" value="{{ $project->email }}" disabled>
                                                    <small class="text-muted">Email не может быть изменен</small>
                                                </div>

                                                @if($project->website)
                                                <div class="mb-3">
                                                    <label for="website" class="form-label">Сайт</label>
                                                    <input type="url" class="form-control" id="website" value="{{ $project->website }}" disabled>
                                                    <small class="text-muted">Сайт не может быть изменен</small>
                                                </div>
                                                @endif

                                                <div class="mb-3">
                                                    <label for="address" class="form-label">Адрес</label>
                                                    <textarea class="form-control" id="address" rows="2" disabled>{{ $project->address ?? 'Не указан' }}</textarea>
                                                    <small class="text-muted">Адрес не может быть изменен</small>
                                                </div>

                                                <div class="d-grid">
                                                        <button type="submit" class="btn btn-primary" id="profile-submit-btn">
                                                        <i class="fas fa-save me-2"></i>Сохранить изменения
                                                    </button>
                                                </div>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('form').on('submit', function() {
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var originalText = $btn.html();
        
        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Обработка...');
        $btn.prop('disabled', true);
        
        setTimeout(function() {
            $btn.html(originalText);
            $btn.prop('disabled', false);
        }, 3000);
    });
    
    $('#password-reset-form').on('submit', function(e) {
    $('#password-reset-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $('#password-submit-btn');
        var originalText = $btn.html();
        
        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Отправка...');
        $btn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("landing.account.password.email") }}',
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#password-form').hide();
                $('#password-success-message').show();
            },
            error: function(xhr) {
                var errorMessage = 'Произошла ошибка при отправке. Попробуйте еще раз.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showAlert('danger', errorMessage);
                
                $btn.html(originalText);
                $btn.prop('disabled', false);
            }
        });
    });

    $('#profile-update-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $('#profile-submit-btn');
        var originalText = $btn.html();
        
        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Сохранение...');
        $btn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("landing.account.profile.update") }}',
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#profile-success-message').show();
                
                setTimeout(function() {
                    $('#profile-success-message').hide();
                }, 3000);
                
                $btn.html(originalText);
                $btn.prop('disabled', false);
            },
            error: function(xhr) {
                var errorMessage = 'Произошла ошибка при сохранении. Попробуйте еще раз.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showAlert('danger', errorMessage);
                
                $btn.html(originalText);
                $btn.prop('disabled', false);
            }
        });
    });
    
    @if(session('success'))
        showAlert('success', '{{ session("success") }}');
    @endif
    
    @if($errors->any())
        showAlert('danger', 'Пожалуйста, исправьте ошибки в форме');
    @endif
});

function showAlert(type, message) {
    var alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.tab-content').prepend(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush
@endsection
