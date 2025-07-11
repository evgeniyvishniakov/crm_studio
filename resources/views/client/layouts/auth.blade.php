<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CRM Studio')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('client/css/style.css') }}?v=1.1">
    <link rel="stylesheet" href="{{ asset('client/css/common.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa !important;
            font-family: 'Inter', Arial, sans-serif !important;
        }
        .auth-header {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 24px;
        }
        .auth-header img {
            width: 48px;
        }
        .auth-header h2 {
            font-weight: 700;
            margin-top: 12px;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-header">
            <img src="{{ asset('favicon.ico') }}" alt="CRM Studio">
            <h2>CRM Studio</h2>
        </div>
        @yield('content')
    </div>
</body>
</html> 