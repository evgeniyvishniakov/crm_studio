@extends('client.layouts.app')

@section('title', __('messages.website_widget'))

@section('content')
<div class="dashboard-container">
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
                        <div class="form-group mb-3">
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
                    </div>
                    <div class="form-col"></div>
                </div>

                <div class="form-row form-row--2col">
                    <div class="form-col">
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
                    <div class="form-col">
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
                </div>

                <div class="form-row form-row--2col">
                    <div class="form-col">
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
                    <div class="form-col">
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

                <!-- Настройки анимации -->
                <h5 class="mt-4">{{ __('messages.animation_settings') }}</h5>
                
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
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
                    <div class="form-col"></div>
                </div>

                <div class="form-row form-row--2col">
                    <div class="form-col">
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
                    <div class="form-col">
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

                <div class="form-row form-row--2col">
                    <div class="form-col">
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
                    <div class="form-col">
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

.form-actions .btn-secondary {
    background: linear-gradient(135deg, #6c757d, #868e96) !important;
    border-color: #6c757d !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3) !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.5rem !important;
}
</style>

@push('scripts')
<script>
// AJAX сохранение настроек виджета
document.getElementById('widgetSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.saving") }}...';
    submitBtn.disabled = true;

    fetch("{{ route('client.widget-settings.update') }}", {
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
    
    console.log('Preview block display set to block');
    window.showNotification('success', '{{ __('messages.widget_preview_loaded') }}');
}
</script>
@endpush 