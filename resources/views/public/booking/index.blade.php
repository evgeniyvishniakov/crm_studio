<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $project->project_name }} - {{ __('messages.online_booking') }}</title>
    
    @php
        $isWidget = request()->has('widget');
    @endphp
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google Maps API (закомментировано - нужен API ключ) -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script> -->
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', Arial, sans-serif;
            color: #333;
        }
        
        /* Глобальные стили шрифтов */
        body, .site-header, .site-footer, 
        .booking-header, .booking-body, .step-indicator, 
        .service-card, .master-card, .calendar, .time-slot,
        .form-control, .btn, h1, h2, h3, h4, h5, h6, p, span, div {
            font-family: 'Inter', Arial, sans-serif !important;
        }
        
        /* Шапка сайта */
        .site-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .project-info h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }
        
        .project-info p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .header-toggle {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .header-toggle:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .header-details {
            display: none;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .header-details.active {
            display: block;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .detail-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .detail-content h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .detail-content p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .map-container {
            grid-column: 1 / -1;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            

            
            /* Мобильные стили для кнопок */
            .btn {
                padding: 12px 20px;
                font-size: 16px;
            }
            
            .btn-sm {
                padding: 10px 16px;
                font-size: 14px;
            }
        }
        
        /* Футер сайта */
        .site-footer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0 70px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }
        
    
        

        
        .footer-powered-by {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }
        
        .footer-powered-by a {
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .footer-powered-by a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        

        
        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 10px;
            }
            
            .footer-divider {
                display: none;
            }
            
            .footer-powered-by {
                flex-direction: column;
                gap: 10px;
            }
            
            .footer-powered-by a {
                font-size: 16px;
                padding: 6px 12px;
            }
            

        }
        

        

        
        
        .booking-body {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 800px;
            border: 1px solid #e9ecef;
            padding: 30px;
            position: relative;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin: 0 10px;
            font-size: 20px;

        }
        
        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-right: 8px;
        }
        
       
        
        
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
        }
        
        .service-card, .master-card, .time-slot {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            box-shadow: 0 4px 12px rgb(0 0 0 / 20%);
        }
        
        .service-card:hover, .master-card:hover, .time-slot:hover {
            border-color: #007bff;
            box-shadow: 0 2px 5px rgba(0,123,255,0.2);
        }
        
        .service-card.selected, .master-card.selected, .time-slot.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        
        .service-card h5, .master-card h5 {
            margin: 0 0 5px 0;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .service-card p, .master-card p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Стили для аватарки мастера */
        .master-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .master-avatar {
            flex-shrink: 0;
        }
        
        .master-avatar-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e0e0;
        }
        
        .master-avatar-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            border: 2px solid #e0e0e0;
        }
        
        .master-details {
            flex: 1;
        }
        
        .master-details h5 {
            margin: 0 0 5px 0;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .master-details p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .time-slot {
            text-align: center;
            font-weight: 500;
        }
        
        /* Стили кнопок в CRM стиле */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            border: 2px solid transparent;
        }
        .mt-3{
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        
        /* Кнопка "Назад" всегда слева */
        .btn-back {
            order: 1;
        }
        
        /* Кнопка "Далее" или "Записаться" всегда справа */
        .btn-next, .btn-submit {
            order: 2;
            margin-left: auto;
        }
        /* Primary кнопки - как в CRM */
        .btn-next {
            background: linear-gradient(135deg, #0765ff, #6bacfb);
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
    
        /* Secondary кнопки - как в CRM */
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #9ca3af);
        color: white;
        padding: 10px 16px;
        border: 2px solid #6c757d;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(108, 117, 125, 0.15);
        }
        
        .btn-secondary:hover {
            background: #545b62;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .btn-outline-secondary {
            background: transparent;
            color: #6c757d;
            border: 2px solid #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .btn-sm {
            padding: 8px 12px;
            font-size: 0.875rem;
        }
        
        /* Стили для кнопок с иконками */
        .btn i {
            font-size: 14px;
        }
        
        .btn-sm i {
            font-size: 12px;
        }
        
        /* Анимация для кнопок при наведении */
        .btn:hover i {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }
        .form-control{
            
            box-shadow: 0 4px 12px rgb(0 0 0 / 20%);

        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        
        .alert {
            border-radius: 6px;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        
        .success-message {
            text-align: center;
            padding: 30px;
            color: #28a745;
        }
        .btn-primary{
            background: linear-gradient(135deg, #28a745, #56bb93);
            color: white;
            border: 2px solid #28a745;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #1e7e34, #28a745);
            border-color: #1e7e34;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
                 .success-message i {
             font-size: 3rem;
             margin-bottom: 15px;
         }
         
         /* Стили для деталей записи в CRM стиле */
         .booking-details {
             background: #f8f9fa;
             border: 1px solid #e9ecef;
             border-radius: 6px;
             padding: 20px;
             margin-top: 20px;
         }
         
         .booking-details .alert {
             background: white;
             border: 1px solid #dee2e6;
             border-radius: 6px;
             padding: 15px;
             margin: 0;
         }
         
         .booking-details strong {
             color: #495057;
             font-weight: 600;
         }
         
         .booking-details br {
             margin-bottom: 8px;
         }
         
         /* Стили календаря */
         .calendar-container {
             background: #f8f9fa;
             border-radius: 8px;
             padding: 20px;
             border: 1px solid #e9ecef;
             box-shadow: 0 4px 12px rgb(0 0 0 / 20%);
         }
         
         .calendar-header {
             display: flex;
             justify-content: space-between;
             align-items: center;
             margin-bottom: 20px;
         }
         
         .calendar-header h5 {
             font-weight: 600;
             color: #495057;
         }
         
         .calendar-grid {
             display: grid;
             grid-template-columns: repeat(7, 1fr);
             gap: 8px;
         }
         
         .calendar-day {
             aspect-ratio: 1;
             display: flex;
             align-items: center;
             justify-content: center;
             border-radius: 6px;
             cursor: pointer;
             transition: all 0.2s ease;
             font-weight: 500;
             font-size: 14px;
             background: white;
             border: 1px solid #e9ecef;
         }
         
         .calendar-day:hover {
             background: #e3f2fd;
             border-color: #2196f3;
             color: #1976d2;
         }
         
         .calendar-day.selected {
             background: #2196f3;
             border-color: #2196f3;
             color: white;
         }
         
         .calendar-day.disabled {
             opacity: 0.4;
             cursor: not-allowed;
             background: #f5f5f5;
         }
         
         .calendar-day.other-month {
             opacity: 0.3;
             background: #f8f9fa;
         }
         
         .calendar-day.today {
             background: #fff3e0;
             border-color: #ff9800;
             color: #e65100;
         }
         
         /* Стили временных слотов */
         .time-slots-grid {
             display: grid;
             grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
             gap: 10px;
         }
    </style>
</head>
<body>
    <!-- Шапка сайта -->
    @if(!$isWidget)
    <header class="site-header">
        <div class="header-container">
            <div class="header-top">
                <div class="logo-section">
                    <div class="logo">
                        @if($project->logo)
                            <img src="{{ $project->logo }}" alt="{{ $project->project_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                        @else
                            {{ strtoupper(substr($project->project_name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="project-info">
                        <h1>{{ $project->project_name }}</h1>
                        
                    </div>
                </div>
                <button class="header-toggle" onclick="toggleHeaderDetails()">
                    <i class="fas fa-info-circle"></i> {{ __('messages.salon_information') }}
                </button>
            </div>
            
            <div class="header-details" id="headerDetails">
                <div class="details-grid">
                    @if($project->address)
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="detail-content">
                            <h4>{{ __('messages.address') }}</h4>
                            <p>{{ $project->address }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($project->phone)
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="detail-content">
                            <h4>{{ __('messages.phone') }}</h4>
                            <p><a href="tel:{{ $project->phone }}" style="color: white; text-decoration: none;">{{ $project->phone }}</a></p>
                        </div>
                    </div>
                    @endif
                    
                                   <div class="detail-item">
                   <div class="detail-icon">
                       <i class="fas fa-clock"></i>
                   </div>
                   <div class="detail-content">
                       <h4>{{ __('messages.working_hours') }}</h4>
                       <p>
                           @if($bookingSettings && $bookingSettings->working_hours_start && $bookingSettings->working_hours_end)
                               {{ \Carbon\Carbon::parse($bookingSettings->working_hours_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($bookingSettings->working_hours_end)->format('H:i') }}
                           @else
                               Пн-Пт: 09:00 - 18:00<br>Сб-Вс: 10:00 - 16:00
                           @endif
                       </p>
                   </div>
               </div>
               

                    
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-info"></i>
                        </div>
                        <div class="detail-content">
                            <h4>{{ __('messages.about_us') }}</h4>
                            <p>
                                @if($project->about)
                                    {{ $project->about }}
                                @else
                                    {{ __('messages.default_about_text') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($project->map_latitude && $project->map_longitude)
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-map"></i>
                        </div>
                        <div class="detail-content">
                            <h4>{{ __('messages.map') }}</h4>
                            <div class="map-preview" style="width: 100%; height: 200px; border-radius: 8px; overflow: hidden; margin-top: 10px;">
                                <iframe 
                                    width="100%" 
                                    height="100%" 
                                    frameborder="0" 
                                    scrolling="no" 
                                    marginheight="0" 
                                    marginwidth="0"
                                    src="https://maps.google.com/maps?q={{ $project->map_latitude }},{{ $project->map_longitude }}&z={{ $project->map_zoom ?? 15 }}&output=embed"
                                    style="border: none;">
                                </iframe>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </header>
    @endif

    <div class="booking-body">
            <!-- Индикатор шагов -->
            <div class="step-indicator" id="step-indicator">
                <!-- Активный шаг будет отрисован через JS -->
            </div>
            
            <!-- Шаг 1: Выбор услуги -->
            <div class="step-content active" id="step1">
                
                <div id="services-list">
                    @php
                    // Используем хелпер для форматирования времени
                    use App\Helpers\TimeHelper;
                @endphp
                    @foreach($services as $service)
                        @if($service)
                            @php
                                // Получаем минимальную цену и максимальную длительность для этой услуги
                                $serviceUserServices = $userServices->where('service_id', $service->id);
                                $minPrice = $serviceUserServices->min('price') ?: $service->price;
                                $maxDuration = $serviceUserServices->min('duration') ?: $service->duration ?: 60;
                            @endphp
                            <div class="service-card" data-service-id="{{ $service->id }}" data-duration="{{ $maxDuration }}">
                                <h5>{{ $service->name }}</h5>
                                <p>
                                    @if($minPrice)
                                        {{ __('messages.price_from') }} {{ number_format($minPrice, 0, ',', ' ') }} ₽
                                    @endif
                                    @if($maxDuration)
                                        • {{ __('messages.duration') }}: {{ \App\Helpers\TimeHelper::formatDuration($maxDuration) }}
                                    @endif
                                </p>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-next" onclick="nextStep()" disabled id="next-step-1">
                        {{ __('messages.next') }} <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Шаг 2: Выбор мастера -->
            <div class="step-content" id="step2">
            
                <div id="masters-list">
                    @foreach($users as $user)
                        @if($user)
                            @php
                                $userServicesForUser = $userServices->where('user_id', $user->id);
                            @endphp
                            <div class="master-card" data-user-id="{{ $user->id }}" style="display: none;">
                                <div class="master-info">
                                    <div class="master-avatar">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="master-avatar-img">
                                        @else
                                            <div class="master-avatar-placeholder">{{ substr($user->name, 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div class="master-details">
                                        <h5>{{ $user->name }}</h5>
                                        <p>{{ $user->position ?? 'Мастер' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-secondary btn-back" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                    </button>
                    <button type="button" class="btn btn-next" onclick="nextStep()" disabled id="next-step-2">
                        {{ __('messages.next') }} <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
                         <!-- Шаг 3: Выбор даты и времени -->
             <div class="step-content" id="step3">
                 
                 
                 <!-- Календарь -->
                 <div class="calendar-container mb-4">
                     <div class="calendar-header">
                         <button type="button" class="btn btn-sm btn-outline-secondary" onclick="previousMonth()">
                             <i class="fas fa-chevron-left"></i>
                         </button>
                         <h5 id="current-month" class="mb-0">Июль 2025</h5>
                         <button type="button" class="btn btn-sm btn-outline-secondary" onclick="nextMonth()">
                             <i class="fas fa-chevron-right"></i>
                         </button>
                     </div>
                     
                     <div class="calendar-grid" id="calendar-grid">
                         <!-- Календарь будет загружен через JavaScript -->
                     </div>
                 </div>
                 
                 <!-- Временные слоты -->
                 <div id="time-slots-container" style="display: none;">
                     <h5 class="mb-3">{{ __('messages.available_time') }}</h5>
                     <div id="time-slots" class="time-slots-grid">
                         <!-- Временные слоты будут загружены через JavaScript -->
                     </div>
                 </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-secondary btn-back" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                    </button>
                    <button type="button" class="btn btn-next" onclick="nextStep()" disabled id="next-step-3">
                        {{ __('messages.next') }} <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Шаг 4: Данные клиента -->
            <div class="step-content" id="step4">
               
                <form id="booking-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client-name" class="form-label">{{ __('messages.client_name') }} *</label>
                                <input type="text" class="form-control" id="client-name" required>
                                <small class="form-text text-muted">Используйте только буквы, пробелы, дефисы, точки и апострофы</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client-phone" class="form-label">{{ __('messages.client_phone') }} *</label>
                                <input type="tel" class="form-control" id="client-phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="client-email" class="form-label">{{ __('messages.client_email') }}</label>
                        <input type="email" class="form-control" id="client-email">
                    </div>
                    <div class="mb-3">
                        <label for="client-notes" class="form-label">{{ __('messages.client_notes') }}</label>
                        <textarea class="form-control" id="client-notes" rows="3" placeholder="{{ __('messages.client_notes') }}"></textarea>
                    </div>
                </form>
                <div class="mt-3">
                    <button type="button" class="btn btn-secondary btn-back" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> Назад
                    </button>
                    <button type="button" class="btn btn-primary btn-submit" onclick="submitBooking()" id="submit-booking">
                        {{ __('messages.book_appointment') }} <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
            
            <!-- Сообщение об успехе -->
            <div class="step-content" id="success">
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                                    <h4>{{ __('messages.booking_successful') }}</h4>
                <p>{{ __('messages.we_will_contact_you') }}</p>
                    <div id="booking-details" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Настройки бронирования из сервера
        const bookingSettings = {
            advance_booking_days: {{ $bookingSettings->advance_booking_days ?? 30 }},
            allow_same_day_booking: {{ $bookingSettings->allow_same_day_booking ? 'true' : 'false' }}
        };
        
        let currentStep = 1;
        let selectedService = null;
        let selectedMaster = null;
        let selectedDate = null;
        let selectedTime = null;
        let currentMonth = new Date();
        let masterSchedule = null; // Расписание выбранного мастера
        
                 // Инициализация
         document.addEventListener('DOMContentLoaded', function() {
             // Обработчики для выбора услуги
             document.querySelectorAll('.service-card').forEach(card => {
                 card.addEventListener('click', function() {
                     document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
                     this.classList.add('selected');
                     selectedService = this.dataset.serviceId;
                     document.getElementById('next-step-1').disabled = false;
                     
                     // Показываем только мастеров, которые предоставляют эту услугу
                     filterMastersByService(selectedService);
                 });
             });
             
             // Обработчики для выбора мастера (будут обновлены после фильтрации)
             updateMasterEventListeners();
             
             // Инициализируем календарь
             renderCalendar();
             renderStepIndicator(); // Вызовем при инициализации
         });
        
        function nextStep() {
            if (currentStep < 4) {
                document.getElementById('step' + currentStep).classList.remove('active');
                currentStep++;
                document.getElementById('step' + currentStep).classList.add('active');
                updateStepIndicator();
                
                // Если переходим на шаг 3 и у нас уже есть выбранная дата, загружаем временные слоты
                if (currentStep === 3 && selectedDate && selectedService && selectedMaster) {
                    loadTimeSlots();
                }
                
                // Сбрасываем выбор времени при переходе на шаг 3
                if (currentStep === 3) {
                    selectedTime = null;
                    document.getElementById('next-step-3').disabled = true;
                }
            }
        }
        
        function prevStep() {
            if (currentStep > 1) {
                document.getElementById('step' + currentStep).classList.remove('active');
                currentStep--;
                document.getElementById('step' + currentStep).classList.add('active');
                updateStepIndicator();
                
                // Если уходим с шага 3, скрываем временные слоты
                if (currentStep === 2) {
                    const timeSlotsContainer = document.getElementById('time-slots-container');
                    if (timeSlotsContainer) {
                        timeSlotsContainer.style.display = 'none';
                    }
                }
            }
        }
        
        function updateStepIndicator() {
            renderStepIndicator();
        }
        
        // Функция переключения видимости деталей шапки
        function toggleHeaderDetails() {
            const details = document.getElementById('headerDetails');
            const button = document.querySelector('.header-toggle');
            
            if (details.classList.contains('active')) {
                details.classList.remove('active');
                button.innerHTML = '<i class="fas fa-info-circle"></i> {{ __('messages.salon_information') }}';
            } else {
                details.classList.add('active');
                button.innerHTML = '<i class="fas fa-times"></i> {{ __('messages.hide_information') }}';
                // Инициализируем карту при открытии деталей
                initMap();
            }
        }
        
        // Инициализация карты (если координаты не настроены)
        function initMap() {
            const mapElement = document.getElementById('map');
            if (!mapElement || mapElement.querySelector('iframe')) return; // Карта уже загружена
            
            const address = '{{ $project->address }}';
            if (!address) return;
            
            // Если координаты не настроены, показываем ссылку на Google Maps
            if (!mapElement.querySelector('iframe')) {
                // Очищаем содержимое
                mapElement.innerHTML = '';
                
                // Создаем ссылку на Google Maps
                const link = document.createElement('a');
                link.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
                link.target = '_blank';
                link.style.display = 'flex';
                link.style.alignItems = 'center';
                link.style.justifyContent = 'center';
                link.style.width = '100%';
                link.style.height = '100%';
                link.style.color = 'white';
                link.style.textDecoration = 'none';
                link.style.fontSize = '14px';
                link.innerHTML = '<i class="fas fa-map" style="font-size: 24px; margin-right: 10px;"></i><span>{{ __('messages.open_in_google_maps') }}</span>';
                
                mapElement.appendChild(link);
            }
        }
        
                 // Функции календаря
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
             // Конвертируем американский формат (0=воскресенье) в европейский (0=понедельник)
             let dayOfWeek = firstDay.getDay();
             dayOfWeek = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // 0=воскресенье становится 6, 1=понедельник становится 0
             startDate.setDate(startDate.getDate() - dayOfWeek);
             
             grid.innerHTML = '';
             
             // Дни недели
             const daysOfWeek = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
             daysOfWeek.forEach(day => {
                 const dayHeader = document.createElement('div');
                 dayHeader.className = 'calendar-day';
                 dayHeader.style.fontWeight = 'bold';
                 dayHeader.style.backgroundColor = '#f8f9fa';
                 dayHeader.textContent = day;
                 grid.appendChild(dayHeader);
             });
             
                           // Дни месяца
              let currentDate = new Date(startDate);
              let endDate = new Date(year, month + 1, 0); // Последний день текущего месяца
              
              // Добавляем дни до конца последней недели, но только если они нужны
              const lastDayOfWeek = endDate.getDay();
              const daysToAdd = lastDayOfWeek === 0 ? 0 : 7 - lastDayOfWeek;
              endDate.setDate(endDate.getDate() + daysToAdd);
              
              while (currentDate <= endDate) {
                  const dayElement = document.createElement('div');
                  dayElement.className = 'calendar-day';
                  dayElement.textContent = currentDate.getDate();
                                     const dayDateString = currentDate.getFullYear() + '-' + String(currentDate.getMonth() + 1).padStart(2, '0') + '-' + String(currentDate.getDate()).padStart(2, '0');
                   dayElement.setAttribute('data-date', dayDateString);
                  
                  // Проверяем, является ли день текущего месяца
                  if (currentDate.getMonth() === month) {
                      const today = new Date();
                      const isToday = currentDate.toDateString() === today.toDateString();
                      const isPast = currentDate < today;
                      
                      // Проверяем ограничения по датам из настроек бронирования
                      const maxBookingDate = new Date();
                      maxBookingDate.setDate(today.getDate() + bookingSettings.advance_booking_days);
                      const isTooFarInFuture = currentDate > maxBookingDate;
                      
                      // Проверяем, можно ли записаться в тот же день
                      const isSameDay = currentDate.toDateString() === today.toDateString();
                      const canBookSameDay = bookingSettings.allow_same_day_booking;
                      
                                                                     // Проверяем расписание мастера
                        let isWorkingDay = true;
                        if (masterSchedule) {
                            const dayOfWeek = currentDate.getDay();
                            // Конвертируем американский формат в наш формат
                            const ourDayOfWeek = dayOfWeek === 0 ? 7 : dayOfWeek;
                            const schedule = masterSchedule[ourDayOfWeek];
                            // Если нет расписания на этот день или мастер не работает
                            if (!schedule || !schedule.is_working) {
                                isWorkingDay = false;
                            }
                        } else {
                            // Если нет расписания мастера вообще - все дни недоступны
                            isWorkingDay = false;
                        }
                        
                        // Отладочная информация
        
                     
                      if (isToday) {
                          dayElement.classList.add('today');
                      }
                     
                                             // Проверяем все условия доступности дня
                                             const isAvailable = !isPast && 
                                                                !isTooFarInFuture && 
                                                                isWorkingDay && 
                                                                (isSameDay ? canBookSameDay : true);
                                             
                                             if (isAvailable) {
                                                 // Добавляем обработчик клика только если мы на шаге 3 или если это просто для выбора даты
                                                 const dayDate = new Date(currentDate); // Создаем копию даты
                                                 dayElement.addEventListener('click', () => selectDate(dayDate));
                                             } else {
                                                 dayElement.classList.add('disabled');
                                                 
                                                 // Устанавливаем подсказку в зависимости от причины недоступности
                                                 if (isPast) {
                                                     dayElement.title = 'Прошедшая дата';
                                                 } else if (isTooFarInFuture) {
                                                     dayElement.title = `Запись доступна только на ${bookingSettings.advance_booking_days} дней вперед`;
                                                 } else if (isSameDay && !canBookSameDay) {
                                                     dayElement.title = 'Запись в тот же день недоступна';
                                                 } else if (!isWorkingDay) {
                                                     const dayOfWeek = currentDate.getDay();
                                                     const ourDayOfWeek = dayOfWeek === 0 ? 7 : dayOfWeek;
                                                     const schedule = masterSchedule ? masterSchedule[ourDayOfWeek] : null;
                                                     if (!schedule) {
                                                         dayElement.title = 'Мастер не работает в этот день';
                                                     } else {
                                                         dayElement.title = 'Выходной день мастера';
                                                     }
                                                 }
                                             }
                  } else {
                      dayElement.classList.add('other-month');
                  }
                 
                  grid.appendChild(dayElement);
                  
                  // Переходим к следующему дню
                  currentDate.setDate(currentDate.getDate() + 1);
              }
         }
         
                 function selectDate(date) {
            const dateString = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');

            
            // --- ПРОВЕРКА: ограничения по датам из настроек бронирования ---
            const today = new Date();
            const isToday = date.toDateString() === today.toDateString();
            const isPast = date < today;
            
            // Проверяем ограничения по датам
            const maxBookingDate = new Date();
            maxBookingDate.setDate(today.getDate() + bookingSettings.advance_booking_days);
            const isTooFarInFuture = date > maxBookingDate;
            
            if (isPast) {
                alert('Нельзя выбрать прошедшую дату!');
                return;
            }
            
            if (isTooFarInFuture) {
                alert(`Запись доступна только на ${bookingSettings.advance_booking_days} дней вперед!`);
                return;
            }
            
            if (isToday && !bookingSettings.allow_same_day_booking) {
                alert('Запись в тот же день недоступна!');
                return;
            }
            
            // --- ПРОВЕРКА: рабочий ли день у мастера ---
            if (masterSchedule) {
                const dayOfWeek = date.getDay();
                const ourDayOfWeek = dayOfWeek === 0 ? 7 : dayOfWeek;
                const schedule = masterSchedule[ourDayOfWeek];
    
                if (!schedule || !schedule.is_working) {
                    alert('Мастер не работает в этот день!');
                    // Убираем выделение с дня, если он не рабочий
                    const clickedDayElement = document.querySelector(`.calendar-day[data-date="${dateString}"]`);
                    if (clickedDayElement) {
                        clickedDayElement.classList.remove('selected');
                    }
                    return;
                }
            }
            
            // Убираем выделение со всех дней
            document.querySelectorAll('.calendar-day').forEach(day => {
                day.classList.remove('selected');
            });
            
            // Находим и выделяем выбранный день по data-date атрибуту
            const clickedDayElement = document.querySelector(`.calendar-day[data-date="${dateString}"]`);
            if (clickedDayElement) {
                clickedDayElement.classList.add('selected');
            }
            
            selectedDate = dateString;
            
            // Показываем сообщение о выбранной дате на шагах 1 и 2
            if (currentStep === 1 || currentStep === 2) {
                const dateStr = new Date(date).toLocaleDateString('ru-RU', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                alert(`Выбрана дата: ${dateStr}\n\nПерейдите к следующему шагу для выбора времени.`);
                return; // Выходим из функции, не загружая временные слоты
            }
            
            // Загружаем временные слоты только если мы на шаге 3
            if (selectedService && selectedMaster && currentStep === 3) {
                loadTimeSlots();
            }
        }
         
                   function previousMonth() {
              currentMonth.setMonth(currentMonth.getMonth() - 1);
              renderCalendar();
              if (selectedService && selectedMaster && masterSchedule) {
                  setTimeout(checkMonthAvailability, 100);
              }
          }
          
          function nextMonth() {
              currentMonth.setMonth(currentMonth.getMonth() + 1);
              renderCalendar();
              if (selectedService && selectedMaster && masterSchedule) {
                  setTimeout(checkMonthAvailability, 100);
              }
          }
         
                 function loadTimeSlots() {

                selectedService,
                selectedMaster,
                selectedDate
            });
            
            const timeSlotsContainer = document.getElementById('time-slots-container');
            const slotsDiv = document.getElementById('time-slots');
            
            // Проверяем, что элементы существуют
            if (!timeSlotsContainer || !slotsDiv) {


                return;
            }
            
            timeSlotsContainer.style.display = 'block';
            slotsDiv.innerHTML = '<div class="loading">Загрузка доступного времени...</div>';
            
            fetch('{{ route("public.booking.slots", $project->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    project_id: {{ $project->id }},
                    user_id: selectedMaster,
                    service_id: selectedService,
                    date: selectedDate
                })
            })
                         .then(response => {

                 if (!response.ok) {
                     throw new Error(`HTTP error! status: ${response.status}`);
                 }
                 return response.json();
             })
                         .then(data => {

                if (data.success) {
                    displayTimeSlots(data.slots);
                } else {
                    if (slotsDiv) {
                        slotsDiv.innerHTML = '<div class="alert alert-warning">' + data.message + '</div>';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (slotsDiv) {
                    slotsDiv.innerHTML = '<div class="alert alert-danger">Ошибка загрузки времени: ' + error.message + '</div>';
                }
            });
        }
        
                         function displayTimeSlots(slots) {
            const slotsDiv = document.getElementById('time-slots');
            if (!slotsDiv) {

                return;
            }
            if (slots.length === 0) {
                slotsDiv.innerHTML = '<div class="alert alert-warning">Нет доступного времени на эту дату</div>';
                return;
            }
             
             let html = '';
             slots.forEach(slot => {
                 html += `<div class="time-slot" data-time="${slot.time}">${slot.time}</div>`;
             });
             slotsDiv.innerHTML = html;
             
             // Обработчики для выбора времени
             document.querySelectorAll('.time-slot').forEach(slot => {
                 slot.addEventListener('click', function() {
                     document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                     this.classList.add('selected');
                     selectedTime = this.dataset.time;
                     document.getElementById('next-step-3').disabled = false;
                 });
             });
         }
        
        // Флаг для предотвращения двойной отправки
        let isSubmitting = false;
        
        function submitBooking() {
            // Проверяем, не отправляется ли уже форма
            if (isSubmitting) {

                return;
            }
            
            const form = document.getElementById('booking-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Устанавливаем флаг отправки
            isSubmitting = true;
            
            const submitBtn = document.getElementById('submit-booking');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отправка...';
            
            const formData = {
                project_id: {{ $project->id }},
                service_id: selectedService,
                user_id: selectedMaster,
                date: selectedDate,
                time: selectedTime,
                client_name: document.getElementById('client-name').value,
                client_phone: document.getElementById('client-phone').value,
                client_email: document.getElementById('client-email').value,
                client_notes: document.getElementById('client-notes').value
            };
            

            
            fetch('{{ route("public.booking.store", $project->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(formData)
            })
            .then(response => {

                
                if (!response.ok) {
                    // Если ответ не OK, пробуем получить текст для диагностики
                    return response.text().then(text => {
                        console.error('Error response text:', text);
                        throw new Error(`HTTP error! status: ${response.status}, response: ${text.substring(0, 200)}`);
                    });
                }
                
                // Проверяем, что ответ действительно JSON
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                    });
                }
                
                return response.json();
            })
            .then(data => {

                if (data.success) {
                    showSuccess(data.booking);
                    // Сбрасываем флаг при успешной отправке
                    isSubmitting = false;
                } else {
                    alert('Ошибка: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '{{ __('messages.book_appointment') }} <i class="fas fa-check"></i>';
                    isSubmitting = false; // Сбрасываем флаг при ошибке
                }
            })
            .catch(error => {
                console.error('Submit error:', error);
                alert('{{ __('messages.booking_error') }}: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __('messages.book_appointment') }} <i class="fas fa-check"></i>';
                isSubmitting = false; // Сбрасываем флаг при ошибке
            });
        }
        
        function filterMastersByService(serviceId) {
            const masterCards = document.querySelectorAll('.master-card');
            const userServicesData = @json($userServices->map(function($us) {
                return ['user_id' => $us->user_id, 'service_id' => $us->service_id];
            }));
            
            masterCards.forEach(card => {
                const userId = parseInt(card.dataset.userId);
                const hasService = userServicesData.some(us => 
                    us.user_id === userId && us.service_id === parseInt(serviceId)
                );
                
                if (hasService) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                    card.classList.remove('selected');
                }
            });
            
                         // Сбрасываем выбор мастера
             selectedMaster = null;
             masterSchedule = null; // Сбрасываем расписание
             selectedTime = null; // Сбрасываем выбор времени
             selectedDate = null; // Сбрасываем выбранную дату
             document.getElementById('next-step-2').disabled = true;
             document.getElementById('next-step-3').disabled = true;
             updateMasterEventListeners();
             
             // Очищаем временные слоты
             const timeSlotsContainer = document.getElementById('time-slots-container');
             if (timeSlotsContainer) {
                 timeSlotsContainer.style.display = 'none';
             }
             
             // Убираем выделение с выбранной даты в календаре
             document.querySelectorAll('.calendar-day').forEach(day => {
                 day.classList.remove('selected');
             });
             
             // Перерисовываем календарь без расписания
             renderCalendar();
        }
        
                 function updateMasterEventListeners() {
             // Удаляем старые обработчики
             document.querySelectorAll('.master-card').forEach(card => {
                 card.replaceWith(card.cloneNode(true));
             });
             
             // Добавляем новые обработчики
             document.querySelectorAll('.master-card').forEach(card => {
                 card.addEventListener('click', function() {
                     document.querySelectorAll('.master-card').forEach(c => c.classList.remove('selected'));
                     this.classList.add('selected');
                     selectedMaster = this.dataset.userId;
                     document.getElementById('next-step-2').disabled = false;
                     
                                           // Загружаем расписание мастера
                      loadMasterSchedule(selectedMaster);
                      
                      // Очищаем временные слоты при смене мастера
                      selectedTime = null;
                      selectedDate = null; // Сбрасываем выбранную дату
                      const timeSlotsContainer = document.getElementById('time-slots-container');
                      if (timeSlotsContainer) {
                          timeSlotsContainer.style.display = 'none';
                      }
                      document.getElementById('next-step-3').disabled = true;
                      
                      // Убираем выделение с выбранной даты в календаре
                      document.querySelectorAll('.calendar-day').forEach(day => {
                          day.classList.remove('selected');
                      });
                 });
             });
         }
         
                   function loadMasterSchedule(userId) {
              fetch('{{ route("public.booking.schedule", $project->slug) }}', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                  },
                  body: JSON.stringify({
                      user_id: userId
                  })
              })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      masterSchedule = data.schedule;
                      // Перерисовываем календарь с учетом расписания
                      renderCalendar();
                      // Проверяем доступность времени для всех дней месяца
                      if (selectedService && selectedMaster) {
                          checkMonthAvailability();
                      }
                  }
              })
              .catch(error => {
                  console.error('Error loading master schedule:', error);
              });
          }
          
          // Функция проверки доступности времени на конкретный день
          function checkDayAvailability(date, userId, serviceId) {
              return fetch('{{ route("public.booking.slots", $project->slug) }}', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                  },
                  body: JSON.stringify({
                      project_id: {{ $project->id }},
                      user_id: userId,
                      service_id: serviceId,
                      date: date
                  })
              })
              .then(response => response.json())
              .then(data => {
                  return data.success && data.slots && data.slots.length > 0;
              })
              .catch(error => {
                  console.error('Error checking availability:', error);
                  return false;
              });
          }
          
          // Функция проверки доступности времени для всех дней месяца
          function checkMonthAvailability() {
              const year = currentMonth.getFullYear();
              const month = currentMonth.getMonth();
              const daysInMonth = new Date(year, month + 1, 0).getDate();
              
              for (let day = 1; day <= daysInMonth; day++) {
                  const date = new Date(year, month, day);
                  const today = new Date();
                  
                  // Проверяем только будущие дни
                  if (date >= today) {
                                             const dateStr = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
                      const dayOfWeek = date.getDay();
                      const ourDayOfWeek = dayOfWeek === 0 ? 7 : dayOfWeek;
                      const schedule = masterSchedule ? masterSchedule[ourDayOfWeek] : null;
                      
                      // Если есть расписание и мастер работает
                      if (schedule && schedule.is_working) {
                          checkDayAvailability(dateStr, selectedMaster, selectedService).then(available => {
                              const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
                              if (dayElement && !available) {
                                  dayElement.classList.add('disabled');
                                  dayElement.title = 'Нет свободного времени';
                                  // Удаляем обработчик клика
                                  const newDayElement = dayElement.cloneNode(true);
                                  dayElement.parentNode.replaceChild(newDayElement, dayElement);
                              }
                          });
                      }
                  }
              }
          }
        
                         function showSuccess(booking) {
            // Сбрасываем флаг отправки при успехе
            isSubmitting = false;
            
            document.getElementById('step4').classList.remove('active');
            document.getElementById('success').classList.add('active');
            
            const details = document.getElementById('booking-details');
            details.className = 'booking-details';
            
            details.innerHTML = `
                <div class="alert alert-info">
                    <strong>{{ __('messages.appointment_details') }}:</strong><br>
                    {{ __('messages.service') }}: ${booking.service_name}<br>
                    {{ __('messages.master') }}: ${booking.master_name}<br>
                    {{ __('messages.date') }}: ${booking.date}<br>
                    {{ __('messages.time') }}: ${booking.time}
                </div>
            `;
        }

            const stepTitles = [null, '{{ __('messages.select_service') }}', '{{ __('messages.select_master') }}', '{{ __('messages.select_date') }} и {{ __('messages.select_time') }}', '{{ __('messages.your_data') }}'];

    function renderStepIndicator() {
        const indicator = document.getElementById('step-indicator');
        indicator.innerHTML = `
            <div class="step active">
                
                <span>${stepTitles[currentStep]}</span>
            </div>
        `;
             }
    </script>
    
    <!-- Футер сайта -->
    @if(!$isWidget)
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-bottom">
                <div class="footer-powered-by">
                    <span>{{ __('messages.powered_by') }}</span>
                    <a href="https://trimora.com" target="_blank" title="Trimora - {{ __('messages.beauty_salon_management_system') }}">
                        Trimora
                    </a>
                </div>
            </div>
        </div>
    </footer>
    @endif
</body>
</html> 