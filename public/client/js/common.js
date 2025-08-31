// ===== ОБЩИЙ ФАЙЛ СКРИПТОВ ДЛЯ CRM =====

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ РАБОТЫ С ОШИБКАМИ =====

/**
 * Очистка ошибок в форме
 * @param {string} formId - ID формы (по умолчанию 'addForm')
 */
function clearErrors(formId = 'addForm') {
    const form = document.getElementById(formId);
    if (form) {
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        form.querySelectorAll('.has-error').forEach(el => {
            el.classList.remove('has-error');
        });
    }
}

/**
 * Отображение ошибок в форме
 * @param {object} errors - Объект с ошибками
 * @param {string} formId - ID формы (по умолчанию 'addForm')
 */
function showErrors(errors, formId = 'addForm') {
    clearErrors(formId);

    Object.entries(errors).forEach(([field, messages]) => {
        const input = document.querySelector(`#${formId} [name="${field}"]`);
        if (input) {
            const inputGroup = input.closest('.form-group');
            inputGroup.classList.add('has-error');

            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;

            inputGroup.appendChild(errorElement);
        }
    });
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ МОБИЛЬНОГО ПРЕДСТАВЛЕНИЯ =====

/**
 * Переключение между таблицей и карточками на мобильных устройствах
 * @param {string} tableSelector - Селектор таблицы
 * @param {string} cardsSelector - Селектор карточек
 * @param {string} paginationSelector - Селектор пагинации
 * @param {string} mobilePaginationSelector - Селектор мобильной пагинации
 */
function toggleMobileView(tableSelector = '.table-wrapper', cardsSelector = null, paginationSelector = null, mobilePaginationSelector = null) {
    const tableWrapper = document.querySelector(tableSelector);
    const cards = cardsSelector ? document.querySelector(cardsSelector) : null;
    const pagination = paginationSelector ? document.querySelector(paginationSelector) : null;
    const mobilePagination = mobilePaginationSelector ? document.querySelector(mobilePaginationSelector) : null;

    if (window.innerWidth <= 768) {
        // На мобильных устройствах показываем карточки
        if (tableWrapper) tableWrapper.style.display = 'none';
        if (pagination) pagination.style.display = 'none';
        if (cards) cards.style.display = 'block';
        if (mobilePagination) mobilePagination.style.display = 'block';
    } else {
        // На десктопе показываем таблицу
        if (tableWrapper) tableWrapper.style.display = 'block';
        if (pagination) pagination.style.display = 'block';
        if (cards) cards.style.display = 'none';
        if (mobilePagination) mobilePagination.style.display = 'none';
    }
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ МОДАЛЬНЫХ ОКОН =====

/**
 * Открытие модального окна
 * @param {string} modalId - ID модального окна
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }
}

/**
 * Закрытие модального окна
 * @param {string} modalId - ID модального окна
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Закрытие модального окна при клике вне его
 */
function setupModalCloseOnOutsideClick() {
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            // Игнорируем модальные окна услуг и категорий
            if (modal.id === 'addServiceModal' || modal.id === 'editServiceModal') {
                return;
            }
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    };
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ ПОДТВЕРЖДЕНИЯ УДАЛЕНИЯ =====

/**
 * Подтверждение удаления элемента
 * @param {number} id - ID элемента для удаления
 * @param {string} modalId - ID модального окна подтверждения
 * @param {function} deleteFunction - Функция удаления
 */
function confirmDelete(id, modalId = 'confirmationModal', deleteFunction = null) {
    window.currentDeleteId = id;
    openModal(modalId);
    
    if (deleteFunction) {
        window.deleteFunction = deleteFunction;
    }
}

/**
 * Выполнение удаления после подтверждения
 * @param {string} url - URL для удаления
 * @param {string} successMessage - Сообщение об успехе
 * @param {string} errorMessage - Сообщение об ошибке
 * @param {function} callback - Функция обратного вызова после удаления
 */
function executeDelete(url, successMessage, errorMessage, callback = null) {
    if (!window.currentDeleteId) return;

    const row = document.getElementById(`row-${window.currentDeleteId}`);
    const card = document.getElementById(`card-${window.currentDeleteId}`);
    
    if (row) row.classList.add('row-deleting');
    if (card) card.classList.add('row-deleting');

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error(errorMessage);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.showNotification('success', successMessage);
            if (callback) callback();
        }
    })
    .catch(error => {
        window.showNotification('error', errorMessage);
    })
    .finally(() => {
        closeModal('confirmationModal');
        window.currentDeleteId = null;
    });
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ РАБОТЫ С ФОРМАМИ =====

