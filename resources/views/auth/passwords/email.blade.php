@extends('client.layouts.auth')

@section('title', 'Восстановление пароля')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <div class="text-muted mb-2">Восстановление доступа</div>
        </div>
        
        <!-- Сообщение об успехе -->
        @if (session('status'))
            <div id="success-message" class="text-center">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="mb-2">Ссылка отправлена!</h5>
                    <p class="mb-0">Ссылка для сброса пароля отправлена на указанный email.</p>
                    <p class="text-muted small mt-2">Проверьте папку "Входящие" или "Спам".</p>
                </div>
                <div class="d-flex justify-content-center">
                    <a href="{{ route('password.request') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Отправить еще раз
                    </a>
                </div>
            </div>
        @endif
        
        <!-- Форма восстановления пароля -->
        <form id="reset-form" method="POST" action="{{ route('password.email') }}" @if(session('status')) style="display: none;" @endif>
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <span class="btn-text">Отправить ссылку для сброса</span>
                    <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reset-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.querySelector('.btn-text');
    const spinner = document.getElementById('spinner');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Показываем спиннер и блокируем кнопку
            submitBtn.disabled = true;
            btnText.textContent = 'Отправка...';
            spinner.classList.remove('d-none');
            
            // Если форма валидна, скрываем её после отправки
            if (form.checkValidity()) {
                setTimeout(function() {
                    form.style.display = 'none';
                    
                    // Показываем сообщение об успехе
                    const successMessage = document.createElement('div');
                    successMessage.id = 'success-message';
                    successMessage.className = 'text-center';
                    successMessage.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                            <h5 class="mb-2">Ссылка отправлена!</h5>
                            <p class="mb-0">Ссылка для сброса пароля отправлена на указанный email.</p>
                            <p class="text-muted small mt-2">Проверьте папку "Входящие" или "Спам".</p>
                        </div>
                        <div class="d-flex justify-content-center">
                            <a href="${window.location.href}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Отправить еще раз
                            </a>
                        </div>
                    `;
                    
                    form.parentNode.appendChild(successMessage);
                }, 1000); // Небольшая задержка для UX
            }
        });
    }
});
</script>
@endpush
@endsection
