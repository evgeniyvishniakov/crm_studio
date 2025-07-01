<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CRM Studio</title>
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
    <link rel="stylesheet" href="{{ asset('client/css/style.css') }}?v=1.1">
    <link rel="stylesheet" href="{{ asset('client/css/common.css') }}">
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
    <script src="{{ asset('client/js/main.js') }}"></script>

    <!-- Page-specific scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <style>
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
            font-weight: normal !important;
        }
        .menu-item-has-children.active .dropdown-toggle,
        .menu-item-has-children.active .dropdown-toggle:hover,
        .menu-item-has-children.active .dropdown-toggle:focus {
            color: #03a9f3 !important;
            background: none !important;
            text-decoration: none !important;
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
            margin-left: 18px;
        }
    </style>
</head>

<body>
<!-- Left Panel -->
<aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">
        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"><i class="menu-icon fas fa-laptop"></i>Панель управления</a>
                </li>
                <li class="menu-title">Работа с клиентами</li><!-- /.menu-title -->

                <li class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <a href="{{ route('clients.list') }}"> <i class="menu-icon fas fa-users"></i>Клиенты</a>
                </li>
                <li class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    <a href="{{ route('appointments.index') }}"> <i class="menu-icon fas fa-calendar"></i>Записи</a>
                </li>
                <li class="{{ request()->routeIs('reports.clients.*') ? 'active' : '' }}">
                    <a href="{{ route('reports.clients.index') }}"><i class="menu-icon fas fa-bar-chart"></i>Аналитика</a>
                </li>
                

                <li class="menu-title">Товарооборот</li>

                <li class="{{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                    <a href="{{ route('warehouses.index') }}"><i class="menu-icon fa fa-boxes-stacked"></i>Склад</a>
                </li>
                <li class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <a href="{{ route('purchases.index') }}"><i class="menu-icon fa fa-cart-plus"></i>Закупки</a>
                </li>
                <li class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <a href="{{ route('sales.index') }}"><i class="menu-icon fa fa-shopping-basket"></i>Продажи</a>
                </li>
                <li class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <a href="{{ route('expenses.index') }}"><i class="menu-icon fa fa-credit-card"></i>Расходы</a>
                </li>
                <li class="{{ request()->routeIs('inventories.*') ? 'active' : '' }}">
                    <a href="{{ route('inventories.index') }}"><i class="menu-icon fa fa-archive"></i>Инвентаризация</a>
                </li>
                <li class="{{ request()->routeIs('reports.turnover.*') ? 'active' : '' }}">
                    <a href="{{ route('reports.turnover') }}"><i class="menu-icon fa fa-bar-chart"></i>Аналитика</a>
                </li>

                <li class="menu-title">Сервисы</li>

                <li class="menu-item-has-children dropdown {{ 
                    request()->routeIs('services.*') || 
                    request()->routeIs('products.*') || 
                    request()->routeIs('product-categories.*') || 
                    request()->routeIs('product-brands.*') || 
                    request()->routeIs('suppliers.*') || 
                    request()->routeIs('client-types.*') ? 'active' : '' 
                }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="menu-icon fa fa-layer-group"></i>Справочники
                    </a>
                    <ul class="sub-menu children dropdown-menu">
                        <li class="{{ request()->routeIs('services.*') ? 'active' : '' }}">
                            <i class="fa fa-briefcase"></i>
                            <a href="{{ route('services.index') }}">Услуги</a>
                        </li>
                        <li class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="fa fa-cube"></i>
                            <a href="{{ route('products.index') }}">Товары</a>
                        </li>
                        <li class="{{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                            <i class="fa fa-folder-open"></i>
                            <a href="{{ route('product-categories.index') }}">Категории товаров</a>
                        </li>
                        <li class="{{ request()->routeIs('product-brands.*') ? 'active' : '' }}">
                            <i class="fa fa-certificate"></i>
                            <a href="{{ route('product-brands.index') }}">Бренды товаров</a>
                </li>
                        <li class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            <i class="fa fa-truck"></i>
                            <a href="{{ route('suppliers.index') }}">Поставщики</a>
                </li>
                        <li class="{{ request()->routeIs('client-types.*') ? 'active' : '' }}">
                            <i class="fa fa-id-badge"></i>
                            <a href="{{ route('client-types.index') }}">Типы клиентов</a>
                </li>
                    </ul>
                </li>
                <li class="menu-item-has-children dropdown {{ 
                    request()->routeIs('admin.users.*') || 
                    request()->routeIs('admin.roles.*') || 
                    request()->routeIs('admin.settings.*') || 
                    request()->routeIs('admin.email-templates.*') || 
                    request()->routeIs('admin.security.*') ? 'active' : '' 
                }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="menu-icon fa fa-cogs"></i>Настройки
                    </a>
                    <ul class="sub-menu children dropdown-menu">
                        <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fa fa-users"></i>
                            <a href="{{ route('admin.users.index') }}">Пользователи</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <i class="fa fa-lock"></i>
                            <a href="{{ route('admin.roles.index') }}">Роли и доступы</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fa fa-cog"></i>
                            <a href="{{ route('admin.settings.index') }}">Общие настройки</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">
                            <i class="fa fa-envelope"></i>
                            <a href="{{ route('admin.email-templates.index') }}">Email-шаблоны</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.security.*') ? 'active' : '' }}">
                            <i class="fa fa-shield"></i>
                            <a href="{{ route('admin.security.index') }}">Безопасность</a>
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
                <a class="navbar-brand" href="./"><img src="" alt="Logo"></a>
                <a class="navbar-brand hidden" href="./"><img src="" alt="Logo"></a>
                <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
            </div>
        </div>
        <div class="top-right">
            <div class="header-menu">
                <div class="header-left">
                    
                    <div class="dropdown for-notification">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell"></i>
                            <span class="count bg-danger">3</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="notification">
                            <p class="red">You have 3 Notification</p>
                            <a class="dropdown-item media" href="#">
                                <i class="fa fa-check"></i>
                                <p>Server #1 overloaded.</p>
                            </a>
                            <a class="dropdown-item media" href="#">
                                <i class="fa fa-info"></i>
                                <p>Server #2 overloaded.</p>
                            </a>
                            <a class="dropdown-item media" href="#">
                                <i class="fa fa-warning"></i>
                                <p>Server #3 overloaded.</p>
                            </a>
                        </div>
                    </div>

                    <div class="dropdown for-message">
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
                </div>

                <div class="user-area dropdown float-right">
                    <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="user-avatar-icon" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:#e5e7eb;">
                            <i class="fa fa-user" style="font-size:20px;color:#64748b;"></i>
                        </span>
                    </a>
                    <div class="user-menu dropdown-menu">
                        <a class="nav-link" href="#"><i class="fa fa-user"></i>Мой профиль</a>

                        <a class="nav-link" href="#"><i class="fa fa-cog"></i>Смена пароля</a>
                        <a class="nav-link" href="#"><i class="fa fa-power-off"></i>Выход</a>
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
                <div class="col-sm-6 text-right">
                    Designed by <a href="https://colorlib.com">Colorlib</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- /.site-footer -->
</div>
<!-- /#right-panel -->

@stack('scripts')
</body>
</html>
