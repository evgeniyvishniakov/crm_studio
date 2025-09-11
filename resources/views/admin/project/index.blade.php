@extends('admin.layouts.app')

@section('title', 'Управление клиентами - Админ')
@section('page-title', 'Управление клиентами')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Список клиентов</h5>
        <div class="d-flex align-items-center justify-content-end gap-2" style="min-width: 350px;">
            <form method="GET" action="" class="mb-0">
                <div class="input-group input-group-sm">
                    <select name="status" class="form-select" onchange="this.form.submit()" style="min-width: 140px;">
                        <option value="">Все статусы</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активный</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Неактивный</option>
                    </select>
                </div>
            </form>
            <button class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                <i class="fas fa-plus me-2"></i>Добавить клиента
            </button>
            <form method="GET" action="" class="mb-0" style="min-width: 220px;">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" placeholder="Поиск по проекту или email" value="{{ request('q') }}" id="project-search-input" autocomplete="off">
                </div>
            </form>
        </div>
    </div>
    
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Название проекта</th>
                            <th>Email администратора</th>
                            <th>Дата регистрации</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    @include('admin.project._table')
                </table>
            </div>
        </div>
</div>
@endsection 

<!-- Модальное окно создания проекта -->
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить проект</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="project-logo" class="form-label">Логотип</label>
                        <input type="file" class="form-control" id="project-logo" name="logo" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="project-name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="project-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="project-project_name" class="form-label">Название проекта</label>
                        <input type="text" class="form-control" id="project-project_name" name="project_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="project-email" class="form-label">Email администратора</label>
                        <input type="email" class="form-control" id="project-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="project-registered-at" class="form-label">Дата регистрации</label>
                        <input type="datetime-local" class="form-control" id="project-registered-at" name="registered_at" required>
                    </div>
                    <div class="mb-3">
                        <label for="project-language" class="form-label">Язык</label>
                        <select class="form-select" id="project-language" name="language" required>
                            <option value="ua">Украинский</option>
                            <option value="ru">Русский</option>
                            <option value="en">Английский</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="project-currency" class="form-label">Валюта</label>
                        <select class="form-select" id="project-currency" name="currency_id" required>
                            @foreach(\App\Models\Currency::where('is_active', true)->get() as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->code }} ({{ $currency->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="project-status" class="form-label">Статус</label>
                        <select class="form-select" id="project-status" name="status" required>
                            <option value="active">Активный</option>
                            <option value="inactive">Неактивный</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="project-phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="project-phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="project-website" class="form-label">Сайт</label>
                        <input type="text" class="form-control" id="project-website" name="website">
                    </div>
                    <div class="mb-3">
                        <label for="project-address" class="form-label">Адрес</label>
                        <input type="text" class="form-control" id="project-address" name="address">
                    </div>
                    
                    <!-- Координаты карты -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="project-map-latitude" class="form-label">Широта карты</label>
                                <input type="text" class="form-control" id="project-map-latitude" name="map_latitude" placeholder="55.7558">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="project-map-longitude" class="form-label">Долгота карты</label>
                                <input type="text" class="form-control" id="project-map-longitude" name="map_longitude" placeholder="37.6176">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="project-map-zoom" class="form-label">Масштаб карты</label>
                                <input type="number" class="form-control" id="project-map-zoom" name="map_zoom" value="15" min="1" max="20">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="project-about" class="form-label">О нас</label>
                        <textarea class="form-control" id="project-about" name="about" rows="4" placeholder="Краткое описание о салоне/компании..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="project-social-links" class="form-label">Соцсети (через запятую)</label>
                        <input type="text" class="form-control" id="project-social-links" name="social_links" placeholder="vk.com/..., facebook.com/...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div> 

@foreach($projects as $project)
<!-- Модальное окно просмотра проекта -->
<div class="modal fade" id="viewProjectModal{{ $project->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Просмотр проекта: {{ $project->project_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Название проекта</dt>
                    <dd class="col-sm-8">{{ $project->project_name }}</dd>
                    <dt class="col-sm-4">Имя</dt>
                    <dd class="col-sm-8">{{ $project->name }}</dd>
                    <dt class="col-sm-4">Логотип</dt>
                    <dd class="col-sm-8">
                        @if($project->logo)
                            <img src="{{ asset($project->logo) }}" alt="Логотип" style="max-width: 100px; max-height: 100px;">
                        @else
                            <span class="text-muted">Нет</span>
                        @endif
                    </dd>
                    <dt class="col-sm-4">Email администратора</dt>
                    <dd class="col-sm-8">{{ $project->email }}</dd>
                    <dt class="col-sm-4">Дата регистрации</dt>
                    <dd class="col-sm-8">{{ $project->registered_at ? $project->registered_at->format('d.m.Y H:i') : '' }}</dd>
                    <dt class="col-sm-4">Язык</dt>
                    <dd class="col-sm-8">{{ $project->language }}</dd>
                    <dt class="col-sm-4">Статус</dt>
                    <dd class="col-sm-8">{{ $project->status === 'active' ? 'Активный' : 'Неактивный' }}</dd>
                    <dt class="col-sm-4">Телефон</dt>
                    <dd class="col-sm-8">{{ $project->phone ?: '-' }}</dd>
                    <dt class="col-sm-4">Сайт</dt>
                    <dd class="col-sm-8">
                        @if($project->website)
                            <a href="{{ $project->website }}" target="_blank">{{ $project->website }}</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>
                    <dt class="col-sm-4">Адрес</dt>
                    <dd class="col-sm-8">{{ $project->address ?: '-' }}</dd>
                    <dt class="col-sm-4">Instagram</dt>
                    <dd class="col-sm-8">
                        @if($project->instagram)
                            <a href="{{ $project->instagram }}" target="_blank"><i class="fab fa-instagram"></i> {{ $project->instagram }}</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>
                    <dt class="col-sm-4">Facebook</dt>
                    <dd class="col-sm-8">
                        @if($project->facebook)
                            <a href="{{ $project->facebook }}" target="_blank"><i class="fab fa-facebook"></i> {{ $project->facebook }}</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>
                    <dt class="col-sm-4">TikTok</dt>
                    <dd class="col-sm-8">
                        @if($project->tiktok)
                            <a href="{{ $project->tiktok }}" target="_blank"><i class="fab fa-tiktok"></i> {{ $project->tiktok }}</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>
                    <dt class="col-sm-4">Создан</dt>
                    <dd class="col-sm-8">{{ $project->created_at ? $project->created_at->format('d.m.Y H:i') : '-' }}</dd>
                    <dt class="col-sm-4">Обновлён</dt>
                    <dd class="col-sm-8">{{ $project->updated_at ? $project->updated_at->format('d.m.Y H:i') : '-' }}</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования проекта -->
<div class="modal fade" id="editProjectModal{{ $project->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать проект: {{ $project->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-project-logo-{{ $project->id }}" class="form-label">Логотип</label>
                        @if($project->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $project->logo) }}" alt="Логотип" style="max-width: 100px; max-height: 100px;">
                            </div>
                        @endif
                        <input type="file" class="form-control" id="edit-project-logo-{{ $project->id }}" name="logo" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-name-{{ $project->id }}" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="edit-project-name-{{ $project->id }}" name="name" value="{{ $project->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-project_name-{{ $project->id }}" class="form-label">Название проекта</label>
                        <input type="text" class="form-control" id="edit-project-project_name-{{ $project->id }}" name="project_name" value="{{ $project->project_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-email-{{ $project->id }}" class="form-label">Email администратора</label>
                        <input type="email" class="form-control" id="edit-project-email-{{ $project->id }}" name="email" value="{{ $project->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-registered-at-{{ $project->id }}" class="form-label">Дата регистрации</label>
                        <input type="datetime-local" class="form-control" id="edit-project-registered-at-{{ $project->id }}" name="registered_at" value="{{ $project->registered_at ? $project->registered_at->format('Y-m-d\TH:i') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-language-{{ $project->id }}" class="form-label">Язык</label>
                        <select class="form-select" id="edit-project-language-{{ $project->id }}" name="language" required>
                            <option value="ua" @if($project->language=='ua') selected @endif>Украинский</option>
                            <option value="ru" @if($project->language=='ru') selected @endif>Русский</option>
                            <option value="en" @if($project->language=='en') selected @endif>Английский</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-currency-{{ $project->id }}" class="form-label">Валюта</label>
                        <select class="form-select" id="edit-project-currency-{{ $project->id }}" name="currency_id" required>
                            @foreach(\App\Models\Currency::where('is_active', true)->get() as $currency)
                                <option value="{{ $currency->id }}" @if($project->currency_id == $currency->id) selected @endif>{{ $currency->code }} ({{ $currency->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-status-{{ $project->id }}" class="form-label">Статус</label>
                        <select class="form-select" id="edit-project-status-{{ $project->id }}" name="status" required>
                            <option value="active" @if($project->status=='active') selected @endif>Активный</option>
                            <option value="inactive" @if($project->status=='inactive') selected @endif>Неактивный</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-phone-{{ $project->id }}" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="edit-project-phone-{{ $project->id }}" name="phone" value="{{ $project->phone }}">
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-website-{{ $project->id }}" class="form-label">Сайт</label>
                        <input type="text" class="form-control" id="edit-project-website-{{ $project->id }}" name="website" value="{{ $project->website }}">
                    </div>
                    <div class="mb-3">
                        <label for="edit-project-address-{{ $project->id }}" class="form-label">Адрес</label>
                        <input type="text" class="form-control" id="edit-project-address-{{ $project->id }}" name="address" value="{{ $project->address }}">
                    </div>
                    
                    <!-- Координаты карты -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-project-map-latitude-{{ $project->id }}" class="form-label">Широта карты</label>
                                <input type="text" class="form-control" id="edit-project-map-latitude-{{ $project->id }}" name="map_latitude" value="{{ $project->map_latitude }}" placeholder="55.7558">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-project-map-longitude-{{ $project->id }}" class="form-label">Долгота карты</label>
                                <input type="text" class="form-control" id="edit-project-map-longitude-{{ $project->id }}" name="map_longitude" value="{{ $project->map_longitude }}" placeholder="37.6176">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-project-map-zoom-{{ $project->id }}" class="form-label">Масштаб карты</label>
                                <input type="number" class="form-control" id="edit-project-map-zoom-{{ $project->id }}" name="map_zoom" value="{{ $project->map_zoom ?? 15 }}" min="1" max="20">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-project-about-{{ $project->id }}" class="form-label">О нас</label>
                        <textarea class="form-control" id="edit-project-about-{{ $project->id }}" name="about" rows="4" placeholder="Краткое описание о салоне/компании...">{{ $project->about }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-project-social-links-{{ $project->id }}" class="form-label">Соцсети (через запятую)</label>
                        <input type="text" class="form-control" id="edit-project-social-links-{{ $project->id }}" name="social_links" value="{{ is_array($project->social_links) ? implode(', ', $project->social_links) : '' }}" placeholder="vk.com/..., facebook.com/...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Модальное окно подтверждения удаления проекта -->
<div class="modal fade" id="confirmDeleteProjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтвердите удаление</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этот проект?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteProjectBtn">Удалить</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var createProjectModal = document.getElementById('createProjectModal');
    if (createProjectModal) {
        createProjectModal.addEventListener('show.bs.modal', function () {
            var now = new Date();
            // Формат YYYY-MM-DDTHH:MM для input type="datetime-local"
            var pad = n => n < 10 ? '0' + n : n;
            var formatted = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
            document.getElementById('project-registered-at').value = formatted;
        });
    }
});

let projectIdToDelete = null;
let deleteBtnRef = null;

// Открытие модального окна подтверждения удаления

document.querySelectorAll('.btn-delete-project').forEach(function(btn) {
    btn.addEventListener('click', function() {
        projectIdToDelete = this.getAttribute('data-id');
        deleteBtnRef = this;
        var modal = new bootstrap.Modal(document.getElementById('confirmDeleteProjectModal'));
        modal.show();
    });
});

// Подтверждение удаления

document.getElementById('confirmDeleteProjectBtn').addEventListener('click', function() {
    if (!projectIdToDelete) return;
    
    fetch('/panel/projects/' + projectIdToDelete, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    })
    .then(response => {
        if (response.ok) {
            // Удалить строку из таблицы
            if (deleteBtnRef) deleteBtnRef.closest('tr').remove();
            bootstrap.Modal.getInstance(document.getElementById('confirmDeleteProjectModal')).hide();
        } else {
            alert('Ошибка при удалении проекта');
        }
    })
    .catch(() => alert('Ошибка при удалении проекта'));
});

// Динамический поиск по проектам
let searchInput = document.getElementById('project-search-input');
let searchTimeout = null;
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            let q = searchInput.value;
            let status = document.querySelector('select[name="status"]').value;
            let url = new URL(window.location.href);
            url.searchParams.set('q', q);
            url.searchParams.set('status', status);
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    let table = document.querySelector('table.table');
                    let tbody = table.querySelector('tbody');
                    tbody.outerHTML = html;
                    // Повторно навесить обработчики на кнопки удаления
                    document.querySelectorAll('.btn-delete-project').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            projectIdToDelete = this.getAttribute('data-id');
                            deleteBtnRef = this;
                            var modal = new bootstrap.Modal(document.getElementById('confirmDeleteProjectModal'));
                            modal.show();
                        });
                    });
                });
        }, 300);
    });
}
</script>
@endpush 