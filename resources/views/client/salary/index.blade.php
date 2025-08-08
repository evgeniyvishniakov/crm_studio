@extends('client.layouts.app')

@section('title', 'Зарплата')

@section('content')

<div class="dashboard-container">
    <div class="settings-header">
        <h1>Зарплата</h1>
    </div>
    
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="salary-overview">
            <i class="fa fa-chart-bar" style="margin-right:8px;"></i>Обзор
        </button>
        <button class="tab-button" data-tab="salary-settings">
            <i class="fa fa-cog" style="margin-right:8px;"></i>Настройки зарплаты
        </button>
        <button class="tab-button" data-tab="salary-calculations">
            <i class="fa fa-calculator" style="margin-right:8px;"></i>Расчеты
        </button>
        <button class="tab-button" data-tab="salary-payments">
            <i class="fa fa-cash-register" style="margin-right:8px;"></i>Выплаты
        </button>
        <button class="tab-button" data-tab="salary-reports">
            <i class="fa fa-chart-line" style="margin-right:8px;"></i>Отчеты
        </button>
    </div>
    
    <div class="settings-content">
        <!-- Вкладка обзора -->
        <div class="settings-pane" id="tab-salary-overview">
            <h5>Обзор зарплаты</h5>
            
            <!-- Статистика -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-muted fw-normal mt-0">Всего сотрудников</h5>
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
                                    <h5 class="text-muted fw-normal mt-0">С настройками зарплаты</h5>
                                    <h3 class="mt-3 mb-3">{{ $stats['employees_with_salary'] ?? 0 }}</h3>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success rounded">
                                        <i class="mdi mdi-calculator font-20 text-success"></i>
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
                                    <h5 class="text-muted fw-normal mt-0">Расчетов в этом месяце</h5>
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
                                    <h5 class="text-muted fw-normal mt-0">Выплачено в этом месяце</h5>
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

            <div class="row">
                <!-- Последние расчеты -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="header-title">Последние расчеты зарплаты</h4>
                                <button class="btn btn-sm btn-outline-primary" onclick="showTab('salary-calculations')">Все расчеты</button>
                            </div>

                            @if(($recentCalculations ?? collect())->count() > 0)
                                <div class="table-wrapper">
                                    <table class="table-striped salary-overview-table">
                                        <thead>
                                            <tr>
                                                <th>Сотрудник</th>
                                                <th>Период</th>
                                                <th>Сумма</th>
                                                <th>Статус</th>
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
                            @else
                                <div class="text-center py-4">
                                    <i class="mdi mdi-information-outline text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2">Расчеты зарплаты отсутствуют</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Последние выплаты -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="header-title">Последние выплаты</h4>
                                <button class="btn btn-sm btn-outline-primary" onclick="showTab('salary-payments')">Все выплаты</button>
                            </div>

                            @if(($recentPayments ?? collect())->count() > 0)
                                <div class="table-wrapper">
                                    <table class="table-striped salary-overview-table">
                                        <thead>
                                            <tr>
                                                <th>Сотрудник</th>
                                                <th>Сумма</th>
                                                <th>Дата</th>
                                                <th>Статус</th>
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
                            @else
                                <div class="text-center py-4">
                                    <i class="mdi mdi-information-outline text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2">Выплаты отсутствуют</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вкладка настроек зарплаты -->
        <div class="settings-pane" id="tab-salary-settings" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Настройки зарплаты</h5>
                <button class="btn btn-primary" onclick="showSalarySettingModal()">
                    <i class="fa fa-plus"></i> Добавить настройки
                </button>
            </div>

            <!-- Таблица настроек зарплаты -->
            <div class="table-wrapper">
                <table class="table-striped salary-settings-table" id="salarySettingsTable">
                    <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Тип зарплаты</th>
                            <th>Процент от услуг</th>
                            <th>Процент от продаж</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody id="salary-settings-tbody">
                        @foreach($salarySettings ?? [] as $setting)
                        <tr data-setting-id="{{ $setting->id }}">
                            <td>{{ $setting->user->name }}</td>
                            <td>
                                <span class="status-badge status-done">
                                    {{ $setting->salary_type_text }}
                                </span>
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

            @if(($salarySettings ?? collect())->count() == 0)
                <div class="text-center py-5">
                    <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                    <h5>Настройки зарплаты отсутствуют</h5>
                    <p class="text-muted">Добавьте настройки зарплаты для ваших сотрудников</p>
                </div>
            @endif
        </div>

        <!-- Вкладка расчетов зарплаты -->
        <div class="settings-pane" id="tab-salary-calculations" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Расчеты зарплаты</h5>
                <button class="btn btn-primary" onclick="showSalaryCalculationModal()">
                    <i class="fa fa-plus"></i> Рассчитать зарплату
                </button>
            </div>

            <!-- Таблица расчетов зарплаты -->
            <div class="table-wrapper">
                <table class="table-striped salary-calculations-table" id="salaryCalculationsTable">
                    <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Период</th>
                            <th>Услуги</th>
                            <th>Продажи</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Действия</th>
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
                                <span class="status-badge status-{{ $calculation->status === 'pending' ? 'pending' : ($calculation->status === 'approved' ? 'done' : 'cancel') }}">
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

            @if(($salaryCalculations ?? collect())->count() == 0)
                <div class="text-center py-5">
                    <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                    <h5>Расчеты зарплаты отсутствуют</h5>
                    <p class="text-muted">Создайте расчет зарплаты для ваших сотрудников</p>
                </div>
            @endif
        </div>

        <!-- Вкладка выплат -->
        <div class="settings-pane" id="tab-salary-payments" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Выплаты зарплаты</h5>
                <button class="btn btn-primary" onclick="showSalaryPaymentModal()">
                    <i class="fa fa-plus"></i> Создать выплату
                </button>
            </div>

            <!-- Таблица выплат -->
            <div class="table-wrapper">
                <table class="table-striped salary-payments-table" id="salaryPaymentsTable">
                    <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Сумма</th>
                            <th>Дата</th>
                            <th>Метод</th>
                            <th>Статус</th>
                            <th>Действия</th>
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

            @if(($salaryPayments ?? collect())->count() == 0)
                <div class="text-center py-5">
                    <i class="fas fa-cash-register fa-3x text-muted mb-3"></i>
                    <h5>Выплаты отсутствуют</h5>
                    <p class="text-muted">Создайте выплату зарплаты для ваших сотрудников</p>
                </div>
            @endif
        </div>

        <!-- Вкладка отчетов -->
        <div class="settings-pane" id="tab-salary-reports" style="display: none;">
            <h5>Отчеты по зарплате</h5>
            
            <!-- Статистика по месяцам -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Статистика по месяцам</h4>
                            <div class="table-wrapper">
                                <table class="table-striped salary-reports-table">
                                    <thead>
                                        <tr>
                                            <th>Месяц</th>
                                            <th>Количество расчетов</th>
                                            <th>Общая сумма</th>
                                            <th>Средняя зарплата</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthlyStats ?? [] as $stat)
                                        <tr>
                                            <td>{{ $stat->year }}/{{ $stat->month }}</td>
                                            <td>{{ $stat->calculations_count }}</td>
                                            <td>
                                                                                <span class="currency-amount" data-amount="{{ $stat->total_salary }}">
                                    {{ \App\Helpers\CurrencyHelper::format($stat->total_salary) }}
                                </span>
                                            </td>
                                            <td>
                                                                                <span class="currency-amount" data-amount="{{ $stat->avg_salary }}">
                                    {{ \App\Helpers\CurrencyHelper::format($stat->avg_salary) }}
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
                            <h4 class="header-title">Топ сотрудников по зарплате</h4>
                            <div class="table-wrapper">
                                <table class="table-striped salary-reports-table">
                                    <thead>
                                        <tr>
                                            <th>Сотрудник</th>
                                            <th>Общий заработок</th>
                                            <th>Количество расчетов</th>
                                            <th>Средняя зарплата</th>
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
                                            <td>{{ $employee->calculations_count }}</td>
                                            <td>
                                                                                <span class="currency-amount" data-amount="{{ $employee->total_earned / $employee->calculations_count }}">
                                    {{ \App\Helpers\CurrencyHelper::format($employee->total_earned / $employee->calculations_count) }}
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
            <h2 id="salarySettingModalTitle">Добавить настройки зарплаты</h2>
            <span class="close" onclick="closeSalarySettingModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="salarySettingForm">
                @csrf
                <input type="hidden" name="setting_id" id="settingId">
                <div class="form-row">
                    <div class="form-group">
                        <label>Сотрудник *</label>
                        <select name="user_id" required class="form-control" id="salarySettingUserId">
                            <option value="">Выберите сотрудника</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Тип зарплаты *</label>
                        <select name="salary_type" required class="form-control" id="salaryType" onchange="toggleSalaryFields()">
                            <option value="">Выберите тип</option>
                            <option value="fixed">Фиксированная</option>
                            <option value="percentage">Процентная</option>
                            <option value="mixed">Смешанная</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" id="fixedSalaryRow" style="display: none;">
                    <div class="form-group">
                        <label>Фиксированная зарплата ({{ \App\Helpers\CurrencyHelper::getSymbol() }})</label>
                        <input type="number" name="fixed_salary" step="0.01" min="0" class="form-control" id="fixedSalary" placeholder="0.00">
                    </div>
                </div>

                <div class="form-row" id="percentageRow" style="display: none;">
                    <div class="form-group">
                        <label>Процент от услуг (%)</label>
                        <input type="number" name="service_percentage" step="0.01" min="0" max="100" class="form-control" id="servicePercentage" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Процент от продаж (%)</label>
                        <input type="number" name="sales_percentage" step="0.01" min="0" max="100" class="form-control" id="salesPercentage" placeholder="0.00">
                    </div>
                </div>



                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeSalarySettingModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно расчета зарплаты -->
