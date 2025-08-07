document.addEventListener('DOMContentLoaded', function() {
    // Показываем первую вкладку по умолчанию
    showTab('salary-overview');
});

// Функция переключения вкладок
function showTab(tabName) {
    // Скрываем все вкладки
    const panes = document.querySelectorAll('.settings-pane');
    panes.forEach(pane => {
        pane.style.display = 'none';
    });

    // Убираем активный класс со всех кнопок
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(button => {
        button.classList.remove('active');
    });

    // Показываем нужную вкладку
    const targetPane = document.getElementById('tab-' + tabName);
    if (targetPane) {
        targetPane.style.display = 'block';
    }

    // Добавляем активный класс к нужной кнопке
    const targetButton = document.querySelector(`[data-tab="${tabName}"]`);
    if (targetButton) {
        targetButton.classList.add('active');
    }
}

// Добавляем обработчики событий для кнопок вкладок
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });
});

// Функции для работы с настройками зарплаты
function showSalarySettingModal(id = null) {
    const modal = document.getElementById('salarySettingModal');
    const form = document.getElementById('salarySettingForm');
    const title = document.getElementById('salarySettingModalTitle');
    
    // Сбрасываем форму
    form.reset();
    clearErrors('salarySettingForm');
    
    if (id) {
        // Редактирование
        title.textContent = 'Редактировать настройки зарплаты';
        document.getElementById('settingId').value = id;
        
        // Загружаем данные для редактирования
        fetch(`/salary/settings/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const setting = data.setting;
                    document.getElementById('salarySettingUserId').value = setting.user_id;
                    document.getElementById('salaryType').value = setting.salary_type;
                    document.getElementById('fixedSalary').value = setting.fixed_salary || '';
                    document.getElementById('servicePercentage').value = setting.service_percentage || '';
                    document.getElementById('salesPercentage').value = setting.sales_percentage || '';
                    document.getElementById('minSalary').value = setting.min_salary || '';
                    document.getElementById('maxSalary').value = setting.max_salary || '';
                    toggleSalaryFields();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка при загрузке данных', 'error');
            });
    } else {
        // Добавление
        title.textContent = 'Добавить настройки зарплаты';
        document.getElementById('settingId').value = '';
    }
    
    // Показываем модальное окно
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
}

function editSalarySetting(id) {
    showSalarySettingModal(id);
}

function toggleSalaryFields() {
    const salaryType = document.getElementById('salaryType').value;
    const fixedSalaryRow = document.getElementById('fixedSalaryRow');
    const percentageRow = document.getElementById('percentageRow');
    
    // Скрываем все поля
    fixedSalaryRow.style.display = 'none';
    percentageRow.style.display = 'none';
    
    // Показываем нужные поля в зависимости от типа
    if (salaryType === 'fixed') {
        fixedSalaryRow.style.display = 'block';
    } else if (salaryType === 'percentage') {
        percentageRow.style.display = 'block';
    } else if (salaryType === 'mixed') {
        fixedSalaryRow.style.display = 'block';
        percentageRow.style.display = 'block';
    }
}

function deleteSalarySetting(id) {
    if (confirm('Вы уверены, что хотите удалить эти настройки зарплаты?')) {
        fetch(`/salary/settings/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Удаляем строку из таблицы
                const row = document.querySelector(`tr[data-setting-id="${id}"]`);
                if (row) {
                    row.remove();
                }
                showNotification('Настройки зарплаты удалены успешно', 'success');
            } else {
                showNotification(data.message || 'Ошибка при удалении', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при удалении настроек', 'error');
        });
    }
}

// Функции для работы с расчетами зарплаты
function showSalaryCalculationModal() {
    const modal = document.getElementById('salaryCalculationModal');
    const form = document.getElementById('salaryCalculationForm');
    
    // Сбрасываем форму
    form.reset();
    clearErrors('salaryCalculationForm');
    
    // Устанавливаем период по умолчанию (текущий месяц)
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    document.getElementById('periodStart').value = firstDay.toISOString().split('T')[0];
    document.getElementById('periodEnd').value = lastDay.toISOString().split('T')[0];
    
    // Показываем модальное окно
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
}

