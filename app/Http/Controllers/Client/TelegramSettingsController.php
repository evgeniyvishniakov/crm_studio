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

        // Получаем или создаем настройки telegram
        $telegramSettings = $project->getOrCreateTelegramSettings();

        return view('client.telegram-settings.index', compact('project', 'telegramSettings'));
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

        // Получаем или создаем настройки telegram
        $telegramSettings = $project->getOrCreateTelegramSettings();

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

        // Обновляем настройки telegram
        $telegramSettings->update($validated);

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
                'message' => __('messages.telegram_fields_required')
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
                'message' => __('messages.telegram_test_error')
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
                'message' => __('messages.telegram_test_success'),
                'bot_info' => $botInfo
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.telegram_test_error')
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
                'step1' => __('messages.telegram_instructions_step1'),
                'step2' => __('messages.telegram_instructions_step2'),
                'step3' => __('messages.telegram_instructions_step3'),
                'step4' => __('messages.telegram_instructions_step4'),
                'step5' => __('messages.telegram_instructions_step5'),
                'step6' => __('messages.telegram_instructions_step6'),
                'step7' => __('messages.telegram_instructions_step7'),
                'step8' => __('messages.telegram_instructions_step8'),
                'step9' => __('messages.telegram_instructions_step9')
            ]
        ]);
    }
}
