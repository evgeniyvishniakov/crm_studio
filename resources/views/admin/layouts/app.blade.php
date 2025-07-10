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