function viewSalaryCalculation(id) {
    const modal = document.getElementById('salaryCalculationDetailsModal');
    
    // Показываем модальное окно с индикатором загрузки
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
    
    // Загружаем данные расчета
    fetch(`/salary/calculations/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fillCalculationDetails(data.calculation);
            } else {
                showNotification('Ошибка при загрузке деталей расчета', 'error');
                closeSalaryCalculationDetailsModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при загрузке деталей расчета', 'error');
            closeSalaryCalculationDetailsModal();
        });
}

function fillCalculationDetails(calculation) {
    // Заполняем основную информацию
    document.getElementById('detailEmployeeName').textContent = calculation.user_name;
    
    const periodStart = new Date(calculation.period_start).toLocaleDateString('ru-RU');
    const periodEnd = new Date(calculation.period_end).toLocaleDateString('ru-RU');
    document.getElementById('detailPeriod').textContent = `${periodStart} - ${periodEnd}`;
    
    const statusElement = document.getElementById('detailStatus');
    statusElement.textContent = calculation.status_text;
    statusElement.className = `status-badge status-${calculation.status_color}`;
    
    const createdAt = new Date(calculation.created_at).toLocaleDateString('ru-RU');
    document.getElementById('detailCreatedAt').textContent = createdAt;
    
    // Заполняем суммы
    document.getElementById('detailTotalSalary').textContent = formatCurrency(calculation.total_salary);
    document.getElementById('detailServicesCount').textContent = calculation.services_count || '0';
    document.getElementById('detailServicesAmount').textContent = formatCurrency(calculation.services_amount);
    document.getElementById('detailSalesCount').textContent = calculation.sales_count || '0';
    document.getElementById('detailSalesAmount').textContent = formatCurrency(calculation.sales_amount);
    
    // Заполняем проценты и доходы от процентов
    const servicePercentage = calculation.service_percentage || 0;
    const salesPercentage = calculation.sales_percentage || 0;
    
    document.getElementById('detailServicePercentage').textContent = servicePercentage > 0 ? `${servicePercentage}%` : 'Не установлен';
    document.getElementById('detailSalesPercentage').textContent = salesPercentage > 0 ? `${salesPercentage}%` : 'Не установлен';
    
    // Рассчитываем доходы от процентов
    const serviceIncome = servicePercentage > 0 ? (calculation.services_amount * servicePercentage / 100) : 0;
    const salesIncome = salesPercentage > 0 ? (calculation.sales_amount * salesPercentage / 100) : 0;
    
    document.getElementById('detailServiceIncome').textContent = formatCurrency(serviceIncome);
    document.getElementById('detailSalesIncome').textContent = formatCurrency(salesIncome);
    
    document.getElementById('detailFixedSalary').textContent = formatCurrency(calculation.fixed_salary);
    document.getElementById('detailPercentageSalary').textContent = formatCurrency(calculation.percentage_salary);
    document.getElementById('detailBonuses').textContent = formatCurrency(calculation.bonuses);
    document.getElementById('detailPenalties').textContent = formatCurrency(calculation.penalties);
    document.getElementById('detailFinalTotal').textContent = formatCurrency(calculation.total_salary);
    
    // Показываем/скрываем примечания
    const notesSection = document.getElementById('calculationNotesSection');
    const notesElement = document.getElementById('detailNotes');
    if (calculation.notes && calculation.notes.trim()) {
        notesElement.textContent = calculation.notes;
        notesSection.style.display = 'block';
    } else {
        notesSection.style.display = 'none';
    }
}

// Функция форматирования валюты
function formatCurrency(amount) {
    if (!amount || amount == 0) return '0 ₽';
    const num = parseFloat(amount);
    return num % 1 === 0 ? `${num.toLocaleString('ru-RU')} ₽` : `${num.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ₽`;
}

function closeSalaryCalculationDetailsModal() {
    const modal = document.getElementById('salaryCalculationDetailsModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

function toggleCalculationPeriod() {
    const period = document.getElementById('calculationPeriod').value;
    const customPeriodRow = document.getElementById('customPeriodRow');
    const periodStart = document.getElementById('periodStart');
    const periodEnd = document.getElementById('periodEnd');
    
    if (period === 'custom') {
        customPeriodRow.style.display = 'block';
    } else {
        customPeriodRow.style.display = 'none';
        
        const now = new Date();
        let firstDay, lastDay;
        
        if (period === 'current_month') {
            // Текущий месяц
            firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        } else if (period === 'last_month') {
            // Прошлый месяц
            firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
        }
        
        if (firstDay && lastDay) {
            periodStart.value = firstDay.toISOString().split('T')[0];
            periodEnd.value = lastDay.toISOString().split('T')[0];
        }
    }
}

function approveSalaryCalculation(id) {
    if (confirm('Вы уверены, что хотите утвердить этот расчет зарплаты?')) {
        fetch(`/salary/calculations/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем статус в таблице
                const row = document.querySelector(`tr[data-calculation-id="${id}"]`);
                if (row) {
                    const statusCell = row.querySelector('td:nth-child(6)');
                    if (statusCell) {
                        statusCell.innerHTML = '<span class="badge bg-success">Утверждено</span>';
                    }
                }
                showNotification('Расчет утвержден успешно', 'success');
            } else {
                showNotification(data.message || 'Ошибка при утверждении', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при утверждении расчета', 'error');
        });
    }
}

