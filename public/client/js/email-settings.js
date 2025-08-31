// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ НАСТРОЕК EMAIL =====

// AJAX сохранение настроек Email
function initializeEmailSettings() {
    const form = document.getElementById('emailSettingsForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Показываем индикатор загрузки
        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Сохранение...';
        submitButton.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.showNotification('success', data.message);
            } else {
                if (data.errors) {
                    // Показываем ошибки валидации
                    let errorMessage = 'Ошибки валидации:\n';
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += data.errors[field].join('\n') + '\n';
                    });
                    window.showNotification('error', errorMessage);
                } else {
                    window.showNotification('error', data.message || 'Ошибка при сохранении настроек');
                }
            }
        })
        .catch(error => {
            window.showNotification('error', 'Ошибка при сохранении настроек: ' + error.message);
        })
        .finally(() => {
            // Восстанавливаем кнопку
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });
}

// Функция тестирования подключения
function testEmailConnection() {
    // Проверяем заполнение обязательных полей
    const host = document.getElementById('email_host').value.trim();
    const port = document.getElementById('email_port').value.trim();
    const username = document.getElementById('email_username').value.trim();
    const password = document.getElementById('email_password').value.trim();
    const encryption = document.getElementById('email_encryption').value;
    
    if (!host || !port || !username || !password) {
        window.showNotification('error', 'Пожалуйста, заполните все обязательные поля для тестирования');
        return;
    }
    
    const formData = new FormData();
    formData.append('email_host', host);
    formData.append('email_port', port);
    formData.append('email_username', username);
    formData.append('email_password', password);
    formData.append('email_encryption', encryption);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('/client/email-settings/test', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showNotification('success', data.message);
        } else {
            window.showNotification('error', data.message);
        }
    })
    .catch(error => {
        window.showNotification('error', 'Ошибка при тестировании: ' + error.message);
    });
}

// Функция показа инструкций
function showEmailInstructions() {
    const modal = document.getElementById('emailInstructionsModal');
    const content = document.getElementById('emailInstructionsContent');
    
    if (!modal || !content) {
        // Modal elements not found
        return;
    }
    

    content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    modal.style.display = 'block';

    const url = 'email-settings/instructions';
    

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {

        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {

        
        if (data.instructions && Object.keys(data.instructions).length > 0) {
            let html = '<div class="email-instructions-list">';
            Object.values(data.instructions).forEach((instruction, index) => {
                html += `<div class="instruction-item">${instruction}</div>`;
            });
            html += '</div>';
            content.innerHTML = html;
        } else {
            content.innerHTML = '<div class="alert alert-warning">Инструкции не найдены</div>';
        }
    })
    .catch(error => {
        // Error loading instructions
        content.innerHTML = '<div class="alert alert-danger">Ошибка при загрузке инструкций: ' + error.message + '</div>';
    });
}

// Функция закрытия модального окна инструкций
function closeEmailInstructionsModal() {
    document.getElementById('emailInstructionsModal').style.display = 'none';
}

// Синхронизация переключателей для десктопа и мобильных
function initializeSwitches() {
    const desktopSwitch = document.getElementById('email_notifications_enabled_desktop');
    const mobileSwitch = document.getElementById('email_notifications_enabled_mobile');
    
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
    const modal = document.getElementById('emailInstructionsModal');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeEmailInstructionsModal();
            }
        });
        
        // Добавляем обработчик для закрытия по Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modal.style.display === 'block') {
                closeEmailInstructionsModal();
            }
        });
    }
}

// Инициализация всех функций при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    initializeEmailSettings();
    initializeSwitches();
    initializeModal();
}); 