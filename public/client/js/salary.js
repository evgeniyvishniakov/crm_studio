document.addEventListener('DOMContentLoaded', function() {
    // Получаем сохраненную вкладку или показываем первую по умолчанию
    const savedTab = localStorage.getItem('salaryActiveTab') || 'salary-overview';
    showTab(savedTab);
});

// Функция переключения вкладок
function showTab(tabName) {
    // Сохраняем активную вкладку в localStorage
    localStorage.setItem('salaryActiveTab', tabName);
    
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
                if (data.success && data.setting) {
                    const setting = data.setting;
                    document.getElementById('salarySettingUserId').value = setting.user_id;
                    document.getElementById('salaryType').value = setting.salary_type;
                    document.getElementById('fixedSalary').value = setting.fixed_salary || '';
                    document.getElementById('servicePercentage').value = setting.service_percentage || '';
                    document.getElementById('salesPercentage').value = setting.sales_percentage || '';

                    toggleSalaryFields();
                } else {
                    window.showNotification('error', 'Ошибка при загрузке данных настроек');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showNotification('error', 'Ошибка при загрузке данных');
            });
    } else {
        // Добавление
        title.textContent = 'Добавить настройки зарплаты';
        document.getElementById('settingId').value = '';
    }
    
    // Показываем модальное окно
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
    
    // Вызываем функцию для правильного отображения полей
    toggleSalaryFields();
}

function editSalarySetting(id) {
    showSalarySettingModal(id);
}

function toggleSalaryFields() {
    const salaryType = document.getElementById('salaryType').value;
    const fixedSalaryRow = document.getElementById('fixedSalaryRow');
    const percentageRow = document.getElementById('percentageRow');
    const fixedSalaryInput = document.getElementById('fixedSalary');
    const servicePercentageInput = document.getElementById('servicePercentage');
    const salesPercentageInput = document.getElementById('salesPercentage');
    
    // Скрываем все поля
    fixedSalaryRow.style.display = 'none';
    percentageRow.style.display = 'none';
    
    // Сбрасываем обязательность полей
    fixedSalaryInput.required = false;
    servicePercentageInput.required = false;
    salesPercentageInput.required = false;
    
    // Показываем нужные поля в зависимости от типа
    if (salaryType === 'fixed') {
        fixedSalaryRow.style.display = 'flex';
        fixedSalaryInput.required = true;
    } else if (salaryType === 'percentage') {
        percentageRow.style.display = 'flex';
        servicePercentageInput.required = true;
        salesPercentageInput.required = true;
    } else if (salaryType === 'mixed') {
        fixedSalaryRow.style.display = 'flex';
        percentageRow.style.display = 'flex';
        fixedSalaryInput.required = true;
        servicePercentageInput.required = true;
        salesPercentageInput.required = true;
    }
}

