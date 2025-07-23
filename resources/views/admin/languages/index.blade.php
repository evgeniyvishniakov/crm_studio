@extends('admin.layouts.app')

@section('title', 'Управление языками - Админ')
@section('page-title', 'Управление языками')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Список языков</h5>
                <div>
                    <button class="btn btn-success me-2" onclick="refreshClientLanguages()">
                        <i class="fas fa-sync-alt me-2"></i>Обновить в клиентах
                    </button>
                    <button class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Добавить язык
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Флаг</th>
                                <th>Код</th>
                                <th>Название</th>
                                <th>Родное название</th>
                                <th>Статус</th>
                                <th>По умолчанию</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="languagesTableBody">
                            @foreach($languages as $language)
                            <tr id="language-{{ $language->id }}">
                                <td>
                                    @if($language->flag)
                                        <img src="{{ $language->flag_url }}" alt="{{ $language->name }}" style="width: 24px; height: 16px; object-fit: cover;">
                                    @else
                                        <span class="badge bg-secondary">Нет флага</span>
                                    @endif
                                </td>
                                <td><strong>{{ $language->code }}</strong></td>
                                <td>{{ $language->name }}</td>
                                <td>{{ $language->native_name }}</td>
                                <td>
                                    <span class="badge bg-{{ $language->is_active ? 'success' : 'secondary' }}">
                                        {{ $language->is_active ? 'Активен' : 'Неактивен' }}
                                    </span>
                                </td>
                                <td>
                                    @if($language->is_default)
                                        <span class="badge bg-primary">По умолчанию</span>
                                    @else
                                        <button class="btn btn-sm btn-outline-primary" onclick="setDefault({{ $language->id }})">
                                            Установить
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editLanguage({{ $language->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if(!$language->is_default)
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteLanguage({{ $language->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-{{ $language->is_active ? 'warning' : 'success' }}" 
                                                onclick="toggleActive({{ $language->id }})">
                                            <i class="fas fa-{{ $language->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно создания/редактирования языка -->
<div class="modal fade" id="languageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Добавить язык</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="languageForm">
                    @csrf
                    <input type="hidden" id="languageId" name="language_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Код языка *</label>
                                <input type="text" class="form-control" id="code" name="code" maxlength="5" required>
                                <div class="form-text">Например: ru, en, ua</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Название *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="form-text">Например: Русский, English, Українська</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="native_name" class="form-label">Родное название *</label>
                                <input type="text" class="form-control" id="native_name" name="native_name" required>
                                <div class="form-text">Название на родном языке</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="flag" class="form-label">Флаг</label>
                                <input type="text" class="form-control" id="flag" name="flag" maxlength="255">
                                <div class="form-text">Например: ru.png, en.png, ua.png</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Активен
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                                    <label class="form-check-label" for="is_default">
                                        По умолчанию
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="saveLanguage()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<script>
let isEditMode = false;

document.addEventListener('DOMContentLoaded', function() {
    
    window.openCreateModal = function() {
        isEditMode = false;
        document.getElementById('modalTitle').textContent = 'Добавить язык';
        document.getElementById('languageForm').reset();
        document.getElementById('languageId').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('languageModal'));
        modal.show();
    };
    
    window.editLanguage = function(id) {
        isEditMode = true;
        document.getElementById('modalTitle').textContent = 'Редактировать язык';
        
        // Загружаем данные языка
        fetch(`/panel/languages/${id}/data`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const language = data.language;
                    document.getElementById('languageId').value = language.id;
                    document.getElementById('code').value = language.code;
                    document.getElementById('name').value = language.name;
                    document.getElementById('native_name').value = language.native_name;
                    document.getElementById('flag').value = language.flag;
                    document.getElementById('is_active').checked = language.is_active;
                    document.getElementById('is_default').checked = language.is_default;
                    
                    const modal = new bootstrap.Modal(document.getElementById('languageModal'));
                    modal.show();
                } else {
                    alert('Ошибка загрузки языка: ' + data.message);
                }
            })
            .catch(error => {
                alert('Ошибка при редактировании языка: ' + error.message);
            });
    };

    window.saveLanguage = function() {
        const form = document.getElementById('languageForm');
        const formData = new FormData(form);
        
        // Правильно обрабатываем чекбоксы
        const isActiveCheckbox = document.getElementById('is_active');
        const isDefaultCheckbox = document.getElementById('is_default');
        
        // Удаляем старые значения чекбоксов
        formData.delete('is_active');
        formData.delete('is_default');
        
        // Добавляем правильные значения
        formData.append('is_active', isActiveCheckbox.checked ? '1' : '0');
        formData.append('is_default', isDefaultCheckbox.checked ? '1' : '0');
        
        const url = isEditMode 
            ? `/panel/languages/${document.getElementById('languageId').value}`
            : '/panel/languages';
        
        const method = isEditMode ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        // Обрабатываем валидационные ошибки
                        let errorMessage = 'Ошибки валидации:\n';
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                errorMessage += `- ${data.errors[field].join(', ')}\n`;
                            });
                        }
                        throw new Error(errorMessage);
                    });
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('languageModal'));
                modal.hide();
                
                // Перезагружаем страницу
                window.location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Произошла ошибка при сохранении: ' + error.message);
        });
    };

    window.deleteLanguage = function(id) {
        // Создаем модальное окно подтверждения
        const confirmModal = document.createElement('div');
        confirmModal.className = 'modal fade';
        confirmModal.id = 'confirmDeleteModal';
        confirmModal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтверждение удаления</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Вы уверены, что хотите удалить этот язык?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete(${id})">Удалить</button>
                    </div>
                </div>
            </div>
        `;
        
        // Добавляем модальное окно на страницу
        document.body.appendChild(confirmModal);
        
        // Показываем модальное окно
        const modal = new bootstrap.Modal(confirmModal);
        modal.show();
        
        // Удаляем модальное окно после скрытия
        confirmModal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(confirmModal);
        });
    };
    
    window.confirmDelete = function(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Ошибка: CSRF токен не найден');
            return;
        }
        
        // Закрываем модальное окно подтверждения
        const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
        if (confirmModal) {
            confirmModal.hide();
        }
        
        fetch(`/panel/languages/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка при удалении языка: ' + error.message);
        });
    };

    window.setDefault = function(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Ошибка: CSRF токен не найден');
            return;
        }
        
        fetch(`/panel/languages/${id}/set-default`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка при установке языка по умолчанию: ' + error.message);
        });
    };

    window.toggleActive = function(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Ошибка: CSRF токен не найден');
            return;
        }
        
        fetch(`/panel/languages/${id}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка при переключении активности языка: ' + error.message);
        });
    };
    
    window.refreshClientLanguages = function() {
        // Создаем модальное окно подтверждения
        const confirmModal = document.createElement('div');
        confirmModal.className = 'modal fade';
        confirmModal.id = 'confirmRefreshModal';
        confirmModal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Обновление языков в клиентах</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Это обновит языки во всех открытых клиентских сессиях. Продолжить?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-success" onclick="confirmRefresh()">Обновить</button>
                    </div>
                </div>
            </div>
        `;
        
        // Добавляем модальное окно на страницу
        document.body.appendChild(confirmModal);
        
        // Показываем модальное окно
        const modal = new bootstrap.Modal(confirmModal);
        modal.show();
        
        // Удаляем модальное окно после скрытия
        confirmModal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(confirmModal);
        });
    };
    
    window.confirmRefresh = function() {
        // Закрываем модальное окно подтверждения
        const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmRefreshModal'));
        if (confirmModal) {
            confirmModal.hide();
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Ошибка: CSRF токен не найден');
            return;
        }
        
        // Отправляем AJAX запрос для очистки кэша
        fetch('/panel/languages/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            }
        }).then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Кэш языков очищен. Клиенты увидят изменения при обновлении страницы.');
            } else {
                alert('Ошибка при очистке кэша: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка при очистке кэша: ' + error.message);
        });
    };
});
</script>
@endsection 