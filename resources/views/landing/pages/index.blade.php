@extends('landing.layouts.app')

@section('title', 'CRM Studio - Система управления салоном красоты')
@section('description', 'Профессиональная CRM система для управления салоном красоты, записями клиентов и аналитикой')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Управляйте салоном красоты эффективно</h1>
                <p class="lead mb-4">CRM Studio - это современная система управления, которая поможет вам организовать работу салона, вести клиентскую базу и увеличить прибыль.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#registerModal">Попробовать бесплатно</a>
                    <a href="{{ route('beautyflow.services') }}" class="btn btn-outline-light btn-lg">Узнать больше</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('images/hero-dashboard.png') }}" alt="CRM Studio Dashboard" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Возможности системы</h2>
            <p class="lead text-muted">Все необходимые инструменты для успешного управления салоном красоты</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h5 class="card-title">Управление клиентами</h5>
                        <p class="card-text text-muted">Ведите базу клиентов, отслеживайте историю посещений и предпочтения каждого клиента.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                        <h5 class="card-title">Записи и расписание</h5>
                        <p class="card-text text-muted">Удобное планирование записей, календарь мастера и автоматические напоминания.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h5 class="card-title">Аналитика и отчеты</h5>
                        <p class="card-text text-muted">Детальная аналитика продаж, популярных услуг и эффективности работы мастеров.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-light py-5">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4">Готовы начать?</h2>
        <p class="lead text-muted mb-4">Присоединяйтесь к тысячам салонов красоты, которые уже используют CRM Studio</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Начать бесплатно</a>
    </div>
</section>

<!-- Модальное окно регистрации -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('beautyflow.register') }}" id="registerForm" autocomplete="off">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-user-plus me-2 text-primary"></i> Регистрация
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="reg-fullname" class="form-label">Имя <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-user" style="color:#a21caf;"></i></span>
              <input type="text" class="form-control @error('fullname') is-invalid @enderror" id="reg-fullname" name="fullname" placeholder="Ваше имя" required>
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
              <input type="text" class="form-control @error('phone') is-invalid @enderror" id="reg-phone" name="phone" placeholder="+7 (___) ___-__-__" autocomplete="tel">
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-3">
            <label for="reg-salon" class="form-label">Название салона или Имя Фамилия <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-store" style="color:#a21caf;"></i></span>
              <input type="text" class="form-control @error('salon') is-invalid @enderror" id="reg-salon" name="salon" placeholder="Beauty Studio или Иван Иванов" required>
              @error('salon')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="privacy" required>
            <label class="form-check-label" for="privacy">
              Я согласен с <a href="#" target="_blank">политикой обработки данных</a>
            </label>
          </div>
          <div class="form-text fw-semibold mb-3" style="color:#2563eb;"><i class="fas fa-info-circle me-1"></i>После регистрации на указанный email придет письмо с дальнейшими инструкциями.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <button type="submit" class="btn btn-primary" id="registerBtn">
            <span class="spinner-border spinner-border-sm d-none" id="regSpinner"></span>
            Зарегистрироваться
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(function() {
  $('#reg-phone').mask('+7 (000) 000-00-00');
  $('#registerForm').on('submit', function() {
    $('#registerBtn').attr('disabled', true);
    $('#regSpinner').removeClass('d-none');
  });
  $('#registerModal').on('hidden.bs.modal', function () {
    $('#registerForm')[0].reset();
    $('#registerBtn').attr('disabled', false);
    $('#regSpinner').addClass('d-none');
    $('.is-invalid').removeClass('is-invalid');
  });
});
</script>
@endpush
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.feature-icon {
    transition: transform 0.3s ease;
}

.card:hover .feature-icon {
    transform: scale(1.1);
}
</style>
@endpush 
