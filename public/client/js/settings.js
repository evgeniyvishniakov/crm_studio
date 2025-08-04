// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ НАСТРОЕК =====

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ НАСТРОЕК =====

/**
 * Переключение между вкладками настроек
 * @param {string} tabName - Название вкладки
 */
function switchTab(tabName) {
    // Убираем активный класс со всех кнопок
    document.querySelectorAll('.dashboard-container .tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Скрываем все панели
    document.querySelectorAll('.dashboard-container .settings-pane').forEach(pane => {
        pane.style.display = 'none';
    });
    
    // Активируем нужную кнопку
    const activeButton = document.querySelector(`.dashboard-container .tab-button[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
    
    // Показываем нужную панель
    const activePane = document.getElementById(`tab-${tabName}`);
    if (activePane) {
        activePane.style.display = '';
    }
    
    // Обновляем hash в URL
    window.location.hash = tabName;
}

/**
 * Инициализация вкладок настроек
 */
function initSettingsTabs() {
    // Добавляем обработчики для всех кнопок вкладок
    document.querySelectorAll('.dashboard-container .tab-button').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });
    
    // Инициализация: показываем вкладку из hash или профиль по умолчанию
    const hash = window.location.hash.replace('#', '');
    if (hash) {
        switchTab(hash);
    } else {
        // По умолчанию показываем профиль
        switchTab('profile');
    }
    
    // Обработчик изменения hash
    window.addEventListener('hashchange', function() {
        const newHash = window.location.hash.replace('#', '');
        if (newHash) {
            switchTab(newHash);
        }
    });
}

// ===== ФУНКЦИИ ДЛЯ ПРОФИЛЯ =====

/**
 * Обработка формы профиля
 */
function initProfileForm() {
    const profileForm = document.getElementById('profileForm');
    if (!profileForm) return;
    
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(profileForm);
        const submitBtn = profileForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
        submitBtn.disabled = true;
        
        fetch(profileForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(response) {
            if (!response.ok) {
                return response.json().then(function(data) { throw data; });
            }
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                window.showNotification('success', data.message || 'Изменения успешно сохранены');
            } else {
                window.showNotification('error', data.message || 'Ошибка сохранения');
            }
        })
        .catch(function(error) {
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
                window.showNotification('error', 'Ошибки валидации');
            } else {
                window.showNotification('error', error.message || 'Ошибка сохранения');
            }
        })
        .finally(function() {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });
}

// ===== ФУНКЦИИ ДЛЯ БЕЗОПАСНОСТИ =====

/**
 * Обработка формы смены email
 */
function initChangeEmailForm() {
    const form = document.getElementById('change-email-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(response) {
            if (response.ok) return response.json();
            return response.json().then(function(data) { throw data; });
        })
        .then(function(data) {
            form.reset();
            window.showNotification('success', 'Письмо с подтверждением отправлено');
        })
        .catch(function(error) {
            let msg = 'Ошибка отправки';
            if (error && error.errors) {
                msg = Object.values(error.errors).join('<br>');
            }
            window.showNotification('error', msg);
        });
    });
}

// ===== ФУНКЦИИ ДЛЯ ЯЗЫКА И ВАЛЮТЫ =====

/**
 * Автоматическое сохранение настроек языка и валюты
 * @param {string} fieldName - Название поля
 * @param {string} value - Значение поля
 */
function autoSaveLanguageCurrency(fieldName, value) {
    const formData = new FormData();
    formData.append(fieldName, value);
    
    // Добавляем текущие значения других полей
    const languageSelect = document.querySelector('select[name="language_id"]');
    const bookingLanguageSelect = document.querySelector('select[name="booking_language_id"]');
    const currencySelect = document.querySelector('select[name="currency_id"]');
    
    if (languageSelect && fieldName !== 'language_id') {
        formData.append('language_id', languageSelect.value);
    }
    if (bookingLanguageSelect && fieldName !== 'booking_language_id') {
        formData.append('booking_language_id', bookingLanguageSelect.value);
    }
    if (currencySelect && fieldName !== 'currency_id') {
        formData.append('currency_id', currencySelect.value);
    }
    
    fetch('/settings/update-language-currency', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(data) { throw data; });
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            let message = '';
            
            if (fieldName === 'language_id' && data.language) {
                message = 'Язык изменен на: ' + data.language.name + '. Перезагрузите страницу для применения.';
            } else if (fieldName === 'booking_language_id' && data.booking_language) {
                message = 'Язык веб-записи изменен на: ' + data.booking_language.name + '. Перезагрузите страницу для применения.';
            } else if (fieldName === 'currency_id' && data.currency) {
                message = 'Валюта изменена на: ' + data.currency.code + '. Перезагрузите страницу для применения.';
            }
            
            if (message) {
                window.showNotification('success', message);
            }
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        window.showNotification('error', 'Ошибка сохранения');
    });
}

/**
 * Инициализация селекторов языка и валюты
 */
function initLanguageCurrencySelectors() {
    const languageSelect = document.querySelector('select[name="language_id"]');
    const bookingLanguageSelect = document.querySelector('select[name="booking_language_id"]');
    const currencySelect = document.querySelector('select[name="currency_id"]');
    
    if (languageSelect) {
        languageSelect.addEventListener('change', function() {
            autoSaveLanguageCurrency('language_id', this.value);
        });
    }
    
    if (bookingLanguageSelect) {
        bookingLanguageSelect.addEventListener('change', function() {
            autoSaveLanguageCurrency('booking_language_id', this.value);
        });
    }
    
    if (currencySelect) {
        currencySelect.addEventListener('change', function() {
            autoSaveLanguageCurrency('currency_id', this.value);
        });
    }
}

// ===== ФУНКЦИИ ДЛЯ КАРТЫ =====

/**
 * Извлечение координат из URL карты
 * @param {string} url - URL карты
 */
function extractCoordinatesFromUrl(url) {
    const mapLatitudeInput = document.getElementById('map_latitude');
    const mapLongitudeInput = document.getElementById('map_longitude');
    const mapZoomInput = document.getElementById('map_zoom');
    const mapPreview = document.getElementById('map_preview');
    
    // Формат: https://maps.app.goo.gl/UMeU52GP5ZWVxx4x5
    if (url.includes('maps.app.goo.gl/')) {
        showMapPlaceholder('Короткие ссылки Google Maps пока не поддерживаются. Используйте полную ссылку.');
        return;
    }
    
    // Формат: https://www.google.com/maps?q=55.7558,37.6176
    let match = url.match(/[?&]q=([^&]+)/);
    if (match) {
        const coords = match[1].split(',');
        if (coords.length >= 2) {
            const lat = parseFloat(coords[0]);
            const lng = parseFloat(coords[1]);
            if (!isNaN(lat) && !isNaN(lng)) {
                updateCoordinates(lat, lng, 15);
                showMapPreview(lat, lng, 15);
                return;
            }
        }
    }
    
    // Формат: https://www.google.com/maps/place/.../@55.7558,37.6176,15z
    match = url.match(/@([^,]+),([^,]+),(\d+)z/);
    if (match) {
        const lat = parseFloat(match[1]);
        const lng = parseFloat(match[2]);
        const zoom = parseInt(match[3]);
        if (!isNaN(lat) && !isNaN(lng)) {
            updateCoordinates(lat, lng, zoom);
            showMapPreview(lat, lng, zoom);
            return;
        }
    }
    
    // Формат: https://www.google.com/maps?ll=55.7558,37.6176&z=15
    match = url.match(/[?&]ll=([^&]+)/);
    if (match) {
        const coords = match[1].split(',');
        if (coords.length >= 2) {
            const lat = parseFloat(coords[0]);
            const lng = parseFloat(coords[1]);
            let zoom = 15;
            
            const zoomMatch = url.match(/[?&]z=(\d+)/);
            if (zoomMatch) {
                zoom = parseInt(zoomMatch[1]);
            }
            
            if (!isNaN(lat) && !isNaN(lng)) {
                updateCoordinates(lat, lng, zoom);
                showMapPreview(lat, lng, zoom);
                return;
            }
        }
    }
    
    showMapPlaceholder('Не удалось извлечь координаты из ссылки. Проверьте формат ссылки.');
}

/**
 * Обновление координат в скрытых полях
 * @param {number} lat - Широта
 * @param {number} lng - Долгота
 * @param {number} zoom - Масштаб
 */
function updateCoordinates(lat, lng, zoom) {
    const mapLatitudeInput = document.getElementById('map_latitude');
    const mapLongitudeInput = document.getElementById('map_longitude');
    const mapZoomInput = document.getElementById('map_zoom');
    
    if (mapLatitudeInput) mapLatitudeInput.value = lat;
    if (mapLongitudeInput) mapLongitudeInput.value = lng;
    if (mapZoomInput) mapZoomInput.value = zoom;
}

/**
 * Показ предварительного просмотра карты
 * @param {number} lat - Широта
 * @param {number} lng - Долгота
 * @param {number} zoom - Масштаб
 */
function showMapPreview(lat, lng, zoom) {
    const mapPreview = document.getElementById('map_preview');
    if (!mapPreview) return;
    
    const embedUrl = `https://maps.google.com/maps?q=${lat},${lng}&z=${zoom}&output=embed`;
    mapPreview.innerHTML = `
        <iframe 
            width="100%" 
            height="100%" 
            frameborder="0" 
            scrolling="no" 
            marginheight="0" 
            marginwidth="0"
            src="${embedUrl}"
            style="border: none; border-radius: 8px;">
        </iframe>
    `;
}

/**
 * Показ заглушки карты
 * @param {string} message - Сообщение для отображения
 */
function showMapPlaceholder(message = 'Вставьте ссылку на Google Maps для предварительного просмотра') {
    const mapPreview = document.getElementById('map_preview');
    if (!mapPreview) return;
    
    mapPreview.innerHTML = `
        <div class="text-center text-muted">
            <i class="fas fa-map fa-3x mb-3"></i>
            <p>${message}</p>
        </div>
    `;
}

/**
 * Инициализация обработки карты
 */
function initMapHandling() {
    const mapUrlInput = document.getElementById('map_url');
    const mapLatitudeInput = document.getElementById('map_latitude');
    const mapLongitudeInput = document.getElementById('map_longitude');
    const mapZoomInput = document.getElementById('map_zoom');

    if (mapUrlInput) {
        mapUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                extractCoordinatesFromUrl(url);
            } else {
                showMapPlaceholder();
            }
        });

        // Инициализация при загрузке страницы
        if (mapLatitudeInput.value && mapLongitudeInput.value) {
            showMapPreview(mapLatitudeInput.value, mapLongitudeInput.value, mapZoomInput.value);
        }
    }
}

