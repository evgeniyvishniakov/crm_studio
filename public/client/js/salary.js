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
                if (data.success) {
                    const setting = data.setting;
                    document.getElementById('salarySettingUserId').value = setting.user_id;
                    document.getElementById('salaryType').value = setting.salary_type;
                    document.getElementById('fixedSalary').value = setting.fixed_salary || '';
                    document.getElementById('servicePercentage').value = setting.service_percentage || '';
                    document.getElementById('salesPercentage').value = setting.sales_percentage || '';

                    toggleSalaryFields();
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
    
    // Скрываем все поля
    fixedSalaryRow.style.display = 'none';
    percentageRow.style.display = 'none';
    
    // Показываем нужные поля в зависимости от типа
    if (salaryType === 'fixed') {
        fixedSalaryRow.style.display = 'flex';
    } else if (salaryType === 'percentage') {
        percentageRow.style.display = 'flex';
    } else if (salaryType === 'mixed') {
        fixedSalaryRow.style.display = 'flex';
        percentageRow.style.display = 'flex';
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
    if (!amount || amount == 0) {
        return window.currencyData && window.currencyData.symbol ? `0 ${window.currencyData.symbol}` : '0 ₴';
    }
    
    const num = parseFloat(amount);
    const symbol = window.currencyData && window.currencyData.symbol ? window.currencyData.symbol : '₴';
    
    if (num % 1 === 0) {
        return `${num.toLocaleString('ru-RU')} ${symbol}`;
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
                            statusCell.innerHTML = '<span class="status-badge status-done">Утверждено</span>';
                        }
                        // Скрываем кнопку утверждения
                        const approveBtn = row.querySelector('button[onclick*="approveSalaryCalculation"]');
                        if (approveBtn) {
                            approveBtn.style.display = 'none';
                        }
                    }
                    window.showNotification('success', 'Расчет утвержден успешно');
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
                     const symbol = window.currencyData && window.currencyData.symbol ? window.currencyData.symbol : '₴';
                    
                    options += `<option value="${calculation.id}" data-amount="${calculation.total_salary}">
                         ${periodStart} - ${periodEnd} (${amount} ${symbol})
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
    document.getElementById('paymentDetailDate').textContent = new Date(payment.payment_date).toLocaleDateString('ru-RU');
    document.getElementById('paymentDetailAmount').textContent = formatCurrency(payment.amount);
    document.getElementById('paymentDetailAmount').setAttribute('data-amount', payment.amount);
    
    // Метод выплаты
    let paymentMethodText = '';
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
            paymentMethodText = payment.payment_method || '-';
    }
    document.getElementById('paymentDetailMethod').textContent = paymentMethodText;
    
    // Статус
    let statusText = '';
    switch(payment.status) {
        case 'pending':
            statusText = 'Ожидает';
            break;
        case 'approved':
            statusText = 'Выплачено';
            break;
        case 'cancelled':
            statusText = 'Отменено';
            break;
        default:
            statusText = 'Неизвестно';
    }
    document.getElementById('paymentDetailStatus').innerHTML = `<span class="status-badge">${statusText}</span>`;
    
    // Дата создания
    document.getElementById('paymentDetailCreatedAt').textContent = new Date(payment.created_at).toLocaleDateString('ru-RU');
    
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
        document.getElementById('paymentDetailApprovedBy').textContent = payment.approved_by_name || 'Неизвестно';
        document.getElementById('paymentDetailApprovedAt').textContent = new Date(payment.approved_at).toLocaleDateString('ru-RU');
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
                    window.showNotification('success', data.message || 'Настройки сохранены успешно');
                    closeSalarySettingModal();
                    // Перезагружаем страницу для обновления данных
                    location.reload();
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
        messageElement.textContent = 'Вы уверены, что хотите удалить этот расчет зарплаты? Это действие нельзя отменить.';
        confirmBtn.textContent = 'Удалить';
        confirmBtn.className = 'btn-delete';
    } else if (type === 'payment') {
        messageElement.textContent = 'Вы уверены, что хотите удалить эту выплату зарплаты? Это действие нельзя отменить.';
        confirmBtn.textContent = 'Удалить';
        confirmBtn.className = 'btn-delete';
    } else if (type === 'setting') {
        messageElement.textContent = 'Вы уверены, что хотите удалить эти настройки зарплаты? Это действие нельзя отменить.';
        confirmBtn.textContent = 'Удалить';
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
    
    // Убираем все дополнительные классы статуса, оставляем только status-badge
    const statusClass = '';
    
    let statusText = '';
    if (calculation.status === 'calculated') {
        statusText = 'Рассчитано';
    } else if (calculation.status === 'approved') {
        statusText = 'Утверждено';
    } else if (calculation.status === 'paid') {
        statusText = 'Оплачено';
    }
    
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
}

// Функция добавления новой выплаты в таблицу
function addPaymentToTable(payment) {
    const tbody = document.getElementById('salary-payments-tbody');
    if (!tbody) return;
    
    const paymentDate = new Date(payment.payment_date).toLocaleDateString('ru-RU');
    const amount = formatCurrency(payment.amount);
    
    let statusBadge = '';
    if (payment.status === 'pending') {
        statusBadge = '<span class="status-badge status-pending">Ожидает</span>';
    } else if (payment.status === 'approved') {
        statusBadge = '<span class="status-badge status-done">Выплачено</span>';
    } else if (payment.status === 'cancelled') {
        statusBadge = '<span class="status-badge status-cancelled">Отменено</span>';
    } else {
        // По умолчанию для новых выплат
        statusBadge = '<span class="status-badge status-pending">Ожидает</span>';
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
    
    // Преобразуем метод выплаты в читаемый вид
    let paymentMethodText = '';
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
            paymentMethodText = payment.payment_method || '-';
    }
    
    const newRow = `
        <tr data-payment-id="${payment.id}">
            <td>${payment.user_name}</td>
            <td>${paymentDate}</td>
            <td>${amount}</td>
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
}

// Функция удаления расчета зарплаты
function deleteSalaryCalculation(id) {
    confirmAction(id, 'calculation');
}

// Функция удаления выплаты зарплаты
function deleteSalaryPayment(id) {
    confirmAction(id, 'payment');
}
