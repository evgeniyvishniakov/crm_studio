<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'CRM Studio')</title>
    <meta name="description" content="CRM Studio - Система управления салоном красоты">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="current-language" content="{{ \App\Helpers\LanguageHelper::getCurrentLanguage() }}">
    
    <!-- Переводы для JavaScript -->
    <script>
        window.translations = {
            minute: '{{ __("messages.minute") }}',
            hour: '{{ __("messages.hour") }}',
            hours: '{{ __("messages.hours") }}',
            hours_many: '{{ __("messages.hours_many") }}',
            duration_prefix: '{{ __("messages.duration_prefix") }}',
            base_duration: '{{ __("messages.base_duration") }}',
            edit: '{{ __("messages.edit") }}',
            delete: '{{ __("messages.delete") }}',
            description: '{{ __("messages.description") }}',
            active: '{{ __("messages.active") }}',
            inactive: '{{ __("messages.inactive") }}',
            category_active: '{{ __("messages.category_active") }}',
            category_inactive: '{{ __("messages.category_inactive") }}',
            brand_active: '{{ __("messages.brand_active") }}',
            brand_inactive: '{{ __("messages.brand_inactive") }}',
            brand_country: '{{ __("messages.brand_country") }}',
            website: '{{ __("messages.website") }}',
            supplier_active: '{{ __("messages.supplier_active") }}',
            supplier_inactive: '{{ __("messages.supplier_inactive") }}',
            supplier_contact_person: '{{ __("messages.supplier_contact_person") }}',
            phone: '{{ __("messages.phone") }}',
            email: '{{ __("messages.email") }}',
            employee_not_specified: '{{ __("messages.employee_not_specified") }}',
            // Дни недели
            monday: '{{ __("messages.monday") }}',
            tuesday: '{{ __("messages.tuesday") }}',
            wednesday: '{{ __("messages.wednesday") }}',
            thursday: '{{ __("messages.thursday") }}',
            friday: '{{ __("messages.friday") }}',
            saturday: '{{ __("messages.saturday") }}',
            sunday: '{{ __("messages.sunday") }}',
            // Статусы и интервал
            working: '{{ __("messages.working_day_status") }}',
            day_off: '{{ __("messages.day_off") }}',
            no_notes: '{{ __("messages.no_notes") }}',
            interval: '{{ __("messages.interval") }}',
            interval_minutes: '{{ __("messages.interval_minutes") }}',
            working_hours: '{{ __("messages.working_hours") }}',
            notes: '{{ __("messages.notes") }}'
        };
    </script>

    <!-- Favicon -->
    <link rel="apple-touch-icon" href="">
    <link rel="shortcut icon" href="">

    <!-- Base Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css">
    <link rel="stylesheet" href="{{ asset('client/css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/style.css') }}?v=1.3">
    <link rel="stylesheet" href="{{ asset('client/css/common.css') }}?v=3.1">
    <link rel="stylesheet" href="/client/css/notifications.css">
    <link rel="stylesheet" href="{{ asset('client/css/responsive.css') }}">
    <!-- Page-specific styles -->
    @if(request()->routeIs('dashboard'))
        <link rel="stylesheet" href="{{ asset('client/css/dashboard.css') }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Base Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/ru.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/en.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/uk.global.min.js'></script>
    <script src="{{ asset('client/js/main.js') }}"></script>
    <script src="{{ asset('client/js/notifications.js') }}"></script>
    <script src="{{ asset('client/js/currency-manager.js') }}"></script>
    <script src="{{ asset('client/js/language-manager.js') }}"></script>
    <script src="{{ asset('client/js/calendar-localization.js') }}"></script>
    <script src="{{ asset('client/js/common.js') }}"></script>
    <script src="{{ asset('client/js/layouts.js') }}"></script>
    <script src="{{ asset('client/js/telegram-settings.js') }}"></script>
    <script src="{{ asset('client/js/email-settings.js') }}"></script>
    <script src="{{ asset('client/js/widget-settings.js') }}"></script>

    <!-- Данные валюты для JavaScript -->
    @php
        use App\Helpers\CurrencyHelper;
        $currencyData = CurrencyHelper::getCurrencyData();
    @endphp
    <script>
        window.currencyData = @json($currencyData);
    </script>

    <!-- Page-specific scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/en.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/uk.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Стили для узкого скроллбара -->
    <style>
        /* Узкий скроллбар для меню */
        #left-panel .main-menu::-webkit-scrollbar,
        #left-panel .navbar-nav::-webkit-scrollbar,
        .navbar-nav::-webkit-scrollbar,
        .main-menu::-webkit-scrollbar,
        aside.left-panel::-webkit-scrollbar,
        .sidebar-menu::-webkit-scrollbar,
        nav.navbar::-webkit-scrollbar {
            width: 3px !important;
            background: transparent !important;
        }
        
        /* Отступ справа для скроллбара */
        #left-panel .main-menu,
        #left-panel .navbar-nav,
        .navbar-nav,
        .main-menu,
        aside.left-panel,
        .sidebar-menu {
            padding-right: 0px !important;
            margin-left: 3px !important;
            padding-bottom: 30px;
        }
        
        #left-panel .main-menu::-webkit-scrollbar-thumb,
        #left-panel .navbar-nav::-webkit-scrollbar-thumb,
        .navbar-nav::-webkit-scrollbar-thumb,
        .main-menu::-webkit-scrollbar-thumb,
        aside.left-panel::-webkit-scrollbar-thumb,
        .sidebar-menu::-webkit-scrollbar-thumb,
        nav.navbar::-webkit-scrollbar-thumb {
            background: #b0b8c1 !important;
            border-radius: 2px !important;
        }
        
        #left-panel .main-menu::-webkit-scrollbar-thumb:hover,
        #left-panel .navbar-nav::-webkit-scrollbar-thumb:hover,
        .navbar-nav::-webkit-scrollbar-thumb:hover,
        .main-menu::-webkit-scrollbar-thumb:hover,
        aside.left-panel::-webkit-scrollbar-thumb:hover,
        .sidebar-menu::-webkit-scrollbar-thumb:hover,
        nav.navbar::-webkit-scrollbar-thumb:hover {
            background: #7a869a !important;
        }
        
        /* Для Firefox */
        #left-panel .main-menu,
        #left-panel .navbar-nav,
        .navbar-nav,
        .main-menu,
        aside.left-panel,
        .sidebar-menu,
        nav.navbar {
            scrollbar-width: thin !important;
            scrollbar-color: #b0b8c1 #f5f6fa !important;
        }
        
        /* Расстояние между иконками и текстом в меню */
        #left-panel .navbar-nav li a .menu-icon {
            margin-right: 6px !important;
        }
        
        #left-panel .navbar-nav li a {
            padding-left: 20px !important;
        }
        
        /* Для подменю */
        #left-panel .sub-menu li a {
            padding-left: 35px !important;
        }
        
        #left-panel .sub-menu li a .menu-icon {
            margin-right: 6px !important;
        }
        
        /* Уменьшение размера navbar */
        .navbar.navbar-expand-sm.navbar-default {
            padding:20px 0 !important;
            min-height: auto !important;
        }
        
        /* Уменьшаем ширину левой панели */
        body:not(.open) aside.left-panel {
            width: 250px !important; /* Было 300px */
        }
        
        body:not(.open) #left-panel {
            width: 250px !important;
            max-width: 250px !important;
        }
        
        /* Корректируем отступ основного контента */
        .right-panel {
            margin-left: 250px;  /* Устанавливаем отступ 350px */
        }
        
        /* Стили для контейнера уведомлений */
        #notification-container {
            position: fixed;
            top: 0;
            right: 0;
            z-index: 9999;
            pointer-events: none;
        }
        
        #notification-container .notification {
            pointer-events: auto;
        }
        
        .navbar .navbar-nav li > a {
            padding: 10px 0 !important; /* Уменьшаем вертикальные отступы */
            line-height: 20px !important; /* Уменьшаем высоту строки */
            font-size: 15px !important; /* Уменьшаем размер шрифта */
        }
        
        .navbar .navbar-nav li.menu-title {
            padding: 6px 0 4px 0 !important; /* Уменьшаем отступы заголовков */
            font-size: 14px !important;
        }
        
        /* Исправляем позиционирование стрелочек после уменьшения ширины */
        .navbar .navbar-nav li.menu-item-has-children a:before {
            right: 5px !important; /* Корректируем позицию стрелочки */
            top: 50% !important;
            margin-top: -4px !important;
        }
        
        /* Исправляем выравнивание иконок после изменения размеров */
        .navbar .navbar-nav li > a .menu-icon {
            margin-top: 0px !important; /* Убираем отступ сверху */
            vertical-align: top !important; /* Выравниваем по верху */
            line-height: 20px !important; /* Соответствует высоте текста */
        }
        
        /* Для подменю тоже корректируем иконки */
        #left-panel .sub-menu li a .menu-icon {
            margin-top: 0px !important; /* Убираем отступ */
            vertical-align: top !important;
        }
        
        /* Поворот стрелочек в подменю - стабильное позиционирование */
        .navbar .navbar-nav li.menu-item-has-children a:before {
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94), border-color 0.6s ease !important;
            transform-origin: center center !important;
            position: absolute !important;
            right: 8px !important;
            top: 50% !important;
            margin-top: -4px !important;
            transform: rotate(45deg) !important; /* Базовое состояние - вправо */
            border-color: #607d8b #607d8b transparent transparent !important;
        }
        
        /* Стили для свернутого меню на десктопе */
        body.open .navbar .navbar-nav li.menu-item-has-children a:before {
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94), border-color 0.6s ease !important;
            transform-origin: center center !important;
            position: absolute !important;
            right: -6px !important;
            top: 37% !important;
            margin-top: -4px !important;
            transform: rotate(45deg) !important;
            border-color: #607d8b #607d8b transparent transparent !important;
        }
        
        /* При раскрытии меню - стрелочка поворачивается вниз */
        .navbar .navbar-nav li.menu-item-has-children.show a:before,
        .navbar .navbar-nav li.menu-item-has-children a[aria-expanded="true"]:before,
        .navbar .navbar-nav li.menu-item-has-children .dropdown-toggle[aria-expanded="true"]:before {
            transform: rotate(135deg) !important;
            border-color: #03a9f3 #03a9f3 transparent transparent !important;
            right: 15px !important;
            top: 50% !important;
            margin-top: -4px !important;
        }
        
        /* Плавная анимация для сдвига меню при раскрытии */
        #left-panel .main-menu,
        #left-panel .navbar-nav,
        .navbar-nav {
            transition: margin-left 0.3s ease, padding 0.3s ease !important;
        }
        
        /* Стили для свернутого меню */
        body.open #left-panel .main-menu, 
        body.open #left-panel .navbar-nav, 
        body.open .navbar-nav, 
        body.open .main-menu, 
        body.open aside.left-panel, 
        body.open .sidebar-menu {
            padding-right: 0px !important;
            margin-left: 0px !important;
            padding-bottom: 30px;
        }
        
        /* Стили для элементов списка в свернутом меню */
        .open aside.left-panel .navbar .navbar-nav li {
            position: relative;
            padding: 0 15px 0 18px;
        }
        
        /* Стили для стрелочек в свернутом меню при раскрытом подменю */
        body.open .navbar .navbar-nav li.menu-item-has-children.show a:before, 
        body.open .navbar .navbar-nav li.menu-item-has-children a[aria-expanded="true"]:before, 
        body.open .navbar .navbar-nav li.menu-item-has-children .dropdown-toggle[aria-expanded="true"]:before {
            transform: rotate(135deg) !important;
            border-color: #03a9f3 #03a9f3 transparent transparent !important;
            right: -6px !important;
            top: 32% !important;
            margin-top: -4px !important;
        }
        
        /* Стили для мобильного меню */
        @media (max-width: 575px) {
            #left-panel .navbar .sub-menu.children.show {
                display: block;
                margin-left: 20px;
            }
            
            .navbar.navbar-expand-sm.navbar-default {
                padding: 60px 0 !important;
                min-height: auto !important;
            }
        }
        
        @media (max-width: 768px) {
            #left-panel .navbar .navbar-nav li > a .menu-icon {
                width: 40px;
                text-align: left;
                font-size: 14px;
            }
        }
        

        
        /* Анимация для подменю */
        .sub-menu.children {
            transition: all 0.3s ease !important;
        }
        
        /* Плавное появление подменю */
        .sub-menu.children.collapse {
            transition: height 0.3s ease !important;
        }
        
        /* Анимация при закрытии подменю */
        .sub-menu.children.collapsing {
            transition: height 0.3s ease !important;
        }
        
        /* Общая анимация для всех состояний меню */
        .menu-item-has-children {
            transition: all 0.3s ease !important;
        }
        
        /* Плавное возвращение меню при закрытии */
        .navbar .navbar-nav li.menu-item-has-children:not(.show) {
            transition: all 0.3s ease !important;
        }
    </style>
