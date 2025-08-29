@extends('landing.layouts.app')

@section('title', __('landing.terms_of_use_title'))
@section('description', __('landing.terms_of_use_description'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="text-center mb-5">{{ __('landing.terms_of_use_heading') }}</h1>
            
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2>{{ __('landing.general_provisions_terms') }}</h2>
                    <p>{{ __('landing.general_provisions_terms_text') }}</p>
                    
                    <h2>{{ __('landing.services') }}</h2>
                    <p>{{ __('landing.services_text') }}</p>
                    <ul>
                        <li>{{ __('landing.clients_appointments_base') }}</li>
                        <li>{{ __('landing.online_booking_schedule') }}</li>
                        <li>{{ __('landing.services_products_management') }}</li>
                        <li>{{ __('landing.analytics_reports') }}</li>
                        <li>{{ __('landing.telegram_integration') }}</li>
                        <li>{{ __('landing.website_widget') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.registration_account') }}</h2>
                    <p>{{ __('landing.registration_account_text') }}</p>
                    <ul>
                        <li>{{ __('landing.create_account_real_data') }}</li>
                        <li>{{ __('landing.keep_login_data_safe') }}</li>
                        <li>{{ __('landing.not_share_access') }}</li>
                        <li>{{ __('landing.update_contact_info') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.usage_rules') }}</h2>
                    <p>{{ __('landing.usage_rules_text') }}</p>
                    <ul>
                        <li>{{ __('landing.illegal_purposes') }}</li>
                        <li>{{ __('landing.interfere_service_hacking') }}</li>
                        <li>{{ __('landing.distribute_malicious_code') }}</li>
                        <li>{{ __('landing.spam_unwanted_mailing') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.payment_tariffs') }}</h2>
                    <ul>
                        <li>{{ __('landing.free_trial_7_days') }}</li>
                        <li>{{ __('landing.monthly_yearly_subscription') }}</li>
                        <li>{{ __('landing.refund_14_days') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.liability') }}</h2>
                    <p>{{ __('landing.liability_text') }}</p>
                    <ul>
                        <li>{{ __('landing.data_loss_user_fault') }}</li>
                        <li>{{ __('landing.technical_issues_not_our_fault') }}</li>
                        <li>{{ __('landing.third_party_actions') }}</li>
                    </ul>
                    <p>{{ __('landing.max_liability_text') }}</p>
                    
                    <h2>{{ __('landing.technical_support') }}</h2>
                    <p>{{ __('landing.technical_support_text') }}</p>
                    <ul>
                        <li>{{ __('landing.email_support') }}</li>
                        <li>{{ __('landing.knowledge_base_instructions') }}</li>
                        <li>{{ __('landing.system_updates') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.terms_changes') }}</h2>
                    <p>{{ __('landing.terms_changes_text') }}</p>
                    
                    <h2>{{ __('landing.terms_contacts') }}</h2>
                    <p>{{ __('landing.terms_contacts_text') }}</p>
                    <p><strong>{{ __('landing.support_email') }}</strong> {{ __('landing.support_email_address') }}</p>
                    
                    <p class="text-muted mt-4"><small>{{ __('landing.last_updated') }} {{ date('d.m.Y') }}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
