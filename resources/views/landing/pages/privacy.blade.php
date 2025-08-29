@extends('landing.layouts.app')

@section('title', __('landing.privacy_policy_title'))
@section('description', __('landing.privacy_policy_description'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="text-center mb-5">{{ __('landing.privacy_policy_heading') }}</h1>
            
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2>{{ __('landing.general_provisions') }}</h2>
                    <p>{{ __('landing.general_provisions_text') }}</p>
                    
                    <h2>{{ __('landing.what_data_we_collect') }}</h2>
                    <p>{{ __('landing.what_data_we_collect_text') }}</p>
                    <ul>
                        <li>{{ __('landing.contact_information') }}</li>
                        <li>{{ __('landing.company_business_info') }}</li>
                        <li>{{ __('landing.clients_appointments_data') }}</li>
                        <li>{{ __('landing.technical_data') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.how_we_use_data') }}</h2>
                    <p>{{ __('landing.how_we_use_data_text') }}</p>
                    <ul>
                        <li>{{ __('landing.provide_improve_services') }}</li>
                        <li>{{ __('landing.support_communication') }}</li>
                        <li>{{ __('landing.system_analytics') }}</li>
                        <li>{{ __('landing.legal_obligations') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.information_protection') }}</h2>
                    <p>{{ __('landing.information_protection_text') }}</p>
                    <ul>
                        <li>{{ __('landing.data_encryption') }}</li>
                        <li>{{ __('landing.regular_system_updates') }}</li>
                        <li>{{ __('landing.limited_access_personal_data') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.data_sharing_third_parties') }}</h2>
                    <p>{{ __('landing.data_sharing_third_parties_text') }}</p>
                    <ul>
                        <li>{{ __('landing.service_operation') }}</li>
                        <li>{{ __('landing.legal_requirements') }}</li>
                    </ul>
                    
                    <h2>{{ __('landing.data_retention') }}</h2>
                    <p>{{ __('landing.data_retention_text') }}</p>
                    
                    <h2>{{ __('landing.your_rights') }}</h2>
                    <p>{{ __('landing.your_rights_text') }}</p>
                    <ul>
                        <li>{{ __('landing.access_your_data') }}</li>
                        <li>{{ __('landing.correct_update_info') }}</li>
                        <li>{{ __('landing.request_data_deletion') }}</li>
                        <li>{{ __('landing.withdraw_consent') }}</li>
                    </ul>
                    <p>{{ __('landing.your_rights_contact') }}</p>
                    
                    <h2>{{ __('landing.cookies') }}</h2>
                    <p>{{ __('landing.cookies_text') }}</p>
                    
                    <h2>{{ __('landing.privacy_contacts') }}</h2>
                    <p>{{ __('landing.privacy_contacts_text') }}</p>
                    <p><strong>{{ __('landing.privacy_email') }}</strong> {{ __('landing.privacy_email_address') }}</p>
                    
                    <h2>{{ __('landing.policy_changes') }}</h2>
                    <p>{{ __('landing.policy_changes_text') }}</p>
                    
                    <p class="text-muted mt-4"><small>{{ __('landing.last_updated') }} {{ date('d.m.Y') }}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