</head>

<body data-page="{{ request()->route() ? request()->route()->getName() : 'not_found' }}">

<!-- Контейнер для уведомлений -->
<div id="notification-container"></div>

@php
    $user = auth()->user();
    $userPermissions = $user ? $user->permissions()->pluck('name')->toArray() : [];
    $isAdmin = $user ? $user->role === 'admin' : false;
    $project = $user ? \App\Models\Admin\Project::find($user->project_id) : null;
    $hasNotificationAccess = $user && ($isAdmin || in_array('notifications', $userPermissions));
    
    // Админы видят все непрочитанные уведомления проекта, мастера - только свои
    if ($isAdmin) {
        $unreadNotifications = $hasNotificationAccess ? \App\Models\Notification::where('project_id', $user->project_id)->where('is_read', false)->orderByDesc('created_at')->limit(5)->get() : collect();
        $unreadCount = $hasNotificationAccess ? \App\Models\Notification::where('project_id', $user->project_id)->where('is_read', false)->count() : 0;
    } else {
        $unreadNotifications = $hasNotificationAccess ? \App\Models\Notification::where('project_id', $user->project_id)->where('user_id', $user->id)->where('is_read', false)->orderByDesc('created_at')->limit(5)->get() : collect();
        $unreadCount = $hasNotificationAccess ? \App\Models\Notification::where('project_id', $user->project_id)->where('user_id', $user->id)->where('is_read', false)->count() : 0;
    }