// Функции для работы с выплатами
function showSalaryPaymentModal() {
    const modal = document.getElementById('salaryPaymentModal');
    const form = document.getElementById('salaryPaymentForm');
    
    // Сбрасываем форму
    form.reset();
    clearErrors('salaryPaymentForm');
    
    // Очищаем список расчетов
    const calculationSelect = document.getElementById('paymentCalculationId');
    calculationSelect.innerHTML = '<option value="">Выберите расчет (необязательно)</option>';
    
    // Устанавливаем сегодняшнюю дату
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('paymentDate').value = today;
    
    // Показываем модальное окно
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
}

// Функция загрузки расчетов для выбранного сотрудника
function loadCalculationsForUser(userId) {
    const calculationSelect = document.getElementById('paymentCalculationId');
    
    if (!userId) {
        calculationSelect.innerHTML = '<option value="">Выберите расчет (необязательно)</option>';
        return;
    }
    
    // Показываем индикатор загрузки
    calculationSelect.innerHTML = '<option value="">Загрузка...</option>';
    
    console.log('Загружаем расчеты для пользователя:', userId);
    
    fetch(`/salary/calculations/by-user/${userId}`)
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response URL:', response.url);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                let options = '<option value="">Выберите расчет (необязательно)</option>';
                
                console.log('Количество расчетов:', data.calculations.length);
                
                data.calculations.forEach(calculation => {
                    const periodStart = new Date(calculation.period_start).toLocaleDateString('ru-RU');
                    const periodEnd = new Date(calculation.period_end).toLocaleDateString('ru-RU');
                    const amount = parseFloat(calculation.total_salary).toLocaleString('ru-RU', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    options += `<option value="${calculation.id}" data-amount="${calculation.total_salary}">
                        ${periodStart} - ${periodEnd} (${amount} ₽)
                    </option>`;
                });
                
                calculationSelect.innerHTML = options;
            } else {
                console.log('API вернул ошибку:', data.message);
                calculationSelect.innerHTML = '<option value="">Нет доступных расчетов</option>';
            }
        })
        .catch(error => {
            console.error('Ошибка запроса:', error);
            calculationSelect.innerHTML = '<option value="">Ошибка загрузки расчетов</option>';
        });
}

// Функция для заполнения суммы при выборе расчета
function fillPaymentAmountFromCalculation(calculationId) {
    const calculationSelect = document.getElementById('paymentCalculationId');
    const amountInput = document.getElementById('paymentAmount');
    
    if (!calculationId) {
        // Если расчет не выбран, очищаем сумму
        amountInput.value = '';
        return;
    }
    
    // Получаем сумму из data-amount атрибута выбранной опции
    const selectedOption = calculationSelect.querySelector(`option[value="${calculationId}"]`);
    if (selectedOption) {
        const amount = selectedOption.getAttribute('data-amount');
        if (amount && parseFloat(amount) > 0) {
            const numAmount = parseFloat(amount);
            // Если копеек нет, показываем без .00
            amountInput.value = numAmount % 1 === 0 ? numAmount.toString() : numAmount.toFixed(2);
        }
    }
}