<div id="salaryCalculationModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2>Рассчитать зарплату</h2>
            <span class="close" onclick="closeSalaryCalculationModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="salaryCalculationForm">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Сотрудник *</label>
                        <select name="user_id" required class="form-control" id="calculationUserId">
                            <option value="">Выберите сотрудника</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Период расчета *</label>
                        <select name="calculation_period" required class="form-control" id="calculationPeriod" onchange="toggleCalculationPeriod()">
                            <option value="">Выберите период</option>
                            <option value="current_month">Текущий месяц</option>
                            <option value="last_month">Прошлый месяц</option>
                            <option value="custom">Произвольный период</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" id="customPeriodRow" style="display: none;">
                    <div class="form-group">
                        <label>Дата начала *</label>
                        <input type="date" name="period_start" class="form-control" id="periodStart">
                    </div>
                    <div class="form-group">
                        <label>Дата окончания *</label>
                        <input type="date" name="period_end" class="form-control" id="periodEnd">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Бонусы ({{ \App\Helpers\CurrencyHelper::getSymbol() }})</label>
                        <input type="number" name="bonuses" step="0.01" min="0" class="form-control" id="bonuses" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Штрафы ({{ \App\Helpers\CurrencyHelper::getSymbol() }})</label>
                        <input type="number" name="penalties" step="0.01" min="0" class="form-control" id="penalties" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label>Примечания</label>
                    <textarea name="notes" rows="3" class="form-control" id="calculationNotes" placeholder="Дополнительная информация о расчете"></textarea>
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
            <h2>Создать выплату</h2>
            <span class="close" onclick="closeSalaryPaymentModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="salaryPaymentForm">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Сотрудник *</label>
                        <select name="user_id" required class="form-control" id="paymentUserId">
                            <option value="">Выберите сотрудника</option>
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
                        <label>Сумма выплаты ({{ \App\Helpers\CurrencyHelper::getSymbol() }}) *</label>
                        <input type="number" name="amount" step="0.01" min="0" required class="form-control" id="paymentAmount" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Дата выплаты *</label>
                        <input type="date" name="payment_date" required class="form-control" id="paymentDate">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Метод выплаты *</label>
                        <select name="payment_method" required class="form-control" id="paymentMethod">
                            <option value="">Выберите метод</option>
                            <option value="cash">Наличные</option>
                            <option value="bank_transfer">Банковский перевод</option>
                            <option value="card">Карта</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Номер документа</label>
                        <input type="text" name="reference_number" class="form-control" id="referenceNumber" placeholder="Номер квитанции, чека и т.д.">
                    </div>
                </div>

                <div class="form-group">
                    <label>Примечания</label>
                    <textarea name="notes" rows="3" class="form-control" id="paymentNotes" placeholder="Дополнительная информация о выплате"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeSalaryPaymentModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Создать выплату</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра расчета зарплаты -->
