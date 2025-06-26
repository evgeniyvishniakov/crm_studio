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
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=1.1">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <!-- Page-specific styles -->
    @if(request()->routeIs('dashboard'))
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Base Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/ru.global.min.js'></script>
    <script src="{{ asset('js/main.js') }}"></script>

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
        
        /* Это правило не дает иконке активного пункта меню становиться синей */
        /*
        .navbar .navbar-nav li.active > a .menu-icon {
            color: #555 !important;
        }
        */

        /* Делаем иконку активного пункта светло-голубой, как на дашборде */
        .navbar .navbar-nav li.active > a .menu-icon {
            color: #03a9f3 !important; /* Цвет текста, который мы хотим для иконки */
            opacity: 0.7 !important; /* Делаем ее полупрозрачной для "светлого" эффекта */
        }
        
        /* Одинаковый светло-синий цвет для активного родителя и дочернего пункта */
        .menu-item-has-children.active > a,
        .sub-menu li.active > a,
        .menu-item-has-children.active > a .menu-icon,
        .sub-menu li.active > i {
            color: #03a9f3 !important;
            background: none !important;
            font-weight: normal !important;
        }
        
        /* Принудительно показываем подменю для активных пунктов */
        .menu-item-has-children.active .sub-menu {
            display: block !important;
            position: static !important;
            float: none !important;
            width: auto !important;
            margin-top: 0 !important;
            background-color: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }
        
        /* Предотвращаем закрытие активного меню и сохраняем видимость */
        .menu-item-has-children.active .dropdown-toggle {
            pointer-events: none;
            color: #03a9f3 !important;
            background: none !important;
        }
        
        /* Обеспечиваем видимость текста в активном меню */
        .menu-item-has-children.active .dropdown-toggle,
        .menu-item-has-children.active .dropdown-toggle:hover,
        .menu-item-has-children.active .dropdown-toggle:focus {
            color: #03a9f3 !important;
            background: none !important;
            text-decoration: none !important;
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
                    <a href="{{ route('dashboard') }}"><i class="menu-icon fas fa-laptop"></i>Dashboard </a>
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

                <li class="{{ request()->routeIs('warehouse.*') ? 'active' : '' }}">
                    <a href="{{ route('warehouse.index') }}"><i class="menu-icon fa fa-dropbox"></i>Склад</a>
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
                    <a href="{{ route('reports.turnover.index') }}"><i class="menu-icon fa fa-bar-chart"></i>Аналитика</a>
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
                        <i class="menu-icon fa fa-book"></i>Справочники
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
                    request()->routeIs('users.*') || 
                    request()->routeIs('roles.*') || 
                    request()->routeIs('settings.*') || 
                    request()->routeIs('email-templates.*') || 
                    request()->routeIs('security.*') ? 'active' : '' 
                }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="menu-icon fa fa-cogs"></i>Настройки
                    </a>
                    <ul class="sub-menu children dropdown-menu">
                        <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="fa fa-users"></i>
                            <a href="{{ route('users.index') }}">Пользователи</a>
                        </li>
                        <li class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <i class="fa fa-lock"></i>
                            <a href="{{ route('roles.index') }}">Роли и доступы</a>
                        </li>
                        <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="fa fa-cog"></i>
                            <a href="{{ route('settings.index') }}">Общие настройки</a>
                        </li>
                        <li class="{{ request()->routeIs('email-templates.*') ? 'active' : '' }}">
                            <i class="fa fa-envelope"></i>
                            <a href="{{ route('email-templates.index') }}">Email-шаблоны</a>
                        </li>
                        <li class="{{ request()->routeIs('security.*') ? 'active' : '' }}">
                            <i class="fa fa-shield"></i>
                            <a href="{{ route('security.index') }}">Безопасность</a>
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
                    <button class="search-trigger"><i class="fa fa-search"></i></button>
                    <div class="form-inline">
                        <form class="search-form">
                            <input class="form-control mr-sm-2" type="text" placeholder="Search ..." aria-label="Search">
                            <button class="search-close" type="submit"><i class="fa fa-close"></i></button>
                        </form>
                    </div>

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
                        <img class="user-avatar rounded-circle" src="" alt="User Avatar">
                    </a>

                    <div class="user-menu dropdown-menu">
                        <a class="nav-link" href="#"><i class="fa fa- user"></i>My Profile</a>

                        <a class="nav-link" href="#"><i class="fa fa- user"></i>Notifications <span class="count">13</span></a>

                        <a class="nav-link" href="#"><i class="fa fa -cog"></i>Settings</a>

                        <a class="nav-link" href="#"><i class="fa fa-power -off"></i>Logout</a>
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

<script>
$(document).ready(function() {
    // Для активных меню принудительно показываем подменю
    $('.menu-item-has-children.active .sub-menu').show();
    
    // Предотвращаем закрытие активного меню при клике на дочерние пункты
    $('.menu-item-has-children.active .sub-menu a').on('click', function(e) {
        // Разрешаем переход по ссылке, но не закрываем меню
    });
});
</script>

@stack('scripts')
</body>
</html>
