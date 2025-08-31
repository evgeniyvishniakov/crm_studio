<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - {{ __('messages.page_not_found') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        .error-content {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .error-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }
        
        .error-icon {
            margin-bottom: 30px;
            color: #667eea;
            opacity: 0.8;
        }
        
        .error-title {
            font-size: 6rem;
            font-weight: 700;
            color: #667eea;
            margin: 0 0 20px 0;
            line-height: 1;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .error-subtitle {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 20px 0;
        }
        
        .error-description {
            font-size: 1.1rem;
            color: #666;
            margin: 0 0 40px 0;
            line-height: 1.6;
        }
        
        .error-actions {
            margin-bottom: 40px;
        }
        
        .btn {
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .btn-outline-primary {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            text-decoration: none;
        }
        
        .gap-3 {
            gap: 1rem;
        }
        

        

        
        /* Анимация появления */
        .error-content {
            animation: slideInUp 0.6s ease-out;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Адаптивность */
        @media (max-width: 768px) {
            .error-content {
                padding: 40px 20px;
            }
            
            .error-title {
                font-size: 4rem;
            }
            
            .error-subtitle {
                font-size: 1.5rem;
            }
            
            .error-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            

        }
        
        @media (max-width: 480px) {
            .error-page {
                padding: 10px;
            }
            
            .error-content {
                padding: 30px 15px;
            }
            
            .error-title {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-icon">
                <svg viewBox="0 0 24 24" fill="currentColor" width="120" height="120">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            
            <h1 class="error-title">404</h1>
            <h2 class="error-subtitle">{{ __('messages.page_not_found') }}</h2>
            <p class="error-description">
                {{ __('messages.page_not_found_description') }}
            </p>
            

            
            <div class="error-actions">
                @if(Auth::guard('client')->check() || Auth::guard('web')->check())
                    <!-- Для зарегистрированных пользователей -->
                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Перейти в систему
                        </a>
                        <a href="{{ route('landing.account.dashboard') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user me-2"></i>
                            Перейти в личный кабинет
                        </a>
                    </div>
                @else
                    <!-- Для незарегистрированных пользователей -->
                    <a href="{{ route('beautyflow.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>
                        Перейти на головну
                    </a>
                @endif
            </div>
            

        </div>
    </div>
</body>
</html>
