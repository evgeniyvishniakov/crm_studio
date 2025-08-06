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
                                       {{ old('widget_enabled', $widgetSettingsArray['enabled']) ? 'checked' : '' }}>
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
                                       value="{{ old('widget_button_text', $widgetSettingsArray['button_text']) }}"
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
                                    <option value="small" {{ old('widget_size', $widgetSettingsArray['size']) == 'small' ? 'selected' : '' }}>
                                        {{ __('messages.small') }}
                                    </option>
                                    <option value="medium" {{ old('widget_size', $widgetSettingsArray['size']) == 'medium' ? 'selected' : '' }}>
                                        {{ __('messages.medium') }}
                                    </option>
                                    <option value="large" {{ old('widget_size', $widgetSettingsArray['size']) == 'large' ? 'selected' : '' }}>
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
                                       value="{{ old('widget_button_color', $widgetSettingsArray['button_color']) }}">
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
                                       value="{{ old('widget_text_color', $widgetSettingsArray['text_color']) }}">
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
                                    <option value="bottom-right" {{ old('widget_position', $widgetSettingsArray['position']) == 'bottom-right' ? 'selected' : '' }}>
                                        {{ __('messages.bottom_right') }}
                                    </option>
                                    <option value="bottom-left" {{ old('widget_position', $widgetSettingsArray['position']) == 'bottom-left' ? 'selected' : '' }}>
                                        {{ __('messages.bottom_left') }}
                                    </option>
                                    <option value="top-right" {{ old('widget_position', $widgetSettingsArray['position']) == 'top-right' ? 'selected' : '' }}>
                                        {{ __('messages.top_right') }}
                                    </option>
                                    <option value="top-left" {{ old('widget_position', $widgetSettingsArray['position']) == 'top-left' ? 'selected' : '' }}>
                                        {{ __('messages.top_left') }}
                                    </option>
                                    <option value="center" {{ old('widget_position', $widgetSettingsArray['position']) == 'center' ? 'selected' : '' }}>
                                        {{ __('messages.center') }}
                                    </option>
                                    <option value="inline-left" {{ old('widget_position', $widgetSettingsArray['position']) == 'inline-left' ? 'selected' : '' }}>
                                        {{ __('messages.inline_left') }}
                                    </option>
                                    <option value="inline-center" {{ old('widget_position', $widgetSettingsArray['position']) == 'inline-center' ? 'selected' : '' }}>
                                        {{ __('messages.inline_center') }}
                                    </option>
                                    <option value="inline-right" {{ old('widget_position', $widgetSettingsArray['position']) == 'inline-right' ? 'selected' : '' }}>
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
                                       value="{{ old('widget_border_radius', $widgetSettingsArray['border_radius']) }}"
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
                                       {{ old('widget_animation_enabled', $widgetSettingsArray['animation_enabled']) ? 'checked' : '' }}>
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
                                    <option value="scale" {{ old('widget_animation_type', $widgetSettingsArray['animation_type']) == 'scale' ? 'selected' : '' }}>
                                        {{ __('messages.scale') }}
                                    </option>
                                    <option value="bounce" {{ old('widget_animation_type', $widgetSettingsArray['animation_type']) == 'bounce' ? 'selected' : '' }}>
                                        {{ __('messages.bounce') }}
                                    </option>
                                    <option value="pulse" {{ old('widget_animation_type', $widgetSettingsArray['animation_type']) == 'pulse' ? 'selected' : '' }}>
                                        {{ __('messages.pulse') }}
                                    </option>
                                    <option value="shake" {{ old('widget_animation_type', $widgetSettingsArray['animation_type']) == 'shake' ? 'selected' : '' }}>
                                        {{ __('messages.shake') }}
                                    </option>
                                    <option value="none" {{ old('widget_animation_type', $widgetSettingsArray['animation_type']) == 'none' ? 'selected' : '' }}>
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
                                       value="{{ old('widget_animation_duration', $widgetSettingsArray['animation_duration']) }}"
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

                    <div class="widget-form-actions d-flex justify-content-between align-items-center">
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