function deleteSalarySetting(id) {
    confirmAction(id, 'setting');
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
                window.showNotification('error', 'Ошибка при загрузке деталей расчета');
                closeSalaryCalculationDetailsModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.showNotification('error', 'Ошибка при загрузке деталей расчета');
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
    statusElement.className = '';
    
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
    
    // Штрафы: красным если больше 0, обычным если 0
    const penaltiesElement = document.getElementById('detailPenalties');
    const penaltiesAmount = parseFloat(calculation.penalties) || 0;
    penaltiesElement.textContent = formatCurrency(calculation.penalties);
    if (penaltiesAmount > 0) {
        penaltiesElement.style.color = '#dc3545'; // красный цвет
    } else {
        penaltiesElement.style.color = '#6c757d'; // обычный серый цвет
    }
    
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
    if (!amount || amount == 0) {
        return window.currencyData && window.currencyData.symbol ? `0 ${window.currencyData.symbol}` : '0 ₴';
    }
    
    const num = parseFloat(amount);
    const symbol = window.currencyData && window.currencyData.symbol ? window.currencyData.symbol : '₴';
    
    if (num % 1 === 0) {
        return `${num.toLocaleString('uk-UA')} ${symbol}`;
    } else {
        return `${num.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${symbol}`;
    }
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
        customPeriodRow.style.display = 'flex';
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
                    // Обновляем статус в таблице и скрываем кнопку утверждения
                const row = document.querySelector(`tr[data-calculation-id="${id}"]`);
                if (row) {
                    const statusCell = row.querySelector('td:nth-child(6)');
                    if (statusCell) {
                            statusCell.innerHTML = '<span class="status-badge">' + (window.translations ? window.translations.approved : 'Утверждено') + '</span>';
                    }
                        // Скрываем кнопку утверждения
                        const approveBtn = row.querySelector('button[onclick*="approveSalaryCalculation"]');
                        if (approveBtn) {
                            approveBtn.style.display = 'none';
                }
                    }
                    window.showNotification('success', 'Расчет утвержден успешно');
            
            // Обновляем статистику в отчетах
            updateSalaryStatistics();
            } else {
            window.showNotification('error', data.message || 'Ошибка при утверждении');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        window.showNotification('error', 'Ошибка при утверждении расчета');
        });
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
    
    
    
    fetch(`/salary/calculations/by-user/${userId}`)
        .then(response => {

            return response.json();
        })
        .then(data => {

            if (data.success) {
                let options = '<option value="">Выберите расчет (необязательно)</option>';
                

                
                data.calculations.forEach(calculation => {
                    const periodStart = new Date(calculation.period_start).toLocaleDateString('ru-RU');
                    const periodEnd = new Date(calculation.period_end).toLocaleDateString('ru-RU');
                    const amount = parseFloat(calculation.total_salary).toLocaleString('ru-RU', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                     const symbol = window.currencyData && window.currencyData.symbol ? window.currencyData.symbol : '₴';
                    
                    options += `<option value="${calculation.id}" data-amount="${calculation.total_salary}">
                         ${periodStart} - ${periodEnd} (${amount} ${symbol})
                    </option>`;
                });
                
                calculationSelect.innerHTML = options;
            } else {
    
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
    fetch(`/salary/payments/${id}/details`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fillPaymentDetails(data.payment);
            document.getElementById('salaryPaymentDetailsModal').style.display = 'block';
            document.body.classList.add('modal-open');
        } else {
            window.showNotification('error', data.message || 'Ошибка при загрузке данных выплаты');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.showNotification('error', 'Ошибка при загрузке данных выплаты');
    });
}

function approveSalaryPayment(id) {
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
            // Обновляем статус в таблице и скрываем кнопку подтверждения
                const row = document.querySelector(`tr[data-payment-id="${id}"]`);
                if (row) {
                    const statusCell = row.querySelector('td:nth-child(5)');
                    if (statusCell) {
                    statusCell.innerHTML = '<span class="status-badge status-done">Выплачено</span>';
                    }
                // Скрываем кнопку подтверждения
                const approveBtn = row.querySelector('button[onclick*="approveSalaryPayment"]');
                if (approveBtn) {
                    approveBtn.style.display = 'none';
                }
            }
            window.showNotification('success', 'Выплата подтверждена успешно');
            
            // Обновляем статистику в отчетах
            updateSalaryStatistics();
            } else {
            window.showNotification('error', data.message || 'Ошибка при подтверждении');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        window.showNotification('error', 'Ошибка при подтверждении выплаты');
        });
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

function closeSalaryPaymentDetailsModal() {
    const modal = document.getElementById('salaryPaymentDetailsModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

function fillPaymentDetails(payment) {
    // Основная информация
    document.getElementById('paymentDetailEmployee').textContent = payment.user_name;
    document.getElementById('paymentDetailDate').textContent = new Date(payment.payment_date).toLocaleDateString('uk-UA');
    document.getElementById('paymentDetailAmount').textContent = formatCurrency(payment.amount);
    document.getElementById('paymentDetailAmount').setAttribute('data-amount', payment.amount);
    
    // Метод выплаты
    let paymentMethodText = '';
    switch(payment.payment_method) {
        case 'cash':
            paymentMethodText = window.translations.cash || 'Готівка';
            break;
        case 'bank':
            paymentMethodText = window.translations.bank_transfer || 'Банківський переказ';
            break;
        case 'card':
            paymentMethodText = window.translations.card || 'Карта';
            break;
        default:
            paymentMethodText = payment.payment_method || '-';
    }
    document.getElementById('paymentDetailMethod').textContent = paymentMethodText;
    
    // Статус
    let statusText = '';
    switch(payment.status) {
        case 'pending':
            statusText = window.translations.pending || 'Очікує';
            break;
        case 'approved':
            statusText = window.translations.paid || 'Виплачено';
            break;
        case 'cancelled':
            statusText = window.translations.cancelled || 'Скасовано';
            break;
        default:
            statusText = window.translations.unknown || 'Невідомо';
    }
    document.getElementById('paymentDetailStatus').innerHTML = `<span class="status-badge">${statusText}</span>`;
    
    // Дата создания
    document.getElementById('paymentDetailCreatedAt').textContent = new Date(payment.created_at).toLocaleDateString('uk-UA');
    
    // Дополнительная информация
    const referenceNumberRow = document.getElementById('referenceNumberRow');
    if (payment.reference_number) {
        document.getElementById('paymentDetailReference').textContent = payment.reference_number;
        referenceNumberRow.style.display = 'table-row';
    } else {
        referenceNumberRow.style.display = 'none';
    }
    
    const approvalSection = document.getElementById('paymentApprovalSection');
    if (payment.approved_by && payment.approved_at) {
        document.getElementById('paymentDetailApprovedBy').textContent = payment.approved_by_name || window.translations.unknown || 'Невідомо';
        document.getElementById('paymentDetailApprovedAt').textContent = new Date(payment.approved_at).toLocaleDateString('uk-UA');
        approvalSection.style.display = 'block';
    } else {
        approvalSection.style.display = 'none';
    }
    
    // Примечания
    const notesSection = document.getElementById('paymentNotesSection');
    if (payment.notes) {
        document.getElementById('paymentDetailNotes').textContent = payment.notes;
        notesSection.style.display = 'block';
    } else {
        notesSection.style.display = 'none';
    }
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
            
            // Для PUT запросов нужно использовать специальный подход
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }
            
            fetch(url, {
                method: 'POST', // Всегда используем POST, но добавляем _method для PUT
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showNotification('success', data.message || 'Настройки сохранены успешно');
                    closeSalarySettingModal();
                    
                    // Добавляем или обновляем настройку в таблице без перезагрузки
                    if (data.setting) {
                        const existingRow = document.querySelector(`tr[data-setting-id="${data.setting.id}"]`);
                        if (existingRow) {
                            // Обновляем существующую строку
                            updateSettingRow(existingRow, data.setting);
                } else {
                            // Добавляем новую строку
                            addSettingToTable(data.setting);
                        }
                    }
                } else {
                    window.showNotification('error', data.message || 'Ошибка при сохранении');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showNotification('error', 'Ошибка при сохранении настроек');
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
                    window.showNotification('success', data.message || 'Расчет создан успешно');
                    closeSalaryCalculationModal();
                    
                    // Добавляем новый расчет в таблицу без перезагрузки
                    if (data.calculation) {
                        addCalculationToTable(data.calculation);
                    }
                } else {
                    window.showNotification('error', data.message || 'Ошибка при создании расчета');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showNotification('error', 'Ошибка при создании расчета');
            });
        });
    }
    
    // Обработчик формы выплаты
    const salaryPaymentForm = document.getElementById('salaryPaymentForm');
    if (salaryPaymentForm) {
        salaryPaymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Отладочная информация
            
            
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
                    window.showNotification('success', data.message || 'Выплата создана успешно');
                    closeSalaryPaymentModal();
                    
                    // Добавляем новую выплату в таблицу без перезагрузки
                    if (data.payment) {
                        addPaymentToTable(data.payment);
                    }
                } else {
                    window.showNotification('error', data.message || 'Ошибка при создании выплаты');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showNotification('error', 'Ошибка при создании выплаты');
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
    
    // Закрытие модального окна деталей выплаты при клике вне его
    window.addEventListener('click', function(event) {
        const paymentDetailsModal = document.getElementById('salaryPaymentDetailsModal');
        if (event.target === paymentDetailsModal) {
            closeSalaryPaymentDetailsModal();
        }
    });
    
    // Обработчик кнопки "Отмена" в модальном окне удаления
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            closeConfirmationModal();
        });
    }
    
    // Обработчик кнопки "Удалить" в модальном окне удаления
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            executeAction();
        });
    }
});

