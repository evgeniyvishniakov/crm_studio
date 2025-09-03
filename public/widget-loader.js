/**
 * Универсальный загрузчик виджета записи
 * Этот файл можно вставить на любой сайт для отображения виджета записи
 */

(function() {
    'use strict';
    
    // Конфигурация виджета (будет загружаться с сервера)
    var widgetConfig = {
        projectSlug: null,
        buttonText: 'Book Now',
        buttonColor: '#007bff',
        textColor: '#ffffff',
        position: 'bottom-right',
        size: 'medium',
        borderRadius: 25,
        animationEnabled: true,
        animationType: 'scale',
        animationDuration: 300
    };
    
    // Функция для загрузки конфигурации виджета
    function loadWidgetConfig(projectSlug) {
        return fetch('http://127.0.0.1:8000/api/widget/config/' + projectSlug)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Object.assign(widgetConfig, data.config);
                    widgetConfig.projectSlug = projectSlug;
                    return true;
                } else {
                    console.error('Ошибка загрузки конфигурации виджета:', data.message);
                    return false;
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки виджета:', error);
                return false;
            });
    }
    
    // Функция для получения стилей позиции
    function getPositionStyles(position) {
        switch (position) {
            case 'bottom-right':
                return 'bottom: 20px; right: 20px;';
            case 'bottom-left':
                return 'bottom: 20px; left: 20px;';
            case 'top-right':
                return 'top: 20px; right: 20px;';
            case 'top-left':
                return 'top: 20px; left: 20px;';
            case 'center':
                return 'top: 50%; left: 50%; transform: translate(-50%, -50%);';
            case 'inline-left':
                return 'position: relative; display: inline-block; margin: 10px 10px 10px 0;';
            case 'inline-center':
                return 'position: relative; display: block; margin: 10px auto; text-align: center;';
            case 'inline-right':
                return 'position: relative; display: inline-block; margin: 10px 0 10px 10px; float: right;';
            default:
                return 'bottom: 20px; right: 20px;';
        }
    }
    
    // Функция для получения стилей размера
    function getSizeStyles(size) {
        switch (size) {
            case 'small':
                return 'padding: 8px 16px; font-size: 14px;';
            case 'large':
                return 'padding: 16px 32px; font-size: 18px;';
            default:
                return 'padding: 12px 24px; font-size: 16px;';
        }
    }
    
    // Функция для получения стилей анимации
    function getAnimationStyles() {
        if (!widgetConfig.animationEnabled || widgetConfig.animationType === 'none') {
            return '';
        }
        
        var duration = widgetConfig.animationDuration + 'ms';
        
        switch (widgetConfig.animationType) {
            case 'scale':
                return 'transition: transform ' + duration + ' ease;';
            case 'bounce':
                return 'transition: transform ' + duration + ' cubic-bezier(0.68, -0.55, 0.265, 1.55);';
            case 'pulse':
                return 'transition: all ' + duration + ' ease;';
            case 'shake':
                return 'transition: transform ' + duration + ' ease;';
            default:
                return '';
        }
    }
    
    // Функция для получения JavaScript анимации
    function getAnimationJavaScript() {
        if (!widgetConfig.animationEnabled || widgetConfig.animationType === 'none') {
            return '';
        }
        
        switch (widgetConfig.animationType) {
            case 'scale':
                return 'onmouseover="this.style.transform=\'scale(1.1)\'" onmouseout="this.style.transform=\'scale(1)\'"';
            case 'bounce':
                return 'onmouseover="this.style.transform=\'scale(1.1) translateY(-5px)\'" onmouseout="this.style.transform=\'scale(1) translateY(0)\'"';
            case 'pulse':
                return 'onmouseover="this.style.transform=\'scale(1.05)\"; this.style.boxShadow=\'0 6px 20px rgba(0,0,0,0.25)\'" onmouseout="this.style.transform=\'scale(1)\"; this.style.boxShadow=\'0 4px 12px rgba(0,0,0,0.15)\'"';
            case 'shake':
                return 'onmouseover="this.style.transform=\'translateX(5px)\'" onmouseout="this.style.transform=\'translateX(0)\'"';
            default:
                return '';
        }
    }
    
    // Функция для создания виджета
    function createWidget() {
        console.log('Создание виджета...');
        
        // Проверяем, не создан ли уже виджет
        if (document.getElementById('booking-widget')) {
            console.log('Виджет уже существует, пропускаем создание');
            return;
        }
        
        console.log('Создаем новый виджет с позицией:', widgetConfig.position);
        
        var widget = document.createElement('div');
        widget.id = 'booking-widget';
        
        var buttonStyle = [
            'background-color: ' + widgetConfig.buttonColor + ';',
            'color: ' + widgetConfig.textColor + ';',
            'border: none;',
            'border-radius: ' + widgetConfig.borderRadius + 'px;',
            'font-weight: 600;',
            'cursor: pointer;',
            'box-shadow: 0 4px 12px rgba(0,0,0,0.15);',
            getSizeStyles(widgetConfig.size),
            getAnimationStyles()
        ].join(' ');
        
        var animationJS = getAnimationJavaScript();
        
        // Для inline позиций добавляем специальные стили
        if (widgetConfig.position.startsWith('inline-')) {
            buttonStyle += ' ' + getPositionStyles(widgetConfig.position);
            widget.innerHTML = '<button id="booking-widget-btn" style="' + buttonStyle + '" ' + animationJS + ' onclick="openBookingModal()">' + widgetConfig.buttonText + '</button>';
            
            // Для inline позиций ищем span с id booking-widget-inline
            var inlineContainer = document.getElementById('booking-widget-inline');
            if (inlineContainer) {
                inlineContainer.appendChild(widget);
            } else {
                // Если контейнер не найден, создаем виджет как обычно
                buttonStyle = [
                    'position: fixed;',
                    'z-index: 9999;',
                    'background-color: ' + widgetConfig.buttonColor + ';',
                    'color: ' + widgetConfig.textColor + ';',
                    'border: none;',
                    'border-radius: ' + widgetConfig.borderRadius + 'px;',
                    'font-weight: 600;',
                    'cursor: pointer;',
                    'box-shadow: 0 4px 12px rgba(0,0,0,0.15);',
                    getPositionStyles('bottom-right'), // fallback
                    getSizeStyles(widgetConfig.size),
                    getAnimationStyles()
                ].join(' ');
                
                widget.innerHTML = '<button id="booking-widget-btn" style="' + buttonStyle + '" ' + animationJS + ' onclick="openBookingModal()">' + widgetConfig.buttonText + '</button>';
                document.body.appendChild(widget);
            }
        } else {
            // Для фиксированных позиций
            buttonStyle = [
                'position: fixed;',
                'z-index: 9999;',
                'background-color: ' + widgetConfig.buttonColor + ';',
                'color: ' + widgetConfig.textColor + ';',
                'border: none;',
                'border-radius: ' + widgetConfig.borderRadius + 'px;',
                'font-weight: 600;',
                'cursor: pointer;',
                'box-shadow: 0 4px 12px rgba(0,0,0,0.15);',
                getPositionStyles(widgetConfig.position),
                getSizeStyles(widgetConfig.size),
                getAnimationStyles()
            ].join(' ');
            
            widget.innerHTML = '<button id="booking-widget-btn" style="' + buttonStyle + '" ' + animationJS + ' onclick="openBookingModal()">' + widgetConfig.buttonText + '</button>';
            document.body.appendChild(widget);
            console.log('Фиксированный виджет создан и добавлен на страницу');
        }
    }
    
    // Функция для открытия модального окна
    window.openBookingModal = function() {
        if (!widgetConfig.projectSlug) {
            console.error('Виджет не настроен');
            return;
        }
        
        var modal = document.createElement('div');
        modal.id = 'booking-modal';
        modal.style.cssText = [
            'position: fixed;',
            'top: 0;',
            'left: 0;',
            'width: 100%;',
            'height: 100%;',
            'background: rgba(0,0,0,0.5);',
            'z-index: 10000;',
            'display: flex;',
            'align-items: center;',
            'justify-content: center;'
        ].join(' ');
        
        var widgetUrl = 'http://127.0.0.1:8000/book/' + widgetConfig.projectSlug + '?widget=true';
        
        modal.innerHTML = [
            '<div style="',
            'background: white;',
            'border-radius: 12px;',
            'width: 90%;',
            'max-width: 800px;',
            'height: 90%;',
            'max-height: 600px;',
            'position: relative;',
            '">',
            '<button onclick="closeBookingModal()" style="',
            'position: absolute;',
            'top: 15px;',
            'right: 15px;',
            'background: none;',
            'border: none;',
            'font-size: 24px;',
            'cursor: pointer;',
            'color: #666;',
            '">&times;</button>',
            '<iframe src="' + widgetUrl + '" style="',
            'width: 100%;',
            'height: 100%;',
            'border: none;',
            'border-radius: 12px;',
            '"></iframe>',
            '</div>'
        ].join(' ');
        
        document.body.appendChild(modal);
    };
    
    // Функция для закрытия модального окна
    window.closeBookingModal = function() {
        var modal = document.getElementById('booking-modal');
        if (modal) {
            modal.remove();
        }
    };
    
    // Функция для инициализации виджета
    window.initBookingWidget = function(projectSlug) {
        console.log('Инициализация виджета для проекта:', projectSlug);
        
        loadWidgetConfig(projectSlug).then(function(success) {
            console.log('Результат загрузки конфигурации:', success);
            if (success) {
                console.log('Конфигурация загружена:', widgetConfig);
                createWidget();
                console.log('Виджет записи успешно загружен для проекта:', projectSlug);
            } else {
                console.error('Не удалось загрузить конфигурацию виджета');
            }
        });
    };
    
    // Автоматическая инициализация если указан data-атрибут
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM загружен, проверяем data-атрибуты...');
        var script = document.currentScript || document.querySelector('script[src*="widget-loader.js"]');
        if (script && script.getAttribute('data-project')) {
            console.log('Найден data-project атрибут:', script.getAttribute('data-project'));
            window.initBookingWidget(script.getAttribute('data-project'));
        } else {
            console.log('data-project атрибут не найден');
        }
    });
    
})();