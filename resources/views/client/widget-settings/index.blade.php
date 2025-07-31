@extends('client.layouts.app')

@section('title', __('messages.website_widget'))

@section('content')
<div class="dashboard-container">
    <!-- Блокировка для мобильной версии -->
    <div class="mobile-block d-md-none">
        <div class="mobile-block-content">
            <div class="mobile-block-icon">
                <i class="fa fa-lock"></i>
            </div>
            <h3>{{ __('messages.desktop_only') }}</h3>
            <p>{{ __('messages.widget_settings_desktop_only') }}</p>
            <div class="mobile-block-features">
                <div class="feature-item">
                    <i class="fa fa-desktop"></i>
                    <span>{{ __('messages.better_on_desktop') }}</span>
                </div>
                <div class="feature-item">
                    <i class="fa fa-code"></i>
                    <span>{{ __('messages.code_generation') }}</span>
                </div>
                <div class="feature-item">
                    <i class="fa fa-palette"></i>
                    <span>{{ __('messages.advanced_customization') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Основной контент (только для десктопа) -->
    <div class="desktop-content d-none d-md-block">
        <div class="settings-header">
            <h1>{{ __('messages.website_widget') }}</h1>
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

                <form id="widgetSettingsForm" method="POST" action="{{ route('client.widget-settings.update') }}">
                    @csrf
                    @method('PUT')

                    <h5>{{ __('messages.widget_settings') }}</h5>
                    
                    <div class="form-row form-row--2col">
                        <div class="form-col">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="widget_enabled" 
                                       name="widget_enabled" 
                                       value="1"
                                       {{ old('widget_enabled', $widgetSettings['enabled']) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="widget_enabled">
                                    <strong>{{ __('messages.enable_website_widget') }}</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">{{ __('messages.enable_widget_hint') }}</small>
                        </div>
                        <div class="form-col"></div>
                    </div>

                    <!-- Основные настройки кнопки -->
                    <div class="widget-section-header">
                        <i class="fa fa-cog"></i> {{ __('messages.basic_settings') }}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="widget_button_text">{{ __('messages.button_text') }}</label>
                                <input type="text" 
                                       class="form-control @error('widget_button_text') is-invalid @enderror" 
                                       id="widget_button_text" 
                                       name="widget_button_text" 
                                       value="{{ old('widget_button_text', $widgetSettings['button_text']) }}"
                                       placeholder="{{ __('messages.button_text_placeholder') }}">
                                @error('widget_button_text')
                                    <div class="invalid-feedback">{{ $error }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.button_text_hint') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="widget_size">{{ __('messages.button_size') }}</label>
                                <select class="form-control @error('widget_size') is-invalid @enderror" 
                                        id="widget_size" 
                                        name="widget_size">
                                    <option value="small" {{ old('widget_size', $widgetSettings['size']) == 'small' ? 'selected' : '' }}>
                                        {{ __('messages.small') }}
                                    </option>
                                    <option value="medium" {{ old('widget_size', $widgetSettings['size']) == 'medium' ? 'selected' : '' }}>
                                        {{ __('messages.medium') }}
                                    </option>
                                    <option value="large" {{ old('widget_size', $widgetSettings['size']) == 'large' ? 'selected' : '' }}>
                                        {{ __('messages.large') }}
                                    </option>
                                </select>
                                @error('widget_size')
                                    <div class="invalid-feedback">{{ $error }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.button_size_hint') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Настройки цветов -->
                    <div class="widget-section-header">
                        <i class="fa fa-palette"></i> {{ __('messages.color_settings') }}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="widget_button_color">{{ __('messages.button_color') }}</label>
                                <input type="color" 
                                       class="form-control @error('widget_button_color') is-invalid @enderror" 
                                       id="widget_button_color" 
                                       name="widget_button_color" 
                                       value="{{ old('widget_button_color', $widgetSettings['button_color']) }}">
                                @error('widget_button_color')
                                    <div class="invalid-feedback">{{ $error }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.button_color_hint') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="widget_text_color">{{ __('messages.text_color') }}</label>
                                <input type="color" 
                                       class="form-control @error('widget_text_color') is-invalid @enderror" 
                                       id="widget_text_color" 
                                       name="widget_text_color" 
                                       value="{{ old('widget_text_color', $widgetSettings['text_color']) }}">
                                @error('widget_text_color')
                                    <div class="invalid-feedback">{{ $error }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.text_color_hint') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Настройки позиции и стиля -->
                    <div class="widget-section-header">
                        <i class="fa fa-arrows-alt"></i> {{ __('messages.position_and_style') }}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="widget_position">{{ __('messages.button_position') }}</label>
                                <select class="form-control @error('widget_position') is-invalid @enderror" 
                                        id="widget_position" 
                                        name="widget_position">
                                    <option value="bottom-right" {{ old('widget_position', $widgetSettings['position']) == 'bottom-right' ? 'selected' : '' }}>
                                        {{ __('messages.bottom_right') }}
                                    </option>
                                    <option value="bottom-left" {{ old('widget_position', $widgetSettings['position']) == 'bottom-left' ? 'selected' : '' }}>
                                        {{ __('messages.bottom_left') }}
                                    </option>
                                    <option value="top-right" {{ old('widget_position', $widgetSettings['position']) == 'top-right' ? 'selected' : '' }}>
                                        {{ __('messages.top_right') }}
                                    </option>
                                    <option value="top-left" {{ old('widget_position', $widgetSettings['position']) == 'top-left' ? 'selected' : '' }}>
                                        {{ __('messages.top_left') }}
                                    </option>
                                    <option value="center" {{ old('widget_position', $widgetSettings['position']) == 'center' ? 'selected' : '' }}>
                                        {{ __('messages.center') }}
                                    </option>
                                    <option value="inline-left" {{ old('widget_position', $widgetSettings['position']) == 'inline-left' ? 'selected' : '' }}>
                                        {{ __('messages.inline_left') }}
                                    </option>
                                    <option value="inline-center" {{ old('widget_position', $widgetSettings['position']) == 'inline-center' ? 'selected' : '' }}>
                                        {{ __('messages.inline_center') }}
                                    </option>
                                    <option value="inline-right" {{ old('widget_position', $widgetSettings['position']) == 'inline-right' ? 'selected' : '' }}>
                                        {{ __('messages.inline_right') }}
                                    </option>
                                </select>
                                @error('widget_position')
                                    <div class="invalid-feedback">{{ $error }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.button_position_hint') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="widget_border_radius">{{ __('messages.border_radius') }}</label>
                                <input type="number" 
                                       class="form-control @error('widget_border_radius') is-invalid @enderror" 
                                       id="widget_border_radius" 
                                       name="widget_border_radius" 
                                       value="{{ old('widget_border_radius', $widgetSettings['border_radius']) }}"
                                       min="0" 
                                       max="50" 
                                       step="1">
                                @error('widget_border_radius')
                                    <div class="invalid-feedback">{{ $error }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.border_radius_hint') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Настройки анимации -->
                    <div class="widget-section-header">
                        <i class="fa fa-magic"></i> {{ __('messages.animation_settings') }}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="widget_animation_enabled" 
                                       name="widget_animation_enabled" 
                                       value="1"
                                       {{ old('widget_animation_enabled', $widgetSettings['animation_enabled']) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="widget_animation_enabled">
                                    <strong>{{ __('messages.enable_animation') }}</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">{{ __('messages.enable_animation_hint') }}</small>
                        </div>
                    </div>

                    <div class="row" id="animationSettingsRow">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="widget_animation_type">{{ __('messages.animation_type') }}</label>
                                <select class="form-control @error('widget_animation_type') is-invalid @enderror" 
                                        id="widget_animation_type" 
                                        name="widget_animation_type">
                                    <option value="scale" {{ old('widget_animation_type', $widgetSettings['animation_type']) == 'scale' ? 'selected' : '' }}>
                                        {{ __('messages.scale') }}
                                    </option>
                                    <option value="bounce" {{ old('widget_animation_type', $widgetSettings['animation_type']) == 'bounce' ? 'selected' : '' }}>
                                        {{ __('messages.bounce') }}
                                    </option>
                                    <option value="pulse" {{ old('widget_animation_type', $widgetSettings['animation_type']) == 'pulse' ? 'selected' : '' }}>
                                        {{ __('messages.pulse') }}
                                    </option>
                                    <option value="shake" {{ old('widget_animation_type', $widgetSettings['animation_type']) == 'shake' ? 'selected' : '' }}>
                                        {{ __('messages.shake') }}
                                    </option>
                                    <option value="none" {{ old('widget_animation_type', $widgetSettings['animation_type']) == 'none' ? 'selected' : '' }}>
                                        {{ __('messages.none') }}
                                    </option>
                                </select>
                                @error('widget_animation_type')
                                    <div class="invalid-feedback">{{ $error }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.animation_type_hint') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="widget_animation_duration">{{ __('messages.animation_duration') }}</label>
                                <input type="number" 
                                       class="form-control @error('widget_animation_duration') is-invalid @enderror" 
                                       id="widget_animation_duration" 
                                       name="widget_animation_duration" 
                                       value="{{ old('widget_animation_duration', $widgetSettings['animation_duration']) }}"
                                       min="100" 
                                       max="2000" 
                                       step="50">
                                @error('widget_animation_duration')
                                    <div class="invalid-feedback">{{ $error }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.animation_duration_hint') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> {{ __('messages.save') }}
                        </button>
                        <div>
                            <button type="button" class="btn btn-info" onclick="generateWidgetCode()">
                                <i class="fa fa-code"></i> {{ __('messages.generate_code') }}
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="previewWidget()">
                                <i class="fa fa-eye"></i> {{ __('messages.preview') }}
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="openInstructionsModal()">
                                <i class="fa fa-question-circle"></i> {{ __('messages.instructions') }}
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Блок с кодом виджета -->
                <div id="widgetCodeBlock" class="mt-4" style="display: none;">
                    <h5>{{ __('messages.widget_code') }}</h5>
                    
                    <!-- Инструкции для inline виджета -->
                    <div id="inlineWidgetInstructions" class="alert alert-warning" style="display: none;">
                        <h6><i class="fa fa-info-circle"></i> {{ __('messages.inline_widget_instructions') }}</h6>
                        <ul class="mb-0">
                            <li>{{ __('messages.inline_widget_step1') }}</li>
                            <li>{{ __('messages.inline_widget_step2') }}</li>
                            <li>{{ __('messages.inline_widget_step3') }}</li>
                            <li>{{ __('messages.inline_widget_step4') }}</li>
                        </ul>
                    </div>
                    
                    <!-- Обычные инструкции для фиксированного виджета -->
                    <div id="fixedWidgetInstructions" class="alert alert-info">
                        <p><strong>{{ __('messages.copy_code_instruction') }}</strong></p>
                        <p>{{ __('messages.paste_code_instruction') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <textarea id="widgetCode" class="form-control" rows="10" readonly></textarea>
                    </div>
                    <button type="button" class="btn btn-success" onclick="copyWidgetCode()">
                        <i class="fa fa-copy"></i> {{ __('messages.copy_code') }}
                    </button>
                </div>

                <!-- Модальное окно с инструкцией -->
                <div id="instructionsModal" class="confirmation-modal">
                    <div class="confirmation-content" style="max-width: 900px;">
                        <h3>
                            <i class="fa fa-question-circle text-info"></i>
                            {{ __('messages.how_to_use_widget') }}
                        </h3>
                        <div id="instructionsContent" style="text-align: left; margin: 20px 0;">
                            <div class="instruction-content">
                                <!-- Что такое виджет -->
                                <div class="instruction-section">
                                    <h6 class="instruction-title">
                                        <i class="fa fa-lightbulb text-warning"></i> 
                                        {{ __('messages.what_is_widget') }}
                                    </h6>
                                    <div class="instruction-text">
                                        <p>{{ __('messages.widget_description') }}</p>
                                        <div class="widget-examples">
                                            <div class="example-item">
                                                <div class="example-icon">
                                                    <i class="fa fa-thumb-tack text-primary"></i>
                                                </div>
                                                <div class="example-text">
                                                    <strong>{{ __('messages.fixed_widget') }}</strong>
                                                    <p>{{ __('messages.fixed_widget_desc') }}</p>
                                                </div>
                                            </div>
                                            <div class="example-item">
                                                <div class="example-icon">
                                                    <i class="fa fa-link text-success"></i>
                                                </div>
                                                <div class="example-text">
                                                    <strong>{{ __('messages.inline_widget') }}</strong>
                                                    <p>{{ __('messages.inline_widget_desc') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Как работает -->
                                <div class="instruction-section">
                                    <h6 class="instruction-title">
                                        <i class="fa fa-cogs text-info"></i> 
                                        {{ __('messages.how_widget_works') }}
                                    </h6>
                                    <div class="instruction-text">
                                        <div class="workflow-steps">
                                            <div class="step">
                                                <div class="step-number">1</div>
                                                <div class="step-content">
                                                    <strong>{{ __('messages.step1_title') }}</strong>
                                                    <p>{{ __('messages.step1_desc') }}</p>
                                                </div>
                                            </div>
                                            <div class="step">
                                                <div class="step-number">2</div>
                                                <div class="step-content">
                                                    <strong>{{ __('messages.step2_title') }}</strong>
                                                    <p>{{ __('messages.step2_desc') }}</p>
                                                </div>
                                            </div>
                                            <div class="step">
                                                <div class="step-number">3</div>
                                                <div class="step-content">
                                                    <strong>{{ __('messages.step3_title') }}</strong>
                                                    <p>{{ __('messages.step3_desc') }}</p>
                                                </div>
                                            </div>
                                            <div class="step">
                                                <div class="step-number">4</div>
                                                <div class="step-content">
                                                    <strong>{{ __('messages.step4_title') }}</strong>
                                                    <p>{{ __('messages.step4_desc') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Примеры использования -->
                                <div class="instruction-section">
                                    <h6 class="instruction-title">
                                        <i class="fa fa-code text-secondary"></i> 
                                        {{ __('messages.usage_examples') }}
                                    </h6>
                                    <div class="instruction-text">
                                        <div class="code-examples">
                                            <div class="code-example">
                                                <h6 class="code-title">{{ __('messages.fixed_widget_example') }}</h6>
                                                <pre class="code-block"><code>&lt;script src="https://your-domain.com/widget-loader.js" data-project="your-project-slug"&gt;&lt;/script&gt;</code></pre>
                                                <p class="code-description">{{ __('messages.fixed_widget_example_desc') }}</p>
                                            </div>
                                            
                                            <div class="code-example">
                                                <h6 class="code-title">{{ __('messages.inline_widget_example') }}</h6>
                                                <pre class="code-block"><code>&lt;script src="https://your-domain.com/widget-loader.js" data-project="your-project-slug"&gt;&lt;/script&gt;
&lt;span id="booking-widget-inline"&gt;&lt;/span&gt;</code></pre>
                                                <p class="code-description">{{ __('messages.inline_widget_example_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Советы по настройке -->
                                <div class="instruction-section">
                                    <h6 class="instruction-title">
                                        <i class="fa fa-star text-warning"></i> 
                                        {{ __('messages.setup_tips') }}
                                    </h6>
                                    <div class="instruction-text">
                                        <div class="tips-list">
                                            <div class="tip-item">
                                                <i class="fa fa-check-circle text-success"></i>
                                                <span>{{ __('messages.tip1') }}</span>
                                            </div>
                                            <div class="tip-item">
                                                <i class="fa fa-check-circle text-success"></i>
                                                <span>{{ __('messages.tip2') }}</span>
                                            </div>
                                            <div class="tip-item">
                                                <i class="fa fa-check-circle text-success"></i>
                                                <span>{{ __('messages.tip3') }}</span>
                                            </div>
                                            <div class="tip-item">
                                                <i class="fa fa-check-circle text-success"></i>
                                                <span>{{ __('messages.tip4') }}</span>
                                            </div>
                                            <div class="tip-item">
                                                <i class="fa fa-check-circle text-success"></i>
                                                <span>{{ __('messages.tip5') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Поддержка -->
                                <div class="instruction-section">
                                    <h6 class="instruction-title">
                                        <i class="fa fa-life-ring text-danger"></i> 
                                        {{ __('messages.need_help') }}
                                    </h6>
                                    <div class="instruction-text">
                                        <p>{{ __('messages.support_text') }}</p>
                                        <div class="support-links">
                                            <a href="{{ route('client.support-tickets.index') }}" class="btn btn-outline-info btn-sm">
                                                <i class="fa fa-envelope"></i> {{ __('messages.contact_support') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="confirmation-buttons d-flex justify-content-end">
                            <button onclick="closeInstructionsModal()" class="cancel-btn">{{ __('messages.close') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Предварительный просмотр -->
                <div id="widgetPreview" class="mt-4" style="display: none;">
                    <h5>{{ __('messages.widget_preview') }}</h5>
                    <div class="preview-container" style="position: relative; height: 400px; border: 2px dashed #ccc; border-radius: 8px; background: #f8f9fa; overflow: hidden;">
                        <div id="previewWidget" style="position: absolute; z-index: 9999; width: 100%; height: 100%;"></div>
                        <div id="previewPlaceholder" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #666; text-align: center;">
                            <i class="fa fa-eye" style="font-size: 48px; margin-bottom: 10px;"></i>
                            <p>{{ __('messages.preview_placeholder') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
/* Стили для страницы виджета */
.preview-container {
    position: relative;
    overflow: hidden;
}

#widgetCode {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.form-row--2col .form-col {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 15px;
    padding-left: 15px;
}

.form-group {
    margin-bottom: 1rem;
}

.custom-control {
    position: relative;
    display: block;
    min-height: 1.5rem;
    padding-left: 1.5rem;
}

.custom-control-input {
    position: absolute;
    left: 0;
    z-index: -1;
    width: 1rem;
    height: 1.25rem;
    opacity: 0;
}

.custom-control-label {
    position: relative;
    margin-bottom: 0;
    vertical-align: top;
    cursor: pointer;
}

.custom-switch .custom-control-label::before {
    left: -2.25rem;
    width: 1.75rem;
    pointer-events: all;
    border-radius: 0.5rem;
}

.custom-control-label::before {
    position: absolute;
    top: 0.25rem;
    left: -1.5rem;
    display: block;
    width: 1rem;
    height: 1rem;
    pointer-events: none;
    content: "";
    background-color: #fff;
    border: #adb5bd solid 1px;
}

.custom-switch .custom-control-label::after {
    top: calc(0.25rem + 2px);
    left: calc(-2.25rem + 2px);
    width: calc(1rem - 4px);
    height: calc(1rem - 4px);
    background-color: #adb5bd;
    border-radius: 0.5rem;
    transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.custom-control-input:checked ~ .custom-control-label::before {
    color: #fff;
    border-color: #007bff;
    background-color: #007bff;
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::after {
    background-color: #fff;
    transform: translateX(0.75rem);
}

/* Стили для кнопок */
.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-info {
    color: #fff;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-success {
    color: #fff;
    background-color: #28a745;
    border-color: #28a745;
}

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

    /* Стили для двухколоночной сетки Bootstrap */
    .settings-pane .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-bottom: 20px !important;
    }
    
    .settings-pane .col-md-6 {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
    
    .settings-pane .form-group {
        margin-bottom: 0 !important;
        width: 100% !important;
    }
    
    .settings-pane .form-control {
        display: block !important;
        width: 100% !important;
        height: 40px !important;
        padding: 8px 12px !important;
        font-size: 1rem !important;
        font-weight: 400 !important;
        line-height: 1.5 !important;
        color: #495057 !important;
        background-color: #fff !important;
        background-clip: padding-box !important;
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
    }
    
    /* Специальные стили для цветовых полей */
    .settings-pane input[type="color"] {
        height: 40px !important;
        padding: 5px !important;
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
    }
    
    /* Стили для селектов */
    .settings-pane select {
        width: 100% !important;
        height: 40px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
        background-color: #fff !important;
    }
    
    /* Стили для числовых полей */
    .settings-pane input[type="number"] {
        width: 100% !important;
        height: 40px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
    }
    
    /* Стили для текстовых полей */
    .settings-pane input[type="text"] {
        width: 100% !important;
        height: 40px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
    }
    
    /* Стили для лейблов */
    .settings-pane label {
        display: block !important;
        margin-bottom: 5px !important;
        font-weight: 500 !important;
        color: #333 !important;
    }
    
    /* Стили для подсказок */
    .settings-pane .form-text {
        margin-top: 5px !important;
        font-size: 12px !important;
        color: #6c757d !important;
    }
    
    /* Стили для переключателей */
    .settings-pane .custom-control {
        padding-left: 2rem !important;
        margin-bottom: 10px !important;
    }
    
    .settings-pane .custom-control-label {
        font-weight: 600 !important;
        color: #333 !important;
    }
    
    /* Стили для настроек анимации */
    .settings-pane #animationSettingsRow {
        transition: all 0.3s ease;
    }
    
    .settings-pane #animationSettingsRow[style*="display: none"] {
        opacity: 0;
        transform: translateY(-10px);
    }
    
    /* Стили для переключателя анимации */
    .settings-pane .col-md-12 .custom-control {
        margin-bottom: 15px;
    }
    
    .settings-pane .col-md-12 .form-text {
        margin-top: 5px;
        margin-bottom: 15px;
    }
    
    /* Мобильная адаптация */
    @media (max-width: 768px) {
        .settings-pane .col-md-6 {
            margin-bottom: 15px !important;
        }
        
        .settings-pane #animationSettingsRow {
            margin-top: 10px;
        }
    }
    
    /* Стили для инструкции */
    .widget-instructions {
        margin-top: 40px;
    }
    
    /* Стили для содержимого инструкции в модальном окне */
    .instruction-content {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-top: 20px;
    }
    
    .instruction-section {
        margin-bottom: 30px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .instruction-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .instruction-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .instruction-title i {
        font-size: 18px;
    }
    
    .instruction-text {
        color: #666;
        line-height: 1.6;
    }
    
    /* Стили для примеров виджетов */
    .widget-examples {
        display: flex;
        gap: 20px;
        margin-top: 15px;
    }
    
    .example-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        flex: 1;
        padding: 15px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .example-icon {
        font-size: 24px;
        margin-top: 2px;
    }
    
    .example-text strong {
        color: #333;
        display: block;
        margin-bottom: 5px;
    }
    
    .example-text p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }
    
    /* Стили для пошагового процесса */
    .workflow-steps {
        margin-top: 15px;
    }
    
    .step {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }
    
    .step-number {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .step-content strong {
        color: #333;
        display: block;
        margin-bottom: 5px;
    }
    
    .step-content p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }
    
    /* Стили для примеров кода */
    .code-examples {
        margin-top: 15px;
    }
    
    .code-example {
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .code-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .code-block {
        background: #2d3748;
        color: #e2e8f0;
        padding: 15px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.4;
        overflow-x: auto;
        margin: 10px 0;
    }
    
    .code-block code {
        color: inherit;
        background: none;
        padding: 0;
    }
    
    .code-description {
        margin: 10px 0 0 0;
        font-size: 14px;
        color: #666;
    }
    
    /* Стили для советов */
    .tips-list {
        margin-top: 15px;
    }
    
    .tip-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        padding: 10px;
        background: white;
        border-radius: 6px;
        border-left: 3px solid #28a745;
    }
    
    .tip-item i {
        font-size: 16px;
        flex-shrink: 0;
    }
    
    .tip-item span {
        color: #333;
        font-size: 14px;
    }
    
    /* Стили для поддержки */
    .support-links {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }
    
    .support-links .btn {
        font-size: 13px;
        padding: 8px 15px;
    }
    
    /* Адаптивность для мобильных устройств */
    @media (max-width: 768px) {
        .widget-examples {
            flex-direction: column;
            gap: 15px;
        }
        
        .step {
            padding: 12px;
        }
        
        .support-links {
            flex-direction: column;
        }
        
        .support-links .btn {
            width: 100%;
            text-align: center;
        }
    }
    
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
        padding: 20px;
        border: 1px solid #888;
        width: 90%;
        max-width: 900px;
        max-height: 80vh;
        border-radius: 8px;
        text-align: center;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .confirmation-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
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
    
    /* Стили для содержимого инструкции в модальном окне */
    .confirmation-modal .instruction-content {
        background: transparent;
        border-radius: 0;
        padding: 0;
        margin-top: 0;
    }
    
    .confirmation-modal .instruction-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .confirmation-modal .instruction-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .confirmation-modal .instruction-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
    }
    
    .confirmation-modal .instruction-title i {
        font-size: 16px;
    }
    
    .confirmation-modal .instruction-text {
        color: #666;
        line-height: 1.5;
        font-size: 14px;
    }
    
    /* Адаптация для мобильных устройств */
    @media (max-width: 768px) {
        .confirmation-modal .confirmation-content {
            margin: 10% auto;
            width: 95%;
            max-height: 85vh;
            padding: 15px;
        }
        
        .confirmation-modal .instruction-content {
            padding: 0;
        }
        
        .confirmation-modal .instruction-section {
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        
        .confirmation-modal .instruction-title {
            font-size: 15px;
        }
        
        .confirmation-modal .instruction-text {
            font-size: 13px;
        }
    }

    /* Стили для заголовков секций */

    

    

    
    /* Стили для двухколоночной сетки */
    .form-row--2col {
        display: flex !important;
        flex-direction: row !important;
        gap: 20px !important;
        margin-bottom: 20px !important;
        width: 100% !important;
    }
    
    .form-col {
        flex: 1 !important;
        min-width: 0 !important;
    }
    
    .form-group {
        margin-bottom: 0 !important;
        width: 100% !important;
    }
    
    .form-group label {
        display: block !important;
        margin-bottom: 5px !important;
        font-weight: 500 !important;
    }
    
    .form-group .form-control {
        width: 100% !important;
        box-sizing: border-box !important;
    }
    
    .form-group .form-text {
        margin-top: 5px !important;
        font-size: 12px !important;
    }
    
    /* Специальные стили для цветовых полей */
    .form-group input[type="color"] {
        height: 40px !important;
        padding: 5px !important;
        border-radius: 6px !important;
    }
    
    /* Стили для селектов */
    .form-group select {
        width: 100% !important;
        height: 40px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
    }
    
    /* Стили для числовых полей */
    .form-group input[type="number"] {
        width: 100% !important;
        height: 40px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
    }
    
    /* Стили для текстовых полей */
    .form-group input[type="text"] {
        width: 100% !important;
        height: 40px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
    }
    
    /* Адаптивные стили для мобильных устройств */
    @media (max-width: 768px) {
        .form-row--2col {
            flex-direction: column !important;
            gap: 15px !important;
        }
        
        .form-col {
            width: 100% !important;
        }
    }
    
    /* Дополнительные стили для лучшего отображения */
    .settings-pane form {
        width: 100% !important;
    }
    
    .settings-pane .form-row--2col {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    /* Стили для переключателей */
    .custom-control {
        padding-left: 2rem;
    }
    
    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    
    
    .settings-header h1 {
        color: #333;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .form-actions {
        margin-top: 40px!important;
    }
    
    .form-actions .btn {
        margin: 0 5px;
    }
    
    #animationDurationRow {
        transition: all 0.3s ease;
    }
    
    #animationDurationRow[style*="display: none"] {
        opacity: 0;
        transform: translateY(-10px);
    }

    /* Стили для блокировки мобильной версии */
    .mobile-block {
        display: none; /* Скрываем по умолчанию */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #f8f9fa; /* Светлый фон */
        z-index: 1000; /* Выше других элементов */
        text-align: center;
        padding-top: 100px; /* Отступ сверху для центрирования */
    }

    .mobile-block-content {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .mobile-block-icon {
        font-size: 60px;
        color: #007bff; /* Синий цвет для иконки */
        margin-bottom: 15px;
    }

    .mobile-block-features {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        font-size: 14px;
    }

    .feature-item i {
        font-size: 20px;
        color: #007bff;
    }

    /* Адаптивность для мобильных устройств */
    @media (max-width: 768px) {
        .mobile-block {
            display: block; /* Показываем на мобильных */
        }
        .desktop-content {
            margin-top: 20px; /* Отступ сверху для мобильного блока */
        }
    }
</style>

@push('scripts')
<script>
// Управление видимостью полей настроек анимации
function toggleAnimationSettings() {
    const animationEnabled = document.getElementById('widget_animation_enabled');
    const animationType = document.getElementById('widget_animation_type');
    const animationSettingsRow = document.getElementById('animationSettingsRow');
    
    if (animationEnabled.checked && animationType.value !== 'none') {
        animationSettingsRow.style.display = 'flex';
        animationSettingsRow.style.opacity = '1';
        animationSettingsRow.style.transform = 'translateY(0)';
    } else {
        animationSettingsRow.style.display = 'none';
        animationSettingsRow.style.opacity = '0';
        animationSettingsRow.style.transform = 'translateY(-10px)';
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    toggleAnimationSettings();
    
    // Добавляем обработчики событий
    document.getElementById('widget_animation_enabled').addEventListener('change', toggleAnimationSettings);
    document.getElementById('widget_animation_type').addEventListener('change', toggleAnimationSettings);
    
    // Добавляем стили для плавных переходов
    const animationSettingsRow = document.getElementById('animationSettingsRow');
    if (animationSettingsRow) {
        animationSettingsRow.style.transition = 'all 0.3s ease';
    }
});

// Обработка отправки формы
document.getElementById('widgetSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.saving") }}...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('{{ route("client.widget-settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Network response was not ok');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message || '{{ __('messages.widget_settings_saved') }}');
        } else {
            window.showNotification('error', data.message || '{{ __('messages.error_saving_settings') }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.errors) {
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

// Генерация кода виджета
function generateWidgetCode() {
    fetch('{{ route("client.widget-settings.generate-code") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('widgetCode').value = data.code;
            document.getElementById('widgetCodeBlock').style.display = 'block';
            
            // Показываем правильные инструкции в зависимости от типа виджета
            const isInline = data.debug && data.debug.is_inline;
            const inlineInstructions = document.getElementById('inlineWidgetInstructions');
            const fixedInstructions = document.getElementById('fixedWidgetInstructions');
            
            if (isInline) {
                inlineInstructions.style.display = 'block';
                fixedInstructions.style.display = 'none';
            } else {
                inlineInstructions.style.display = 'none';
                fixedInstructions.style.display = 'block';
            }
            
            window.showNotification('success', '{{ __('messages.widget_code_generated') }}');
        } else {
            window.showNotification('error', data.message);
        }
    })
    .catch(error => {
        window.showNotification('error', '{{ __('messages.error_generating_code') }}');
        console.error('Error:', error);
    });
}

// Копирование кода виджета
function copyWidgetCode() {
    const codeTextarea = document.getElementById('widgetCode');
    codeTextarea.select();
    document.execCommand('copy');
    window.showNotification('success', '{{ __('messages.widget_code_copied') }}');
}

// Предварительный просмотр виджета
function previewWidget() {
    console.log('previewWidget function called');
    
    const buttonText = document.getElementById('widget_button_text').value || 'Записаться';
    const buttonColor = document.getElementById('widget_button_color').value || '#007bff';
    const position = document.getElementById('widget_position').value || 'bottom-right';
    const size = document.getElementById('widget_size').value || 'medium';
    const animationEnabled = document.getElementById('widget_animation_enabled').checked;
    const animationType = document.getElementById('widget_animation_type').value || 'scale';
    const animationDuration = document.getElementById('widget_animation_duration').value || 300;
    const borderRadius = document.getElementById('widget_border_radius').value || 25;
    const textColor = document.getElementById('widget_text_color').value || '#ffffff';
    
    console.log('Button text:', buttonText);
    console.log('Button color:', buttonColor);
    console.log('Position:', position);
    console.log('Size:', size);
    console.log('Animation enabled:', animationEnabled);
    console.log('Animation type:', animationType);
    console.log('Animation duration:', animationDuration);
    console.log('Border radius:', borderRadius);
    console.log('Text color:', textColor);
    
    const previewContainer = document.getElementById('previewWidget');
    const previewBlock = document.getElementById('widgetPreview');
    
    console.log('Preview container:', previewContainer);
    console.log('Preview block:', previewBlock);
    
    // Определяем стили позиции
    let positionStyles = '';
    switch (position) {
        case 'bottom-right':
            positionStyles = 'bottom: 20px; right: 20px;';
            break;
        case 'bottom-left':
            positionStyles = 'bottom: 20px; left: 20px;';
            break;
        case 'top-right':
            positionStyles = 'top: 20px; right: 20px;';
            break;
        case 'top-left':
            positionStyles = 'top: 20px; left: 20px;';
            break;
        case 'center':
            positionStyles = 'top: 50%; left: 50%; transform: translate(-50%, -50%);';
            break;
        case 'inline-left':
            positionStyles = 'position: relative; display: inline-block; margin: 10px 10px 10px 0;';
            break;
        case 'inline-center':
            positionStyles = 'position: relative; display: block; margin: 10px auto; text-align: center;';
            break;
        case 'inline-right':
            positionStyles = 'position: relative; display: inline-block; margin: 10px 0 10px 10px; float: right;';
            break;
    }
    
    // Определяем стили размера
    let sizeStyles = '';
    switch (size) {
        case 'small':
            sizeStyles = 'padding: 8px 16px; font-size: 14px;';
            break;
        case 'large':
            sizeStyles = 'padding: 16px 32px; font-size: 18px;';
            break;
        default:
            sizeStyles = 'padding: 12px 24px; font-size: 16px;';
    }
    
    // Определяем стили анимации
    let animationStyles = '';
    let animationEvents = '';
    
    if (animationEnabled && animationType !== 'none') {
        animationStyles = `transition: transform ${animationDuration}ms ease;`;
        
        switch (animationType) {
            case 'scale':
                animationEvents = `onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"`;
                break;
            case 'bounce':
                animationStyles = `transition: transform ${animationDuration}ms cubic-bezier(0.68, -0.55, 0.265, 1.55);`;
                animationEvents = `onmouseover="this.style.transform='scale(1.1) translateY(-5px)'" onmouseout="this.style.transform='scale(1) translateY(0)'"`;
                break;
            case 'pulse':
                animationStyles = `transition: all ${animationDuration}ms ease;`;
                animationEvents = `onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.25)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'"`;
                break;
            case 'shake':
                animationEvents = `onmouseover="this.style.transform='translateX(5px)'" onmouseout="this.style.transform='translateX(0)'"`;
                break;
        }
    }
    
    const buttonHtml = `
        <button style="
            position: absolute;
            z-index: 9999;
            ${sizeStyles}
            background-color: ${buttonColor};
            color: ${textColor};
            border: none;
            border-radius: ${borderRadius}px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            ${positionStyles}
            ${animationStyles}
        " ${animationEvents}>
            ${buttonText}
        </button>
    `;
    
    console.log('Button HTML:', buttonHtml);
    
    // Скрываем placeholder
    const placeholder = document.getElementById('previewPlaceholder');
    if (placeholder) {
        placeholder.style.display = 'none';
    }
    
    previewContainer.innerHTML = buttonHtml;
    previewBlock.style.display = 'block';
    
    window.showNotification('success', '{{ __('messages.widget_preview_updated') }}');
}

// Открытие модального окна с инструкцией
function openInstructionsModal() {
    const modal = document.getElementById('instructionsModal');
    if (modal) {
        modal.style.display = 'block';
    }
}

// Закрытие модального окна с инструкцией
function closeInstructionsModal() {
    const modal = document.getElementById('instructionsModal');
    if (modal) {
        modal.style.display = 'none';
    }
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