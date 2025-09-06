@extends('landing.layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\App;
@endphp

@section('title', __('landing.pricing_title') . ' - Trimora')
@section('description', __('landing.pricing_description'))

@section('content')


<!-- Hero Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4 text-dark">{{ __('landing.pricing_title') }}</h1>
                <p class="lead mb-4 text-muted">{{ __('landing.pricing_subtitle') }}</p>
                <p class="text-muted mb-4">{{ __('landing.pricing_description') }}</p>
                @if(Auth::guard('client')->check())
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg animate-pulse">
                        <i class="fas fa-sign-in-alt me-2"></i>{{ __('landing.pricing_login') }}
                    </a>
                @else
                    <a href="#" class="btn btn-primary btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-rocket me-2"></i>{{ __('landing.pricing_try_free') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>



<!-- Pricing Section -->
<section id="pricing" class="py-5 bg-light">
    <div class="container">
        <!-- Navigation -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <ul class="nav nav-pills nav-fill" id="pricingTabs" role="tablist">
                    @foreach($plans as $index => $plan)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                    id="{{ $plan->slug }}-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#{{ $plan->slug }}" 
                                    type="button" 
                                    role="tab"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                @if($index === 0)
                                    {{ __('landing.plan_small_name') }}
                                @elseif($index === 1)
                                    {{ __('landing.plan_medium_name') }}
                                @else
                                    {{ __('landing.plan_unlimited_name') }}
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="tab-content" id="pricingTabsContent">
            @foreach($plans as $index => $plan)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="{{ $plan->slug }}" role="tabpanel">
                    <div class="row justify-content-center">
                        <!-- Monthly -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm {{ $planColors[$plan->slug]['border'] ?? 'border-success' }} border-2" data-plan-id="{{ $plan->id }}">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] ?? 'bg-success' }}" style="font-size: 0.7rem;">{{ __('landing.pricing_period_month') }}</span>
                                </div>
                                <div class="card-body p-4" style="padding-top: 3rem !important;">
                                    <div class="text-center mb-4">
                                        <h3 class="h5 fw-bold text-dark mb-3">{{ __('landing.pricing_period_month') }}</h3>
                                        <div class="mb-3">
                                            @php
                                                $currentLang = App::getLocale();
                                                $monthlyPrice = $plan->getPriceForLanguage($currentLang, 'monthly');
                                                $currency = $currentLang === 'en' ? '$' : '₴';
                                            @endphp
                                            <span class="h2 fw-bold text-dark">{{ number_format($monthlyPrice, 0, ',', ' ') }}{{ $currency }}</span>
                                            <span class="text-muted">{{ __('landing.pricing_period_monthly') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <div class="{{ $planColors[$plan->slug]['badge'] }} bg-opacity-10 rounded p-3 mb-3">
                                            <i class="{{ $planColors[$plan->slug]['icon_class'] }} {{ $planColors[$plan->slug]['icon'] }} fa-2x mb-2"></i>
                                            <h6 class="fw-bold {{ $planColors[$plan->slug]['icon'] }} mb-0">
                                                @if($index === 0)
                                                    {{ __('landing.plan_small_name') }}
                                                @elseif($index === 1)
                                                    {{ __('landing.plan_medium_name') }}
                                                @else
                                                    {{ __('landing.plan_unlimited_name') }}
                                                @endif
                                            </h6>
                                        </div>
                                        <p class="text-muted small">
                                            @php
                                                // Всегда используем переводы, игнорируем данные из базы
                                                $planDescription = $index === 0 ? __('landing.plan_small_description') : 
                                                    ($index === 1 ? __('landing.plan_medium_description') : 
                                                    __('landing.plan_unlimited_description'));
                                            @endphp
                                            {{ $planDescription }}
                                        </p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <p class="fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>{{ __('landing.plan_features_included') }}</p>
                                        <p class="text-muted small">{{ __('landing.plan_full_access') }}</p>
                                    </div>
                                    
                                    @if(Auth::guard('client')->check())
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn">
                                            {{ __('landing.pricing_choose_plan') }}
                                        </a>
                                    @else
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                            {{ __('landing.pricing_try_free') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 3 Months -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 position-relative {{ $planColors[$plan->slug]['border'] }} border-3 shadow-lg" style="transform: scale(1.05); z-index: 10;" data-plan-id="{{ $plan->id }}">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] }} px-2 py-1" style="font-size: 0.7rem; font-weight: 700;">
                                        <i class="fas fa-star me-1"></i>{{ __('landing.pricing_savings_top') }}
                                    </span>
                                </div>
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] }}" style="font-size: 0.7rem;">{{ __('landing.pricing_savings_10') }}</span>
                                </div>
                                <div class="card-body p-4" style="padding-top: 3.5rem !important;">
                                    <div class="text-center mb-4">
                                        <h3 class="h5 fw-bold text-dark mb-3">{{ __('landing.pricing_period_quarter') }}</h3>
                                        <div class="mb-3">
                                            @php
                                                $quarterlyPrice = $plan->getPriceForLanguage($currentLang, 'quarterly');
                                            @endphp
                                            <span class="h2 fw-bold text-dark">{{ number_format($quarterlyPrice, 0, ',', ' ') }}{{ $currency }}</span>
                                            <span class="text-muted">{{ __('landing.pricing_period_quarterly') }}</span>
                                        </div>
                                        <p class="text-muted small">{{ number_format($quarterlyPrice / 3, 0, ',', ' ') }}{{ $currency }} {{ __('landing.pricing_period_monthly') }}</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <div class="{{ $planColors[$plan->slug]['badge'] }} bg-opacity-10 rounded p-3 mb-3">
                                            <i class="{{ $planColors[$plan->slug]['icon_class'] }} {{ $planColors[$plan->slug]['icon'] }} fa-2x mb-2"></i>
                                            <h6 class="fw-bold {{ $planColors[$plan->slug]['icon'] }} mb-0">
                                                @if($index === 0)
                                                    {{ __('landing.plan_small_name') }}
                                                @elseif($index === 1)
                                                    {{ __('landing.plan_medium_name') }}
                                                @else
                                                    {{ __('landing.plan_unlimited_name') }}
                                                @endif
                                            </h6>
                                        </div>
                                        <p class="text-muted small">
                                            @php
                                                // Всегда используем переводы, игнорируем данные из базы
                                                $planDescription = $index === 0 ? __('landing.plan_small_description') : 
                                                    ($index === 1 ? __('landing.plan_medium_description') : 
                                                    __('landing.plan_unlimited_description'));
                                            @endphp
                                            {{ $planDescription }}
                                        </p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <p class="{{ $planColors[$plan->slug]['icon'] }} fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>{{ __('landing.plan_features_included') }}</p>
                                        <p class="text-muted small">{{ __('landing.plan_full_access') }}</p>
                                    </div>
                                    
                                    @if(Auth::guard('client')->check())
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn">
                                            {{ __('landing.pricing_choose_plan') }}
                                        </a>
                                    @else
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                            {{ __('landing.pricing_try_free') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 6 Months -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm {{ $planColors[$plan->slug]['border'] }} border-2" data-plan-id="{{ $plan->id }}">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] }}" style="font-size: 0.7rem;">{{ __('landing.pricing_savings_15') }}</span>
                                </div>
                                <div class="card-body p-4" style="padding-top: 3rem !important;">
                                    <div class="text-center mb-4">
                                        <h3 class="h5 fw-bold text-dark mb-3">{{ __('landing.pricing_period_semiannual') }}</h3>
                                        <div class="mb-3">
                                            @php
                                                $semiannualPrice = $plan->getPriceForLanguage($currentLang, 'six_months');
                                            @endphp
                                            <span class="h2 fw-bold text-dark">{{ number_format($semiannualPrice, 0, ',', ' ') }}{{ $currency }}</span>
                                            <span class="text-muted">{{ __('landing.pricing_period_semiannual_full') }}</span>
                                        </div>
                                        <p class="text-muted small">{{ number_format($semiannualPrice / 6, 0, ',', ' ') }}{{ $currency }} {{ __('landing.pricing_period_monthly') }}</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <div class="{{ $planColors[$plan->slug]['badge'] }} bg-opacity-10 rounded p-3 mb-3">
                                            <i class="{{ $planColors[$plan->slug]['icon_class'] }} {{ $planColors[$plan->slug]['icon'] }} fa-2x mb-2"></i>
                                            <h6 class="fw-bold {{ $planColors[$plan->slug]['icon'] }} mb-0">
                                                @if($index === 0)
                                                    {{ __('landing.plan_small_name') }}
                                                @elseif($index === 1)
                                                    {{ __('landing.plan_medium_name') }}
                                                @else
                                                    {{ __('landing.plan_unlimited_name') }}
                                                @endif
                                            </h6>
                                        </div>
                                        <p class="text-muted small">
                                            @php
                                                // Всегда используем переводы, игнорируем данные из базы
                                                $planDescription = $index === 0 ? __('landing.plan_small_description') : 
                                                    ($index === 1 ? __('landing.plan_medium_description') : 
                                                    __('landing.plan_unlimited_description'));
                                            @endphp
                                            {{ $planDescription }}
                                        </p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <p class="{{ $planColors[$plan->slug]['icon'] }} fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>{{ __('landing.plan_features_included') }}</p>
                                        <p class="text-muted small">{{ __('landing.plan_full_access') }}</p>
                                    </div>
                                    
                                    @if(Auth::guard('client')->check())
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn">
                                            {{ __('landing.pricing_choose_plan') }}
                                        </a>
                                    @else
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                            {{ __('landing.pricing_try_free') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Year -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm {{ $planColors[$plan->slug]['border'] }} border-2" data-plan-id="{{ $plan->id }}">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] }}" style="font-size: 0.7rem;">{{ __('landing.pricing_savings_25') }}</span>
                                </div>
                                <div class="card-body p-4" style="padding-top: 3rem !important;">
                                    <div class="text-center mb-4">
                                        <h3 class="h5 fw-bold text-dark mb-3">{{ __('landing.pricing_period_year') }}</h3>
                                        <div class="mb-3">
                                            @php
                                                $yearlyPrice = $plan->getPriceForLanguage($currentLang, 'yearly');
                                            @endphp
                                            <span class="h2 fw-bold text-dark">{{ number_format($yearlyPrice, 0, ',', ' ') }}{{ $currency }}</span>
                                            <span class="text-muted">{{ __('landing.pricing_period_yearly') }}</span>
                                        </div>
                                        <p class="text-muted small">{{ number_format($yearlyPrice / 12, 0, ',', ' ') }}{{ $currency }} {{ __('landing.pricing_period_monthly') }}</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <div class="{{ $planColors[$plan->slug]['badge'] }} bg-opacity-10 rounded p-3 mb-3">
                                            <i class="{{ $planColors[$plan->slug]['icon_class'] }} {{ $planColors[$plan->slug]['icon'] }} fa-2x mb-2"></i>
                                            <h6 class="fw-bold {{ $planColors[$plan->slug]['icon'] }} mb-0">
                                                @if($index === 0)
                                                    {{ __('landing.plan_small_name') }}
                                                @elseif($index === 1)
                                                    {{ __('landing.plan_medium_name') }}
                                                @else
                                                    {{ __('landing.plan_unlimited_name') }}
                                                @endif
                                            </h6>
                                        </div>
                                        <p class="text-muted small">
                                            @php
                                                // Всегда используем переводы, игнорируем данные из базы
                                                $planDescription = $index === 0 ? __('landing.plan_small_description') : 
                                                    ($index === 1 ? __('landing.plan_medium_description') : 
                                                    __('landing.plan_unlimited_description'));
                                            @endphp
                                            {{ $planDescription }}
                                        </p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <p class="{{ $planColors[$plan->slug]['icon'] }} fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>{{ __('landing.plan_features_included') }}</p>
                                        <p class="text-muted small">{{ __('landing.plan_full_access') }}</p>
                                    </div>
                                    
                                    @if(Auth::guard('client')->check())
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn">
                                            {{ __('landing.pricing_choose_plan') }}
                                        </a>
                                    @else
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                            {{ __('landing.pricing_try_free') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach


        </div>
    </div>
</section>

@include('landing.components.register-modal')

<!-- Plan Selection Modal -->
<div class="modal fade" id="planSelectionModal" tabindex="-1" aria-labelledby="planSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="planSelectionModalLabel">{{ __('landing.pricing_modal_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">{{ __('landing.pricing_modal_title') }}:</h6>
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h5 class="card-title" id="selectedPlanName"></h5>
                                <p class="card-text" id="selectedPlanDescription"></p>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge bg-primary fs-6 px-3 py-2" id="selectedPlanPeriod"></span>
                                    <span class="fs-5 fw-bold text-dark" id="selectedPlanPrice"></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2" id="selectedPlanSavings" style="display: none;">
                                    <span class="badge bg-success" id="savingsBadge"></span>
                                    <span class="text-muted small" id="selectedPlanMonthlyPrice"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">{{ __('landing.pricing_modal_title') }}:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>{{ __('landing.pricing_modal_all_features') }}</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>{{ __('landing.pricing_modal_full_access') }}</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>{{ __('landing.pricing_modal_support') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('landing.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="proceedToPayment">{{ __('landing.pricing_modal_proceed') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Features Overview Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="h3 fw-bold mb-4 text-dark">{{ __('landing.pricing_features_title') }}</h2>
                <p class="text-muted">{{ __('landing.pricing_features_subtitle') }}</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-users text-primary fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">{{ __('landing.pricing_feature_clients') }}</h5>
                    <p class="text-muted small">{{ __('landing.pricing_feature_clients_desc') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-calendar-alt text-success fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">{{ __('landing.pricing_feature_booking') }}</h5>
                    <p class="text-muted small">{{ __('landing.pricing_feature_booking_desc') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-line text-warning fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">{{ __('landing.pricing_feature_analytics') }}</h5>
                    <p class="text-muted small">{{ __('landing.pricing_feature_analytics_desc') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-headset text-info fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">{{ __('landing.pricing_feature_support') }}</h5>
                    <p class="text-muted small">{{ __('landing.pricing_feature_support_desc') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Contact Button -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.contact') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-envelope me-2"></i>
                    {{ __('landing.contact_us') }}
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('landing/main.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('#pricingTabs .nav-link');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            tabs.forEach(function(t) {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            
            document.querySelectorAll('#pricingTabsContent .tab-pane').forEach(function(pane) {
                pane.classList.remove('show', 'active');
            });
            
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            
            var targetId = this.getAttribute('data-bs-target');
            var targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('pricing-btn') && !e.target.hasAttribute('data-bs-toggle')) {
            e.preventDefault();
            
            var card = e.target.closest('.card');
            var planName = card.querySelector('h6').textContent;
            var planPeriod = card.querySelector('h3').textContent;
            var planPrice = card.querySelector('.h2').textContent;
            
            var savings = '';
            var monthlyPrice = '';
            
            var badges = card.querySelectorAll('.badge');
            badges.forEach(function(badge) {
                if (badge.textContent.includes('Экономия')) {
                    savings = badge.textContent;
                }
            });
            
            var smallTexts = card.querySelectorAll('.text-muted.small');
            smallTexts.forEach(function(text) {
                if (text.textContent.includes('₴ / месяц')) {
                    monthlyPrice = text.textContent;
                }
            });
            
            var button = e.target;
            var buttonClasses = button.className;
            var planColor = '';
            
            if (buttonClasses.includes('btn-tab-green')) {
                planColor = 'bg-success';
            } else if (buttonClasses.includes('btn-tab-blue')) {
                planColor = 'bg-primary';
            } else if (buttonClasses.includes('btn-tab-yellow')) {
                planColor = 'bg-warning';
            } else {
                planColor = 'bg-primary';
            }
            
            document.getElementById('selectedPlanName').textContent = planName;
            document.getElementById('selectedPlanDescription').textContent = '{{ __("landing.pricing_modal_perfect") }}';
            document.getElementById('selectedPlanPeriod').textContent = planPeriod;
            document.getElementById('selectedPlanPrice').textContent = planPrice;
            
            var savingsContainer = document.getElementById('selectedPlanSavings');
            var savingsBadge = document.getElementById('savingsBadge');
            if (planPeriod.includes('3 месяца') || planPeriod.includes('3 Months')) {
                savingsBadge.textContent = '{{ __("landing.pricing_savings_10") }}';
                savingsContainer.style.display = 'flex';
            } else if (planPeriod.includes('6 месяцев') || planPeriod.includes('6 Months')) {
                savingsBadge.textContent = '{{ __("landing.pricing_savings_15") }}';
                savingsContainer.style.display = 'flex';
            } else if (planPeriod.includes('год') || planPeriod.includes('Year')) {
                savingsBadge.textContent = '{{ __("landing.pricing_savings_25") }}';
                savingsContainer.style.display = 'flex';
            } else {
                savingsContainer.style.display = 'none';
            }
            
            var monthlyPriceContainer = document.getElementById('selectedPlanMonthlyPrice');
            if (monthlyPrice) {
                monthlyPriceContainer.textContent = monthlyPrice;
            } else {
                monthlyPriceContainer.textContent = '';
            }
            
            var periodBadge = document.getElementById('selectedPlanPeriod');
            periodBadge.className = 'badge me-3 fs-6 px-3 py-2 ' + planColor;
            
            var proceedButton = document.getElementById('proceedToPayment');
            var cardPlanId = card.getAttribute('data-plan-id');
            proceedButton.setAttribute('data-plan-id', cardPlanId);
            
            var periodText = planPeriod.trim();
            var period = 'monthly';
            
            if (periodText.includes('3 месяца') || periodText.includes('3 мес')) {
                period = 'quarterly';
            } else if (periodText.includes('6 месяцев') || periodText.includes('6 мес')) {
                period = 'yearly';
            } else if (periodText.includes('год') || periodText.includes('12 месяцев')) {
                period = 'yearly';
            }
            
            proceedButton.setAttribute('data-period', period);
            
            var modal = new bootstrap.Modal(document.getElementById('planSelectionModal'));
            modal.show();
        }
    });
    
    document.getElementById('proceedToPayment').addEventListener('click', function() {
        var planId = this.getAttribute('data-plan-id');
        var period = this.getAttribute('data-period');
        
        if (planId && period) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("landing.payment.create") }}';
            
            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            var planIdInput = document.createElement('input');
            planIdInput.type = 'hidden';
            planIdInput.name = 'plan_id';
            planIdInput.value = planId;
            form.appendChild(planIdInput);
            
            var periodInput = document.createElement('input');
            periodInput.type = 'hidden';
            periodInput.name = 'period';
            periodInput.value = period;
            form.appendChild(periodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>

<style>
/* Стили для активных вкладок по тарифам */
#pricingTabs .nav-link.active[data-bs-target="#small"] {
    background-color: #28a745 !important; /* Зеленый для первого тарифа */
    color: white !important;
    border-color: #28a745 !important;
}

#pricingTabs .nav-link.active[data-bs-target="#medium"] {
    background-color: #007bff !important; /* Синий для второго тарифа */
    color: white !important;
    border-color: #007bff !important;
}

#pricingTabs .nav-link.active[data-bs-target="#unlimited"] {
    background-color: #ffc107 !important; /* Желтый для третьего тарифа */
    color: white !important; /* Белый текст как у всех */
    border-color: #ffc107 !important;
}

/* Стили для неактивных вкладок */
#pricingTabs .nav-link:not(.active) {
    background-color: transparent !important;
    color: #6c757d !important;
    border-color: #dee2e6 !important;
}

#pricingTabs .nav-link:not(.active):hover {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

/* Стили для компактных кнопок тарифов */
.pricing-btn {
    padding: 8px 16px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    transition: all 0.3s ease !important;
}

.pricing-btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
</style>
@endpush