@extends('client.layouts.app')

@section('title', 'Расписание мастеров - Веб-запись')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Расписание мастеров - Веб-запись
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Выбор мастера -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-select">Выберите мастера</label>
                                <select class="form-control" id="user-select">
                                    <option value="">Выберите мастера...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Расписание -->
                    <div id="schedule-container" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">Расписание на неделю</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>День недели</th>
                                                <th>Рабочие часы</th>
                                                <th>Статус</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody id="schedule-tbody">
                                            <!-- Расписание будет загружено через AJAX -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" onclick="saveSchedule()">
                                        <i class="fas fa-save"></i> Сохранить расписание
                                    </button>
                                    <a href="{{ route('client.booking.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Назад к настройкам
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Сообщение о выборе мастера -->
                    <div id="select-user-message" class="text-center py-5">
                        <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                        <h5>Выберите мастера</h5>
                        <p class="text-muted">Выберите мастера из списка выше, чтобы настроить его расписание</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для редактирования дня -->
<div class="modal fade" id="editDayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Настройка расписания</h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="day-schedule-form">
                    <input type="hidden" id="edit-day-of-week">
                    <input type="hidden" id="edit-user-id">
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit-is-working">
                            <label class="custom-control-label" for="edit-is-working">
                                <strong>Рабочий день</strong>
                            </label>
                        </div>
                    </div>

                    <div id="working-hours-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit-start-time">Начало работы</label>
                                    <input type="time" class="form-control" id="edit-start-time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit-end-time">Конец работы</label>
                                    <input type="time" class="form-control" id="edit-end-time">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit-notes">Примечания</label>
                            <textarea class="form-control" id="edit-notes" rows="3" placeholder="Дополнительная информация..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditDayModal()">Отмена</button>
                <button type="button" class="btn-submit" onclick="saveDaySchedule()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Уведомление -->

    <div class="alert alert-success">
        <i class="fas fa-check"></i> <span id="notification-text"></span>
    </div>
</div>


@push('scripts')
<script>
// Добавляем переводы к существующим
if (window.translations) {
    console.log('Debug translations before:', window.translations);
    
    const newTranslations = {
        monday: '{{ __("messages.monday") }}',
        tuesday: '{{ __("messages.tuesday") }}',
        wednesday: '{{ __("messages.wednesday") }}',
        thursday: '{{ __("messages.thursday") }}',
        friday: '{{ __("messages.friday") }}',
        saturday: '{{ __("messages.saturday") }}',
        sunday: '{{ __("messages.sunday") }}',
        working: '{{ __("messages.working_day_status") }}',
        day_off: '{{ __("messages.day_off") }}',
        no_notes: '{{ __("messages.no_notes") }}',
        edit: '{{ __("messages.edit_schedule") }}',
        interval: '{{ __("messages.interval") }}',
        interval_minutes: '{{ __("messages.interval_minutes") }}',
        working_hours: '{{ __("messages.working_hours") }}',
        notes: '{{ __("messages.notes") }}'
    };
    
    console.log('Debug new translations:', newTranslations);
    console.log('Debug interval_minutes value:', '{{ __("messages.interval_minutes") }}');
    
    Object.assign(window.translations, newTranslations);
    
    console.log('Debug translations after:', window.translations);
}
</script>
<script src="{{ asset('client/js/booking.js') }}"></script>
@endpush

@endsection