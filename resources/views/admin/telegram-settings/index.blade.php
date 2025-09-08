@extends('admin.layouts.app')

@section('title', 'Настройки Telegram')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paper-plane"></i>
                        Настройки Telegram уведомлений
                    </h3>
                </div>
                
                <form id="telegram-settings-form" method="POST" action="{{ route('admin.telegram-settings.update') }}">
                    @csrf
                    
                    <div class="card-body">
                        <!-- Основные настройки -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telegram_bot_token">Токен бота</label>
                                    <input type="text" 
                                           class="form-control @error('telegram_bot_token') is-invalid @enderror" 
                                           id="telegram_bot_token" 
                                           name="telegram_bot_token" 
                                           value="{{ old('telegram_bot_token', $settings->telegram_bot_token) }}"
                                           placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz">
                                    @error('telegram_bot_token')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Получите токен у @BotFather в Telegram
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telegram_chat_id">ID чата</label>
                                    <input type="text" 
                                           class="form-control @error('telegram_chat_id') is-invalid @enderror" 
                                           id="telegram_chat_id" 
                                           name="telegram_chat_id" 
                                           value="{{ old('telegram_chat_id', $settings->telegram_chat_id) }}"
                                           placeholder="123456789">
                                    @error('telegram_chat_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        ID чата, куда будут отправляться уведомления
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Включение уведомлений -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="telegram_notifications_enabled" 
                                       name="telegram_notifications_enabled" 
                                       value="1"
                                       {{ old('telegram_notifications_enabled', $settings->telegram_notifications_enabled) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="telegram_notifications_enabled">
                                    Включить Telegram уведомления
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Типы уведомлений -->
                        <h5>Типы уведомлений</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="notify_new_projects" 
                                               name="notify_new_projects" 
                                               value="1"
                                               {{ old('notify_new_projects', $settings->notify_new_projects) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="notify_new_projects">
                                            Новые проекты
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="notify_new_subscriptions" 
                                               name="notify_new_subscriptions" 
                                               value="1"
                                               {{ old('notify_new_subscriptions', $settings->notify_new_subscriptions) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="notify_new_subscriptions">
                                            Новые подписки
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="notify_new_messages" 
                                               name="notify_new_messages" 
                                               value="1"
                                               {{ old('notify_new_messages', $settings->notify_new_messages) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="notify_new_messages">
                                            Новые сообщения в поддержку
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="notify_subscription_expires" 
                                               name="notify_subscription_expires" 
                                               value="1"
                                               {{ old('notify_subscription_expires', $settings->notify_subscription_expires) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="notify_subscription_expires">
                                            Истечение подписок
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="notify_payment_issues" 
                                               name="notify_payment_issues" 
                                               value="1"
                                               {{ old('notify_payment_issues', $settings->notify_payment_issues) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="notify_payment_issues">
                                            Проблемы с платежами
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" id="save-settings-btn">
                            <i class="fas fa-save"></i>
                            Сохранить настройки
                        </button>
                        
                        <button type="button" class="btn btn-info" id="test-connection">
                            <i class="fas fa-paper-plane"></i>
                            Тестировать подключение
                        </button>
                        
                        <button type="button" class="btn btn-secondary" id="show-instructions">
                            <i class="fas fa-question-circle"></i>
                            Инструкции по настройке
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно с инструкциями -->
<div class="modal fade" id="instructionsModal" tabindex="-1" role="dialog" aria-labelledby="instructionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instructionsModalLabel">Инструкции по настройке Telegram</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ol>
                    <li>Найдите @BotFather в Telegram</li>
                    <li>Отправьте команду /newbot</li>
                    <li>Следуйте инструкциям для создания бота</li>
                    <li>Сохраните полученный токен</li>
                    <li>Для получения ID чата:</li>
                    <li>&nbsp;&nbsp;&nbsp;- Отправьте боту любое сообщение</li>
                    <li>&nbsp;&nbsp;&nbsp;- Перейдите по ссылке: https://api.telegram.org/bot&lt;TOKEN&gt;/getUpdates</li>
                    <li>&nbsp;&nbsp;&nbsp;- Найдите в ответе "chat":{"id":123456789}</li>
                    <li>Вставьте токен и ID чата в настройки</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Сохранение настроек
    const saveBtn = document.getElementById('save-settings-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = document.getElementById('telegram-settings-form');
            const formData = new FormData(form);
            
            console.log('Form submitted to:', form.action);
            
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success response:', data);
                if (data.success) {
                    showNotification(data.message, 'success');
                } else {
                    showNotification('Ошибка при сохранении настроек', 'error');
                }
            })
            .catch(error => {
                console.log('Error response:', error);
                showNotification('Ошибка при сохранении настроек', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    }
    
    // Тестирование подключения
    const testBtn = document.getElementById('test-connection');
    if (testBtn) {
        testBtn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Тестирование...';
            
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("admin.telegram-settings.test") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Ошибка при тестировании подключения', 'error');
                console.log('Test connection error:', error);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    }
    
    // Показать инструкции
    const instructionsBtn = document.getElementById('show-instructions');
    if (instructionsBtn) {
        instructionsBtn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('instructionsModal'));
            modal.show();
        });
    }
});

// Функция для показа уведомлений (используем встроенную из admin/main.js)
function showNotification(message, type = 'info') {
    if (window.AdminPanel && window.AdminPanel.showNotification) {
        window.AdminPanel.showNotification(message, type);
    } else {
        // Fallback если AdminPanel не доступен
        alert(message);
    }
}
</script>
@endpush
