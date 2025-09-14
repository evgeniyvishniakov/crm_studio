@extends('client.layouts.app')

@section('content')
<style>
.period-filters {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    width: 100%;
    margin: 0 auto;
}
.filter-section {
    width: 100%;
}
#calendarRangeDisplay {
    text-align: center;
    vertical-align: middle;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
}
#calendarRangeDisplay:empty {
    display: none;
}
.period-tooltip {
    cursor: pointer;
    position: relative;
    display: inline-block;
}
.period-tooltip i {
    color: #64748b;
    font-size: 18px;
    vertical-align: middle;
}
.period-tooltip-text {
    display: none;
    position: absolute;
    left: 120%;
    top: 50%;
    transform: translateY(-50%);
    background: #fff;
    color: #222;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 14px 18px;
    font-size: 14px;
    min-width: 320px;
    z-index: 1000;
    white-space: pre-line;
}
.period-tooltip:hover .period-tooltip-text,
.period-tooltip:focus .period-tooltip-text {
    display: block;
}
</style>
<div class="dashboard-container">
    <h1 class="dashboard-title">{{ __('messages.client_work') }}</h1>

    <!-- Навигация по вкладкам -->
    <div class="dashboard-tabs">
        <button class="tab-button active" data-tab="clients-analytics"><i class="fa fa-users"></i> {{ __('messages.client_analytics') }}</button>
        <button class="tab-button" data-tab="appointments-analytics"><i class="fa fa-calendar"></i> {{ __('messages.appointments_analytics') }}</button>
        <button class="tab-button" data-tab="employees-analytics"><i class="fa fa-user-md"></i> {{ __('messages.employees_analytics') }}</button>
        <button class="tab-button" data-tab="complex-analytics"><i class="fa fa-money"></i> {{ __('messages.financial_analytics') }}</button>
    </div>

    <!-- Новый блок фильтров -->
    <div class="filter-section">
        <div class="period-filters">
            <span class="period-tooltip" tabindex="0">
                <i class="fa fa-question-circle" aria-hidden="true"></i>
                <span class="period-tooltip-text">
                    <b>{{ __('messages.period_explanations') }}:</b><br>
                    <b>{{ __('messages.for_week') }}</b>: {{ __('messages.week_explanation') }}<br>
                    <b>{{ __('messages.for_2_weeks') }}</b>: {{ __('messages.2weeks_explanation') }}<br>
                    <b>{{ __('messages.for_month') }}</b>: {{ __('messages.month_explanation') }}<br>
                    <b>{{ __('messages.for_half_year') }}</b>: {{ __('messages.half_year_explanation') }}<br>
                    <b>{{ __('messages.for_year') }}</b>: {{ __('messages.year_explanation') }}
                </span>
            </span>
            <button class="filter-button active">{{ __('messages.for_week') }}</button>
            <button class="filter-button">{{ __('messages.for_2_weeks') }}</button>
            <button class="filter-button">{{ __('messages.for_month') }}</button>
            <button class="filter-button">{{ __('messages.for_half_year') }}</button>
            <button class="filter-button">{{ __('messages.for_year') }}</button>
            <button class="filter-button calendar-button" id="dateRangePicker">
                <i class="fa fa-calendar"></i>
            </button>
            <span id="calendarRangeDisplay"></span>
        </div>
    </div>

    <!-- Содержимое вкладок -->
    <div class="tab-content">
        <!-- Вкладка "Аналитика по клиентам" -->
        <div class="tab-pane active" id="clients-analytics">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.client_base_dynamics') }}</h4>
                        <p class="text-muted">{{ __('messages.new_clients_count_period') }}</p>
                        <canvas id="clientDynamicsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_clients_by_visits') }}</h4>
                        <p class="text-muted">{{ __('messages.most_frequent_visitors') }}</p>
                        <canvas id="topClientsByVisitsChart"></canvas>
                    </div>
                </div>
                
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.visits_primary_returning') }}</h4>
                        <p class="text-muted">{{ __('messages.primary_returning_visits_ratio') }}</p>
                        <canvas id="newVsReturningChart"></canvas>
                    </div>
                </div>
                 <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.client_types_distribution') }}</h4>
                        <p class="text-muted">{{ __('messages.client_types_ratio') }}</p>
                        <canvas id="clientTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вкладка "Аналитика по записям" -->
        <div class="tab-pane" id="appointments-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.load_by_days') }}</h4>
                        <p class="text-muted">{{ __('messages.appointments_count_by_days') }}</p>
                        <canvas id="loadChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-5 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.appointment_statuses') }}</h4>
                        <p class="text-muted">{{ __('messages.completed_cancelled_missed_ratio') }}</p>
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                     <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.services_popularity') }}</h4>
                        <p class="text-muted">{{ __('messages.services_rating_by_appointments') }}</p>
                        <canvas id="servicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вкладка "Аналитика по сотрудникам" -->
        <div class="tab-pane" id="employees-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_employees_by_procedures') }}</h4>
                        <p class="text-muted">{{ __('messages.employees_most_procedures') }}</p>
                        <canvas id="topEmployeesProceduresBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.procedures_dynamics_by_employees') }}</h4>
                        <p class="text-muted">{{ __('messages.procedures_change_over_time') }}</p>
                        <canvas id="employeesProceduresDynamicsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_employees_by_revenue') }}</h4>
                        <p class="text-muted">{{ __('messages.employees_most_revenue') }}</p>
                        <canvas id="topEmployeesRevenueBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.average_check_by_employees') }}</h4>
                        <p class="text-muted">{{ __('messages.average_procedure_amount') }}</p>
                        <canvas id="employeesAverageCheckBar"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вкладка "Финансовая аналитика" -->
        <div class="tab-pane" id="complex-analytics" style="display: none;">
             <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_clients_by_revenue') }}</h4>
                        <p class="text-muted">{{ __('messages.clients_most_money') }}</p>
                        <canvas id="topClientsByRevenueChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.average_check_dynamics') }}</h4>
                        <p class="text-muted">{{ __('messages.average_amount_per_visit') }}</p>
                        <canvas id="avgCheckChart"></canvas>
                    </div>
                </div>
            </div>
             <div class="row">
                <div class="col-lg-12 mb-12">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.services_popularity_by_revenue') }}</h4>
                        <p class="text-muted">{{ __('messages.top_10_services_by_revenue') }}</p>
                        <canvas id="topServicesByRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="{{ asset('client/js/clients-analytics.js') }}?v={{ time() }}"></script>
@endpush


@endsection



    
