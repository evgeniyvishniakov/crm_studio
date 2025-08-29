<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Trimora</h5>
                <p class="text-muted">{{ __('landing.professional_system') }}</p>
                <div class="social-links">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-telegram"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>{{ __('landing.product') }}</h5>
                <ul class="list-unstyled">
                    <li><a href="#features-grid" class="text-muted text-decoration-none">{{ __('landing.features') }}</a></li>
                    <li><a href="#niches-section" class="text-muted text-decoration-none">{{ __('landing.niches') }}</a></li>
                    <li><a href="{{ route('beautyflow.pricing') }}" class="text-muted text-decoration-none">{{ __('landing.pricing') }}</a></li>
                    <li><a href="{{ route('beautyflow.knowledge') }}" class="text-muted text-decoration-none">{{ __('landing.knowledge_base') }}</a></li>
                    <li><a href="{{ route('beautyflow.contact') }}" class="text-muted text-decoration-none">{{ __('landing.contacts') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>{{ __('landing.services') }}</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-muted text-decoration-none">{{ __('landing.all_services') }}</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">{{ __('landing.client_management') }}</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">{{ __('landing.appointments_schedule') }}</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">{{ __('landing.product_management') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>{{ __('landing.contacts') }}</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('beautyflow.contact') }}" class="text-muted text-decoration-none">{{ __('landing.contact_us') }}</a></li>
                    <li><i class="fas fa-phone me-2"></i>{{ __('landing.phone') }}</li>
                    <li><i class="fas fa-envelope me-2"></i>{{ __('landing.email') }}</li>
                    <li><i class="fas fa-map-marker-alt me-2"></i>{{ __('landing.address') }}</li>
                </ul>
            </div>

        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-md-6">
                <p class="text-muted mb-0">&copy; 2024 Trimora. {{ __('landing.all_rights_reserved') }}.</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ \App\Helpers\LanguageHelper::addLanguageToUrl(route('beautyflow.privacy')) }}" class="text-muted text-decoration-none me-3">{{ __('landing.privacy_policy') }}</a>
                <a href="{{ \App\Helpers\LanguageHelper::addLanguageToUrl(route('beautyflow.terms')) }}" class="text-muted text-decoration-none">{{ __('landing.terms_of_use') }}</a>
            </div>
        </div>
    </div>
</footer> 
