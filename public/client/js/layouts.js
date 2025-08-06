// ===== СКРИПТЫ ДЛЯ LAYOUT'ОВ =====

// ===== СКРИПТЫ ДЛЯ APP.BLADE.PHP =====

/**
 * Инициализация скриптов для app.blade.php
 */
function initializeAppLayout() {
    // Автопрокрутка к активному элементу меню
    setTimeout(function() {
        var activeMenuItem = document.querySelector('#left-panel .navbar-nav li.active > a');
        var containers = [
            document.querySelector('#left-panel .main-menu'),
            document.querySelector('#left-panel .navbar-nav'),
            document.getElementById('left-panel')
        ];
        var scrollContainer = containers.find(function(el) {
            return el && el.scrollHeight > el.clientHeight;
        });
        if (activeMenuItem && scrollContainer) {
            var itemRect = activeMenuItem.getBoundingClientRect();
            var containerRect = scrollContainer.getBoundingClientRect();
            var offset = itemRect.top - containerRect.top;
            var itemHeight = activeMenuItem.offsetHeight;
            var containerHeight = scrollContainer.clientHeight;
            scrollContainer.scrollTop += offset - (containerHeight / 2) + (itemHeight / 2);
        }
    }, 200);
}

// ===== СКРИПТЫ ДЛЯ AUTH.BLADE.PHP =====

/**
 * Инициализация скриптов для auth.blade.php
 */
function initializeAuthLayout() {
    // Здесь можно добавить специфичные скрипты для страниц авторизации

}

// ===== ОБЩИЕ ФУНКЦИИ =====

/**
 * Инициализация всех layout'ов
 */
function initializeLayouts() {
    // Определяем какой layout используется
    const body = document.body;
    const pageData = body.getAttribute('data-page');
    
    if (pageData) {
        // Это app.blade.php layout
        initializeAppLayout();
    } else {
        // Это auth.blade.php layout
        initializeAuthLayout();
    }
}

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    initializeLayouts();
}); 