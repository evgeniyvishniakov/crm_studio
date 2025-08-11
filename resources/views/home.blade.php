<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Studio - Главная страница</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            color: white;
            max-width: 600px;
            padding: 2rem;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
        }
        .btn-primary {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
        }
        .btn-primary:hover {
            background: rgba(255,255,255,0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CRM Studio</h1>
        <p>Добро пожаловать в систему управления клиентами и проектами</p>
        
        <div class="buttons">
            <a href="{{ route('admin.login') }}" class="btn btn-primary">
                Войти в админку
            </a>
            <a href="{{ route('knowledge.index') }}" class="btn">
                База знаний
            </a>
        </div>
        
        <div style="margin-top: 3rem; opacity: 0.7; font-size: 0.9rem;">
            <p>Если у вас возникли проблемы с доступом, попробуйте:</p>
            <ul style="list-style: none; padding: 0;">
                <li>• Очистить кэш браузера</li>
                <li>• Разрешить куки для этого сайта</li>
                <li>• Использовать режим инкогнито</li>
            </ul>
        </div>
    </div>
</body>
</html>
