<!-- Модальное окно регистрации -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('beautyflow.register') }}" id="registerForm" autocomplete="off" @if(session('success')) style="display:none;" @endif>
        @csrf
        <input type="hidden" name="language" id="registerLanguage" value="{{ app()->getLocale() }}">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-user-plus me-2 text-primary"></i> {{ __('landing.registration') }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
        </div>
        <div class="modal-body">
          @if ($errors->any() || session('success'))
            <div class="mb-3">
              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              @if (session('success'))
                <div class="alert alert-success text-center" id="register-success-message">
                  <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                  <h5 class="mb-2">{{ __('landing.registration_success_title') }}</h5>
                  <p class="mb-0">{{ __('landing.registration_success_message') }}</p>
                  <p class="text-muted small mt-2">{{ __('landing.check_inbox_spam') }}</p>
                  <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">{{ __('landing.ok') }}</button>
                </div>
              @endif
            </div>
          @endif
          <div class="mb-3">
            <label for="reg-fullname" class="form-label">{{ __('landing.full_name') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-user" style="color:#a21caf;"></i></span>
              <input type="text" class="form-control @error('fullname') is-invalid @enderror" id="reg-fullname" name="fullname" placeholder="{{ __('landing.full_name') }}" required>
              @error('fullname')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-3">
            <label for="reg-email" class="form-label">Email <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-envelope" style="color:#2563eb;"></i></span>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="reg-email" name="email" placeholder="you@email.com" required autocomplete="email">
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-3">
            <label for="reg-phone" class="form-label">Телефон</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-phone" style="color:#22c55e;"></i></span>
              <input type="text" class="form-control @error('phone') is-invalid @enderror" id="reg-phone" name="phone" placeholder="+380991234567" autocomplete="tel">
              <small class="form-text text-muted">Введите номер в международном формате, например: +380991234567</small>
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-3">
            <label for="reg-salon" class="form-label">{{ __('landing.salon_name') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-store" style="color:#a21caf;"></i></span>
              <input type="text" class="form-control @error('salon') is-invalid @enderror" id="reg-salon" name="salon" placeholder="{{ __('landing.salon_placeholder') }}" required>
              @error('salon')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="privacy" required>
            <label class="form-check-label" for="privacy">
              {!! __('landing.privacy_agreement', ['privacy_policy' => '<a href="' . route('beautyflow.privacy') . '" target="_blank">' . __('landing.privacy_policy') . '</a>']) !!}
            </label>
          </div>
          <div class="form-text fw-semibold mb-3" style="color:#2563eb;"><i class="fas fa-info-circle me-1"></i>{{ __('landing.registration_help') }}</div>
          
          <hr class="my-3">
          
          <div class="text-center">
            <p class="text-muted mb-2">{{ __('landing.already_have_project') }}</p>
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-sign-in-alt me-1"></i>{{ __('landing.enter_personal_account') }}
            </a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('landing.cancel') }}</button>
          <button type="submit" class="btn btn-primary" id="registerBtn">
            <span class="spinner-border spinner-border-sm d-none" id="regSpinner"></span>
            {{ __('landing.register') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const registerBtn = document.getElementById('registerBtn');
    const regSpinner = document.getElementById('regSpinner');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            registerBtn.disabled = true;
            regSpinner.classList.remove('d-none');
            registerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Регистрация...';
        });
    }
    
    if (typeof $.fn.mask !== 'undefined') {
        $('#reg-phone').mask('+380999999999');
    }
});
</script>
