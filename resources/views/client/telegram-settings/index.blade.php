@extends('client.layouts.app')

@section('title', __('messages.telegram_settings'))

@section('content')
<div class="dashboard-container">
    <div class="settings-header telegram-settings-header">
        <h1>{{ __('messages.telegram_settings') }}</h1>
        <div id="notification"></div>
    </div>

    <div class="settings-content telegram-settings-content">
        <div class="settings-pane telegram-settings-pane">
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
                
                <div class="form-row form-row--2col telegram-form-row">
                    <div class="form-col telegram-form-col">
                        <div class="form-group mb-3 telegram-form-group">
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

                    <div class="form-col telegram-form-col">
                        <div class="form-group mb-3 telegram-form-group">
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

                <div class="form-row form-row--2col telegram-form-row">
                    <div class="form-col telegram-form-col">
                        <div class="form-group mb-3 telegram-form-group">
                            <div class="telegram-switch-container">
                                <label class="telegram-switch-label" for="telegram_notifications_enabled">
                                    {{ __('messages.telegram_notifications_enabled') }}
                                </label>
                                <!-- Десктопный переключатель Bootstrap -->
                                <div class="custom-control custom-switch d-none d-md-block">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="telegram_notifications_enabled_desktop" 
                                           name="telegram_notifications_enabled" 
                                           value="1"
                                           {{ old('telegram_notifications_enabled', $project->telegram_notifications_enabled) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="telegram_notifications_enabled_desktop"></label>
                                </div>
                                <!-- Мобильный кастомный переключатель -->
                                <label class="telegram-custom-switch d-md-none">
                                    <input type="checkbox" 
                                           id="telegram_notifications_enabled_mobile" 
                                           name="telegram_notifications_enabled" 
                                           value="1"
                                           {{ old('telegram_notifications_enabled', $project->telegram_notifications_enabled) ? 'checked' : '' }}>
                                    <span class="telegram-slider"></span>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('messages.telegram_notifications_hint') }}
                            </small>
                        </div>
                    </div>
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

            <div id="testResult" class="mt-3 telegram-test-result" style="display: none;"></div>
        </div>
    </div>
</div>

<!-- Модальное окно с инструкциями -->
<div id="instructionsModal" class="confirmation-modal">
    <div class="confirmation-content" style="max-width: 700px; max-height: 80vh;">
        <h3>{{ __('messages.telegram_instructions') }}</h3>
        <div id="instructionsContent" style="text-align: left; margin: 20px 0; max-height: 60vh; overflow-y: auto; padding-right: 10px;">
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
    margin: 5% auto;
    padding: 30px;
    border: 1px solid #888;
    width: 90%;
    max-width: 400px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

/* Стили для инструкций */
.telegram-instructions-list {
    text-align: left;
}

.instruction-item {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.instruction-item strong {
    color: #007bff;
    font-weight: 600;
}

.instruction-item br {
    margin-bottom: 8px;
}

/* Стили для прокрутки */
#instructionsContent::-webkit-scrollbar {
    width: 8px;
}

#instructionsContent::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#instructionsContent::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

#instructionsContent::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
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
            let html = '<div class="telegram-instructions-list">';
            Object.values(data.instructions).forEach((instruction, index) => {
                html += `<div class="instruction-item" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">${instruction}</div>`;
            });
            html += '</div>';
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

// Синхронизация переключателей для десктопа и мобильных
document.addEventListener('DOMContentLoaded', function() {
    const desktopSwitch = document.getElementById('telegram_notifications_enabled_desktop');
    const mobileSwitch = document.getElementById('telegram_notifications_enabled_mobile');
    
    if (desktopSwitch && mobileSwitch) {
        // Синхронизация с десктопа на мобильный
        desktopSwitch.addEventListener('change', function() {
            mobileSwitch.checked = this.checked;
        });
        
        // Синхронизация с мобильного на десктоп
        mobileSwitch.addEventListener('change', function() {
            desktopSwitch.checked = this.checked;
        });
    }
    
    // Обработчик для закрытия модального окна при клике вне его
    const modal = document.getElementById('instructionsModal');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeInstructionsModal();
            }
        });
        
        // Добавляем обработчик для закрытия по Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modal.style.display === 'block') {
                closeInstructionsModal();
            }
        });
    }
});
</script>
@endpush 