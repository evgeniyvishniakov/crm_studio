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
                                <label for="thousands_separator" class="form-label">Разделитель тысяч *</label>
                                <input type="text" class="form-control" id="thousands_separator" name="thousands_separator" maxlength="1" value="," required>
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

function openCreateModal() {
    isEditMode = false;
    document.getElementById('modalTitle').textContent = 'Добавить валюту';
    document.getElementById('currencyForm').reset();
    document.getElementById('currencyId').value = '';
    document.getElementById('preview').textContent = 'Введите данные для предварительного просмотра';
    
    const modal = new bootstrap.Modal(document.getElementById('currencyModal'));
    modal.show();
}

function editCurrency(id) {
    isEditMode = true;
    document.getElementById('modalTitle').textContent = 'Редактировать валюту';
    
    // Загружаем данные валюты
    fetch(`/admin/currencies/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const currency = data.currency;
                document.getElementById('currencyId').value = currency.id;
                document.getElementById('code').value = currency.code;
                document.getElementById('name').value = currency.name;
                document.getElementById('symbol').value = currency.symbol;
                document.getElementById('symbol_position').value = currency.symbol_position;
                document.getElementById('decimal_places').value = currency.decimal_places;
                document.getElementById('decimal_separator').value = currency.decimal_separator;
                document.getElementById('thousands_separator').value = currency.thousands_separator;
                document.getElementById('is_active').checked = currency.is_active;
                document.getElementById('is_default').checked = currency.is_default;
                
                updatePreview();
                
                const modal = new bootstrap.Modal(document.getElementById('currencyModal'));
                modal.show();
            }
        });
}

function saveCurrency() {
    const form = document.getElementById('currencyForm');
    const formData = new FormData(form);
    
    const url = isEditMode 
        ? `/admin/currencies/${document.getElementById('currencyId').value}`
        : '/admin/currencies';
    
    const method = isEditMode ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
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
        alert('Произошла ошибка при сохранении');
    });
}

function deleteCurrency(id) {
    if (confirm('Вы уверены, что хотите удалить эту валюту?')) {
        fetch(`/admin/currencies/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        });
    }
}

function setDefault(id) {
    fetch(`/admin/currencies/${id}/set-default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Ошибка: ' + data.message);
        }
    });
}

function toggleActive(id) {
    fetch(`/admin/currencies/${id}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Ошибка: ' + data.message);
        }
    });
}

function updatePreview() {
    const symbol = document.getElementById('symbol').value;
    const symbolPosition = document.getElementById('symbol_position').value;
    const decimalPlaces = document.getElementById('decimal_places').value;
    const decimalSeparator = document.getElementById('decimal_separator').value;
    const thousandsSeparator = document.getElementById('thousands_separator').value;
    
    if (symbol && decimalPlaces !== '') {
        const amount = 1234.56;
        const formatted = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: parseInt(decimalPlaces),
            maximumFractionDigits: parseInt(decimalPlaces),
            useGrouping: true
        }).format(amount).replace(/\./g, decimalSeparator).replace(/,/g, thousandsSeparator);
        
        const preview = symbolPosition === 'before' 
            ? `${symbol}${formatted}`
            : `${formatted} ${symbol}`;
            
        document.getElementById('preview').textContent = `Пример: ${preview}`;
    }
}

// Обновляем предварительный просмотр при изменении полей
document.addEventListener('DOMContentLoaded', function() {
    const fields = ['symbol', 'symbol_position', 'decimal_places', 'decimal_separator', 'thousands_separator'];
    fields.forEach(field => {
        document.getElementById(field).addEventListener('input', updatePreview);
    });
});

// Функция для обновления валют в клиентской части
function refreshClientCurrencies() {
    // Отправляем уведомление всем активным клиентам через WebSocket или Server-Sent Events
    // Пока что просто показываем уведомление
    if (confirm('Это обновит валюты во всех открытых клиентских сессиях. Продолжить?')) {
        // Здесь можно добавить WebSocket уведомление
        alert('Валюты будут обновлены в клиентских сессиях при следующем обновлении страницы.');
        
        // Также можно отправить AJAX запрос для очистки кэша
        fetch('/admin/currencies/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Кэш валют очищен. Клиенты увидят изменения при обновлении страницы.');
            }
        });
    }
}
</script>
@endsection 