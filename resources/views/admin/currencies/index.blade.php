@extends('admin.layouts.app')

@section('title', 'Управление валютами - Админ')
@section('page-title', 'Управление валютами')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Список валют</h5>
                <div>
                    <button class="btn btn-success me-2" onclick="refreshClientCurrencies()">
                        <i class="fas fa-sync-alt me-2"></i>Обновить в клиентах
                    </button>
                    <button class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Добавить валюту
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Код</th>
                                <th>Название</th>
                                <th>Символ</th>
                                <th>Позиция</th>
                                <th>Десятичные знаки</th>
                                <th>Статус</th>
                                <th>По умолчанию</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="currenciesTableBody">
                            @foreach($currencies as $currency)
                            <tr id="currency-{{ $currency->id }}">
                                <td><strong>{{ $currency->code }}</strong></td>
                                <td>{{ $currency->name }}</td>
                                <td>{{ $currency->symbol }}</td>
                                <td>{{ $currency->symbol_position === 'before' ? 'Перед' : 'После' }}</td>
                                <td>{{ $currency->decimal_places }}</td>
                                <td>
                                    <span class="badge bg-{{ $currency->is_active ? 'success' : 'secondary' }}">
                                        {{ $currency->is_active ? 'Активна' : 'Неактивна' }}
                                    </span>
                                </td>
                                <td>
                                    @if($currency->is_default)
                                        <span class="badge bg-primary">По умолчанию</span>
                                    @else
                                        <button class="btn btn-sm btn-outline-primary" onclick="setDefault({{ $currency->id }})">
                                            Установить
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCurrency({{ $currency->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if(!$currency->is_default)
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCurrency({{ $currency->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-{{ $currency->is_active ? 'warning' : 'success' }}" 
                                                onclick="toggleActive({{ $currency->id }})">
                                            <i class="fas fa-{{ $currency->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно создания/редактирования валюты -->
<div class="modal fade" id="currencyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Добавить валюту</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="currencyForm">
                    @csrf
                    <input type="hidden" id="currencyId" name="currency_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Код валюты *</label>
                                <input type="text" class="form-control" id="code" name="code" maxlength="3" required>
                                <div class="form-text">Например: USD, EUR, UAH</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Название *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="form-text">Например: US Dollar, Euro, Украинская гривна</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="symbol" class="form-label">Символ *</label>
                                <input type="text" class="form-control" id="symbol" name="symbol" maxlength="10" required>
                                <div class="form-text">Например: $, €, ₴</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="symbol_position" class="form-label">Позиция символа *</label>
                                <select class="form-select" id="symbol_position" name="symbol_position" required>
                                    <option value="before">Перед числом</option>
                                    <option value="after">После числа</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="decimal_places" class="form-label">Десятичные знаки *</label>
                                <input type="number" class="form-control" id="decimal_places" name="decimal_places" min="0" max="4" value="2" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="decimal_separator" class="form-label">Разделитель десятичных *</label>
                                <input type="text" class="form-control" id="decimal_separator" name="decimal_separator" maxlength="1" value="." required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="thousands_separator" class="form-label">Разделитель тысяч</label>
                                <input type="text" class="form-control" id="thousands_separator" name="thousands_separator" maxlength="1" value="" placeholder="Оставьте пустым для отключения">
                                <div class="form-text">Оставьте пустым, чтобы отключить разделители тысяч (например, 1400 вместо 1,400)</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Активна
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                                    <label class="form-check-label" for="is_default">
                                        По умолчанию
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Предварительный просмотр:</label>
                        <div class="alert alert-info" id="preview">
                            Введите данные для предварительного просмотра
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="saveCurrency()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<script>
let isEditMode = false;

// Обновляем предварительный просмотр при изменении полей
document.addEventListener('DOMContentLoaded', function() {
    
    // Функция для очистки дублированных элементов preview
    function cleanupPreviewElements() {
        const modal = document.getElementById('currencyModal');
        if (modal) {
            const modalBody = modal.querySelector('.modal-body');
            if (modalBody) {
                const existingPreviews = modalBody.querySelectorAll('#preview');
                if (existingPreviews.length > 1) {
                    // Оставляем только первый элемент, удаляем остальные
                    for (let i = 1; i < existingPreviews.length; i++) {
                        const container = existingPreviews[i].closest('.mb-3');
                        if (container) {
                            container.remove();
                        } else {
                            existingPreviews[i].remove();
                        }
                    }
                }
            }
        }
    }
    
    // Очищаем дублированные элементы при загрузке
    cleanupPreviewElements();
    
    // Делаем функции глобальными
    window.openCreateModal = function() {
    isEditMode = false;
    document.getElementById('modalTitle').textContent = 'Добавить валюту';
    document.getElementById('currencyForm').reset();
    document.getElementById('currencyId').value = '';
    
    // Устанавливаем значения по умолчанию для новой валюты
    document.getElementById('symbol_position').value = 'after';
    document.getElementById('decimal_places').value = '2';
    document.getElementById('decimal_separator').value = '.';
    document.getElementById('thousands_separator').value = '';
    document.getElementById('is_active').checked = true;
    document.getElementById('is_default').checked = false;
    
    const modal = new bootstrap.Modal(document.getElementById('currencyModal'));
    modal.show();
        
        // Обновляем предварительный просмотр после показа модального окна
        setTimeout(() => {
            // Проверяем, есть ли элемент preview
            let previewElement = document.getElementById('preview');
            if (!previewElement) {
                const modal = document.getElementById('currencyModal');
                if (modal) {
                    const modalBody = modal.querySelector('.modal-body');
                    if (modalBody) {
                        // Удаляем все существующие элементы preview и их контейнеры
                        const existingPreviews = modalBody.querySelectorAll('#preview');
                        existingPreviews.forEach(el => {
                            const container = el.closest('.mb-3');
                            if (container) {
                                container.remove();
                            } else {
                                el.remove();
                            }
                        });
                        
                        // Создаем новый элемент preview
                        const previewContainer = document.createElement('div');
                        previewContainer.className = 'mb-3';
                        previewContainer.innerHTML = `
                            <label class="form-label">Предварительный просмотр:</label>
                            <div class="alert alert-info" id="preview">Введите данные для предварительного просмотра</div>
                        `;
                        
                        // Добавляем в конец формы
                        const form = modalBody.querySelector('#currencyForm');
                        if (form) {
                            form.appendChild(previewContainer);
                        }
                    }
                }
            }
            
            // Добавляем обработчики событий к полям формы
            addFormEventListeners();
            // Обновляем предварительный просмотр
            updatePreview();
        }, 100);
    };
    
    window.editCurrency = function(id) {
    isEditMode = true;
    document.getElementById('modalTitle').textContent = 'Редактировать валюту';
    
    // Загружаем данные валюты
        fetch(`/panel/currencies/${id}/data`)
        .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const currency = data.currency;
                document.getElementById('currencyId').value = currency.id;
                document.getElementById('code').value = currency.code;
                document.getElementById('name').value = currency.name;
                document.getElementById('symbol').value = currency.symbol;
                document.getElementById('symbol_position').value = currency.symbol_position;
                document.getElementById('decimal_places').value = currency.decimal_places.toString();
                document.getElementById('decimal_separator').value = currency.decimal_separator;
                document.getElementById('thousands_separator').value = currency.thousands_separator;
                
                // Отладочная информация
                
                document.getElementById('is_active').checked = currency.is_active;
                document.getElementById('is_default').checked = currency.is_default;
                
                const modal = new bootstrap.Modal(document.getElementById('currencyModal'));
                modal.show();
                    
                    // Добавляем обработчики событий к полям формы
                    setTimeout(() => {
                        // Проверяем, есть ли элемент preview
                        let previewElement = document.getElementById('preview');
                        if (!previewElement) {
                            const modal = document.getElementById('currencyModal');
                            if (modal) {
                                const modalBody = modal.querySelector('.modal-body');
                                if (modalBody) {
                                    // Удаляем все существующие элементы preview и их контейнеры
                                    const existingPreviews = modalBody.querySelectorAll('#preview');
                                    existingPreviews.forEach(el => {
                                        const container = el.closest('.mb-3');
                                        if (container) {
                                            container.remove();
                                        } else {
                                            el.remove();
                                        }
                                    });
                                    
                                    // Создаем новый элемент preview
                                    const previewContainer = document.createElement('div');
                                    previewContainer.className = 'mb-3';
                                    previewContainer.innerHTML = `
                                        <label class="form-label">Предварительный просмотр:</label>
                                        <div class="alert alert-info" id="preview">Введите данные для предварительного просмотра</div>
                                    `;
                                    
                                    // Добавляем в конец формы
                                    const form = modalBody.querySelector('#currencyForm');
                                    if (form) {
                                        form.appendChild(previewContainer);
                                    }
                                }
                            }
                        }
                        
                        addFormEventListeners();
                        updatePreview();
                    }, 100);
            } else {
                alert('Ошибка загрузки валюты: ' + data.message);
            }
        })
        .catch(error => {
                alert('Ошибка при редактировании валюты: ' + error.message);
        });
    };

    window.saveCurrency = function() {
    const form = document.getElementById('currencyForm');
    
    // Проверяем обязательные поля
    const code = document.getElementById('code').value.trim();
    const name = document.getElementById('name').value.trim();
    const symbol = document.getElementById('symbol').value.trim();
    const symbolPosition = document.getElementById('symbol_position').value;
    const decimalPlaces = document.getElementById('decimal_places').value;
    const decimalSeparator = document.getElementById('decimal_separator').value.trim();
    
    if (!code) {
        alert('Пожалуйста, введите код валюты');
        return;
    }
    if (!name) {
        alert('Пожалуйста, введите название валюты');
        return;
    }
    if (!symbol) {
        alert('Пожалуйста, введите символ валюты');
        return;
    }
    if (!symbolPosition) {
        alert('Пожалуйста, выберите позицию символа');
        return;
    }
    if (decimalPlaces === '' || decimalPlaces === null || decimalPlaces === undefined) {
        alert('Пожалуйста, введите количество десятичных знаков');
        return;
    }
    if (!decimalSeparator) {
        alert('Пожалуйста, введите разделитель десятичных');
        return;
    }
    
    const formData = new FormData(form);
    
    // Добавляем CSRF-токен вручную
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        // Правильно обрабатываем чекбоксы
        const isActiveCheckbox = document.getElementById('is_active');
        const isDefaultCheckbox = document.getElementById('is_default');
        
        // Удаляем старые значения чекбоксов
        formData.delete('is_active');
        formData.delete('is_default');
        
        // Добавляем правильные значения
        formData.append('is_active', isActiveCheckbox.checked ? '1' : '0');
        formData.append('is_default', isDefaultCheckbox.checked ? '1' : '0');
        

    
    const url = isEditMode 
            ? `/panel/currencies/${document.getElementById('currencyId').value}`
            : '/panel/currencies';
    
    const method = isEditMode ? 'PUT' : 'POST';
    
    // Создаем объект с данными
    const data = {
        code: document.getElementById('code').value,
        name: document.getElementById('name').value,
        symbol: document.getElementById('symbol').value,
        symbol_position: document.getElementById('symbol_position').value,
        decimal_places: document.getElementById('decimal_places').value,
        decimal_separator: document.getElementById('decimal_separator').value,
        thousands_separator: document.getElementById('thousands_separator').value,
        is_active: isActiveCheckbox.checked ? '1' : '0',
        is_default: isDefaultCheckbox.checked ? '1' : '0',
        _token: document.querySelector('meta[name="csrf-token"]').content
    };
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        // Обрабатываем валидационные ошибки
                        let errorMessage = 'Ошибки валидации:\n';
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                errorMessage += `- ${data.errors[field].join(', ')}\n`;
                            });
                        }
                        throw new Error(errorMessage);
                    });
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
    .then(data => {
        if (data.success) {
            // Показываем уведомление об успехе
            alert('Валюта успешно обновлена!');
            
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('currencyModal'));
            modal.hide();
            
            // Перезагружаем страницу
            window.location.reload();
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
            alert('Произошла ошибка при сохранении: ' + error.message);
        });
    };
    
    window.deleteCurrency = function(id) {
        // Создаем модальное окно подтверждения
        const confirmModal = document.createElement('div');
        confirmModal.className = 'modal fade';
        confirmModal.id = 'confirmDeleteModal';
        confirmModal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтверждение удаления</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Вы уверены, что хотите удалить эту валюту?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete(${id})">Удалить</button>
                    </div>
                </div>
            </div>
        `;
        
        // Добавляем модальное окно на страницу
        document.body.appendChild(confirmModal);
        
        // Показываем модальное окно
        const modal = new bootstrap.Modal(confirmModal);
        modal.show();
        
        // Удаляем модальное окно после скрытия
        confirmModal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(confirmModal);
        });
    };
    
    window.confirmDelete = function(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Ошибка: CSRF токен не найден');
            return;
        }
        
        // Закрываем модальное окно подтверждения
        const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
        if (confirmModal) {
            confirmModal.hide();
        }
        
        fetch(`/panel/currencies/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка при удалении валюты: ' + error.message);
        });
    };
    
    window.setDefault = function(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Ошибка: CSRF токен не найден');
            return;
        }
        
        fetch(`/panel/currencies/${id}/set-default`, {
        method: 'POST',
        headers: {
                'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json',
        }
    })
    .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
            alert('Ошибка при установке валюты по умолчанию: ' + error.message);
    });
    };

    window.toggleActive = function(id) {
        fetch(`/panel/currencies/${id}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
            alert('Ошибка при переключении активности валюты: ' + error.message);
        });
    };
    
    window.refreshClientCurrencies = function() {
        // Создаем модальное окно подтверждения
        const confirmModal = document.createElement('div');
        confirmModal.className = 'modal fade';
        confirmModal.id = 'confirmRefreshModal';
        confirmModal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Обновление валют в клиентах</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Это обновит валюты во всех открытых клиентских сессиях. Продолжить?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-success" onclick="confirmRefresh()">Обновить</button>
                    </div>
                </div>
            </div>
        `;
        
        // Добавляем модальное окно на страницу
        document.body.appendChild(confirmModal);
        
        // Показываем модальное окно
        const modal = new bootstrap.Modal(confirmModal);
        modal.show();
        
        // Удаляем модальное окно после скрытия
        confirmModal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(confirmModal);
        });
    };
    
    window.confirmRefresh = function() {
        // Закрываем модальное окно подтверждения
        const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmRefreshModal'));
        if (confirmModal) {
            confirmModal.hide();
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Ошибка: CSRF токен не найден');
            return;
        }
        
        // Отправляем AJAX запрос для очистки кэша
        fetch('/panel/currencies/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            }
        }).then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Кэш валют очищен. Клиенты увидят изменения при обновлении страницы.');
            } else {
                alert('Ошибка при очистке кэша: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка при очистке кэша: ' + error.message);
        });
    };
    
    // Функция для обновления предварительного просмотра
    function updatePreview() {
        const symbolElement = document.getElementById('symbol');
        const symbolPositionElement = document.getElementById('symbol_position');
        const decimalPlacesElement = document.getElementById('decimal_places');
        const decimalSeparatorElement = document.getElementById('decimal_separator');
        const thousandsSeparatorElement = document.getElementById('thousands_separator');
        let previewElement = document.getElementById('preview');
        
        // Проверяем, что все элементы существуют
        if (!symbolElement || !symbolPositionElement || !decimalPlacesElement || 
            !decimalSeparatorElement || !thousandsSeparatorElement) {
            return;
        }
        
        // Если элемент preview не найден, попробуем его найти в модальном окне
        if (!previewElement) {
            const modal = document.getElementById('currencyModal');
            if (modal) {
                previewElement = modal.querySelector('#preview');
            }
        }
        
        // Если элемент preview все еще не найден, создаем его заново
        if (!previewElement) {
            const modal = document.getElementById('currencyModal');
            if (modal) {
                const modalBody = modal.querySelector('.modal-body');
                if (modalBody) {
                    // Удаляем все существующие элементы preview и их контейнеры
                    const existingPreviews = modalBody.querySelectorAll('#preview');
                    existingPreviews.forEach(el => {
                        const container = el.closest('.mb-3');
                        if (container) {
                            container.remove();
                        } else {
                            el.remove();
                        }
                    });
                    
                    // Создаем новый элемент preview
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'mb-3';
                    previewContainer.innerHTML = `
                        <label class="form-label">Предварительный просмотр:</label>
                        <div class="alert alert-info" id="preview">Введите данные для предварительного просмотра</div>
                    `;
                    
                    // Добавляем в конец формы
                    const form = modalBody.querySelector('#currencyForm');
                    if (form) {
                        form.appendChild(previewContainer);
                    } else {
                        modalBody.appendChild(previewContainer);
                    }
                    
                    previewElement = modalBody.querySelector('#preview');
                }
            }
        }
        
        if (!previewElement) {
            return;
        }
        
        const symbol = symbolElement.value || '[Символ]';
        const symbolPosition = symbolPositionElement.value || 'after';
        const decimalPlaces = decimalPlacesElement.value || '2';
        const decimalSeparator = decimalSeparatorElement.value || '.';
        const thousandsSeparator = thousandsSeparatorElement.value || '';
        
        try {
        const amount = 1234.56;
        
        // Если разделитель тысяч пустой, не используем группировку
        const useGrouping = thousandsSeparator && thousandsSeparator.trim() !== '';
        
        const formatted = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: parseInt(decimalPlaces),
            maximumFractionDigits: parseInt(decimalPlaces),
            useGrouping: useGrouping
        }).format(amount).replace(/\./g, decimalSeparator).replace(/,/g, thousandsSeparator);
        
        const preview = symbolPosition === 'before' 
            ? `${symbol}${formatted}`
            : `${formatted} ${symbol}`;
            
            previewElement.textContent = `Пример: ${preview}`;
        } catch (error) {
            previewElement.textContent = 'Пример: [Ошибка форматирования]';
    }
}

    // Функция для добавления обработчиков событий к полям формы
    function addFormEventListeners() {
    const fields = ['symbol', 'symbol_position', 'decimal_places', 'decimal_separator', 'thousands_separator'];
    fields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                // Удаляем все старые обработчики
                element.removeEventListener('input', updatePreview);
                element.removeEventListener('change', updatePreview);
                
                // Добавляем обработчики событий
                element.addEventListener('input', function() {
                    updatePreview();
                });
                element.addEventListener('change', function() {
                    updatePreview();
                });
            }
        });
    }
    
    addFormEventListeners();
    
    // Добавляем обработчик события показа модального окна
    const modal = document.getElementById('currencyModal');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function() {
            // Принудительно восстанавливаем элемент preview при каждом открытии
            setTimeout(() => {
                // Проверяем, есть ли элемент preview
                let previewElement = document.getElementById('preview');
                if (!previewElement) {
                    const modalBody = modal.querySelector('.modal-body');
                    if (modalBody) {
                        // Удаляем все существующие элементы preview и их контейнеры
                        const existingPreviews = modalBody.querySelectorAll('#preview');
                        existingPreviews.forEach(el => {
                            const container = el.closest('.mb-3');
                            if (container) {
                                container.remove();
                            } else {
                                el.remove();
                            }
                        });
                        
                        // Создаем новый элемент preview
                        const previewContainer = document.createElement('div');
                        previewContainer.className = 'mb-3';
                        previewContainer.innerHTML = `
                            <label class="form-label">Предварительный просмотр:</label>
                            <div class="alert alert-info" id="preview">Введите данные для предварительного просмотра</div>
                        `;
                        
                        // Добавляем в конец формы
                        const form = modalBody.querySelector('#currencyForm');
                        if (form) {
                            form.appendChild(previewContainer);
                        }
                    }
                }
                
                addFormEventListeners();
                updatePreview();
            }, 50);
        });
        

    }
    
    // Принудительно обновляем предварительный просмотр каждые 5 секунд (уменьшили частоту)
    const previewInterval = setInterval(() => {
        const modal = document.getElementById('currencyModal');
        if (modal && modal.classList.contains('show')) {
            // Проверяем, что элемент preview существует перед обновлением
            const previewElement = document.getElementById('preview');
            if (previewElement) {
                updatePreview();
            }
        }
    }, 5000);
    
    // Отслеживаем изменения в DOM модального окна (упрощенная версия)
    const modalForObserver = document.getElementById('currencyModal');
    if (modalForObserver) {
        const observer = new MutationObserver(function(mutations) {
            let shouldUpdate = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.target.id === 'preview') {
                    shouldUpdate = true;
                }
            });
            if (shouldUpdate) {
                setTimeout(() => {
                    updatePreview();
                }, 100);
            }
        });
        
        observer.observe(modalForObserver, {
            childList: true,
            subtree: true
        });
    }
    
    // Очищаем интервал при скрытии модального окна
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            clearInterval(previewInterval);
        });
    }
});
</script>
@endsection 