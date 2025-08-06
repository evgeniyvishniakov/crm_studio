// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ НАСТРОЕК WIDGET =====

// Управление видимостью полей настроек анимации
function toggleAnimationSettings() {
    const animationEnabled = document.getElementById('widget_animation_enabled');
    const animationType = document.getElementById('widget_animation_type');
    const animationSettingsRow = document.getElementById('animationSettingsRow');
    
    // Проверяем существование элементов перед обращением к их свойствам
    if (!animationEnabled || !animationType || !animationSettingsRow) {
        return;
    }
    
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
function initializeWidgetSettings() {
    // Проверяем, что элементы существуют перед инициализацией
    const animationEnabled = document.getElementById('widget_animation_enabled');
    const animationType = document.getElementById('widget_animation_type');
    const animationSettingsRow = document.getElementById('animationSettingsRow');
    
    // Если элементы не найдены, выходим из функции
    if (!animationEnabled || !animationType || !animationSettingsRow) {
        return;
    }
    
    toggleAnimationSettings();
    
    // Добавляем обработчики событий
    if (animationEnabled) {
        animationEnabled.addEventListener('change', toggleAnimationSettings);
    }
    if (animationType) {
        animationType.addEventListener('change', toggleAnimationSettings);
    }
    
    // Добавляем стили для плавных переходов
    if (animationSettingsRow) {
        animationSettingsRow.style.transition = 'all 0.3s ease';
    }
}

// Обработка отправки формы
function initializeFormSubmission() {
    const form = document.getElementById('widgetSettingsForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Сохранение...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        // Добавляем скрытое поле _method для PUT запроса
        formData.append('_method', 'PUT');
        
        fetch('/widget-settings', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                window.showNotification('success', data.message || 'Настройки виджета сохранены');
            } else {
                window.showNotification('error', data.message || 'Ошибка при сохранении настроек');
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
                window.showNotification('error', 'Ошибки валидации');
            } else {
                window.showNotification('error', error.message || 'Ошибка при сохранении настроек');
            }
        })
        .finally(() => {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });
}

// Генерация кода виджета
function generateWidgetCode() {
    fetch('/widget-settings/generate-code', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            
            window.showNotification('success', 'Код виджета сгенерирован');
        } else {
            window.showNotification('error', data.message);
        }
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка при генерации кода');
        console.error('Error:', error);
    });
}

// Копирование кода виджета
function copyWidgetCode() {
    const codeTextarea = document.getElementById('widgetCode');
    codeTextarea.select();
    document.execCommand('copy');
    window.showNotification('success', 'Код виджета скопирован');
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
    
    window.showNotification('success', 'Предварительный просмотр обновлен');
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

// Инициализация модального окна
function initializeModal() {
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
}

// Инициализация всех функций при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Проверяем, находимся ли мы на странице настроек виджета
    const isWidgetSettingsPage = document.getElementById('widgetSettingsForm') !== null;
    
    if (isWidgetSettingsPage) {
        initializeWidgetSettings();
        initializeFormSubmission();
        initializeModal();
    }
}); 