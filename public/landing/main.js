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