@extends('client.layouts.app')

@section('title', __('messages.salary'))

@section('content')

<div class="dashboard-container">
    <div class="settings-header">
        <h1>{{ __('messages.salary') }}</h1>
    </div>
    
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="salary-overview">
            <i class="fa fa-chart-bar" style="margin-right:8px;"></i>{{ __('messages.salary_overview') }}
        </button>
        <button class="tab-button" data-tab="salary-settings">
            <i class="fa fa-cog" style="margin-right:8px;"></i>{{ __('messages.salary_settings') }}
        </button>
        <button class="tab-button" data-tab="salary-calculations">
            <i class="fa fa-calculator" style="margin-right:8px;"></i>{{ __('messages.salary_calculations') }}
        </button>
        <button class="tab-button" data-tab="salary-payments">
            <i class="fa fa-cash-register" style="margin-right:8px;"></i>{{ __('messages.salary_payments') }}
        </button>
        <button class="tab-button" data-tab="salary-reports">
            <i class="fa fa-chart-line" style="margin-right:8px;"></i>{{ __('messages.salary_reports') }}
        </button>
    </div>
    
    <div class="settings-content">
        <!-- Вкладка обзора -->
        <div class="settings-pane" id="tab-salary-overview">
            <h5>{{ __('messages.salary_overview') }}</h5>
            
            <!-- Статистика -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">{{ __('messages.total_employees') }}</h5>
                                    <h3 class="mt-3 mb-3">{{ $stats['total_employees'] ?? 0 }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded">
                                        <i class="mdi mdi-account-group font-20 text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">{{ __('messages.payments_this_month') }}</h5>
                                    <h3 class="mt-3 mb-3">{{ $stats['payments_this_month'] ?? 0 }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success rounded">
                                        <i class="mdi mdi-cash-multiple font-20 text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">{{ __('messages.calculations_this_month') }}</h5>
                                    <h3 class="mt-3 mb-3">{{ $stats['calculations_this_month'] ?? 0 }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-info rounded">
                                        <i class="mdi mdi-chart-line font-20 text-info"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">{{ __('messages.total_payments_this_month') }}</h5>
                                    <h3 class="mt-3 mb-3">{{ \App\Helpers\CurrencyHelper::format($stats['total_payments_this_month'] ?? 0) }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-warning rounded">
                                        <i class="mdi mdi-cash font-20 text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Последние расчеты -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">{{ __('messages.recent_calculations') }}</h4>
                    </div>

                    @if(($recentCalculations ?? collect())->count() > 0)
                        <div class="table-wrapper">
                            <table class="table-striped salary-overview-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.employee') }}</th>
                                        <th>{{ __('messages.period') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCalculations ?? [] as $calculation)
                                    <tr>
                                        <td>{{ $calculation->user->name }}</td>
                                        <td>
                                            {{ $calculation->period_start->format('d.m.Y') }} - 
                                            {{ $calculation->period_end->format('d.m.Y') }}
                                        </td>
                                        <td>
                                            <span class="currency-amount" data-amount="{{ $calculation->total_salary }}">
                                {{ \App\Helpers\CurrencyHelper::format($calculation->total_salary) }}
                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-{{ $calculation->status === 'pending' ? 'pending' : ($calculation->status === 'approved' ? 'done' : 'cancel') }}">
                                                {{ $calculation->status_text }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Мобильные карточки для расчетов -->
                        <div class="calculations-cards">
                            @foreach($recentCalculations ?? [] as $calculation)
                            <div class="calculation-card">
                                <div class="calculation-card-header">
                                    <div class="calculation-main-info">
                                        <div class="calculation-employee">
                                            <i class="mdi mdi-account"></i>
                                            <span>{{ $calculation->user->name }}</span>
                                        </div>
                                        <div class="calculation-status">
                                            <span class="status-badge status-{{ $calculation->status === 'pending' ? 'pending' : ($calculation->status === 'approved' ? 'done' : 'cancel') }}">
                                                {{ $calculation->status_text }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="calculation-info">
                                    <div class="calculation-info-item">
                                        <div class="calculation-info-label">
                                            <i class="mdi mdi-calendar-range"></i>
                                            <span>{{ __('messages.period') }}</span>
                                        </div>
                                        <div class="calculation-info-value">
                                            {{ $calculation->period_start->format('d.m.Y') }} - {{ $calculation->period_end->format('d.m.Y') }}
                                        </div>
                                    </div>
                                    <div class="calculation-info-item">
                                        <div class="calculation-info-label">
                                            <i class="mdi mdi-cash"></i>
                                            <span>{{ __('messages.amount') }}</span>
                                        </div>
                                        <div class="calculation-info-value">
                                            <span class="currency-amount" data-amount="{{ $calculation->total_salary }}">
                                                {{ \App\Helpers\CurrencyHelper::format($calculation->total_salary) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-information-outline text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">{{ __('messages.no_calculations') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Последние выплаты -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">{{ __('messages.recent_payments') }}</h4>
                    </div>

                    @if(($recentPayments ?? collect())->count() > 0)
                        <div class="table-wrapper">
                            <table class="table-striped salary-overview-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.employee') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th>{{ __('messages.date') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments ?? [] as $payment)
                                    <tr>
                                        <td>{{ $payment->user->name }}</td>
                                        <td>
                                            <span class="currency-amount" data-amount="{{ $payment->amount }}">
                                {{ \App\Helpers\CurrencyHelper::format($payment->amount) }}
                            </span>
                                        </td>
                                        <td>{{ $payment->payment_date->format('d.m.Y') }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $payment->status === 'pending' ? 'pending' : ($payment->status === 'approved' ? 'done' : 'cancel') }}">
                                                {{ $payment->status_text }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Мобильные карточки для выплат -->
                        <div class="payments-cards">
                            @foreach($recentPayments ?? [] as $payment)
                            <div class="payment-card">
                                <div class="payment-card-header">
                                    <div class="payment-main-info">
                                        <div class="payment-employee">
                                            <i class="mdi mdi-account"></i>
                                            <span>{{ $payment->user->name }}</span>
                                        </div>
                                        <div class="payment-status">
                                            <span class="status-badge status-{{ $payment->status === 'pending' ? 'pending' : ($payment->status === 'approved' ? 'done' : 'cancel') }}">
                                                {{ $payment->status_text }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-info">
                                    <div class="payment-info-item">
                                        <div class="payment-info-label">
                                            <i class="mdi mdi-cash"></i>
                                            <span>{{ __('messages.amount') }}</span>
                                        </div>
                                        <div class="payment-info-value">
                                            <span class="currency-amount" data-amount="{{ $payment->amount }}">
                                                {{ \App\Helpers\CurrencyHelper::format($payment->amount) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="payment-info-item">
                                        <div class="payment-info-label">
                                            <i class="mdi mdi-calendar"></i>
                                            <span>{{ __('messages.date') }}</span>
                                        </div>
                                        <div class="payment-info-value">
                                            {{ $payment->payment_date->format('d.m.Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-information-outline text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">{{ __('messages.no_payments') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Вкладка настроек зарплаты -->
        <div class="settings-pane" id="tab-salary-settings" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>{{ __('messages.salary_settings') }}</h5>
                <button class="btn btn-primary" onclick="showSalarySettingModal()">
                    <i class="fa fa-plus"></i> {{ __('messages.add_salary_settings') }}
                </button>
            </div>

            <!-- Таблица настроек зарплаты -->
            <div class="table-wrapper">
                <table class="table-striped salary-settings-table" id="salarySettingsTable">
                    <thead>
                        <tr>
                            <th>{{ __('messages.employee') }}</th>
                            <th>{{ __('messages.salary_type') }}</th>
                            <th>{{ __('messages.service_percentage') }}</th>
                            <th>{{ __('messages.sales_percentage') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="salary-settings-tbody">
                        @foreach($salarySettings ?? [] as $setting)
                        <tr data-setting-id="{{ $setting->id }}">
                            <td>{{ $setting->user->name }}</td>
                            <td>
                                {{ $setting->salary_type_text }}
                            </td>
                            <td>
                                @if($setting->service_percentage)
                                    {{ $setting->service_percentage }}%
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($setting->sales_percentage)
                                    {{ $setting->sales_percentage }}%
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <button class="btn-edit" onclick="editSalarySetting({{ $setting->id }})" title="Редактировать">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </button>
                                    <button class="btn-delete" onclick="deleteSalarySetting({{ $setting->id }})" title="Удалить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Мобильные карточки для настроек зарплаты -->
            <div class="salary-settings-cards">
                @foreach($salarySettings ?? [] as $setting)
                <div class="salary-setting-card">
                    <div class="salary-setting-card-header">
                        <div class="salary-setting-main-info">
                            <div class="salary-setting-employee">
                                <i class="mdi mdi-account"></i>
                                <span>{{ $setting->user->name }}</span>
                            </div>
                            <div class="salary-setting-type">
                                <span class="type-badge type-{{ $setting->salary_type }}">
                                    {{ $setting->salary_type_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="salary-setting-info">
                        <div class="salary-setting-info-item">
                            <div class="salary-setting-info-label">
                                <i class="mdi mdi-percent"></i>
                                <span>{{ __('messages.service_percentage') }}</span>
                            </div>
                            <div class="salary-setting-info-value">
                                @if($setting->service_percentage)
                                    {{ $setting->service_percentage }}%
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="salary-setting-info-item">
                            <div class="salary-setting-info-label">
                                <i class="mdi mdi-chart-line"></i>
                                <span>{{ __('messages.sales_percentage') }}</span>
                            </div>
                            <div class="salary-setting-info-value">
                                @if($setting->sales_percentage)
                                    {{ $setting->sales_percentage }}%
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="salary-setting-actions">
                        <button class="btn-edit" onclick="editSalarySetting({{ $setting->id }})" title="Редактировать">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                        </button>
                        <button class="btn-delete" onclick="deleteSalarySetting({{ $setting->id }})" title="Удалить">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            @if(($salarySettings ?? collect())->count() == 0)
                <div class="text-center py-5">
                    <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                                <h5>{{ __('messages.salary_settings_absent') }}</h5>
            <p class="text-muted">{{ __('messages.add_salary_settings_for_employees') }}</p>
                </div>
            @endif
        </div>

        <!-- Вкладка расчетов зарплаты -->
        <div class="settings-pane" id="tab-salary-calculations" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>{{ __('messages.salary_calculations_title') }}</h5>
                <button class="btn btn-primary" onclick="showSalaryCalculationModal()">
                    <i class="fa fa-plus"></i> {{ __('messages.calculate_salary') }}
                </button>
            </div>

            <!-- Таблица расчетов зарплаты -->
            <div class="table-wrapper">
                <table class="table-striped salary-calculations-table" id="salaryCalculationsTable">
                    <thead>
                        <tr>
                            <th>{{ __('messages.employee') }}</th>
                            <th>{{ __('messages.period') }}</th>
                            <th>{{ __('messages.services') }}</th>
                            <th>{{ __('messages.sales') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="salary-calculations-tbody">
                        @foreach($salaryCalculations ?? [] as $calculation)
                        <tr data-calculation-id="{{ $calculation->id }}">
                            <td>{{ $calculation->user->name }}</td>
                            <td>
                                {{ $calculation->period_start->format('d.m.Y') }} - 
                                {{ $calculation->period_end->format('d.m.Y') }}
                            </td>
                            <td>{{ $calculation->services_count }} ({{ \App\Helpers\CurrencyHelper::format($calculation->services_amount) }})</td>
                            <td>{{ $calculation->sales_count }} ({{ \App\Helpers\CurrencyHelper::format($calculation->sales_amount) }})</td>
                            <td>
                                <span class="currency-amount" data-amount="{{ $calculation->total_salary }}">
                                    {{ \App\Helpers\CurrencyHelper::format($calculation->total_salary) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge">
                                    {{ $calculation->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <button class="btn-view" onclick="viewSalaryCalculation({{ $calculation->id }})" title="Просмотр">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    @if($calculation->canApprove())
                                    <button class="btn-edit" onclick="approveSalaryCalculation({{ $calculation->id }})" title="Утвердить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    @endif
                                    @if($calculation->canDelete())
                                    <button class="btn-delete" onclick="deleteSalaryCalculation({{ $calculation->id }})" title="Удалить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Мобильные карточки для расчетов зарплаты -->
            <div class="salary-calculations-cards">
                @foreach($salaryCalculations ?? [] as $calculation)
                <div class="salary-calculation-card">
                    <div class="salary-calculation-card-header">
                        <div class="salary-calculation-main-info">
                            <div class="salary-calculation-employee">
                                <i class="mdi mdi-account"></i>
                                <span>{{ $calculation->user->name }}</span>
                            </div>
                            <div class="salary-calculation-status">
                                <span class="status-badge">
                                    {{ $calculation->status_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="salary-calculation-info">
                        <div class="salary-calculation-info-item">
                            <div class="salary-calculation-info-label">
                                <i class="mdi mdi-calendar-range"></i>
                                <span>{{ __('messages.period') }}</span>
                            </div>
                            <div class="salary-calculation-info-value">
                                {{ $calculation->period_start->format('d.m.Y') }} - {{ $calculation->period_end->format('d.m.Y') }}
                            </div>
                        </div>
                        <div class="salary-calculation-info-item">
                            <div class="salary-calculation-info-label">
                                <i class="mdi mdi-briefcase"></i>
                                <span>{{ __('messages.services') }}</span>
                            </div>
                            <div class="salary-calculation-info-value">
                                {{ $calculation->services_count }} ({{ \App\Helpers\CurrencyHelper::format($calculation->services_amount) }})
                            </div>
                        </div>
                        <div class="salary-calculation-info-item">
                            <div class="salary-calculation-info-label">
                                <i class="mdi mdi-chart-line"></i>
                                <span>{{ __('messages.sales') }}</span>
                            </div>
                            <div class="salary-calculation-info-value">
                                {{ $calculation->sales_count }} ({{ \App\Helpers\CurrencyHelper::format($calculation->sales_amount) }})
                            </div>
                        </div>
                        <div class="salary-calculation-info-item">
                            <div class="salary-calculation-info-label">
                                <i class="mdi mdi-cash"></i>
                                <span>{{ __('messages.amount') }}</span>
                            </div>
                            <div class="salary-calculation-info-value">
                                <span class="currency-amount" data-amount="{{ $calculation->total_salary }}">
                                    {{ \App\Helpers\CurrencyHelper::format($calculation->total_salary) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="salary-calculation-actions">
                        <button class="btn-view" onclick="viewSalaryCalculation({{ $calculation->id }})" title="Просмотр">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        @if($calculation->canApprove())
                        <button class="btn-edit" onclick="approveSalaryCalculation({{ $calculation->id }})" title="Утвердить">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        @endif
                        @if($calculation->canDelete())
                        <button class="btn-delete" onclick="deleteSalaryCalculation({{ $calculation->id }})" title="Удалить">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if(($salaryCalculations ?? collect())->count() == 0)
                <div class="text-center py-5">
                    <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                    <h5>{{ __('messages.no_calculations_found') }}</h5>
                    <p class="text-muted">Создайте расчет зарплаты для ваших сотрудников</p>
                </div>
            @endif
        </div>

        <!-- Вкладка выплат -->
        <div class="settings-pane" id="tab-salary-payments" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>{{ __('messages.salary_payments_title') }}</h5>
                <button class="btn btn-primary" onclick="showSalaryPaymentModal()">
                    <i class="fa fa-plus"></i> {{ __('messages.create_payment') }}
                </button>
            </div>

            <!-- Таблица выплат -->
            <div class="table-wrapper">
                <table class="table-striped salary-payments-table" id="salaryPaymentsTable">
                    <thead>
                        <tr>
                            <th>{{ __('messages.employee') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.payment_method') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="salary-payments-tbody">
                        @foreach($salaryPayments ?? [] as $payment)
                        <tr data-payment-id="{{ $payment->id }}">
                            <td>{{ $payment->user->name }}</td>
                            <td>
                                <span class="currency-amount" data-amount="{{ $payment->amount }}">
                                    {{ \App\Helpers\CurrencyHelper::format($payment->amount) }}
                                </span>
                            </td>
                            <td>{{ $payment->payment_date->format('d.m.Y') }}</td>
                            <td>{{ $payment->payment_method_text }}</td>
                            <td>
                                <span class="status-badge status-{{ $payment->status === 'pending' ? 'pending' : ($payment->status === 'approved' ? 'done' : 'cancelled') }}">
                                    {{ $payment->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <button class="btn-view" onclick="viewSalaryPayment({{ $payment->id }})" title="Просмотр">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    @if($payment->canApprove())
                                    <button class="btn-edit" onclick="approveSalaryPayment({{ $payment->id }})" title="Подтвердить выплату">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    @endif
                                    @if($payment->canDelete())
                                    <button class="btn-delete" onclick="deleteSalaryPayment({{ $payment->id }})" title="Удалить">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Мобильные карточки для выплат зарплаты -->
            <div class="salary-payments-cards">
                @foreach($salaryPayments ?? [] as $payment)
                <div class="salary-payment-card">
                    <div class="salary-payment-card-header">
                        <div class="salary-payment-main-info">
                            <div class="salary-payment-employee">
                                <i class="mdi mdi-account"></i>
                                <span>{{ $payment->user->name }}</span>
                            </div>
                            <div class="salary-payment-status">
                                <span class="status-badge status-{{ $payment->status === 'pending' ? 'pending' : ($payment->status === 'approved' ? 'done' : 'cancelled') }}">
                                    {{ $payment->status_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="salary-payment-info">
                        <div class="salary-payment-info-item">
                            <div class="salary-payment-info-label">
                                <i class="mdi mdi-cash"></i>
                                <span>{{ __('messages.amount') }}</span>
                            </div>
                            <div class="salary-payment-info-value">
                                <span class="currency-amount" data-amount="{{ $payment->amount }}">
                                    {{ \App\Helpers\CurrencyHelper::format($payment->amount) }}
                                </span>
                            </div>
                        </div>
                        <div class="salary-payment-info-item">
                            <div class="salary-payment-info-label">
                                <i class="mdi mdi-calendar"></i>
                                <span>{{ __('messages.date') }}</span>
                            </div>
                            <div class="salary-payment-info-value">
                                {{ $payment->payment_date->format('d.m.Y') }}
                            </div>
                        </div>
                        <div class="salary-payment-info-item">
                            <div class="salary-payment-info-label">
                                <i class="mdi mdi-credit-card"></i>
                                <span>{{ __('messages.payment_method') }}</span>
                            </div>
                            <div class="salary-payment-info-value">
                                {{ $payment->payment_method_text }}
                            </div>
                        </div>
                    </div>
                    <div class="salary-payment-actions">
                        <button class="btn-view" onclick="viewSalaryPayment({{ $payment->id }})" title="Просмотр">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        @if($payment->canApprove())
                        <button class="btn-edit" onclick="approveSalaryPayment({{ $payment->id }})" title="Подтвердить выплату">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        @endif
                        @if($payment->canDelete())
                        <button class="btn-delete" onclick="deleteSalaryPayment({{ $payment->id }})" title="Удалить">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if(($salaryPayments ?? collect())->count() == 0)
                <div class="text-center py-5">
                    <i class="fas fa-cash-register fa-3x text-muted mb-3"></i>
                    <h5>{{ __('messages.no_payments_found') }}</h5>
                    <p class="text-muted">Создайте выплату зарплаты для ваших сотрудников</p>
                </div>
            @endif
        </div>

        <!-- Вкладка отчетов -->
        <div class="settings-pane" id="tab-salary-reports" style="display: none;">
            <h5>{{ __('messages.salary_reports_title') }}</h5>
            
            <!-- Статистика по месяцам -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">{{ __('messages.monthly_statistics') }}</h4>
                            <div class="table-wrapper">
                                <table class="table-striped salary-reports-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.month') }}</th>
                                            <th>{{ __('messages.payments_count_header') }}</th>
                                            <th>{{ __('messages.total_amount') }}</th>
                                            <th>{{ __('messages.average_payment') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthlyStats ?? [] as $stat)
                                        <tr>
                                            <td>{{ $stat->year }}/{{ $stat->month }}</td>
                                            <td>{{ $stat->payments_count }}</td>
                                            <td>
                                                <span class="currency-amount" data-amount="{{ $stat->total_amount }}">
                                    {{ \App\Helpers\CurrencyHelper::format($stat->total_amount) }}
                                </span>
                                            </td>
                                            <td>
                                                <span class="currency-amount" data-amount="{{ $stat->avg_amount }}">
                                    {{ \App\Helpers\CurrencyHelper::format($stat->avg_amount) }}
                                </span>
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

            <!-- Топ сотрудников -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">{{ __('messages.top_employees_by_salary') }}</h4>
                            <div class="table-wrapper">
                                <table class="table-striped salary-reports-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.employee') }}</th>
                                            <th>{{ __('messages.total_earnings') }}</th>
                                            <th>{{ __('messages.payments_count') }}</th>
                                            <th>{{ __('messages.average_payment') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topEmployees ?? [] as $employee)
                                        <tr>
                                            <td>{{ $employee->user->name }}</td>
                                            <td>
                                                <span class="currency-amount" data-amount="{{ $employee->total_earned }}">
                                    {{ \App\Helpers\CurrencyHelper::format($employee->total_earned) }}
                                </span>
                                            </td>
                                            <td>{{ $employee->payments_count }}</td>
                                            <td>
                                                <span class="currency-amount" data-amount="{{ $employee->total_earned / $employee->payments_count }}">
                                    {{ \App\Helpers\CurrencyHelper::format($employee->total_earned / $employee->payments_count) }}
                                </span>
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
        </div>
    </div>
</div>

<!-- Модальные окна для настроек зарплаты -->

<!-- Модальное окно добавления/редактирования настроек зарплаты -->
<div id="salarySettingModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2 id="salarySettingModalTitle">{{ __('messages.add_salary_settings_modal') }}</h2>
            <span class="close" onclick="closeSalarySettingModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="salarySettingForm">
                @csrf
                <input type="hidden" name="setting_id" id="settingId">
                <div class="form-row">
                    <div class="form-group">
                        <label>{{ __('messages.select_employee') }} *</label>
                        <select name="user_id" required class="form-control" id="salarySettingUserId">
                            <option value="">{{ __('messages.select_employee') }}</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.salary_type_label') }} *</label>
                        <select name="salary_type" required class="form-control" id="salaryType" onchange="toggleSalaryFields()">
                            <option value="">{{ __('messages.select_type') }}</option>
                            <option value="fixed">{{ __('messages.fixed_salary') }}</option>
                            <option value="percentage">{{ __('messages.percentage_salary') }}</option>
                            <option value="mixed">{{ __('messages.mixed_salary') }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" id="fixedSalaryRow" style="display: none;">
                    <div class="form-group">
                        <label>{{ __('messages.fixed_salary_label') }} ({{ \App\Helpers\CurrencyHelper::getSymbol() }})</label>
                        <input type="number" name="fixed_salary" step="0.01" min="0" class="form-control" id="fixedSalary" placeholder="0.00" required>
                    </div>
                </div>

                <div class="form-row" id="percentageRow" style="display: none;">
                    <div class="form-group">
                        <label>{{ __('messages.service_percentage_label') }}</label>
                        <input type="number" name="service_percentage" step="0.01" min="0" max="100" class="form-control" id="servicePercentage" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.sales_percentage_label') }}</label>
                        <input type="number" name="sales_percentage" step="0.01" min="0" max="100" class="form-control" id="salesPercentage" placeholder="0.00">
                    </div>
                </div>



                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeSalarySettingModal()">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно расчета зарплаты -->
<div id="salaryCalculationModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2>{{ __('messages.calculate_salary_modal') }}</h2>
            <span class="close" onclick="closeSalaryCalculationModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="salaryCalculationForm">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>{{ __('messages.select_employee') }} *</label>
                        <select name="user_id" required class="form-control" id="calculationUserId">
                            <option value="">{{ __('messages.select_employee') }}</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.calculation_period_label') }} *</label>
                        <select name="calculation_period" required class="form-control" id="calculationPeriod" onchange="toggleCalculationPeriod()">
                            <option value="">{{ __('messages.select_period') }}</option>
                            <option value="current_month">{{ __('messages.current_month') }}</option>
                            <option value="last_month">{{ __('messages.last_month') }}</option>
                            <option value="custom">{{ __('messages.custom_period') }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" id="customPeriodRow" style="display: none;">
                    <div class="form-group">
                        <label>{{ __('messages.start_date') }} *</label>
                        <input type="date" name="period_start" class="form-control" id="periodStart">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.end_date') }} *</label>
                        <input type="date" name="period_end" class="form-control" id="periodEnd">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>{{ __('messages.bonuses') }} ({{ \App\Helpers\CurrencyHelper::getSymbol() }})</label>
                        <input type="number" name="bonuses" step="0.01" min="0" class="form-control" id="bonuses" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.penalties') }} ({{ \App\Helpers\CurrencyHelper::getSymbol() }})</label>
                        <input type="number" name="penalties" step="0.01" min="0" class="form-control" id="penalties" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.notes_label') }}</label>
                    <textarea name="notes" rows="3" class="form-control" id="calculationNotes" placeholder="{{ __('messages.notes_placeholder') }}"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeSalaryCalculationModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Рассчитать</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно создания выплаты -->
<div id="salaryPaymentModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2>{{ __('messages.create_payment_modal') }}</h2>
            <span class="close" onclick="closeSalaryPaymentModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="salaryPaymentForm">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>{{ __('messages.select_employee') }} *</label>
                        <select name="user_id" required class="form-control" id="paymentUserId">
                            <option value="">{{ __('messages.select_employee') }}</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Расчет зарплаты</label>
                        <select name="calculation_id" class="form-control" id="paymentCalculationId">
                            <option value="">Выберите расчет (необязательно)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>{{ __('messages.payment_amount_label') }} ({{ \App\Helpers\CurrencyHelper::getSymbol() }}) *</label>
                        <input type="number" name="amount" step="0.01" min="0" required class="form-control" id="paymentAmount" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.payment_date_label') }} *</label>
                        <input type="date" name="payment_date" required class="form-control" id="paymentDate">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>{{ __('messages.payment_method_label') }} *</label>
                        <select name="payment_method" required class="form-control" id="paymentMethod">
                            <option value="">{{ __('messages.select_method') }}</option>
                            <option value="cash">{{ __('messages.cash') }}</option>
                            <option value="bank">{{ __('messages.bank_transfer') }}</option>
                            <option value="card">{{ __('messages.card') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.reference_number') }}</label>
                        <input type="text" name="reference_number" class="form-control" id="referenceNumber" placeholder="{{ __('messages.reference_placeholder') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.notes_label') }}</label>
                    <textarea name="notes" rows="3" class="form-control" id="paymentNotes" placeholder="{{ __('messages.payment_notes_placeholder') }}"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeSalaryPaymentModal()">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn-submit">{{ __('messages.create_payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра расчета зарплаты -->
<div id="salaryCalculationDetailsModal" class="modal">
    <div class="modal-content" style="width: 90%; max-width: 1000px;">
        <div class="modal-header">
            <h2>{{ __('messages.calculation_details_modal') }}</h2>
            <span class="close" onclick="closeSalaryCalculationDetailsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="calculationDetailsContent">
                <div class="calculation-info">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.employee') }}:</strong> <span id="detailEmployeeName"></span></p>
                            <p><strong>{{ __('messages.period') }}:</strong> <span id="detailPeriod"></span></p>
                            <p><strong>{{ __('messages.status') }}:</strong> <span id="detailStatus"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.calculation_created_at') }}:</strong> <span id="detailCreatedAt"></span></p>
                            <p><strong>{{ __('messages.calculation_total') }}:</strong> <span id="detailTotalSalary" class="currency-amount"></span></p>
                        </div>
                    </div>
                </div>

                <div class="calculation-breakdown">
                    <h4>{{ __('messages.calculation_breakdown') }}</h4>
                    <div class="table-wrapper">
                        <table class="table-striped">
                            <tbody>
                                <tr>
                                    <td><strong>{{ __('messages.services_count') }}</strong></td>
                                    <td><span id="detailServicesCount"></span></td>
                                    <td><strong>{{ __('messages.services_amount') }}</strong></td>
                                    <td><span id="detailServicesAmount" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('messages.service_percentage_label') }}</strong></td>
                                    <td><span id="detailServicePercentage"></span></td>
                                    <td><strong>{{ __('messages.service_income') }}</strong></td>
                                    <td><span id="detailServiceIncome" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('messages.sales_count') }}</strong></td>
                                    <td><span id="detailSalesCount"></span></td>
                                    <td><strong>{{ __('messages.sales_amount') }}</strong></td>
                                    <td><span id="detailSalesAmount" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('messages.sales_percentage_label') }}</strong></td>
                                    <td><span id="detailSalesPercentage"></span></td>
                                    <td><strong>{{ __('messages.sales_income') }}</strong></td>
                                    <td><span id="detailSalesIncome" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('messages.fixed_salary_label') }}</strong></td>
                                    <td colspan="3"><span id="detailFixedSalary" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('messages.percentage_salary_label') }}</strong></td>
                                    <td colspan="3"><span id="detailPercentageSalary" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('messages.bonuses_label') }}</strong></td>
                                    <td colspan="3"><span id="detailBonuses" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('messages.penalties_label') }}</strong></td>
                                    <td colspan="3"><span id="detailPenalties" class="currency-amount"></span></td>
                                </tr>
                                <tr class="total-row">
                                    <td><strong>{{ __('messages.total_to_pay') }}</strong></td>
                                    <td colspan="3"><strong><span id="detailFinalTotal" class="currency-amount"></span></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="calculation-notes" id="calculationNotesSection" style="display: none;">
                    <h4>Примечания:</h4>
                    <p id="detailNotes"></p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeSalaryCalculationDetailsModal()">{{ __('messages.close') }}</button>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра выплаты -->
<div id="salaryPaymentDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>{{ __('messages.payment_details_modal') }}</h2>
            <span class="close" onclick="closeSalaryPaymentDetailsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="payment-details">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('messages.payment_employee') }}:</strong> <span id="paymentDetailEmployee"></span></p>
                        <p><strong>{{ __('messages.payment_date') }}:</strong> <span id="paymentDetailDate"></span></p>
                        <p><strong>{{ __('messages.payment_amount') }}:</strong> <span id="paymentDetailAmount" class="currency-amount"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ __('messages.payment_method') }}:</strong> <span id="paymentDetailMethod"></span></p>
                        <p><strong>{{ __('messages.payment_status') }}:</strong> <span id="paymentDetailStatus"></span></p>
                        <p><strong>{{ __('messages.payment_created_at') }}:</strong> <span id="paymentDetailCreatedAt"></span></p>
                    </div>
                </div>

                <div class="payment-info" id="paymentInfoSection">
                    <div class="table-wrapper">
                        <table class="table-striped">
                            <tbody>
                                <tr id="referenceNumberRow" style="display: none;">
                                    <td><strong>Номер референса:</strong></td>
                                    <td><span id="paymentDetailReference"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="payment-approval-info" id="paymentApprovalSection" style="display: none;">
                    <div class="approval-card">
                        <div class="approval-icon">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="approval-details">
                            <div class="approval-person">
                                <strong>Подтвердил:</strong> <span id="paymentDetailApprovedBy"></span>
                            </div>
                            <div class="approval-date">
                                <strong>Дата:</strong> <span id="paymentDetailApprovedAt"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="payment-notes" id="paymentNotesSection" style="display: none;">
                    <h4>Примечания:</h4>
                    <p id="paymentDetailNotes"></p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeSalaryPaymentDetailsModal()">{{ __('messages.close') }}</button>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>{{ __('messages.confirm_delete') }}</h2>
            <span class="close" onclick="closeConfirmationModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p id="confirmationMessage"></p>
        </div>
        <div class="modal-footer">
            <button id="cancelDelete" class="btn-cancel">{{ __('messages.cancel') }}</button>
            <button id="confirmDeleteBtn" class="btn-delete">{{ __('messages.delete') }}</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Данные о валюте для JavaScript
window.currencyData = @json($currencyData);

// Переводы для JavaScript
window.translations = {
    approved: '{{ __("messages.approved") }}',
    paid: '{{ __("messages.paid") }}',
    pending: '{{ __("messages.pending") }}',
    calculated: '{{ __("messages.calculated") }}',
    cancelled: '{{ __("messages.cancelled") }}',
    unknown: '{{ __("messages.unknown") }}',
    // Переводы для типов зарплаты
    fixed_salary: '{{ __("messages.fixed_salary") }}',
    percentage_salary: '{{ __("messages.percentage_salary") }}',
    mixed_salary: '{{ __("messages.mixed_salary") }}',
    // Переводы для модального окна удаления
    confirm_delete_calculation: '{{ __("messages.confirm_delete_calculation") }}',
    confirm_delete_payment: '{{ __("messages.confirm_delete_payment") }}',
    confirm_delete_setting: '{{ __("messages.confirm_delete_setting") }}',
    delete: '{{ __("messages.delete") }}',
    close: '{{ __("messages.close") }}'
};
</script>
<script src="{{ asset('client/js/salary.js') }}"></script>
@endpush
@endsection
