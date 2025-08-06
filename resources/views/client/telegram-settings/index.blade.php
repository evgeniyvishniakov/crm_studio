@extends('client.layouts.app')

@section('title', __('messages.telegram_settings'))

@section('content')
<div class="dashboard-container">
    <div class="settings-header telegram-settings-header">
        <h1>{{ __('messages.telegram_settings') }}</h1>
    
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
                                   value="{{ old('telegram_bot_token', $telegramSettings->telegram_bot_token) }}"
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
                                   value="{{ old('telegram_chat_id', $telegramSettings->telegram_chat_id) }}"
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
                                           {{ old('telegram_notifications_enabled', $telegramSettings->telegram_notifications_enabled) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="telegram_notifications_enabled_desktop"></label>
                                </div>
                                <!-- Мобильный кастомный переключатель -->
                                <label class="telegram-custom-switch d-md-none">
                                    <input type="checkbox" 
                                           id="telegram_notifications_enabled_mobile" 
                                           name="telegram_notifications_enabled" 
                                           value="1"
                                           {{ old('telegram_notifications_enabled', $telegramSettings->telegram_notifications_enabled) ? 'checked' : '' }}>
                                    <span class="telegram-slider"></span>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('messages.telegram_notifications_hint') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-actions d-flex justify-content-between align-items-center telegram-form-actions">
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

 