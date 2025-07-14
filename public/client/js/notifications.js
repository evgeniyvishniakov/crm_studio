// Универсальная функция для показа уведомлений
window.showNotification = function(type, message) {
    let notification = document.getElementById('notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        document.body.appendChild(notification);
    }
    notification.className = `notification ${type} show shake`;
    const icon = type === 'success'
        ? '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>'
        : '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';
    notification.innerHTML = `
        <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
            ${icon}
        </svg>
        <span class="notification-message">${message}</span>
    `;
    notification.addEventListener('animationend', function handler() {
        notification.classList.remove('shake');
        notification.removeEventListener('animationend', handler);
    });
    setTimeout(() => {
        notification.className = `notification ${type}`;
    }, 3000);
}; 