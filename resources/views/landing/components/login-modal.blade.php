<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('landing.account.login.post') }}" id="loginForm" autocomplete="off">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-sign-in-alt me-2 text-primary"></i> Вход в личный кабинет
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-3">
            <p class="text-muted mb-0">Вход только для руководителей проектов</p>
          </div>
          
          @if ($errors->any())
            <div class="mb-3">
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif
          
          <div class="mb-3">
            <label for="login-email" class="form-label">Email руководителя <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-envelope" style="color:#2563eb;"></i></span>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="login-email" name="email" placeholder="you@email.com" required autocomplete="email">
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          
          <div class="mb-3">
            <label for="login-password" class="form-label">Пароль руководителя <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock" style="color:#22c55e;"></i></span>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="login-password" name="password" placeholder="Введите пароль" required autocomplete="current-password">
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          
          <div class="form-text fw-semibold mb-3" style="color:#2563eb;">
            <i class="fas fa-info-circle me-1"></i>Используйте данные руководителя проекта для входа в личный кабинет.
          </div>
          
          <hr class="my-3">
          
          <div class="text-center">
            <p class="text-muted mb-2">Нет проекта?</p>
            <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-user-plus me-1"></i>Зарегистрировать проект
            </a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <button type="submit" class="btn btn-primary" id="loginBtn">
            <span class="spinner-border spinner-border-sm d-none" id="loginSpinner"></span>
            Войти
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(function() {
  $('#loginForm').on('submit', function(e) {
    e.preventDefault();
    var $form = $(this);
    var $btn = $('#loginBtn');
    var $spinner = $('#loginSpinner');
    var $modalBody = $('.modal-body');
    
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.alert-danger').remove();
    
    $btn.attr('disabled', true);
    $spinner.removeClass('d-none');

    $.ajax({
      url: $form.attr('action'),
      method: 'POST',
      data: $form.serialize(),
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(response) {
        window.location.href = '{{ route("landing.account.dashboard") }}';
      },
      error: function(xhr) {
        if (xhr.status === 422) {
          var errors = xhr.responseJSON.errors;
          $.each(errors, function(field, messages) {
            var $input = $form.find('[name=' + field + ']');
            $input.addClass('is-invalid');
            $input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
          });
        } else {
          $modalBody.prepend(
            '<div class="alert alert-danger">' +
              '<i class="fas fa-exclamation-triangle me-2"></i>' +
              'Произошла ошибка при входе. Попробуйте позже или обратитесь в поддержку.' +
            '</div>'
          );
        }
      },
      complete: function() {
        $btn.attr('disabled', false);
        $spinner.addClass('d-none');
      }
    });
  });

  $('#loginModal').on('hidden.bs.modal', function () {
    $('#loginForm')[0].reset();
    $('#loginBtn').attr('disabled', false);
    $('#loginSpinner').addClass('d-none');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.alert-danger').remove();
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
  });
});
</script>
@if ($errors->any())
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('loginModal'));
    modal.show();
  });
</script>
@endif
@endpush
