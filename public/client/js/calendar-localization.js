// Локализация календаря с использованием flatpickr
document.addEventListener('DOMContentLoaded', function() {
    // Проверяем, загружен ли flatpickr
    if (typeof flatpickr === 'undefined') {
        console.error('Flatpickr не загружен!');
        return;
    }
    
    // Добавляем CSS стили для полей даты
    addDateFieldStyles();
    
    initializeCalendarLocalization();
});

function addDateFieldStyles() {
    const style = document.createElement('style');
    style.textContent = `
        /* Стили для всех полей даты */
        input[type="date"] {
            cursor: pointer !important;
        }
        
        input[type="date"][data-locale] {
            cursor: pointer !important;
        }
        
        /* Стили для обертки календаря */
        .date-picker-wrapper {
            cursor: pointer !important;
        }
        
        .date-picker-wrapper input {
            cursor: pointer !important;
        }
        
        /* Стили для иконки календаря */
        .date-picker-wrapper .calendar-icon {
            cursor: pointer !important;
        }
        
        /* Стили для мобильных устройств */
        @media (max-width: 768px) {
            input[type="date"] {
                cursor: pointer !important;
            }
            
            input[type="date"][data-locale] {
                cursor: pointer !important;
            }
            
            .date-picker-wrapper {
                cursor: pointer !important;
            }
        }
        
        /* Стили для focus состояния */
        input[type="date"]:focus {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            outline: none !important;
        }
        
        input[type="date"][data-locale]:focus {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            outline: none !important;
        }
        
        .date-picker-wrapper input:focus {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            outline: none !important;
        }
    `;
    document.head.appendChild(style);
}

