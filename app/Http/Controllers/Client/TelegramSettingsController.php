<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Project;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TelegramSettingsController extends Controller
{
    private TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Показать страницу настроек Telegram
     */
    public function index()
    {
        $user = Auth::user();
        $project = Project::find($user->project_id);

        return view('client.telegram-settings.index', compact('project'));
    }

    /**
     * Обновить настройки Telegram
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $project = Project::find($user->project_id);

        $validated = $request->validate([
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:255',
            'telegram_notifications_enabled' => 'boolean',
        ]);

        // Если включены уведомления, проверяем обязательность токена и chat_id
        if ($validated['telegram_notifications_enabled'] ?? false) {
            if (empty($validated['telegram_bot_token'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['telegram_bot_token' => [__('messages.telegram_bot_token_required')]]
                    ], 422);
                }
                return back()->withErrors(['telegram_bot_token' => __('messages.telegram_bot_token_required')]);
            }
            if (empty($validated['telegram_chat_id'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['telegram_chat_id' => [__('messages.telegram_chat_id_required')]]
                    ], 422);
                }
                return back()->withErrors(['telegram_chat_id' => __('messages.telegram_chat_id_required')]);
            }
        }

        $project->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.telegram_settings_saved')
            ]);
        }

        return back()->with('success', __('messages.telegram_settings_saved'));
    }

    /**
     * Проверить подключение к Telegram
     */
    public function testConnection(Request $request)
    {
        $user = Auth::user();
        $project = Project::find($user->project_id);

        if (!$project->telegram_bot_token || !$project->telegram_chat_id) {
            return response()->json([
                'success' => false,
                'message' => 'Токен бота и ID чата должны быть заполнены'
            ]);
        }

        // Временно устанавливаем токен и chat_id для тестирования
        config(['services.telegram.bot_token' => $project->telegram_bot_token ?? '']);
        config(['services.telegram.chat_id' => $project->telegram_chat_id ?? '']);

        // Получаем информацию о боте
        $botInfo = $this->telegramService->getBotInfo();
        
        if (!$botInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось подключиться к Telegram API. Проверьте токен бота.'
            ]);
        }

        // Отправляем тестовое сообщение
        $testMessage = "🧪 <b>Тестовое уведомление</b>\n\n";
        $testMessage .= "✅ Подключение к Telegram успешно настроено!\n";
        $testMessage .= "🤖 <b>Бот:</b> @{$botInfo['username']}\n";
        $testMessage .= "🏢 <b>Салон:</b> {$project->project_name}\n";
        $testMessage .= "📅 <b>Дата:</b> " . now()->format('d.m.Y H:i:s');

        $sent = $this->telegramService->sendMessage($testMessage);

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'Тестовое сообщение успешно отправлено в Telegram!',
                'bot_info' => $botInfo
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось отправить тестовое сообщение. Проверьте ID чата.'
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
                'step1' => '1. Найдите @BotFather в Telegram и создайте нового бота командой /newbot',
                'step2' => '2. Получите токен бота от BotFather',
                'step3' => '3. Добавьте бота в нужный чат или канал',
                'step4' => '4. Для получения ID чата:',
                'step5' => '   - Для личного чата: отправьте боту сообщение и перейдите по ссылке https://api.telegram.org/bot<TOKEN>/getUpdates',
                'step6' => '   - Для группы: добавьте бота в группу и сделайте его администратором',
                'step7' => '   - Для канала: добавьте бота в канал как администратора',
                'step8' => '5. Вставьте токен и ID чата в настройки',
                'step9' => '6. Включите уведомления и протестируйте подключение'
            ]
        ]);
    }
}
