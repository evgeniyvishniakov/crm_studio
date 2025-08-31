@extends('landing.layouts.app')

@section('title', 'Контакты - Trimora')
@section('description', 'Свяжитесь с нами для получения поддержки или консультации по Trimora')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">{{ __('landing.contact_title') }}</h1>
                <p class="lead text-muted">{{ __('landing.contact_subtitle') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">{{ __('landing.contact_write_us') }}</h2>
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('landing.contact_name') }}</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('landing.contact_email') }}</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">{{ __('landing.contact_subject') }}</label>
                        <input type="text" class="form-control" id="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">{{ __('landing.contact_message') }}</label>
                        <textarea class="form-control" id="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('landing.contact_send_message') }}</button>
                </form>
            </div>
            
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">{{ __('landing.contact_contact_info') }}</h2>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ __('landing.contact_phone') }}</h5>
                                <p class="text-muted mb-0">+7 (999) 123-45-67</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ __('landing.contact_email') }}</h5>
                                <p class="text-muted mb-0">info@crmstudio.ru</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ __('landing.contact_address') }}</h5>
                                <p class="text-muted mb-0">Украина, Киев</p>
                                <p class="text-muted mb-0">{{ __('landing.contact_support_24_7') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 