function viewSalaryPayment(id) {
    // Заглушка - будет реализована позже
    alert('Просмотр выплаты будет реализован');
}

function approveSalaryPayment(id) {
    if (confirm('Вы уверены, что хотите подтвердить эту выплату?')) {
        fetch(`/salary/payments/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем статус в таблице
                const row = document.querySelector(`tr[data-payment-id="${id}"]`);
                if (row) {
                    const statusCell = row.querySelector('td:nth-child(5)');
                    if (statusCell) {
                        statusCell.innerHTML = '<span class="badge bg-success">Подтверждено</span>';
                    }
                }
                showNotification('Выплата подтверждена успешно', 'success');
            } else {
                showNotification(data.message || 'Ошибка при подтверждении', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при подтверждении выплаты', 'error');
        });
    }
}

// Функции закрытия модальных окон
function closeSalarySettingModal() {
    const modal = document.getElementById('salarySettingModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

function closeSalaryCalculationModal() {
    const modal = document.getElementById('salaryCalculationModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

function closeSalaryPaymentModal() {
    const modal = document.getElementById('salaryPaymentModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

// Функция очистки ошибок
function clearErrors(formId) {
    const form = document.getElementById(formId);
    if (form) {
        const errorElements = form.querySelectorAll('.error-message');
        errorElements.forEach(element => element.remove());
        
        const errorFields = form.querySelectorAll('.error-field');
        errorFields.forEach(field => field.classList.remove('error-field'));
    }
}

// Обработчики форм
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик формы настроек зарплаты
    const salarySettingForm = document.getElementById('salarySettingForm');
    if (salarySettingForm) {
        salarySettingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const settingId = formData.get('setting_id');
            const url = settingId ? `/salary/settings/${settingId}` : '/salary/settings';
            const method = settingId ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Настройки сохранены успешно', 'success');
                    closeSalarySettingModal();
                    // Перезагружаем страницу для обновления данных
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при сохранении', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка при сохранении настроек', 'error');
            });
        });
    }
    
    // Обработчик формы расчета зарплаты
    const salaryCalculationForm = document.getElementById('salaryCalculationForm');
    if (salaryCalculationForm) {
        salaryCalculationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/salary/calculations', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Расчет создан успешно', 'success');
                    closeSalaryCalculationModal();
                    // Перезагружаем страницу для обновления данных
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при создании расчета', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка при создании расчета', 'error');
            });
        });
    }
    
    // Обработчик формы выплаты
    const salaryPaymentForm = document.getElementById('salaryPaymentForm');
    if (salaryPaymentForm) {
        salaryPaymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/salary/payments', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Выплата создана успешно', 'success');
                    closeSalaryPaymentModal();
                    // Перезагружаем страницу для обновления данных
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при создании выплаты', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка при создании выплаты', 'error');
            });
        });
    }
    
    // Обработчик изменения периода расчета
    const calculationPeriod = document.getElementById('calculationPeriod');
    if (calculationPeriod) {
        calculationPeriod.addEventListener('change', toggleCalculationPeriod);
    }
    
    // Обработчик изменения сотрудника в форме выплаты
    const paymentUserId = document.getElementById('paymentUserId');
    if (paymentUserId) {
        paymentUserId.addEventListener('change', function(e) {
            loadCalculationsForUser(e.target.value);
        });
    }
    
    // Обработчик изменения расчета в форме выплаты
    const paymentCalculationId = document.getElementById('paymentCalculationId');
    if (paymentCalculationId) {
        paymentCalculationId.addEventListener('change', function(e) {
            fillPaymentAmountFromCalculation(e.target.value);
        });
    }
    
    // Закрытие модальных окон при клике вне их
    window.addEventListener('click', function(event) {
        const modals = ['salarySettingModal', 'salaryCalculationModal', 'salaryPaymentModal', 'salaryCalculationDetailsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
    });
});

// Вспомогательная функция для показа уведомлений
function showNotification(message, type = 'info') {
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Автоматически удаляем уведомление через 5 секунд
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
