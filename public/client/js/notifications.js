// Универсальная функция для показа уведомлений
window.showNotification = function(type, message) {
    // Создаем уникальный ID для каждого уведомления
    const notificationId = 'notification-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    
    // Создаем новый элемент уведомления
    const notification = document.createElement('div');
    notification.id = notificationId;
    notification.className = `notification ${type} show shake`;
    
    const icon = type === 'success'
        ? '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>'
        : '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';
    
    notification.innerHTML = `
        <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
            ${icon}
        </svg>
        <span class="notification-message">${message}</span>
        <button class="notification-close" onclick="this.parentElement.classList.remove('show'); setTimeout(() => { if(this.parentElement.parentNode) this.parentElement.parentNode.removeChild(this.parentElement); }, 400);">×</button>
    `;
    
                    // Добавляем уведомление в body
                document.body.appendChild(notification);

                // Позиционируем уведомление
                const existingNotifications = document.querySelectorAll('.notification');
                const notificationIndex = existingNotifications.length - 1;
                if (notificationIndex > 0) {
                    notification.style.top = (20 + notificationIndex * 70) + 'px';
                }

                // Небольшая задержка для начала анимации
                setTimeout(() => {
                    notification.classList.add('show');
                }, 50);

                // Обработчик анимации
                notification.addEventListener('animationend', function handler() {
                    notification.classList.remove('shake');
                    notification.removeEventListener('animationend', handler);
                });

                // Автоматически скрываем уведомление через 3 секунды
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.classList.remove('show');
                        // Удаляем элемент после завершения анимации скрытия
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 800);
                    }
                }, 3000);
}; 