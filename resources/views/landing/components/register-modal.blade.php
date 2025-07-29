<!-- Модальное окно регистрации -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('beautyflow.register') }}" id="registerForm" autocomplete="off" @if(session('success')) style="display:none;" @endif>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-user-plus me-2 text-primary"></i> Регистрация
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
                  <h5 class="mb-2">Регистрация успешно отправлена!</h5>
                  <p class="mb-0">Вам на почту отправлено письмо с ссылкой на создание пароля.</p>
                  <p class="text-muted small mt-2">Проверьте папку "Входящие" или "Спам".</p>
                  <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">Ок</button>
                </div>
              @endif
            </div>
          @endif
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
              <input type="text" class="form-control @error('phone') is-invalid @enderror" id="reg-phone" name="phone" placeholder="+380991234567" autocomplete="tel">
              <small class="form-text text-muted">Введите номер в международном формате, например: +380991234567</small>
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-3">
            <label for="reg-salon" class="form-label">Название салона или Имя Фамилия <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-store" style="color:#a21caf;"></i></span>
              <input type="text" class="form-control @error('salon') is-invalid @enderror" id="reg-salon" name="salon" placeholder="Beauty Studio или Дмитрий Андреев" required>
              @error('salon')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="privacy" required>
            <label class="form-check-label" for="privacy">
              Я согласен с <a href="{{ route('beautyflow.privacy') }}" target="_blank">политикой обработки данных</a>
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
<script>
$(function() {
  $('#reg-phone').mask('+000000000000000', {placeholder: '+380991234567'});

  $('#registerForm').on('submit', function(e) {
    e.preventDefault();
    var $form = $(this);
    var $btn = $('#registerBtn');
    var $spinner = $('#regSpinner');
    var $modalBody = $('.modal-body');
    
    // Очищаем предыдущие ошибки
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
        $('#register-success-message').remove();
        // Скрываем все элементы формы и футера
        $form.find('.modal-body > *:not(#register-success-message)').hide();
        $form.closest('.modal-content').find('.modal-footer').hide();
        $modalBody.prepend(
          '<div class="alert alert-success text-center" id="register-success-message" style="min-height:200px;display:flex;flex-direction:column;justify-content:center;align-items:center;z-index:2;position:relative;">' +
            '<i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>' +
            '<h5 class="mb-2">Регистрация успешно отправлена!</h5>' +
            '<p class="mb-0">Вам на почту отправлено письмо с ссылкой на создание пароля.</p>' +
            '<p class="text-muted small mt-2">Проверьте папку \"Входящие\" или \"Спам\".</p>' +
          '</div>'
        );
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
          // Показываем общую ошибку
          $modalBody.prepend(
            '<div class="alert alert-danger">' +
              '<i class="fas fa-exclamation-triangle me-2"></i>' +
              'Произошла ошибка при регистрации. Попробуйте позже или обратитесь в поддержку.' +
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

  $('#registerModal').on('hidden.bs.modal', function () {
    // Показываем форму и футер обратно
    $('#registerForm').show();
    $('.modal-footer').show();
    // Удаляем сообщения
    $('#register-success-message').remove();
    $('.alert-danger').remove();
    // Сбрасываем форму
    $('#registerForm')[0].reset();
    $('#registerBtn').attr('disabled', false);
    $('#regSpinner').addClass('d-none');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    // Удаляем backdrop и сбрасываем классы, если вдруг остались
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
  });
});
</script>
@if ($errors->any() || session('success'))
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('registerModal'));
    modal.show();
  });
</script>
@endif
@endpush