<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Helpers\SystemHelper::getSiteName() . ' - Система управления')</title>
    <meta name="description" content="@yield('description', \App\Helpers\SystemHelper::getSiteDescription())">
    <meta name="keywords" content="CRM, управление, клиенты, записи, аналитика">
    <meta name="author" content="{{\App\Helpers\SystemHelper::getSiteName()}}">
    <meta property="og:title" content="@yield('title', \App\Helpers\SystemHelper::getSiteName() . ' - Система управления')">
    <meta property="og:description" content="@yield('description', \App\Helpers\SystemHelper::getSiteDescription())">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', \App\Helpers\SystemHelper::getSiteName() . ' - Система управления')">
    <meta name="twitter:description" content="@yield('description', \App\Helpers\SystemHelper::getSiteDescription())">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    
    <!-- Favicon -->
    @if(\App\Helpers\SystemHelper::hasFavicon())
        <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SystemHelper::getFavicon() }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap"></noscript>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('landing/main.css') }}" rel="stylesheet">
    <style>
      html, body {
        font-family: 'Manrope', 'Inter', Arial, sans-serif !important;
      }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    @include('landing.components.header')
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('landing.components.footer')
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('landing/main.js') }}"></script>
    
    @stack('scripts')
    
    <!-- Модальные окна -->
    @include('landing.components.login-modal')
</body>
</html> 
