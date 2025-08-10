// Универсальная функция для показа уведомлений с красивой анимацией
window.showNotification = function(type, message) {
    // Создаем или находим контейнер для уведомлений
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = 'position: fixed; top: 0; right: 0; z-index: 9999; pointer-events: none;';
        document.body.appendChild(container);
    }
    
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    // Определяем иконку в зависимости от типа
    let icon = '';
    switch(type) {
        case 'success':
            icon = '<i class="fas fa-check-circle notification-icon"></i>';
            break;
        case 'error':
            icon = '<i class="fas fa-exclamation-circle notification-icon"></i>';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-triangle notification-icon"></i>';
            break;
        case 'info':
            icon = '<i class="fas fa-info-circle notification-icon"></i>';
            break;
        default:
            icon = '<i class="fas fa-bell notification-icon"></i>';
    }
    
    // HTML уведомления
    notification.innerHTML = `
        ${icon}
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    // Добавляем в контейнер
    container.appendChild(notification);
    
    // Показываем уведомление
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Автоматически скрываем через 5 секунд
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 800);
        }
    }, 5000);
}; 