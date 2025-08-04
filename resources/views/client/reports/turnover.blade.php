@extends('client.layouts.app')

@php
use App\Helpers\CurrencyHelper;
$currency = $project->currency ?? 'UAH';
$currencySymbol = CurrencyHelper::getSymbol($currency);
@endphp

@section('content')
<div class="dashboard-container">
    <div class="report-header">
        <h1 class="dashboard-title">{{ __('messages.turnover_analytics') }}</h1>

        <!-- Навигация по вкладкам -->
        <div class="dashboard-tabs">
            <button class="tab-button active" data-tab="dynamic-analytics"><i class="fa fa-line-chart"></i> {{ __('messages.dynamics_and_structure') }}</button>
            <button class="tab-button" data-tab="tops-analytics"><i class="fa fa-star"></i> {{ __('messages.tops') }}</button>
            <button class="tab-button" data-tab="suppliers-analytics"><i class="fa fa-truck"></i> {{ __('messages.suppliers_and_stock') }}</button>
            <button class="tab-button" data-tab="expenses-analytics"><i class="fa fa-credit-card"></i> {{ __('messages.expenses') }}</button>
            <button class="tab-button" data-tab="employees-analytics"><i class="fa fa-users"></i> {{ __('messages.employees') }}</button>
        </div>

        <!-- Фильтры периода -->
        <div class="filter-section" id="periodFiltersSection">
            <div class="period-filters" style="display:flex;align-items:center;gap:8px;">
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
                <span id="calendarRangeDisplay" style="min-width:110px;text-align:center;vertical-align:middle;font-family:inherit;font-size:14px;font-weight:600;color: #64748b;"></span>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="dynamic-analytics">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.sales_structure_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.distribution_by_categories') }}</p>
                        <canvas id="turnoverCategoryPie"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.sales_structure_by_brands') }}</h4>
                        <p class="text-muted">{{ __('messages.distribution_by_brands') }}</p>
                        <canvas id="turnoverBrandPie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.gross_profit_dynamics') }}</h4>
                        <p class="text-muted">{{ __('messages.difference_between_sales_and_cost') }}</p>
                        <canvas id="grossProfitChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.structure_by_product_types') }}</h4>
                        <p class="text-muted">{{ __('messages.distribution_services_products') }}</p>
                        <canvas id="turnoverTypePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.turnover_dynamics') }}</h4>
                        <p class="text-muted">{{ __('messages.total_sales_and_purchases_by_days') }}</p>
                        <canvas id="turnoverDynamicChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tops-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_products_by_sales') }}</h4>
                        <p class="text-muted">{{ __('messages.most_sold_products_for_period') }}</p>
                        <canvas id="topSalesBar"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_products_by_purchases') }}</h4>
                        <p class="text-muted">{{ __('messages.products_with_highest_purchase_volume') }}</p>
                        <canvas id="topPurchasesBar"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_clients_by_product_purchases') }}</h4>
                        <p class="text-muted">{{ __('messages.clients_who_bought_products_for_highest_amount') }}</p>
                        <canvas id="topClientsBySalesBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="suppliers-analytics" style="display: none;">
            <div class="row mb-4" id="stockSummaryRow">
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>{{ __('messages.total_products_on_warehouse') }}: </span><span id="stockTotalQty">—</span> {{ __('messages.pieces') }}
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>{{ __('messages.total_wholesale_amount_on_warehouse') }}: </span><span id="stockTotalWholesale" data-currency data-amount="0">—</span> {{ $currencySymbol }}
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>{{ __('messages.total_retail_amount_on_warehouse') }}: </span><span id="stockTotalRetail" data-currency data-amount="0">—</span> {{ $currencySymbol }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.average_turnover_period') }}</h4>
                        <p class="text-muted">{{ __('messages.average_time_between_purchase_and_sale') }}</p>
                        <canvas id="turnoverDaysChart"></canvas>
                        <div id="avgTurnoverDaysValue" style="font-size:1.2rem;font-weight:600;margin-top:12px;"></div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.structure_by_suppliers') }}</h4>
                        <p class="text-muted">{{ __('messages.share_of_top_5_suppliers_in_total_purchases') }}</p>
                        <canvas id="supplierStructurePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.suppliers_by_purchase_volume') }}</h4>
                        <p class="text-muted">{{ __('messages.top_6_suppliers_with_highest_purchase_volume') }}</p>
                        <canvas id="topSuppliersBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.warehouse_stock_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.quantity_and_amount_of_stock_by_wholesale_and_retail_price') }}</p>
                        <canvas id="stockByCategoryBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="expenses-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.share_of_fixed_and_variable_expenses') }}</h4>
                        <p class="text-muted">{{ __('messages.comparison_of_fixed_and_variable_expenses') }}</p>
                        <canvas id="expensesFixedVariablePie"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.expenses_structure_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.share_of_each_category_in_total_expenses') }}</p>
                        <canvas id="expensesByCategoryPie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.expenses_dynamics_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.how_expenses_changed_by_each_category_over_time') }}</p>
                        <canvas id="expensesCategoryDynamicsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.expenses_dynamics_by_months') }}</h4>
                        <p class="text-muted">{{ __('messages.how_total_expenses_changed_by_months') }}</p>
                        <canvas id="expensesByMonthChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_3_months_by_expenses') }}</h4>
                        <p class="text-muted">{{ __('messages.months_with_highest_expenses') }}</p>
                        <canvas id="expensesTopMonthsBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.average_expense_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.average_monthly_expense_by_each_category') }}</p>
                        <canvas id="expensesAverageByCategoryBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="employees-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_employees_by_sales_volume') }}</h4>
                        <p class="text-muted">{{ __('messages.employees_with_highest_sales_amount_for_period') }}</p>
                        <canvas id="topEmployeesBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.sales_structure_by_employees') }}</h4>
                        <p class="text-muted">{{ __('messages.share_of_each_employee_in_total_sales') }}</p>
                        <canvas id="employeesStructurePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.sales_dynamics_by_employees') }}</h4>
                        <p class="text-muted">{{ __('messages.how_sales_of_each_employee_changed_over_time') }}</p>
                        <canvas id="employeesDynamicsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.average_sale_amount_by_employees') }}</h4>
                        <p class="text-muted">{{ __('messages.average_amount_of_one_sale_for_each_employee') }}</p>
                        <canvas id="employeesAverageBar"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@push('scripts')
<script src="{{ asset('client/js/turnover-analytics.js') }}"></script>
@endpush                 
@endsection                    