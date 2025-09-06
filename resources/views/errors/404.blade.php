<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Страница не найдена</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            max-width: 500px;
            width: 100%;
            text-align: center;
            background: white;
            border-radius: 20px;
            padding: 50px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
            line-height: 1;
        }
        
        .error-message {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .error-description {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .back-button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .back-button[style*="background: transparent"]:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }
        
        @media (max-width: 480px) {
            .error-container {
                padding: 30px 20px;
            }
            
            .error-code {
                font-size: 4rem;
            }
            
            .error-message {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">Страница не найдена</div>
        <div class="error-description">
            К сожалению, запрашиваемая страница не существует или была перемещена.
        </div>
        

        @if(Auth::guard('client')->check() || Auth::guard('web')->check())
            <!-- Для зарегистрированных пользователей -->
            <div style="display: flex; flex-direction: column; gap: 15px; align-items: center;">
                <a href="{{ route('dashboard') }}" class="back-button" style="margin-bottom: 10px;">
                    Перейти в систему
                </a>
                <a href="{{ route('landing.account.dashboard') }}" class="back-button" style="background: transparent; color: #667eea; border: 2px solid #667eea;">
                    Перейти в личный кабинет
                </a>
            </div>
        @else
            <!-- Для незарегистрированных пользователей -->
            <a href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.index') }}" class="back-button">
                Перейти на головну
            </a>
        @endif
    </div>
</body>
</html>