function initializeCalendarLocalization() {
    // Локализации для flatpickr
    const locales = {
        ru: {
            weekdays: {
                shorthand: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                longhand: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота']
            },
            months: {
                shorthand: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                longhand: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
            },
            firstDayOfWeek: 1,
            rangeSeparator: ' до ',
            time_24hr: true
        },
        en: {
            weekdays: {
                shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                longhand: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
            },
            firstDayOfWeek: 0,
            rangeSeparator: ' to ',
            time_24hr: false
        },
        uk: {
            weekdays: {
                shorthand: ['Нд', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                longhand: ['Неділя', 'Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П\'ятниця', 'Субота']
            },
            months: {
                shorthand: ['Січ', 'Лют', 'Бер', 'Кві', 'Тра', 'Чер', 'Лип', 'Сер', 'Вер', 'Жов', 'Лис', 'Гру'],
                longhand: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень']
            },
            firstDayOfWeek: 1,
            rangeSeparator: ' до ',
            time_24hr: true
        }
    };

    // Регистрируем локализации
    Object.keys(locales).forEach(locale => {
        flatpickr.localize(locales[locale]);
    });

    // Функция для инициализации календаря
    function initializeDatePicker(input) {
        if (!input || input.type !== 'date') return;
        
        // Проверяем, не был ли уже инициализирован flatpickr для этого поля
        if (input._flatpickr || input.dataset.flatpickrInitialized) return;
        
        let locale = input.dataset.locale || 'en';
        
        // Маппинг локалей для flatpickr
        const localeMapping = {
            'ru-RU': 'ru',
            'ru': 'ru',
            'en-US': 'en',
            'en': 'en',
            'ua': 'uk',
            'uk': 'uk'
        };
        
        locale = localeMapping[locale] || 'en';
        const currentValue = input.value;
        
        // Создаем новый input для flatpickr
        const wrapper = document.createElement('div');
        wrapper.className = 'date-picker-wrapper';
        wrapper.style.position = 'relative';
        wrapper.style.cursor = 'pointer';
        
        const flatpickrInput = document.createElement('input');
        flatpickrInput.type = 'text';
        flatpickrInput.className = input.className;
        flatpickrInput.placeholder = input.placeholder;
        flatpickrInput.style.width = '100%';
        flatpickrInput.style.padding = '8px 12px';
        flatpickrInput.style.border = '1px solid #ddd';
        flatpickrInput.style.borderRadius = '4px';
        flatpickrInput.style.fontSize = '14px';
        flatpickrInput.style.cursor = 'pointer';
        
        // Добавляем иконку календаря
        const calendarIcon = document.createElement('span');
        calendarIcon.innerHTML = '📅';
        calendarIcon.className = 'calendar-icon';
        calendarIcon.style.position = 'absolute';
        calendarIcon.style.right = '10px';
        calendarIcon.style.top = '50%';
        calendarIcon.style.transform = 'translateY(-50%)';
        calendarIcon.style.pointerEvents = 'none';
        calendarIcon.style.fontSize = '16px';
        calendarIcon.style.color = '#666';
        calendarIcon.style.cursor = 'pointer';
        
        wrapper.appendChild(flatpickrInput);
        wrapper.appendChild(calendarIcon);
        
        // Заменяем оригинальный input
        input.parentNode.insertBefore(wrapper, input);
        input.style.display = 'none';
        input.style.visibility = 'hidden';
        input.style.position = 'absolute';
        input.style.left = '-9999px';
        
        // Инициализируем flatpickr
        const fp = flatpickr(flatpickrInput, {
            locale: locale,
            dateFormat: 'Y-m-d',
            allowInput: true,
            clickOpens: true,
            closeOnSelect: true,
            disableMobile: false,
            onChange: function(selectedDates, dateStr) {
                input.value = dateStr;
                // Вызываем событие change на оригинальном input
                const event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            }
        });
        
        // Устанавливаем значение если есть, иначе устанавливаем сегодняшнюю дату
        if (currentValue) {
            fp.setDate(currentValue);
        } else {
            // Устанавливаем сегодняшнюю дату
            const today = new Date();
            const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0'); // формат YYYY-MM-DD
            fp.setDate(todayStr);
            input.value = todayStr;
        }
        
        // Сохраняем ссылку на flatpickr для возможного использования
        input._flatpickr = fp;
        input.dataset.flatpickrInitialized = 'true';
    }

    // Инициализируем все существующие поля даты
    document.querySelectorAll('input[type="date"][data-locale]').forEach(initializeDatePicker);
    
    // Наблюдатель за изменениями DOM для новых полей даты
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    // Проверяем сам узел
                    if (node.matches && node.matches('input[type="date"][data-locale]')) {
                        initializeDatePicker(node);
                    }
                    // Проверяем дочерние элементы
                    if (node.querySelectorAll) {
                        node.querySelectorAll('input[type="date"][data-locale]').forEach(initializeDatePicker);
                    }
                }
            });
        });
    });
    
    // Начинаем наблюдение
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Глобальная функция для инициализации новых полей даты
    window.initializeDatePicker = initializeDatePicker;
    
    // Глобальная функция для установки сегодняшней даты в поле
    window.setTodayDate = function(input) {
        if (!input) return;
        
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0]; // формат YYYY-MM-DD
        
        // Ищем поле flatpickr в обертке (новое поле, которое видит пользователь)
        const wrapper = input.parentNode;
        if (wrapper && wrapper.classList.contains('date-picker-wrapper')) {
            const flatpickrInput = wrapper.querySelector('input[type="text"]');
            if (flatpickrInput && flatpickrInput._flatpickr) {
                flatpickrInput._flatpickr.setDate(todayStr);
                // Также устанавливаем значение в скрытое поле
                input.value = todayStr;
            }
        } else {
            // Если это обычный input[type="date"] без flatpickr
            if (input.type === 'date') {
                input.value = todayStr;
            }
            
            // Если у поля есть flatpickr экземпляр
            if (input._flatpickr) {
                input._flatpickr.setDate(todayStr);
            }
        }
        
        // Вызываем событие change
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    };
    
    // Глобальная функция для установки сегодняшней даты во всех полях даты в модальном окне
    window.setTodayDateInModal = function(modalElement) {
        if (!modalElement) return;
        
        const dateInputs = modalElement.querySelectorAll('input[type="date"][data-locale]');
        dateInputs.forEach(input => {
            if (!input.value) {
                setTodayDate(input);
            }
        });
    };
    
    // Функция для форматирования даты в соответствии с локалью
    window.formatDateForLocale = function(dateString, locale) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        
        const options = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        };
        
        return date.toLocaleDateString(locale, options);
    };
    
    // Функция для форматирования даты для input[type="date"]
    window.formatDateForInput = function(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        return `${year}-${month}-${day}`;
    };
} 