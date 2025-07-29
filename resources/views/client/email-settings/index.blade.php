@extends('client.layouts.app')

@section('title', __('messages.email_settings'))

@section('content')
<div class="dashboard-container">
    <div class="settings-header">
        <h1>{{ __('messages.email_settings') }}</h1>
        <div id="notification"></div>
    </div>

    <div class="settings-content">
        <div class="settings-pane">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="emailSettingsForm" method="POST" action="{{ route('client.email-settings.update') }}">
                @csrf
                @method('PUT')
                
                <h5>{{ __('messages.email_settings') }}</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email_host">{{ __('messages.email_host') }}</label>
                            <input type="text" class="form-control" id="email_host" name="email_host" 
                                   value="{{ old('email_host', $project->email_host) }}" 
                                   placeholder="{{ __('messages.email_host_placeholder') }}"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_host_hint') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email_port">{{ __('messages.email_port') }}</label>
                            <input type="number" class="form-control" id="email_port" name="email_port" 
                                   value="{{ old('email_port', $project->email_port) }}" 
                                   placeholder="587"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_port_hint') }}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email_username">{{ __('messages.email_username') }}</label>
                            <input type="email" class="form-control" id="email_username" name="email_username" 
                                   value="{{ old('email_username', $project->email_username) }}" 
                                   placeholder="your-email@gmail.com"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_username_hint') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email_password">{{ __('messages.email_password') }}</label>
                            <input type="password" class="form-control" id="email_password" name="email_password" 
                                   value="{{ old('email_password', $project->email_password) }}" 
                                   placeholder="{{ __('messages.email_password_placeholder') }}"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_password_hint') }}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email_encryption">{{ __('messages.email_encryption') }}</label>
                            <select class="form-control" id="email_encryption" name="email_encryption" required>
                                <option value="tls" {{ old('email_encryption', $project->email_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('email_encryption', $project->email_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="none" {{ old('email_encryption', $project->email_encryption) == 'none' ? 'selected' : '' }}>{{ __('messages.none') }}</option>
                            </select>
                            <small class="form-text text-muted">{{ __('messages.email_encryption_hint') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email_from_name">{{ __('messages.email_from_name') }}</label>
                            <input type="text" class="form-control" id="email_from_name" name="email_from_name" 
                                   value="{{ old('email_from_name', $project->email_from_name) }}" 
                                   placeholder="{{ __('messages.email_from_name_placeholder') }}"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_from_name_hint') }}</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" 
                               id="email_notifications_enabled" 
                               name="email_notifications_enabled" 
                               {{ old('email_notifications_enabled', $project->email_notifications_enabled) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="email_notifications_enabled">
                            {{ __('messages.email_notifications_enabled') }}
                        </label>
                    </div>
                    <small class="form-text text-muted">{{ __('messages.email_notifications_hint') }}</small>
                </div>

                <div class="form-actions d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> {{ __('messages.save') }}
                    </button>
                    <div>
                        <button type="button" class="btn btn-info" onclick="testEmailConnection()">
                            <i class="fa fa-exchange-alt"></i> {{ __('messages.test_connection') }}
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="showEmailInstructions()">
                            <i class="fa fa-question-circle"></i> {{ __('messages.instructions') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно с инструкциями -->
<div id="emailInstructionsModal" class="confirmation-modal">
    <div class="confirmation-content" style="max-width: 600px;">
        <h3>{{ __('messages.email_instructions') }}</h3>
        <div id="emailInstructionsContent" style="text-align: left; margin: 20px 0;">
            <!-- Инструкции будут загружены через AJAX -->
        </div>
        <div class="confirmation-buttons d-flex justify-content-end">
            <button onclick="closeEmailInstructionsModal()" class="cancel-btn">{{ __('messages.close') }}</button>
        </div>
    </div>
</div>

<style>
/* Стили для модального окна подтверждения */
.confirmation-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.confirmation-modal .confirmation-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    border-radius: 8px;
    text-align: center;
}

.confirmation-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.confirm-btn {
    background-color: #dc3545;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.confirm-btn:hover {
    background-color: #c82333;
}

.cancel-btn {
    background-color: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.cancel-btn:hover {
    background-color: #5a6268;
}

/* Стили для уведомлений уже подключены в notifications.css */

/* Стили для новой структуры страницы */


.settings-header h1 {
    color: #333;
    font-weight: 700;
    margin-bottom: 10px;
}

.form-actions {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 30px;
    border: 1px solid #e9ecef;
}

.form-actions .btn {
    margin: 0 5px;
}

/* Стили для кнопок в стиле системы (только для этой страницы) */
.form-actions .btn-info {
    background: linear-gradient(135deg, #17a2b8, #20c997) !important;
    border-color: #17a2b8 !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3) !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.5rem !important;
}

.form-actions .btn-info:active, .form-actions .btn-info:focus, .form-actions .btn-info:hover {
    background: linear-gradient(135deg, #138496, #17a2b8) !important;
    border-color: #138496 !important;
    color: #fff !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.5rem !important;
}

.form-actions .btn-secondary {
    background: linear-gradient(135deg, #6c757d, #868e96) !important;
    border-color: #6c757d !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3) !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.5rem !important;
}

.form-actions .btn-secondary:active, .form-actions .btn-secondary:focus, .form-actions .btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268, #6c757d) !important;
    border-color: #5a6268 !important;
    color: #fff !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.5rem !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик отправки формы
    document.getElementById('emailSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Показываем индикатор загрузки
        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.saving") }}...';
        submitButton.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.showNotification('success', data.message);
            } else {
                if (data.errors) {
                    // Показываем ошибки валидации
                    let errorMessage = '{{ __("messages.validation_errors") }}:\n';
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += data.errors[field].join('\n') + '\n';
                    });
                    window.showNotification('error', errorMessage);
                } else {
                    window.showNotification('error', data.message || '{{ __("messages.error_saving_settings") }}');
                }
            }
        })
        .catch(error => {
            window.showNotification('error', '{{ __("messages.error_saving_settings") }}: ' + error.message);
        })
        .finally(() => {
            // Восстанавливаем кнопку
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });
});

function testEmailConnection() {
    // Проверяем заполнение обязательных полей
    const host = document.getElementById('email_host').value.trim();
    const port = document.getElementById('email_port').value.trim();
    const username = document.getElementById('email_username').value.trim();
    const password = document.getElementById('email_password').value.trim();
    const encryption = document.getElementById('email_encryption').value;
    
    if (!host || !port || !username || !password) {
        window.showNotification('error', '{{ __("messages.email_fields_required_test") }}');
        return;
    }
    
    const formData = new FormData();
    formData.append('email_host', host);
    formData.append('email_port', port);
    formData.append('email_username', username);
    formData.append('email_password', password);
    formData.append('email_encryption', encryption);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("client.email-settings.test") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message);
        } else {
            window.showNotification('error', data.message);
        }
    })
    .catch(error => {
        window.showNotification('error', '{{ __("messages.error_saving_settings") }}: ' + error.message);
    });
}

function showEmailInstructions() {
    const modal = document.getElementById('emailInstructionsModal');
    const content = document.getElementById('emailInstructionsContent');
    
    content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    modal.style.display = 'block';

    fetch('{{ route("client.email-settings.instructions") }}')
        .then(response => response.json())
        .then(data => {
            let html = '<ol class="list-group list-group-flush">';
            Object.values(data.instructions).forEach(instruction => {
                html += `<li class="list-group-item">${instruction}</li>`;
            });
            html += '</ol>';
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">{{ __('messages.error_loading_instructions') }}</div>';
            console.error('Error:', error);
        });
}

function closeEmailInstructionsModal() {
    document.getElementById('emailInstructionsModal').style.display = 'none';
}

// Добавляем обработчик для закрытия модального окна при клике вне его
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('emailInstructionsModal');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeEmailInstructionsModal();
            }
        });
    }
});
</script>
@endsection 