<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->project_name }} - Онлайн запись</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .booking-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px auto;
            max-width: 800px;
        }
        
        .booking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .booking-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        
        .booking-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .booking-body {
            padding: 40px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin: 0 15px;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .step.active .step-number {
            background: #667eea;
            color: white;
        }
        
        .step.completed .step-number {
            background: #28a745;
            color: white;
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
        }
        
        .service-card, .master-card, .time-slot {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .service-card:hover, .master-card:hover, .time-slot:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        
        .service-card.selected, .master-card.selected, .time-slot.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
            border-radius: 25px;
            padding: 12px 30px;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            border-color: #667eea;
        }
        
        .calendar {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .calendar-day:hover {
            background: #667eea;
            color: white;
        }
        
        .calendar-day.selected {
            background: #667eea;
            color: white;
        }
        
        .calendar-day.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="booking-container">
            <!-- Заголовок -->
            <div class="booking-header">
                <h1>{{ $project->project_name }}</h1>
                <p>Онлайн запись на услуги</p>
            </div>
            
            <!-- Индикатор шагов -->
            <div class="booking-body">
                <div class="step-indicator">
                    <div class="step active" id="step-1">
                        <div class="step-number">1</div>
                        <span>Услуга</span>
                    </div>
                    <div class="step" id="step-2">
                        <div class="step-number">2</div>
                        <span>Мастер</span>
                    </div>
                    <div class="step" id="step-3">
                        <div class="step-number">3</div>
                        <span>Дата и время</span>
                    </div>
                    <div class="step" id="step-4">
                        <div class="step-number">4</div>
                        <span>Подтверждение</span>
                    </div>
                </div>
                
                <!-- Шаг 1: Выбор услуги -->
                <div class="step-content active" id="step-content-1">
                    <h3 class="mb-4">Выберите услугу</h3>
                    <div class="row">
                        @foreach($services as $service)
                        <div class="col-md-6 mb-3">
                            <div class="service-card" data-service-id="{{ $service->id }}" data-service-name="{{ $service->name }}" data-service-price="{{ $service->price }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">{{ $service->name }}</h5>
                                        <p class="mb-0 text-muted">{{ $service->description }}</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="h5 mb-0">{{ number_format($service->price) }} ₽</div>
                                        <small class="text-muted">{{ $service->duration }} мин</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-end mt-4">
                        <button class="btn btn-primary" onclick="nextStep()" id="next-step-1" disabled>
                            Далее <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Шаг 2: Выбор мастера -->
                <div class="step-content" id="step-content-2">
                    <h3 class="mb-4">Выберите мастера</h3>
                    <div class="row">
                        @foreach($users as $user)
                        <div class="col-md-6 mb-3">
                            <div class="master-card" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-user-circle fa-3x text-muted"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $user->name }}</h5>
                                        <p class="mb-0 text-muted">{{ $user->role }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-primary" onclick="prevStep()">
                            <i class="fas fa-arrow-left"></i> Назад
                        </button>
                        <button class="btn btn-primary" onclick="nextStep()" id="next-step-2" disabled>
                            Далее <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Шаг 3: Выбор даты и времени -->
                <div class="step-content" id="step-content-3">
                    <h3 class="mb-4">Выберите дату и время</h3>
                    
                    <div class="calendar">
                        <div class="calendar-header">
                            <button class="btn btn-sm btn-outline-primary" onclick="previousMonth()">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h5 id="current-month">Июль 2025</h5>
                            <button class="btn btn-sm btn-outline-primary" onclick="nextMonth()">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        
                        <div class="calendar-grid" id="calendar-grid">
                            <!-- Календарь будет загружен через JavaScript -->
                        </div>
                    </div>
                    
                    <div id="time-slots-container" style="display: none;">
                        <h5 class="mt-4 mb-3">Доступное время</h5>
                        <div class="time-slots" id="time-slots">
                            <!-- Временные слоты будут загружены через JavaScript -->
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-primary" onclick="prevStep()">
                            <i class="fas fa-arrow-left"></i> Назад
                        </button>
                        <button class="btn btn-primary" onclick="nextStep()" id="next-step-3" disabled>
                            Далее <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Шаг 4: Подтверждение -->
                <div class="step-content" id="step-content-4">
                    <h3 class="mb-4">Подтверждение записи</h3>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Выбранная услуга:</h6>
                                    <p id="confirm-service"></p>
                                    
                                    <h6>Мастер:</h6>
                                    <p id="confirm-master"></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Дата и время:</h6>
                                    <p id="confirm-datetime"></p>
                                    
                                    <h6>Стоимость:</h6>
                                    <p id="confirm-price"></p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6>Ваши контакты:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client-name">Имя *</label>
                                        <input type="text" class="form-control" id="client-name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client-phone">Телефон *</label>
                                        <input type="tel" class="form-control" id="client-phone" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-3">
                                <label for="client-email">Email</label>
                                <input type="email" class="form-control" id="client-email">
                            </div>
                            
                            <div class="form-group mt-3">
                                <label for="client-comment">Комментарий</label>
                                <textarea class="form-control" id="client-comment" rows="3" placeholder="Дополнительная информация..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-primary" onclick="prevStep()">
                            <i class="fas fa-arrow-left"></i> Назад
                        </button>
                        <button class="btn btn-primary" onclick="confirmBooking()" id="confirm-booking">
                            <i class="fas fa-check"></i> Подтвердить запись
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentStep = 1;
        let selectedService = null;
        let selectedMaster = null;
        let selectedDate = null;
        let selectedTime = null;
        let currentMonth = new Date();
        
        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendar();
        });
        
        // Выбор услуги
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                selectedService = {
                    id: this.dataset.serviceId,
                    name: this.dataset.serviceName,
                    price: this.dataset.servicePrice
                };
                document.getElementById('next-step-1').disabled = false;
            });
        });
        
        // Выбор мастера
        document.querySelectorAll('.master-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.master-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                selectedMaster = {
                    id: this.dataset.userId,
                    name: this.dataset.userName
                };
                document.getElementById('next-step-2').disabled = false;
            });
        });
        
        // Переход к следующему шагу
        function nextStep() {
            if (currentStep < 4) {
                currentStep++;
                updateStepIndicator();
                showStep(currentStep);
            }
        }
        
        // Переход к предыдущему шагу
        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateStepIndicator();
                showStep(currentStep);
            }
        }
        
        // Обновление индикатора шагов
        function updateStepIndicator() {
            document.querySelectorAll('.step').forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    step.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    step.classList.add('active');
                }
            });
        }
        
        // Показать шаг
        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`step-content-${step}`).classList.add('active');
            
            if (step === 4) {
                updateConfirmation();
            }
        }
        
        // Рендер календаря
        function renderCalendar() {
            const grid = document.getElementById('calendar-grid');
            const monthYear = document.getElementById('current-month');
            
            const year = currentMonth.getFullYear();
            const month = currentMonth.getMonth();
            
            monthYear.textContent = new Date(year, month).toLocaleDateString('ru-RU', { 
                month: 'long', 
                year: 'numeric' 
            });
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            grid.innerHTML = '';
            
            // Дни недели
            const daysOfWeek = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
            daysOfWeek.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'calendar-day';
                dayHeader.style.fontWeight = 'bold';
                dayHeader.textContent = day;
                grid.appendChild(dayHeader);
            });
            
            // Дни месяца
            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);
                
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = date.getDate();
                
                // Проверяем, является ли день текущего месяца
                if (date.getMonth() === month) {
                    const today = new Date();
                    const isToday = date.toDateString() === today.toDateString();
                    const isPast = date < today;
                    
                    if (isToday) {
                        dayElement.classList.add('selected');
                    }
                    
                    if (!isPast) {
                        dayElement.addEventListener('click', () => selectDate(date));
                    } else {
                        dayElement.classList.add('disabled');
                    }
                } else {
                    dayElement.style.opacity = '0.3';
                }
                
                grid.appendChild(dayElement);
            }
        }
        
        // Выбор даты
        function selectDate(date) {
            document.querySelectorAll('.calendar-day').forEach(day => {
                day.classList.remove('selected');
            });
            event.target.classList.add('selected');
            
            selectedDate = date;
            loadTimeSlots(date);
        }
        
        // Загрузка временных слотов
        function loadTimeSlots(date) {
            const container = document.getElementById('time-slots-container');
            const slotsDiv = document.getElementById('time-slots');
            
            container.style.display = 'block';
            slotsDiv.innerHTML = '<div class="loading"><div class="spinner"></div><p>Загрузка доступного времени...</p></div>';
            
            fetch('{{ route("public.booking.slots", $project->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    date: date.toISOString().split('T')[0],
                    service_id: selectedService.id,
                    user_id: selectedMaster.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTimeSlots(data.slots);
                } else {
                    slotsDiv.innerHTML = '<p class="text-muted">Нет доступного времени на эту дату</p>';
                }
            })
            .catch(error => {
                slotsDiv.innerHTML = '<p class="text-danger">Ошибка загрузки времени</p>';
            });
        }
        
        // Рендер временных слотов
        function renderTimeSlots(slots) {
            const slotsDiv = document.getElementById('time-slots');
            slotsDiv.innerHTML = '';
            
            if (slots.length === 0) {
                slotsDiv.innerHTML = '<p class="text-muted">Нет доступного времени на эту дату</p>';
                return;
            }
            
            slots.forEach(slot => {
                const slotElement = document.createElement('div');
                slotElement.className = 'time-slot';
                slotElement.textContent = slot;
                slotElement.addEventListener('click', () => {
                    document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                    slotElement.classList.add('selected');
                    selectedTime = slot;
                    document.getElementById('next-step-3').disabled = false;
                });
                slotsDiv.appendChild(slotElement);
            });
        }
        
        // Навигация по месяцам
        function previousMonth() {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
            renderCalendar();
        }
        
        function nextMonth() {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
            renderCalendar();
        }
        
        // Обновление подтверждения
        function updateConfirmation() {
            document.getElementById('confirm-service').textContent = selectedService.name;
            document.getElementById('confirm-master').textContent = selectedMaster.name;
            document.getElementById('confirm-datetime').textContent = `${selectedDate.toLocaleDateString('ru-RU')} в ${selectedTime}`;
            document.getElementById('confirm-price').textContent = `${selectedService.price} ₽`;
        }
        
        // Подтверждение записи
        function confirmBooking() {
            const name = document.getElementById('client-name').value;
            const phone = document.getElementById('client-phone').value;
            const email = document.getElementById('client-email').value;
            const comment = document.getElementById('client-comment').value;
            
            if (!name || !phone) {
                alert('Пожалуйста, заполните обязательные поля');
                return;
            }
            
            const submitBtn = document.getElementById('confirm-booking');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отправка...';
            
            fetch('{{ route("public.booking.store", $project->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    service_id: selectedService.id,
                    user_id: selectedMaster.id,
                    date: selectedDate.toISOString().split('T')[0],
                    time: selectedTime,
                    client_name: name,
                    client_phone: phone,
                    client_email: email,
                    comment: comment
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Запись успешно создана! Мы свяжемся с вами для подтверждения.');
                    window.location.reload();
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                alert('Произошла ошибка при создании записи');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    </script>
</body>
</html> 