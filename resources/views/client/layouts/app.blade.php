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
    <script src="/client/js/notifications.js"></script>
    <script src="/client/js/currency-manager.js"></script>
    <script src="/client/js/language-manager.js"></script>
    <script src="/client/js/calendar-localization.js"></script>
    <script src="{{ asset('client/js/common.js') }}"></script>

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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ua.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, .navbar, .sidebar, .form-control, .btn, .nav, .dropdown-menu, .site-footer, .header, .content {
            font-family: 'Inter', Arial, sans-serif !important;
        }
        body {
            background: #f8f9fa !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        #right-panel {
            background: #f8f9fa !important;
        }
        
        #weatherWidget .currentDesc {
            color: #ffffff!important;
        }
        .traffic-chart {
            min-height: 335px;
        }
        #flotPie1  {
            height: 150px;
        }
        #flotPie1 td {
            padding:3px;
        }
        #flotPie1 table {
            top: 20px!important;
            right: -10px!important;
        }
        .chart-container {
            display: table;
            min-width: 270px ;
            text-align: left;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        #flotLine5  {
            height: 105px;
        }

        #flotBarChart {
            height: 150px;
        }
        #cellPaiChart{
            height: 160px;
        }
        
        /* Оставляю только стили для .active и иконки, удаляю универсальные color: #03a9f3 !important для всех ссылок меню */

        .navbar .navbar-nav li.active > a .menu-icon {
            color: #03a9f3 !important;
            opacity: 0.7 !important;
        }
        .menu-item-has-children.active > a,
        .sub-menu li.active > a,
        .menu-item-has-children.active > a .menu-icon,
        .sub-menu li.active > i {
            color: #03a9f3 !important;
            background: none !important;
            background-color: transparent !important;
           
        }
        .menu-item-has-children.active .dropdown-toggle,
        .menu-item-has-children.active .dropdown-toggle:hover,
        .menu-item-has-children.active .dropdown-toggle:focus {
            background-color: #007bff;
            color: #fff;
        }

        /* Позволяем Bootstrap управлять отображением подменю */
        .menu-item-has-children .sub-menu {
            position: relative;
            z-index: 1000;
        }

        /* Убеждаемся, что другие элементы меню не перекрываются */
        .menu-item-has-children.dropdown + .menu-item-has-children.dropdown {
            margin-top: 0;
        }

        /* Стили для корректного отображения подменю */
        .sub-menu.children {
            position: relative;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 2px;
        }

        /* Управление высотой меню */
        .sidebar-menu {
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }

        /* Убеждаемся, что элементы меню не перекрываются */
        .menu-item-has-children {
            position: relative;
        }

        @media (max-width: 768px) {
            .navbar-nav .open > a,
            .navbar-nav .show > a {
                background: none !important;
                background-color: transparent !important;
                color: #03a9f3 !important;
                box-shadow: none !important;
                outline: none !important;
            }
        }

        .navbar-nav .active > a,
        .navbar-nav li.active > a,
        .navbar-nav li.active > a:focus,
        .navbar-nav li.active > a:active,
        .navbar-nav li.active > a:hover,
        .navbar-nav a:focus,
        .navbar-nav a:active,
        .navbar-nav a:visited {
            background: none !important;
            background-color: transparent !important;
            box-shadow: none !important;
            outline: none !important;
            filter: none !important;
            -webkit-tap-highlight-color: transparent !important;
            -webkit-box-shadow: none !important;
            -moz-box-shadow: none !important;
            border: none !important;
            border-color: transparent !important;
            user-select: none !important;
        }

        .header-menu {
            display: flex;
            align-items: center;
        
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-area {
            display: flex;
            align-items: center;
            
        }
        .tab-button.active {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #60a5fa) !important;
            border-color: #3b82f6 !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
            border-radius: 12px !important;
            padding: 0.75rem 1.5rem !important;
        }
        .btn-primary:active, .btn-primary:focus, .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
            border-color: #2563eb !important;
            color: #fff !important;
            border-radius: 12px !important;
            padding: 0.75rem 1.5rem !important;
        }
    </style>
</head>

<body data-page="{{ request()->route()->getName() }}">
    <div id="notification"></div>
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
        <button type="button" class="btn-close" title="Закрыть меню">
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
                        request()->routeIs('client.users.*') || 
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
                        request()->routeIs('client.users.*') || 
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
                        <li class="{{ request()->routeIs('client.users.*') ? 'active' : '' }}">
                            @php $hasAccess = $isAdmin || in_array('client.users', $userPermissions); @endphp
                            <a href="{{ $hasAccess ? route('client.users.index') : '#' }}" class="{{ !$hasAccess ? 'disabled-link' : '' }}" title="{{ __('messages.users') }}">
                                @if($hasAccess)
                                    <i class="fa fa-users"></i>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                                <span class="menu-label">{{ __('messages.users') }}</span>
                                
                            </a>
                        </li>
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
<style>
.disabled-link {
    pointer-events: none;
    color: #aaa !important;
    opacity: 0.7;
}
.menu-label {
    margin-left: 6px;
}
.lock-icon {
    margin-left: 8px;
    color: #aaa;
    font-size: 1em;
    vertical-align: middle;
}
</style>
<!-- /#left-panel -->
<!-- Right Panel -->
<div id="right-panel" class="right-panel">
    <!-- Header-->
    <header id="header" class="header">
        <div class="top-left">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <img src="{{ $project && $project->logo ? $project->logo : asset('client/img/avatar-default.png') }}" alt="Logo" style="height:48px;max-width:48px;object-fit:cover;border-radius:50%;">
                </a>
                <a class="navbar-brand hidden" href="{{ route('dashboard') }}">
                    <img src="{{ $project && $project->logo ? $project->logo : asset('client/img/avatar-default.png') }}" alt="Logo" style="height:48px;max-width:160px;object-fit:contain;">
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
                    Copyright &copy; 2018 Ela Admin
                </div>
                
            </div>
        </div>
    </footer>
    <!-- /.site-footer -->
</div>
<!-- /#right-panel -->

<!-- Scripts -->
@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
</body>
</html>