<div id="salaryCalculationDetailsModal" class="modal">
    <div class="modal-content" style="width: 90%; max-width: 1000px;">
        <div class="modal-header">
            <h2>Детали расчета зарплаты</h2>
            <span class="close" onclick="closeSalaryCalculationDetailsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="calculationDetailsContent">
                <div class="calculation-info">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Сотрудник:</strong> <span id="detailEmployeeName"></span></p>
                            <p><strong>Период:</strong> <span id="detailPeriod"></span></p>
                            <p><strong>Статус:</strong> <span id="detailStatus" class="status-badge"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Дата создания:</strong> <span id="detailCreatedAt"></span></p>
                            <p><strong>Общая сумма:</strong> <span id="detailTotalSalary" class="currency-amount"></span></p>
                        </div>
                    </div>
                </div>

                <div class="calculation-breakdown">
                    <h4>Детализация расчета:</h4>
                    <div class="table-wrapper">
                        <table class="table-striped">
                            <tbody>
                                <tr>
                                    <td><strong>Количество услуг:</strong></td>
                                    <td><span id="detailServicesCount"></span></td>
                                    <td><strong>Сумма услуг:</strong></td>
                                    <td><span id="detailServicesAmount" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Процент от услуг:</strong></td>
                                    <td><span id="detailServicePercentage"></span></td>
                                    <td><strong>Доход от услуг:</strong></td>
                                    <td><span id="detailServiceIncome" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Количество продаж:</strong></td>
                                    <td><span id="detailSalesCount"></span></td>
                                    <td><strong>Сумма продаж:</strong></td>
                                    <td><span id="detailSalesAmount" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Процент от продаж:</strong></td>
                                    <td><span id="detailSalesPercentage"></span></td>
                                    <td><strong>Доход от продаж:</strong></td>
                                    <td><span id="detailSalesIncome" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Фиксированная зарплата:</strong></td>
                                    <td colspan="3"><span id="detailFixedSalary" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Процентная зарплата:</strong></td>
                                    <td colspan="3"><span id="detailPercentageSalary" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Бонусы:</strong></td>
                                    <td colspan="3"><span id="detailBonuses" class="currency-amount"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Штрафы:</strong></td>
                                    <td colspan="3"><span id="detailPenalties" class="currency-amount"></span></td>
                                </tr>
                                <tr class="total-row">
                                    <td><strong>Итого к выплате:</strong></td>
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
            <button type="button" class="btn-cancel" onclick="closeSalaryCalculationDetailsModal()">Закрыть</button>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра выплаты -->