@endphp
<!-- Left Panel -->
<aside id="left-panel" class="left-panel">
    <!-- Mobile close button -->
    <div class="mobile-close-btn">
        <button type="button" class="btn-close" title="{{ __('messages.close_menu') }}">
            <i class="fa fa-times"></i>
        </button>
    </div>
    <nav class="navbar navbar-expand-sm navbar-default">
        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('dashboard', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('dashboard') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.dashboard') }}">
                        @if($hasAccess)
                            <i class="menu-icon fas fa-laptop"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.dashboard') }}
                       
                    </a>
                </li>
                <li class="menu-title">{{ __('messages.client_work') }}</li><!-- /.menu-title -->

                <li class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('clients', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('clients.list') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.clients') }}">
                        @if($hasAccess)
                            <i class="menu-icon fas fa-users"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.clients') }}
                        
                    </a>
                </li>
                <li class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('appointments', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('appointments.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.appointments') }}">
                        @if($hasAccess)
                            <i class="menu-icon fas fa-calendar"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.appointments') }}
                        
                    </a>
                </li>
                <li class="{{ request()->routeIs('reports.clients.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('analytics', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('reports.clients.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.analytics') }}">
                        @if($hasAccess)
                            <i class="menu-icon fas fa-bar-chart"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.analytics') }}
                       
                    </a>
                </li>
                

                <li class="menu-title">{{ __('messages.turnover') }}</li>

                <li class="{{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('warehouse', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('warehouses.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.warehouse') }}">
                        @if($hasAccess)
                            <i class="menu-icon fa fa-boxes-stacked"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.warehouse') }}
                        
                    </a>
                </li>
                <li class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('purchases', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('purchases.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.purchases') }}">
                        @if($hasAccess)
                            <i class="menu-icon fa fa-cart-plus"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.purchases') }}
                        
                    </a>
                </li>
                <li class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('sales', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('sales.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.sales') }}">
                        @if($hasAccess)
                            <i class="menu-icon fa fa-shopping-basket"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.sales') }}
                        
                    </a>
                </li>
                <li class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('expenses', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('expenses.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.expenses') }}">
                        @if($hasAccess)
                            <i class="menu-icon fa fa-credit-card"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.expenses') }}
                        
                    </a>
                </li>
                <li class="{{ request()->routeIs('inventories.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('inventory', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('inventories.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.inventory') }}">
                        @if($hasAccess)
                            <i class="menu-icon fa fa-archive"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.inventory') }}
                        
                    </a>
                </li>
                <li class="{{ request()->routeIs('reports.turnover') || request()->routeIs('reports.turnover.*') ? 'active' : '' }}">
                    @php $hasAccess = $isAdmin || in_array('analytics', $userPermissions); @endphp
                    <a href="{{ $hasAccess ? route('reports.turnover') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.analytics') }}">
                        @if($hasAccess)
                            <i class="menu-icon fa fa-bar-chart"></i>
                        @else
                            <i class="menu-icon fas fa-lock"></i>
                        @endif
                        {{ __('messages.analytics') }}
                        
                    </a>
                </li>


                <li class="menu-title">{{ __('messages.services_title') }}</li>

                <li class="menu-item-has-children {{ 
                    request()->routeIs('services.*') || 
                    request()->routeIs('products.*') || 
                    request()->routeIs('product-categories.*') || 
                    request()->routeIs('product-brands.*') || 
                    request()->routeIs('suppliers.*') || 
                    request()->routeIs('client-types.*') ? 'active' : '' 
                }}">
                    <a href="#referenceMenu" data-toggle="collapse" aria-expanded="{{ 
                        request()->routeIs('services.*') || 
                        request()->routeIs('products.*') || 
                        request()->routeIs('product-categories.*') || 
                        request()->routeIs('product-brands.*') || 
                        request()->routeIs('suppliers.*') || 
                        request()->routeIs('client-types.*') ? 'true' : 'false' 
                    }}" class="dropdown-toggle" title="{{ __('messages.directories') }}">
                        <i class="menu-icon fa fa-layer-group"></i>{{ __('messages.directories') }}
                    </a>
                    <ul id="referenceMenu" class="sub-menu children collapse {{ 
                        request()->routeIs('services.*') || 
                        request()->routeIs('products.*') || 
                        request()->routeIs('product-categories.*') || 
                        request()->routeIs('product-brands.*') || 
                        request()->routeIs('suppliers.*') || 
                        request()->routeIs('client-types.*') ? 'show' : '' 
                    }}">
                        <li class="{{ request()->routeIs('services.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('services', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('services.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.services_menu') }}">
                                @if($hasAccess)
                                    <i class="fa fa-briefcase"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.services_menu') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('products', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('products.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.products') }}">
                                @if($hasAccess)
                                    <i class="fa fa-cube"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.products') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('product-categories', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('product-categories.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.product_categories') }}">
                                @if($hasAccess)
                                    <i class="fa fa-folder-open"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.product_categories') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('product-brands.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('product-brands', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('product-brands.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.product_brands') }}">
                                @if($hasAccess)
                                    <i class="fa fa-certificate"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.product_brands') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('suppliers', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('suppliers.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.suppliers') }}">
                                @if($hasAccess)
                                    <i class="fa fa-truck"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.suppliers') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('client-types.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('client-types', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client-types.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.client_types') }}">
                                @if($hasAccess)
                                    <i class="fa fa-id-badge"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.client_types') }}</span>
                                
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item-has-children {{ 
                    request()->routeIs('client.booking.*') || 
                    request()->routeIs('client.telegram-settings.*') ||
                    request()->routeIs('client.email-settings.*') ||
                    request()->routeIs('client.widget-settings.*') ? 'active' : '' 
                }}">
                    <a href="#integrationsMenu" data-toggle="collapse" aria-expanded="{{
                        request()->routeIs('client.booking.*') || 
                        request()->routeIs('client.telegram-settings.*') ||
                        request()->routeIs('client.email-settings.*') ||
                        request()->routeIs('client.widget-settings.*') ? 'true' : 'false' 
                    }}" class="dropdown-toggle" title="{{ __('messages.integrations') }}">
                        <i class="menu-icon fa fa-plug"></i>{{ __('messages.integrations') }}
                    </a>
                    <ul id="integrationsMenu" class="sub-menu children collapse {{ 
                        request()->routeIs('client.booking.*') || 
                        request()->routeIs('client.telegram-settings.*') ||
                        request()->routeIs('client.email-settings.*') ||
                        request()->routeIs('client.widget-settings.*') ? 'show' : '' 
                    }}">
                        <li class="{{ request()->routeIs('client.booking.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('booking', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.booking.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.web_booking_integration') }}">
                                @if($hasAccess)
                                    <i class="fa fa-calendar-check"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.web_booking_integration') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('client.telegram-settings.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('settings', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.telegram-settings.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.telegram_integration') }}">
                                @if($hasAccess)
                                    <i class="fab fa-telegram"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.telegram_integration') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('client.email-settings.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('settings', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.email-settings.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.email_integration') }}">
                                @if($hasAccess)
                                    <i class="fa fa-envelope"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.email_integration') }}</span>
                            </a>
                        </li>
                        <li class="widget-menu-item {{ request()->routeIs('client.widget-settings.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('settings', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.widget-settings.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.website_widget') }}">
                                @if($hasAccess)
                                    <i class="fa fa-code"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.website_widget') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item-has-children {{ 
                    request()->routeIs('client.users.*') || 
                    request()->routeIs('salary.*') || 
                    request()->routeIs('work-schedules.*') ? 'active' : '' 
                }}">
                    <a href="#personnelMenu" data-toggle="collapse" aria-expanded="{{
                        request()->routeIs('client.users.*') || 
                        request()->routeIs('salary.*') || 
                        request()->routeIs('work-schedules.*') ? 'true' : 'false' 
                    }}" class="dropdown-toggle" title="{{ __('messages.personnel') }}">
                        <i class="menu-icon fa fa-users-cog"></i>{{ __('messages.personnel') }}
                    </a>
                    <ul id="personnelMenu" class="sub-menu children collapse {{ 
                        request()->routeIs('client.users.*') || 
                        request()->routeIs('salary.*') || 
                        request()->routeIs('work-schedules.*') ? 'show' : '' 
                    }}">
                        <li class="{{ request()->routeIs('client.users.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('client.users', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.users.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.employees') }}">
                                @if($hasAccess)
                                    <i class="fa fa-user-tie"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.employees') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('salary.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('salary', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('salary.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.salary') }}">
                                @if($hasAccess)
                                    <i class="fa fa-money-bill-wave"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.salary') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('work-schedules.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('work-schedules', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('work-schedules.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.work_schedule') }}">
                                @if($hasAccess)
                                    <i class="fa fa-calendar-alt"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.work_schedule') }}</span>
                                
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item-has-children {{ 
                    request()->routeIs('roles.*') || 
                    request()->routeIs('client.settings.*') || 
                    request()->routeIs('admin.roles.*') || 
                    request()->routeIs('admin.settings.*') || 
                    request()->routeIs('admin.email-templates.*') || 
                    request()->routeIs('admin.security.*') || 
                    request()->routeIs('support-tickets.*') || 
                    request()->routeIs('client.notifications.*') || 
                    request()->routeIs('client.subscriptions.*') ? 'active' : '' 
                }}">
                                        <a href="#settingsMenu" data-toggle="collapse" aria-expanded="{{
                        request()->routeIs('roles.*') || 
                        request()->routeIs('client.settings.*') || 
                        request()->routeIs('admin.roles.*') || 
                        request()->routeIs('admin.settings.*') || 
                        request()->routeIs('admin.email-templates.*') || 
                        request()->routeIs('admin.security.*') || 
                        request()->routeIs('client.support-tickets.*') || 
                        request()->routeIs('client.notifications.*') || 
                        request()->routeIs('client.subscriptions.*') ? 'true' : 'false' 
                    }}" class="dropdown-toggle" title="{{ __('messages.settings') }}">
                        <i class="menu-icon fa fa-cogs"></i>{{ __('messages.settings') }}
                    </a>
                    <ul id="settingsMenu" class="sub-menu children collapse {{ 
                        request()->routeIs('roles.*') || 
                        request()->routeIs('client.settings.*') || 
                        request()->routeIs('admin.roles.*') || 
                        request()->routeIs('admin.settings.*') || 
                        request()->routeIs('admin.email-templates.*') || 
                        request()->routeIs('admin.security.*') || 
                        request()->routeIs('client.support-tickets.*') || 
                        request()->routeIs('client.notifications.*') || 
                        request()->routeIs('client.subscriptions.*') ? 'show' : '' 
                    }}">
                        <li class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('roles', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('roles.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.roles_and_permissions') }}">
                                @if($hasAccess)
                                    <i class="fa fa-lock"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.roles_and_permissions') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('client.settings.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('settings', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.settings.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.general_settings') }}">
                                @if($hasAccess)
                                    <i class="fa fa-cog"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.general_settings') }}</span>
                                
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('client.support-tickets.*') || request()->routeIs('support-tickets.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('support-tickets', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.support-tickets.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }} {{ request()->routeIs('client.support-tickets.*') || request()->routeIs('support-tickets.*') ? 'active' : '' }}" title="{{ __('messages.support') }}">
                                @if($hasAccess)
                                    <i class="fa fa-life-ring"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.support') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('client.notifications.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('notifications', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.notifications.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.notifications') }}">
                                @if($hasAccess)
                                    <i class="fa fa-bell"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.notifications') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('client.subscriptions.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('subscriptions', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.subscriptions.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.subscriptions') }}">
                                @if($hasAccess)
                                    <i class="fa fa-credit-card"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.subscriptions') }}</span>
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside>
<!-- /#left-panel -->
<!-- Right Panel -->
<div id="right-panel" class="right-panel">
    <!-- Header-->
    <header id="header" class="header">
        <div class="top-left">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <img src="{{ $project && $project->logo ? $project->logo : asset('client/img/avatar-default.png') }}" alt="{{ __('messages.logo') }}" style="height:48px;max-width:48px;object-fit:cover;border-radius:50%;">
                </a>
                <a class="navbar-brand hidden" href="{{ route('dashboard') }}">
                    <img src="{{ $project && $project->logo ? $project->logo : asset('client/img/avatar-default.png') }}" alt="{{ __('messages.logo') }}" style="height:48px;max-width:160px;object-fit:contain;">
                </a>
                <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
            </div>
        </div>
        <div class="top-right">
            <div class="header-menu">
                <div class="header-left">
                    
                    @if($hasNotificationAccess)
                    <div class="dropdown for-notification">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell"></i>
                            @if($unreadCount > 0)
                                <span class="count bg-danger">{{ $unreadCount }}</span>
                            @endif
                        </button>
                        <div class="dropdown-menu" aria-labelledby="notification">
                            @if($unreadNotifications->count())
                                <p class="red">{{ __('messages.you_have_new_notifications', ['count' => $unreadCount]) }}</p>
                                @foreach($unreadNotifications as $notification)
                                    <form method="POST" action="{{ route('client.notifications.read', $notification->id) }}" style="display:block; margin:0;">
                                        @csrf
                                        <button type="submit" class="dropdown-item media" style="width:100%;text-align:left;white-space:normal;">
                                            <i class="fa fa-info-circle"></i>
                                            <span style="margin-left:8px;">{{ $notification->title }}</span>
                                            <br><small class="text-muted">{{ $notification->created_at->format('d.m.Y H:i') }}</small>
                                        </button>
                                    </form>
                                @endforeach
                            @else
                                <p class="dropdown-item text-muted">{{ __('messages.no_new_notifications') }}</p>
                            @endif
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="{{ route('client.notifications.index') }}">{{ __('messages.show_all_notifications') }}</a>
                        </div>
                    </div>
                    @endif

                  <!-- <div class="dropdown for-message">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="message" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-envelope"></i>
                            <span class="count bg-primary">4</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="message">
                            <p class="red">You have 4 Mails</p>
                            <a class="dropdown-item media" href="#">
                                <span class="photo media-left"><img alt="avatar" src=""></span>
                                <div class="message media-body">
                                    <span class="name float-left">Jonathan Smith</span>
                                    <span class="time float-right">Just now</span>
                                    <p>Hello, this is an example msg</p>
                                </div>
                            </a>
                            <a class="dropdown-item media" href="#">
                                <span class="photo media-left"><img alt="avatar" src=""></span>
                                <div class="message media-body">
                                    <span class="name float-left">Jack Sanders</span>
                                    <span class="time float-right">5 minutes ago</span>
                                    <p>Lorem ipsum dolor sit amet, consectetur</p>
                                </div>
                            </a>
                            <a class="dropdown-item media" href="#">
                                <span class="photo media-left"><img alt="avatar" src=""></span>
                                <div class="message media-body">
                                    <span class="name float-left">Cheryl Wheeler</span>
                                    <span class="time float-right">10 minutes ago</span>
                                    <p>Hello, this is an example msg</p>
                                </div>
                            </a>
                            <a class="dropdown-item media" href="#">
                                <span class="photo media-left"><img alt="avatar" src=""></span>
                                <div class="message media-body">
                                    <span class="name float-left">Rachel Santos</span>
                                    <span class="time float-right">15 minutes ago</span>
                                    <p>Lorem ipsum dolor sit amet, consectetur</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>-->

                <div class="user-area dropdown float-right">
                    <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="user-avatar-icon" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:#e5e7eb;">
                            <i class="fa fa-user" style="font-size:20px;color:#64748b;"></i>
                        </span>
                    </a>
                    <div class="user-menu dropdown-menu">
                        <a class="nav-link" href="{{ route('client.settings.index') }}#profile"><i class="fa fa-user"></i>{{ __('messages.my_profile') }}</a>

                        <a class="nav-link" href="{{ route('client.settings.index') }}#security"><i class="fa fa-cog"></i>{{ __('messages.change_password') }}</a>
                        <a class="nav-link" href="{{ route('client.subscriptions.index') }}"><i class="fa fa-credit-card"></i>{{ __('messages.subscriptions') }}</a>
                        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off"></i>{{ __('messages.logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </header>
    <!-- /#header -->
    
    <!-- Уведомление о смене языка с лендинга -->
    @if(session('language_changed_from_landing'))
        <div class="alert alert-info alert-dismissible fade show" style="margin: 20px; margin-top: 10px;" id="language-changed-notification">
            <i class="fa fa-info-circle"></i>
            <strong>{{ __('messages.language_changed') }}</strong>
            <p class="mb-0">{{ __('messages.language_synced_from_landing') }}</p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @php
            session()->forget('language_changed_from_landing');
        @endphp
    @endif
    
    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>
    <!-- /.content -->
    <div class="clearfix"></div>
    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-inner bg-white">
            <div class="row">
                <div class="col-sm-6">
                    {{ __('messages.copyright_text') }}
                </div>
                
            </div>
        </div>
    </footer>
    <!-- /.site-footer -->
</div>
<!-- /#right-panel -->

<!-- Scripts -->
@stack('scripts')
</body>
</html>