// ===== ФУНКЦИИ ДЛЯ АККОРДЕОНА =====

/**
 * Инициализация аккордеона настроек
 */
function initSettingsAccordion() {
    document.querySelectorAll('.settings-accordion .accordion-header').forEach(header => {
        header.addEventListener('click', function() {
            const body = this.nextElementSibling;
            body.classList.toggle('open');
        });
    });
}

// ===== ОБРАБОТКА ЗАГРУЗКИ ФАЙЛОВ =====

/**
 * Инициализация загрузки логотипа
 */
function initLogoUpload() {
    const logoInput = document.getElementById('logo-input');
    if (logoInput) {
        logoInput.addEventListener('change', function() {
            const filename = this.files[0]?.name || '';
            const filenameSpan = document.getElementById('logo-filename');
            if (filenameSpan) {
                filenameSpan.textContent = filename;
            }
        });
    }
}

// ===== ОСНОВНАЯ ИНИЦИАЛИЗАЦИЯ =====

/**
 * Инициализация всех функций настроек
 */
function initSettings() {
    // Инициализация вкладок
    initSettingsTabs();
    
    // Инициализация форм
    initProfileForm();
    initChangeEmailForm();
    
    // Инициализация селекторов языка и валюты
    initLanguageCurrencySelectors();
    
    // Инициализация карты
    initMapHandling();
    
    // Инициализация аккордеона
    initSettingsAccordion();
    
    // Инициализация загрузки файлов
    initLogoUpload();
}

// Запуск инициализации при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    initSettings();
}); 