// Глобальные переменные для модального окна удаления
let currentDeleteId = null;
let currentDeleteType = null; // 'calculation' или 'payment'

// Функции для работы с модальным окном подтверждения удаления
function confirmAction(id, type) {
    currentDeleteId = id;
    currentDeleteType = type;
    
    // Устанавливаем сообщение в зависимости от типа
    const messageElement = document.getElementById('confirmationMessage');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    if (type === 'calculation') {
        messageElement.textContent = window.translations ? window.translations.confirm_delete_calculation : 'Вы уверены, что хотите удалить этот расчет зарплаты? Это действие нельзя отменить.';
        confirmBtn.textContent = window.translations ? window.translations.delete : 'Удалить';
        confirmBtn.className = 'btn-delete';
    } else if (type === 'payment') {
        messageElement.textContent = window.translations ? window.translations.confirm_delete_payment : 'Вы уверены, что хотите удалить эту выплату зарплаты? Это действие нельзя отменить.';
        confirmBtn.textContent = window.translations ? window.translations.delete : 'Удалить';
        confirmBtn.className = 'btn-delete';
    } else if (type === 'setting') {
        messageElement.textContent = window.translations ? window.translations.confirm_delete_setting : 'Вы уверены, что хотите удалить эти настройки зарплаты? Это действие нельзя отменить.';
        confirmBtn.textContent = window.translations ? window.translations.delete : 'Удалить';
        confirmBtn.className = 'btn-delete';
    }
    
    // Показываем модальное окно
    document.getElementById('confirmationModal').style.display = 'block';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    currentDeleteId = null;
    currentDeleteType = null;
}

