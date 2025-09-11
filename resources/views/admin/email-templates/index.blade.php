@extends('admin.layouts.app')

@section('title', 'Email шаблоны - Админ')
@section('page-title', 'Email шаблоны')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Email шаблоны</h5>
        <div>
            <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#placeholdersModal">
                <i class="fas fa-question-circle me-2"></i>Плейсхолдеры
            </button>
            <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#testTemplateModal">
                <i class="fas fa-paper-plane me-2"></i>Тест
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                <i class="fas fa-plus me-2"></i>Добавить шаблон
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Тема</th>
                        <th>Тип</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                    <tr>
                        <td>{{ $template['id'] }}</td>
                        <td>{{ $template['name'] }}</td>
                        <td>{{ $template['subject'] }}</td>
                        <td>
                            @switch($template['type'])
                                @case('registration')
                                    <span class="badge bg-primary">Регистрация</span>
                                    @break
                                @case('appointment')
                                    <span class="badge bg-info">Записи</span>
                                    @break
                                @case('reminder')
                                    <span class="badge bg-warning">Напоминания</span>
                                    @break
                                @case('notification')
                                    <span class="badge bg-secondary">Уведомления</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $template['type'] }}</span>
                            @endswitch
                        </td>
                        <td>
                            <span class="badge bg-{{ $template['status'] == 'active' ? 'success' : 'danger' }}">
                                {{ $template['status'] == 'active' ? 'Активен' : 'Неактивен' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="editTemplate({{ $template['id'] }})" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="viewTemplate({{ $template['id'] }})" title="Просмотр">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteTemplate({{ $template['id'] }})" title="Удалить">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Email шаблоны не найдены
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Модальное окно создания шаблона -->
<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить email шаблон</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="template_name" class="form-label">Название шаблона</label>
                        <input type="text" class="form-control" id="template_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="template_subject" class="form-label">Тема письма</label>
                        <input type="text" class="form-control" id="template_subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="template_type" class="form-label">Тип шаблона</label>
                        <select class="form-select" id="template_type" required>
                            <option value="">Выберите тип</option>
                            <option value="registration">Регистрация</option>
                            <option value="appointment">Записи</option>
                            <option value="reminder">Напоминания</option>
                            <option value="notification">Уведомления</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="template_content" class="form-label">Содержимое шаблона</label>
                        <textarea class="form-control" id="template_content" rows="10" placeholder="Введите содержимое email шаблона..."></textarea>
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

<!-- Модальное окно редактирования шаблона -->
<div class="modal fade" id="editTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать email шаблон</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTemplateForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_template_id">
                    <div class="mb-3">
                        <label for="edit_template_name" class="form-label">Название шаблона</label>
                        <input type="text" class="form-control" id="edit_template_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_subject" class="form-label">Тема письма</label>
                        <input type="text" class="form-control" id="edit_template_subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_type" class="form-label">Тип шаблона</label>
                        <select class="form-select" id="edit_template_type" required>
                            <option value="">Выберите тип</option>
                            <option value="registration">Регистрация</option>
                            <option value="appointment">Записи</option>
                            <option value="reminder">Напоминания</option>
                            <option value="notification">Уведомления</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_status" class="form-label">Статус</label>
                        <select class="form-select" id="edit_template_status" required>
                            <option value="active">Активен</option>
                            <option value="inactive">Неактивен</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_content" class="form-label">Содержимое шаблона</label>
                        <textarea class="form-control" id="edit_template_content" rows="10" placeholder="Введите содержимое email шаблона..."></textarea>
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

<!-- Модальное окно просмотра шаблона -->
<div class="modal fade" id="viewTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Просмотр email шаблона</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Название:</strong>
                        <p id="view_template_name"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Тема:</strong>
                        <p id="view_template_subject"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Тип:</strong>
                        <p id="view_template_type"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Статус:</strong>
                        <p id="view_template_status"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Содержимое:</strong>
                    <div id="view_template_content" class="border p-3 mt-2 bg-light" style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно с плейсхолдерами -->
<div class="modal fade" id="placeholdersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Доступные плейсхолдеры</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Используйте эти плейсхолдеры в содержимом и теме письма. Они будут автоматически заменены на реальные данные при отправке.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Основные данные</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><code>@{{date}}</code></td>
                                <td>Дата записи (15.09.2025)</td>
                            </tr>
                            <tr>
                                <td><code>@{{time}}</code></td>
                                <td>Время записи (14:30)</td>
                            </tr>
                            <tr>
                                <td><code>@{{client_name}}</code></td>
                                <td>Имя клиента</td>
                            </tr>
                            <tr>
                                <td><code>@{{service_name}}</code></td>
                                <td>Название услуги</td>
                            </tr>
                            <tr>
                                <td><code>@{{master_name}}</code></td>
                                <td>Имя мастера</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Дополнительные данные</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><code>@{{project_name}}</code></td>
                                <td>Название проекта/салона</td>
                            </tr>
                            <tr>
                                <td><code>@{{price}}</code></td>
                                <td>Стоимость услуги</td>
                            </tr>
                            <tr>
                                <td><code>@{{notes}}</code></td>
                                <td>Примечания к записи</td>
                            </tr>
                            <tr>
                                <td><code>@{{phone}}</code></td>
                                <td>Телефон салона</td>
                            </tr>
                            <tr>
                                <td><code>@{{address}}</code></td>
                                <td>Адрес салона</td>
                            </tr>
                            <tr>
                                <td><code>@{{working_hours}}</code></td>
                                <td>Время работы салона</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="text-success">Пример использования:</h6>
                    <div class="bg-light p-3 rounded">
                        <strong>Тема:</strong> Напоминание о записи на @{{date}}<br>
                        <strong>Содержимое:</strong><br>
                        Здравствуйте, @{{client_name}}!<br>
                        Напоминаем о вашей записи на @{{date}} в @{{time}}.<br>
                        Услуга: @{{service_name}}<br>
                        Мастер: @{{master_name}}<br>
                        Стоимость: @{{price}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно тестирования -->
<div class="modal fade" id="testTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Тестирование email шаблона</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="testTemplateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="test_template_type" class="form-label">Тип шаблона</label>
                        <select class="form-select" id="test_template_type" required>
                            <option value="">Выберите тип</option>
                            <option value="registration">Регистрация</option>
                            <option value="appointment">Записи</option>
                            <option value="reminder">Напоминания</option>
                            <option value="notification">Уведомления</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="test_email" class="form-label">Email для тестирования</label>
                        <input type="email" class="form-control" id="test_email" required placeholder="test@example.com">
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Будет отправлено тестовое письмо с примером данных для выбранного типа шаблона.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane me-2"></i>Отправить тест
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Создание шаблона
document.querySelector('#createTemplateModal form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('name', document.getElementById('template_name').value);
    formData.append('subject', document.getElementById('template_subject').value);
    formData.append('type', document.getElementById('template_type').value);
    formData.append('content', document.getElementById('template_content').value);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("admin.email-templates.store") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
});

// Редактирование шаблона
function editTemplate(id) {
    fetch(`/panel/email-templates/${id}/edit`)
    .then(response => response.json())
    .then(data => {
        if (data.template) {
            document.getElementById('edit_template_id').value = data.template.id;
            document.getElementById('edit_template_name').value = data.template.name;
            document.getElementById('edit_template_subject').value = data.template.subject;
            document.getElementById('edit_template_type').value = data.template.type;
            document.getElementById('edit_template_status').value = data.template.status;
            document.getElementById('edit_template_content').value = data.template.content;
            
            new bootstrap.Modal(document.getElementById('editTemplateModal')).show();
        }
    });
}

// Просмотр шаблона
function viewTemplate(id) {
    fetch(`{{ url('panel/email-templates') }}/${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.template) {
            document.getElementById('view_template_name').textContent = data.template.name;
            document.getElementById('view_template_subject').textContent = data.template.subject;
            document.getElementById('view_template_type').textContent = data.template.type;
            document.getElementById('view_template_status').textContent = data.template.status === 'active' ? 'Активен' : 'Неактивен';
            document.getElementById('view_template_content').textContent = data.template.content;
            
            new bootstrap.Modal(document.getElementById('viewTemplateModal')).show();
        }
    });
}

// Удаление шаблона
function deleteTemplate(id) {
    if (confirm('Вы уверены, что хотите удалить этот шаблон?')) {
        fetch(`{{ url('panel/email-templates') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Сохранение редактирования
document.getElementById('editTemplateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('edit_template_id').value;
    const formData = new FormData();
    formData.append('name', document.getElementById('edit_template_name').value);
    formData.append('subject', document.getElementById('edit_template_subject').value);
    formData.append('type', document.getElementById('edit_template_type').value);
    formData.append('status', document.getElementById('edit_template_status').value);
    formData.append('content', document.getElementById('edit_template_content').value);
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    fetch(`{{ url('panel/email-templates') }}/${id}`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        }
    });
});

// Тестирование шаблона
document.getElementById('testTemplateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('type', document.getElementById('test_template_type').value);
    formData.append('email', document.getElementById('test_email').value);
    formData.append('_token', '{{ csrf_token() }}');
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Отправка...';
    submitBtn.disabled = true;
    
    fetch('{{ route("admin.email-templates.test") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            bootstrap.Modal.getInstance(document.getElementById('testTemplateModal')).hide();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Ошибка: ' + error.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
@endsection 