/**
 * Отправка формы через AJAX
 * @param {string} formId - ID формы
 * @param {string} url - URL для отправки
 * @param {string} method - HTTP метод (по умолчанию 'POST')
 * @param {function} successCallback - Функция обратного вызова при успехе
 * @param {function} errorCallback - Функция обратного вызова при ошибке
 */
function submitForm(formId, url, method = 'POST', successCallback = null, errorCallback = null) {
    const form = document.getElementById(formId);
    if (!form) return;

    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.innerHTML = '<span class="loader"></span> Отправка...';
        submitBtn.disabled = true;
    }

    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (successCallback) successCallback(data);
        } else {
            if (data.errors && typeof data.errors === 'object') {
                showErrors(data.errors, formId);
            }
            if (errorCallback) errorCallback(data);
        }
    })
    .catch(error => {
        // Не выводим в консоль ошибки валидации
        if (!error.errors || Object.keys(error.errors).length === 0) {
            // Ошибка
        }
        if (error && error.errors && typeof error.errors === 'object') {
            showErrors(error.errors, formId);
        }
        if (errorCallback) errorCallback(error);
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        }
    });
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ РАБОТЫ С ИЗОБРАЖЕНИЯМИ =====

/**
 * Открытие модального окна с изображением
 * @param {HTMLElement} imgElement - Элемент изображения
 */
function openImageModal(imgElement) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    if (modal && modalImg) {
        modalImg.src = imgElement.src;
        modal.style.display = "block";
    }
}

/**
 * Закрытие модального окна с изображением
 */
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.style.display = "none";
    }
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ РАБОТЫ С ПОИСКОМ =====

/**
 * Очистка поиска
 * @param {string} searchInputId - ID поля поиска
 * @param {function} reloadFunction - Функция перезагрузки данных
 */
function clearSearch(searchInputId = 'searchInput', reloadFunction = null) {
    const searchInput = document.getElementById(searchInputId);
    if (searchInput) {
        searchInput.value = '';
        if (reloadFunction) reloadFunction();
    }
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ РАБОТЫ С ДАТАМИ =====

/**
 * Форматирование даты и времени
 * @param {string} dateString - Строка с датой
 * @returns {string} Отформатированная дата
 */
function formatDateTime(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return date.toLocaleString('ru-RU', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Форматирование даты
 * @param {string} dateString - Строка с датой
 * @returns {string} Отформатированная дата
 */
function formatDate(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ РАБОТЫ С ЦЕНАМИ =====

/**
 * Форматирование цены
 * @param {number} value - Значение цены
 * @returns {string} Отформатированная цена
 */
function formatPrice(value) {
    if (value === null || value === undefined) return '0';
    const num = parseFloat(value);
    if (num % 1 === 0) {
        return Math.floor(num).toString();
    } else {
        return num.toFixed(2);
    }
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ РАБОТЫ С HTML =====

/**
 * Экранирование HTML
 * @param {string} unsafe - Небезопасная строка
 * @returns {string} Экранированная строка
 */
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ===== ОБЩИЕ ФУНКЦИИ ДЛЯ РАБОТЫ С УВЕДОМЛЕНИЯМИ =====



// ===== ИНИЦИАЛИЗАЦИЯ ОБЩИХ ФУНКЦИЙ =====

/**
 * Инициализация общих функций при загрузке страницы
 */
function initializeCommonFunctions() {
    // Настройка закрытия модальных окон при клике вне их
    setupModalCloseOnOutsideClick();
    
    // Настройка обработчиков для изображений
    document.addEventListener('DOMContentLoaded', function() {
        const productImages = document.querySelectorAll('.product-photo');
        productImages.forEach(img => {
            img.onclick = function() {
                openImageModal(this);
            };
        });
    });
}

// Экспорт функций в глобальную область видимости
window.clearErrors = clearErrors;
window.showErrors = showErrors;
window.toggleMobileView = toggleMobileView;
window.openModal = openModal;
window.closeModal = closeModal;
window.openImageModal = openImageModal;
window.closeImageModal = closeImageModal;
window.escapeHtml = escapeHtml;
window.formatPrice = formatPrice;

// Автоматическая инициализация при загрузке
document.addEventListener('DOMContentLoaded', initializeCommonFunctions); 