function executeAction() {
    if (!currentDeleteId || !currentDeleteType) return;

    const actionBtn = document.getElementById('confirmDeleteBtn');
    const originalText = actionBtn.innerHTML;
    actionBtn.innerHTML = 'Выполнение...';
    actionBtn.disabled = true;

    let url = '';
    let method = 'DELETE';
    let successMessage = '';
    let errorMessage = '';

    if (currentDeleteType === 'calculation') {
        url = `/salary/calculations/${currentDeleteId}`;
        successMessage = 'Расчет зарплаты удален успешно';
        errorMessage = 'Ошибка при удалении расчета';
    } else if (currentDeleteType === 'payment') {
        url = `/salary/payments/${currentDeleteId}`;
        successMessage = 'Выплата зарплаты удалена успешно';
        errorMessage = 'Ошибка при удалении выплаты';
    } else if (currentDeleteType === 'setting') {
        url = `/salary/settings/${currentDeleteId}`;
        successMessage = 'Настройки зарплаты удалены успешно';
        errorMessage = 'Ошибка при удалении настроек';
    }

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (currentDeleteType === 'calculation' || currentDeleteType === 'payment' || currentDeleteType === 'setting') {
                // Удаляем строку из таблицы
                const row = document.querySelector(`tr[data-${currentDeleteType.replace('approve_', '')}-id="${currentDeleteId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Проверяем, стала ли таблица пустой после удаления
                if (currentDeleteType === 'setting') {
                    const tbody = document.getElementById('salary-settings-tbody');
                    if (tbody && tbody.children.length === 0) {
                        // Таблица пустая, показываем сообщение
                        const tableWrapper = tbody.closest('.table-wrapper');
                        if (tableWrapper) {
                            tableWrapper.style.display = 'none';
                        }
                        
                        // Показываем сообщение об отсутствии настроек
                        const emptyMessage = document.querySelector('#tab-salary-settings .text-center');
                        if (emptyMessage) {
                            emptyMessage.style.display = 'block';
                        }
                    }
                } else if (currentDeleteType === 'calculation') {
                    const tbody = document.getElementById('salary-calculations-tbody');
                    if (tbody && tbody.children.length === 0) {
                        // Таблица пустая, показываем сообщение
                        const tableWrapper = tbody.closest('.table-wrapper');
                        if (tableWrapper) {
                            tableWrapper.style.display = 'none';
                        }
                        
                        // Показываем сообщение об отсутствии расчетов
                        const emptyMessage = document.querySelector('#tab-salary-calculations .text-center');
                        if (emptyMessage) {
                            emptyMessage.style.display = 'block';
                        }
                    }
                } else if (currentDeleteType === 'payment') {
                    const tbody = document.getElementById('salary-payments-tbody');
                    if (tbody && tbody.children.length === 0) {
                        // Таблица пустая, показываем сообщение
                        const tableWrapper = tbody.closest('.table-wrapper');
                        if (tableWrapper) {
                            tableWrapper.style.display = 'none';
                        }
                        
                        // Показываем сообщение об отсутствии выплат
                        const emptyMessage = document.querySelector('#tab-salary-payments .text-center');
                        if (emptyMessage) {
                            emptyMessage.style.display = 'block';
                        }
                    }
                }
            }
            window.showNotification('success', data.message || successMessage);
        } else {
            window.showNotification('error', data.message || errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.showNotification('error', errorMessage);
    })
    .finally(() => {
        closeConfirmationModal();
        actionBtn.innerHTML = originalText;
        actionBtn.disabled = false;
    });
}

// Используем глобальную функцию уведомлений из notifications.js

// Функция добавления нового расчета в таблицу
function addCalculationToTable(calculation) {
    const tbody = document.getElementById('salary-calculations-tbody');
    if (!tbody) return;
    
    const periodStart = new Date(calculation.period_start).toLocaleDateString('ru-RU');
    const periodEnd = new Date(calculation.period_end).toLocaleDateString('ru-RU');
    const totalSalary = formatCurrency(calculation.total_salary);
    
    // Для расчетов НЕ добавляем цветовые классы статуса - только базовый status-badge
    let statusText = '';
    if (calculation.status === 'calculated') {
        statusText = window.translations ? window.translations.calculated : 'Рассчитано';
    } else if (calculation.status === 'approved') {
        statusText = window.translations ? window.translations.approved : 'Утверждено';
    } else if (calculation.status === 'paid') {
        statusText = window.translations ? window.translations.paid : 'Оплачено';
    }
    
    // Только базовый класс status-badge без цветовых модификаторов
    const statusBadge = `<span class="status-badge">${statusText}</span>`;
    
    let approveButton = '';
    if (calculation.status === 'calculated') {
        approveButton = `
            <button class="btn-edit" onclick="approveSalaryCalculation(${calculation.id})" title="Утвердить">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>
        `;
    }
    
    const servicesAmount = formatCurrency(calculation.services_amount || 0);
    const salesAmount = formatCurrency(calculation.sales_amount || 0);
    
    const newRow = `
        <tr data-calculation-id="${calculation.id}">
            <td>${calculation.user_name}</td>
            <td>${periodStart} - ${periodEnd}</td>
            <td>${calculation.services_count || 0} (${servicesAmount})</td>
            <td>${calculation.sales_count || 0} (${salesAmount})</td>
            <td>
                <span class="currency-amount" data-amount="${calculation.total_salary}">
                    ${totalSalary}
                </span>
            </td>
            <td>${statusBadge}</td>
            <td>
                <div class="actions-cell">
                    <button class="btn-view" onclick="viewSalaryCalculation(${calculation.id})" title="Просмотр">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    ${approveButton}
                    <button class="btn-delete" onclick="deleteSalaryCalculation(${calculation.id})" title="Удалить">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `;
    
    // Добавляем новую строку в начало таблицы
    tbody.insertAdjacentHTML('afterbegin', newRow);
    
    // Скрываем сообщение об отсутствии расчетов и показываем таблицу
    const tableWrapper = tbody.closest('.table-wrapper');
    if (tableWrapper) {
        tableWrapper.style.display = 'block';
    }
    
    const emptyMessage = document.querySelector('#tab-salary-calculations .text-center');
    if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }
}

// Функция добавления новой выплаты в таблицу
function addPaymentToTable(payment) {
    const tbody = document.getElementById('salary-payments-tbody');
    if (!tbody) return;
    
    const paymentDate = new Date(payment.payment_date).toLocaleDateString('ru-RU');
    const amount = formatCurrency(payment.amount);
    
    let statusBadge = '';
    if (payment.status === 'pending') {
        const statusText = window.translations ? window.translations.pending : 'Ожидает';
        statusBadge = `<span class="status-badge status-pending">${statusText}</span>`;
    } else if (payment.status === 'approved') {
        const statusText = window.translations ? window.translations.paid : 'Выплачено';
        statusBadge = `<span class="status-badge status-done">${statusText}</span>`;
    } else if (payment.status === 'cancelled') {
        const statusText = window.translations ? window.translations.cancelled : 'Отменено';
        statusBadge = `<span class="status-badge status-cancelled">${statusText}</span>`;
    } else {
        // По умолчанию для новых выплат
        const statusText = window.translations ? window.translations.pending : 'Ожидает';
        statusBadge = `<span class="status-badge status-pending">${statusText}</span>`;
    }
    
    let approveButton = '';
    if (payment.status === 'pending' || !payment.status) {
        approveButton = `
            <button class="btn-edit" onclick="approveSalaryPayment(${payment.id})" title="Подтвердить выплату">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>
        `;
    }
    
    // Преобразуем метод выплаты в читаемый вид с использованием переводов
    let paymentMethodText = '-';
    
    // Отладочная информация
    
    
    // Проверяем, что payment_method определен и не является строкой "undefined"
    if (payment.payment_method && 
        payment.payment_method !== 'undefined' && 
        payment.payment_method !== undefined && 
        payment.payment_method !== null) {
        
        if (window.translations && window.translations.cash && window.translations.bank_transfer && window.translations.card) {
            switch(payment.payment_method) {
                case 'cash':
                    paymentMethodText = window.translations.cash;
                    break;
                case 'bank':
                    paymentMethodText = window.translations.bank_transfer;
                    break;
                case 'card':
                    paymentMethodText = window.translations.card;
                    break;
                default:
                    paymentMethodText = '-';
            }
        } else {
            // Fallback на русский язык
            switch(payment.payment_method) {
                case 'cash':
                    paymentMethodText = 'Наличные';
                    break;
                case 'bank':
                    paymentMethodText = 'Банковский перевод';
                    break;
                case 'card':
                    paymentMethodText = 'Карта';
                    break;
                default:
                    paymentMethodText = '-';
            }
        }
    }
    
    
    
    const newRow = `
        <tr data-payment-id="${payment.id}">
            <td>${payment.user_name}</td>
            <td>
                <span class="currency-amount">${amount}</span>
            </td>
            <td>${paymentDate}</td>
            <td>${paymentMethodText}</td>
            <td>${statusBadge}</td>
            <td>
                <div class="actions-cell">
                    <button class="btn-view" onclick="viewSalaryPayment(${payment.id})" title="Просмотр">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    ${approveButton}
                    <button class="btn-delete" onclick="deleteSalaryPayment(${payment.id})" title="Удалить">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `;
    
    // Добавляем новую строку в начало таблицы
    tbody.insertAdjacentHTML('afterbegin', newRow);
    
    // Скрываем сообщение об отсутствии выплат и показываем таблицу
    const tableWrapper = tbody.closest('.table-wrapper');
    if (tableWrapper) {
        tableWrapper.style.display = 'block';
    }
    
    const emptyMessage = document.querySelector('#tab-salary-payments .text-center');
    if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }
}

// Функция удаления расчета зарплаты
function deleteSalaryCalculation(id) {
    confirmAction(id, 'calculation');
}

// Функция удаления выплаты зарплаты
function deleteSalaryPayment(id) {
    confirmAction(id, 'payment');
}

// Функция обновления существующей строки настроек
function updateSettingRow(row, setting) {
    let salaryTypeText = '';
    if (setting.salary_type === 'fixed') {
        salaryTypeText = window.translations ? window.translations.fixed_salary : 'Фиксированная';
    } else if (setting.salary_type === 'percentage') {
        salaryTypeText = window.translations ? window.translations.percentage_salary : 'Процентная';
    } else if (setting.salary_type === 'mixed') {
        salaryTypeText = window.translations ? window.translations.mixed_salary : 'Смешанная';
    }

    // Обновляем ячейки
    row.cells[0].textContent = setting.user_name;
    row.cells[1].textContent = salaryTypeText;
    
    // Показываем проценты только для процентной и смешанной зарплаты
    if (setting.salary_type === 'fixed') {
        row.cells[2].textContent = '-';
        row.cells[3].textContent = '-';
    } else {
        row.cells[2].textContent = setting.service_percentage ? setting.service_percentage + '%' : '-';
        row.cells[3].textContent = setting.sales_percentage ? setting.sales_percentage + '%' : '-';
    }
}

// Функция добавления новой настройки в таблицу
function addSettingToTable(setting) {
    const tbody = document.getElementById('salary-settings-tbody');
    if (!tbody) return;
    
    let salaryTypeText = '';
    if (setting.salary_type === 'fixed') {
        salaryTypeText = window.translations ? window.translations.fixed_salary : 'Фиксированная';
    } else if (setting.salary_type === 'percentage') {
        salaryTypeText = window.translations ? window.translations.percentage_salary : 'Процентная';
    } else if (setting.salary_type === 'mixed') {
        salaryTypeText = window.translations ? window.translations.mixed_salary : 'Смешанная';
    }
    
    // Определяем отображение процентов в зависимости от типа зарплаты
    let servicePercentageText = '-';
    let salesPercentageText = '-';
    
    if (setting.salary_type !== 'fixed') {
        servicePercentageText = setting.service_percentage ? setting.service_percentage + '%' : '-';
        salesPercentageText = setting.sales_percentage ? setting.sales_percentage + '%' : '-';
    }
    
    const newRow = `
        <tr data-setting-id="${setting.id}">
            <td>${setting.user_name}</td>
            <td>${salaryTypeText}</td>
            <td>${servicePercentageText}</td>
            <td>${salesPercentageText}</td>
            <td>
                <div class="actions-cell">
                    <button class="btn-edit" onclick="editSalarySetting(${setting.id})" title="Редактировать">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                    </button>
                    <button class="btn-delete" onclick="deleteSalarySetting(${setting.id})" title="Удалить">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `;
    
    // Добавляем новую строку в начало таблицы
    tbody.insertAdjacentHTML('afterbegin', newRow);
    
    // Скрываем сообщение об отсутствии настроек и показываем таблицу
    const tableWrapper = tbody.closest('.table-wrapper');
    if (tableWrapper) {
        tableWrapper.style.display = 'block';
    }
    
    const emptyMessage = document.querySelector('#tab-salary-settings .text-center');
    if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }
}

// Функция обновления статистики зарплаты
function updateSalaryStatistics() {
    // Обновляем статистику в обзоре зарплаты
    fetch('/salary/statistics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем статистику в обзоре
                const statsContainer = document.querySelector('#tab-salary-overview .stats-cards');
                if (statsContainer) {
                    // Обновляем количество выплат в этом месяце
                    const paymentsThisMonthElement = statsContainer.querySelector('.stat-card:nth-child(2) .stat-number');
                    if (paymentsThisMonthElement) {
                        paymentsThisMonthElement.textContent = data.stats.payments_this_month || 0;
                    }
                }
                
                // Обновляем статистику в отчетах
                updateReportsStatistics(data);
            }
        })
        .catch(error => {
            console.error('Error updating statistics:', error);
        });
}

// Функция обновления статистики в отчетах
function updateReportsStatistics(data) {
    // Обновляем статистику по месяцам
    if (data.monthlyStats) {
        updateMonthlyStatistics(data.monthlyStats);
    }
    
    // Обновляем топ сотрудников
    if (data.topEmployees) {
        updateTopEmployeesStatistics(data.topEmployees);
    }
}

// Функция обновления статистики по месяцам
function updateMonthlyStatistics(monthlyStats) {
    const tbody = document.querySelector('.salary-reports-table tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    monthlyStats.forEach(stat => {
        const row = `
            <tr>
                <td>${stat.year}/${stat.month}</td>
                <td>${stat.payments_count}</td>
                <td>
                    <span class="currency-amount" data-amount="${stat.total_amount}">
                        ${formatCurrency(stat.total_amount)}
                    </span>
                </td>
                <td>
                    <span class="currency-amount" data-amount="${stat.avg_amount}">
                        ${formatCurrency(stat.avg_amount)}
                    </span>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Функция обновления топ сотрудников
function updateTopEmployeesStatistics(topEmployees) {
    const tbody = document.querySelector('.salary-reports-table:nth-child(2) tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    topEmployees.forEach(employee => {
        const avgSalary = employee.payments_count > 0 ? employee.total_earned / employee.payments_count : 0;
        const row = `
            <tr>
                <td>${employee.name}</td>
                <td>
                    <span class="currency-amount" data-amount="${employee.total_earned}">
                        ${formatCurrency(employee.total_earned)}
                    </span>
                </td>
                <td>${employee.payments_count}</td>
                <td>
                    <span class="currency-amount" data-amount="${avgSalary}">
                        ${formatCurrency(avgSalary)}
                    </span>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}