<div id="salaryPaymentDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Детали выплаты</h2>
            <span class="close" onclick="closeSalaryPaymentDetailsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="payment-details">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Сотрудник:</strong> <span id="paymentDetailEmployee"></span></p>
                        <p><strong>Дата выплаты:</strong> <span id="paymentDetailDate"></span></p>
                        <p><strong>Сумма:</strong> <span id="paymentDetailAmount" class="currency-amount"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Метод выплаты:</strong> <span id="paymentDetailMethod"></span></p>
                        <p><strong>Статус:</strong> <span id="paymentDetailStatus"></span></p>
                        <p><strong>Дата создания:</strong> <span id="paymentDetailCreatedAt"></span></p>
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
            <button type="button" class="btn-cancel" onclick="closeSalaryPaymentDetailsModal()">Закрыть</button>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Подтверждение удаления</h2>
            <span class="close" onclick="closeConfirmationModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p id="confirmationMessage">Вы уверены, что хотите удалить этот элемент? Это действие нельзя отменить.</p>
        </div>
        <div class="modal-footer">
            <button id="cancelDelete" class="btn-cancel">Отмена</button>
            <button id="confirmDeleteBtn" class="btn-delete">Удалить</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Данные о валюте для JavaScript
window.currencyData = @json($currencyData);
</script>
<script src="{{ asset('client/js/salary.js') }}"></script>
@endpush
@endsection
