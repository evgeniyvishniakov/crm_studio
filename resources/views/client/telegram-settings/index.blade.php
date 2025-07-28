@extends('client.layouts.app')

@section('title', __('messages.telegram_settings'))

@section('content')
<div class="dashboard-container">
    <div class="settings-header">
        <h1>{{ __('messages.telegram_settings') }}</h1>
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

            <form id="telegramSettingsForm" method="POST" action="{{ route('client.telegram-settings.update') }}">
                @csrf
                @method('PUT')

                <h5>{{ __('messages.telegram_settings') }}</h5>
                
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="telegram_bot_token">{{ __('messages.telegram_bot_token') }}</label>
                            <input type="text" 
                                   class="form-control @error('telegram_bot_token') is-invalid @enderror" 
                                   id="telegram_bot_token" 
                                   name="telegram_bot_token" 
                                   value="{{ old('telegram_bot_token', $project->telegram_bot_token) }}"
                                   placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz">
                            @error('telegram_bot_token')
                                <div class="invalid-feedback">{{ $error }}</div>
                            @enderror
                                                         <small class="form-text text-muted">
                                 {{ __('messages.telegram_bot_token_hint') }}
                             </small>
                        </div>
                    </div>

                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label for="telegram_chat_id">{{ __('messages.telegram_chat_id') }}</label>
                            <input type="text" 
                                   class="form-control @error('telegram_chat_id') is-invalid @enderror" 
                                   id="telegram_chat_id" 
                                   name="telegram_chat_id" 
                                   value="{{ old('telegram_chat_id', $project->telegram_chat_id) }}"
                                   placeholder="-1001234567890 или 123456789">
                            @error('telegram_chat_id')
                                <div class="invalid-feedback">{{ $error }}</div>
                            @enderror
                                                         <small class="form-text text-muted">
                                 {{ __('messages.telegram_chat_id_hint') }}
                             </small>
                        </div>
                    </div>
                </div>

                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="telegram_notifications_enabled" 
                                       name="telegram_notifications_enabled" 
                                       value="1"
                                       {{ old('telegram_notifications_enabled', $project->telegram_notifications_enabled) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="telegram_notifications_enabled">
                                    {{ __('messages.telegram_notifications_enabled') }}
                                </label>
                            </div>
                                                         <small class="form-text text-muted">
                                 {{ __('messages.telegram_notifications_hint') }}
                             </small>
                        </div>
                    </div>
                    <div class="form-col"></div>
                </div>

                <div class="form-actions d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> {{ __('messages.save') }}
                    </button>
                    <div>
                        <button type="button" class="btn btn-info" onclick="testConnection()">
                            <i class="fa fa-exchange-alt"></i> {{ __('messages.test_connection') }}
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="showInstructions()">
                            <i class="fa fa-question-circle"></i> {{ __('messages.instructions') }}
                        </button>
                    </div>
                </div>
            </form>

            <div id="testResult" class="mt-3" style="display: none;"></div>
        </div>
    </div>
</div>

<!-- Модальное окно с инструкциями -->
<div id="instructionsModal" class="confirmation-modal">
    <div class="confirmation-content" style="max-width: 600px;">
        <h3>{{ __('messages.telegram_instructions') }}</h3>
        <div id="instructionsContent" style="text-align: left; margin: 20px 0;">
            <!-- Инструкции будут загружены через AJAX -->
        </div>
        <div class="confirmation-buttons d-flex justify-content-end">
            <button onclick="closeInstructionsModal()" class="cancel-btn">{{ __('messages.close') }}</button>
        </div>
    </div>
</div>
@endsection

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

@push('scripts')
<script>
// Используем глобальную функцию showNotification из notifications.js

// AJAX сохранение настроек Telegram
document.getElementById('telegramSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Проверяем обязательные поля
    const botToken = document.getElementById('telegram_bot_token').value.trim();
    const chatId = document.getElementById('telegram_chat_id').value.trim();
    
    if (!botToken || !chatId) {
        window.showNotification('error', '{{ __('messages.telegram_fields_required') }}');
        return;
    }

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
    submitBtn.disabled = true;

    fetch("{{ route('client.telegram-settings.update') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message || '{{ __('messages.telegram_settings_saved') }}');
        } else {
            window.showNotification('error', data.message || '{{ __('messages.error_saving_settings') }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.errors) {
            // Показываем ошибки валидации
            Object.entries(error.errors).forEach(([field, messages]) => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const errorDiv = input.parentNode.querySelector('.invalid-feedback') || 
                                   document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = Array.isArray(messages) ? messages[0] : messages;
                    if (!input.parentNode.querySelector('.invalid-feedback')) {
                        input.parentNode.appendChild(errorDiv);
                    }
                }
            });
            window.showNotification('error', '{{ __('messages.validation_errors') }}');
        } else {
            window.showNotification('error', error.message || '{{ __('messages.error_saving_settings') }}');
        }
    })
    .finally(() => {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
});

function testConnection() {
    // Проверяем обязательные поля
    const botToken = document.getElementById('telegram_bot_token').value.trim();
    const chatId = document.getElementById('telegram_chat_id').value.trim();
    
    if (!botToken || !chatId) {
        window.showNotification('error', '{{ __('messages.telegram_fields_required_test') }}');
        return;
    }

    const resultDiv = document.getElementById('testResult');
    resultDiv.innerHTML = '<div class="alert alert-info">Тестирование подключения...</div>';
    resultDiv.style.display = 'block';

    fetch('{{ route("client.telegram-settings.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        } else {
            resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<div class="alert alert-danger">{{ __('messages.telegram_test_error') }}</div>';
        console.error('Error:', error);
    });
}

function showInstructions() {
    const modal = document.getElementById('instructionsModal');
    const content = document.getElementById('instructionsContent');
    
    content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    modal.style.display = 'block';

    fetch('{{ route("client.telegram-settings.instructions") }}')
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

function closeInstructionsModal() {
    const modal = document.getElementById('instructionsModal');
    modal.style.display = 'none';
}

// Добавляем обработчик для закрытия модального окна при клике вне его
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('instructionsModal');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeInstructionsModal();
            }
        });
    }
});
</script>
@endpush 