<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ панель - CRM Studio')</title>
    <meta name="description" content="@yield('description', 'Административная панель CRM Studio')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('admin/main.css') }}" rel="stylesheet">
    
    <!-- Стили для узкого скроллбара админ панель -->
    <style>
        /* Узкий скроллбар для админ сайдбара */
        .sidebar-content::-webkit-scrollbar,
        .sidebar::-webkit-scrollbar,
        nav.sidebar::-webkit-scrollbar {
            width: 3px !important;
            background: transparent !important;
        }
        
        /* Отступ справа для скроллбара */
        .sidebar-content,
        .sidebar,
        nav.sidebar {
            padding-right: 1px !important;
        }
        
        .sidebar-content::-webkit-scrollbar-thumb,
        .sidebar::-webkit-scrollbar-thumb,
        nav.sidebar::-webkit-scrollbar-thumb {
            background: #6c757d !important;
            border-radius: 2px !important;
        }
        
        .sidebar-content::-webkit-scrollbar-thumb:hover,
        .sidebar::-webkit-scrollbar-thumb:hover,
        nav.sidebar::-webkit-scrollbar-thumb:hover {
            background: #adb5bd !important;
        }
        
        /* Для Firefox */
        .sidebar-content,
        .sidebar,
        nav.sidebar {
            scrollbar-width: thin !important;
            scrollbar-color: #6c757d #343a40 !important;
        }
        
        /* Расстояние между иконками и текстом в админ меню */
        .sidebar .nav-link i.me-2 {
            margin-right: 6px !important;
        }
        
        .sidebar .nav-link {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('admin.components.sidebar')
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        @include('admin.components.header')
        <!-- Page Content -->
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin/main.js') }}"></script>
    
    @stack('scripts')
</body>
</html> 
