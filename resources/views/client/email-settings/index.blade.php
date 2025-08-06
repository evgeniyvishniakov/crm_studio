@extends('client.layouts.app')

@section('title', __('messages.email_settings'))

@section('content')
<div class="dashboard-container">
    <div class="settings-header email-settings-header">
        <h1>{{ __('messages.email_settings') }}</h1>
        <div id="notification"></div>
    </div>

    <div class="settings-content email-settings-content">
        <div class="settings-pane email-settings-pane">
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
                
                <div class="row email-form-row">
                    <div class="col-md-6 email-form-col">
                        <div class="form-group email-form-group">
                            <label for="email_host">{{ __('messages.email_host') }}</label>
                            <input type="text" class="form-control" id="email_host" name="email_host" 
                                   value="{{ old('email_host', $emailSettings->email_host) }}" 
                                   placeholder="{{ __('messages.email_host_placeholder') }}"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_host_hint') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6 email-form-col">
                        <div class="form-group email-form-group">
                            <label for="email_port">{{ __('messages.email_port') }}</label>
                            <input type="number" class="form-control" id="email_port" name="email_port" 
                                   value="{{ old('email_port', $emailSettings->email_port) }}" 
                                   placeholder="587"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_port_hint') }}</small>
                        </div>
                    </div>
                </div>

                <div class="row email-form-row">
                    <div class="col-md-6 email-form-col">
                        <div class="form-group email-form-group">
                            <label for="email_username">{{ __('messages.email_username') }}</label>
                            <input type="email" class="form-control" id="email_username" name="email_username" 
                                   value="{{ old('email_username', $emailSettings->email_username) }}" 
                                   placeholder="your-email@gmail.com"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_username_hint') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6 email-form-col">
                        <div class="form-group email-form-group">
                            <label for="email_password">{{ __('messages.email_password') }}</label>
                            <input type="password" class="form-control" id="email_password" name="email_password" 
                                   value="{{ old('email_password', $emailSettings->email_password) }}" 
                                   placeholder="{{ __('messages.email_password_placeholder') }}"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_password_hint') }}</small>
                        </div>
                    </div>
                </div>

                <div class="row email-form-row">
                    <div class="col-md-6 email-form-col">
                        <div class="form-group email-form-group">
                            <label for="email_encryption">{{ __('messages.email_encryption') }}</label>
                            <select class="form-control" id="email_encryption" name="email_encryption" required>
                                <option value="tls" {{ old('email_encryption', $emailSettings->email_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('email_encryption', $emailSettings->email_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="none" {{ old('email_encryption', $emailSettings->email_encryption) == 'none' ? 'selected' : '' }}>{{ __('messages.none') }}</option>
                            </select>
                            <small class="form-text text-muted">{{ __('messages.email_encryption_hint') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6 email-form-col">
                        <div class="form-group email-form-group">
                            <label for="email_from_name">{{ __('messages.email_from_name') }}</label>
                            <input type="text" class="form-control" id="email_from_name" name="email_from_name" 
                                   value="{{ old('email_from_name', $emailSettings->email_from_name) }}" 
                                   placeholder="{{ __('messages.email_from_name_placeholder') }}"
                                   required>
                            <small class="form-text text-muted">{{ __('messages.email_from_name_hint') }}</small>
                        </div>
                    </div>
                </div>

                <div class="form-group email-form-group">
                    <div class="email-switch-container">
                        <label class="email-switch-label" for="email_notifications_enabled">
                            {{ __('messages.email_notifications_enabled') }}
                        </label>
                        <!-- Десктопный переключатель Bootstrap -->
                        <div class="custom-control custom-switch d-none d-md-block">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="email_notifications_enabled_desktop" 
                                   name="email_notifications_enabled" 
                                   {{ old('email_notifications_enabled', $emailSettings->email_notifications_enabled) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="email_notifications_enabled_desktop"></label>
                        </div>
                        <!-- Мобильный кастомный переключатель -->
                        <label class="email-custom-switch d-md-none">
                            <input type="checkbox" 
                                   id="email_notifications_enabled_mobile" 
                                   name="email_notifications_enabled" 
                                   {{ old('email_notifications_enabled', $emailSettings->email_notifications_enabled) ? 'checked' : '' }}>
                            <span class="email-slider"></span>
                        </label>
                    </div>
                    <small class="form-text text-muted">{{ __('messages.email_notifications_hint') }}</small>
                </div>

                <div class="form-actions d-flex justify-content-between align-items-center email-form-actions">
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
    <div class="confirmation-content email-instructions-modal">
        <h3>{{ __('messages.email_instructions') }}</h3>
        <div id="emailInstructionsContent" class="email-instructions-content">
            <!-- Инструкции будут загружены через AJAX -->
        </div>
        <div class="confirmation-buttons email-instructions-buttons d-flex justify-content-end">
            <button onclick="closeEmailInstructionsModal()" class="cancel-btn">{{ __('messages.close') }}</button>
        </div>
    </div>
</div>



@endsection 