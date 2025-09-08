<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminTelegramSetting;
use App\Services\AdminTelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminTelegramSettingsController extends Controller
{
    private AdminTelegramService $adminTelegramService;

    public function __construct(AdminTelegramService $adminTelegramService)
    {
        $this->adminTelegramService = $adminTelegramService;
    }

    /**
     * Показать страницу настроек Telegram админ панели
     */
    public function index()
    {
        $settings = AdminTelegramSetting::getOrCreate();
        return view('admin.telegram-settings.index', compact('settings'));
    }

    /**
     * Обновить настройки Telegram
     */
    public function update(Request $request)
    {
        \Log::info('AdminTelegramSettingsController::update called', [
            'method' => $request->method(),
            'data' => $request->all()
        ]);
        
        $validated = $request->validate([
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:255',
            'telegram_notifications_enabled' => 'boolean',
            'notify_new_projects' => 'boolean',
            'notify_new_subscriptions' => 'boolean',
            'notify_new_messages' => 'boolean',
            'notify_subscription_expires' => 'boolean',
            'notify_payment_issues' => 'boolean',
        ]);

        // Если включены уведомления, проверяем обязательность токена и chat_id
        if ($validated['telegram_notifications_enabled'] ?? false) {
            if (empty($validated['telegram_bot_token'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['telegram_bot_token' => ['Токен бота обязателен для включения уведомлений']]
                    ], 422);
                }
                return back()->withErrors(['telegram_bot_token' => 'Токен бота обязателен для включения уведомлений']);
            }
            if (empty($validated['telegram_chat_id'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['telegram_chat_id' => ['ID чата обязателен для включения уведомлений']]
                    ], 422);
                }
                return back()->withErrors(['telegram_chat_id' => 'ID чата обязателен для включения уведомлений']);
            }
        }

        // Обновляем настройки
        $settings = AdminTelegramSetting::getOrCreate();
        $settings->update($validated);

        \Log::info('AdminTelegramSettingsController::update completed', [
            'settings_updated' => true,
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Настройки Telegram успешно сохранены'
            ]);
        }

        return back()->with('success', 'Настройки Telegram успешно сохранены');
    }

    /**
     * Проверить подключение к Telegram
     */
    public function testConnection(Request $request)
    {
        $settings = AdminTelegramSetting::getOrCreate();

        if (!$settings->telegram_bot_token || !$settings->telegram_chat_id) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо указать токен бота и ID чата'
            ]);
        }

        // Отправляем тестовое сообщение
        $sent = $this->adminTelegramService->sendTestMessage();

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'Тестовое сообщение успешно отправлено!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отправке тестового сообщения'
            ]);
        }
    }

    /**
     * Получить инструкции по настройке
     */
    public function getInstructions()
    {
        return response()->json([
            'instructions' => [
                'step1' => '1. Найдите @BotFather в Telegram',
                'step2' => '2. Отправьте команду /newbot',
                'step3' => '3. Следуйте инструкциям для создания бота',
                'step4' => '4. Сохраните полученный токен',
                'step5' => '5. Для получения ID чата:',
                'step6' => '   - Отправьте боту любое сообщение',
                'step7' => '   - Перейдите по ссылке: https://api.telegram.org/bot<TOKEN>/getUpdates',
                'step8' => '   - Найдите в ответе "chat":{"id":123456789}',
                'step9' => '6. Вставьте токен и ID чата в настройки'
            ]
        ]);
    }
}
