// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ НАСТРОЕК TELEGRAM =====

// AJAX сохранение настроек Telegram
function initializeTelegramSettings() {
    const form = document.getElementById('telegramSettingsForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Проверяем обязательные поля
        const botToken = document.getElementById('telegram_bot_token').value.trim();
        const chatId = document.getElementById('telegram_chat_id').value.trim();
        
        if (!botToken || !chatId) {
            window.showNotification('error', 'Пожалуйста, заполните все обязательные поля');
            return;
        }

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<span class="loader"></span> Сохранение...';
        submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                window.showNotification('success', data.message || 'Настройки Telegram успешно сохранены');
            } else {
                window.showNotification('error', data.message || 'Ошибка при сохранении настроек');
            }
        })
        .catch(error => {
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
                window.showNotification('error', error.message || 'Ошибка при сохранении настроек');
            }
        })
        .finally(() => {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });
}

// Функция тестирования подключения
function testConnection() {
    // Проверяем обязательные поля
    const botToken = document.getElementById('telegram_bot_token').value.trim();
    const chatId = document.getElementById('telegram_chat_id').value.trim();
    
    if (!botToken || !chatId) {
        window.showNotification('error', 'Пожалуйста, заполните все обязательные поля для тестирования');
        return;
    }

    const resultDiv = document.getElementById('testResult');
    resultDiv.innerHTML = '<div class="alert alert-info">Тестирование подключения...</div>';
    resultDiv.style.display = 'block';

    fetch('/client/telegram-settings/test', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        } else {
            resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<div class="alert alert-danger">Ошибка при тестировании подключения</div>';
        console.error('Error:', error);
    });
}

// Функция показа инструкций
function showInstructions() {
    const modal = document.getElementById('instructionsModal');
    const content = document.getElementById('instructionsContent');
    
    content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    modal.style.display = 'block';

    fetch('/client/telegram-settings/instructions')
        .then(response => response.json())
        .then(data => {
            let html = '<div class="telegram-instructions-list">';
            Object.values(data.instructions).forEach((instruction, index) => {
                html += `<div class="instruction-item">${instruction}</div>`;
            });
            html += '</div>';
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Ошибка при загрузке инструкций</div>';
            console.error('Error:', error);
        });
}

// Функция закрытия модального окна инструкций
function closeInstructionsModal() {
    const modal = document.getElementById('instructionsModal');
    modal.style.display = 'none';
}

// Синхронизация переключателей для десктопа и мобильных
function initializeSwitches() {
    const desktopSwitch = document.getElementById('telegram_notifications_enabled_desktop');
    const mobileSwitch = document.getElementById('telegram_notifications_enabled_mobile');
    
    if (desktopSwitch && mobileSwitch) {
        // Синхронизация с десктопа на мобильный
        desktopSwitch.addEventListener('change', function() {
            mobileSwitch.checked = this.checked;
        });
        
        // Синхронизация с мобильного на десктоп
        mobileSwitch.addEventListener('change', function() {
            desktopSwitch.checked = this.checked;
        });
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
    initializeTelegramSettings();
    initializeSwitches();
    initializeModal();
}); 