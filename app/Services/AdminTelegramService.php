<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Admin\AdminTelegramSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminTelegramService
{
    private ?string $botToken;
    private ?string $chatId;
    private bool $notificationsEnabled;

    public function __construct()
    {
        $settings = AdminTelegramSetting::getOrCreate();
        $this->botToken = $settings->telegram_bot_token;
        $this->chatId = $settings->telegram_chat_id;
        $this->notificationsEnabled = $settings->telegram_notifications_enabled;
    }

    /**
     * Отправка сообщения в Telegram админам
     */
    public function sendMessage(string $message): bool
    {
        if (!$this->notificationsEnabled || empty($this->botToken) || empty($this->chatId)) {
            Log::info('Admin Telegram notifications disabled or not configured', [
                'enabled' => $this->notificationsEnabled,
                'bot_token' => $this->botToken ? 'set' : 'not set',
                'chat_id' => $this->chatId ? 'set' : 'not set'
            ]);
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                Log::info('Admin Telegram message sent successfully', [
                    'message' => $message,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send Admin Telegram message', [
                    'message' => $message,
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception in AdminTelegramService::sendMessage', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Уведомление о новом проекте
     */
    public function sendNewProjectNotification(array $projectData): bool
    {
        $settings = AdminTelegramSetting::getOrCreate();
        if (!$settings->notify_new_projects) {
            return false;
        }

        $message = $this->formatNewProjectMessage($projectData);
        return $this->sendMessage($message);
    }

    /**
     * Уведомление о новой подписке
     */
    public function sendNewSubscriptionNotification(array $subscriptionData): bool
    {
        $settings = AdminTelegramSetting::getOrCreate();
        if (!$settings->notify_new_subscriptions) {
            return false;
        }

        $message = $this->formatNewSubscriptionMessage($subscriptionData);
        return $this->sendMessage($message);
    }

    /**
     * Уведомление о новом сообщении в поддержку
     */
    public function sendNewMessageNotification(array $messageData): bool
    {
        $settings = AdminTelegramSetting::getOrCreate();
        if (!$settings->notify_new_messages) {
            return false;
        }

        $message = $this->formatNewMessageMessage($messageData);
        return $this->sendMessage($message);
    }

    /**
     * Уведомление об истечении подписки
     */
    public function sendSubscriptionExpiresNotification(array $subscriptionData): bool
    {
        $settings = AdminTelegramSetting::getOrCreate();
        if (!$settings->notify_subscription_expires) {
            return false;
        }

        $message = $this->formatSubscriptionExpiresMessage($subscriptionData);
        return $this->sendMessage($message);
    }

    /**
     * Уведомление о проблемах с платежами
     */
    public function sendPaymentIssueNotification(array $paymentData): bool
    {
        $settings = AdminTelegramSetting::getOrCreate();
        if (!$settings->notify_payment_issues) {
            return false;
        }

        $message = $this->formatPaymentIssueMessage($paymentData);
        return $this->sendMessage($message);
    }

    /**
     * Форматирование сообщения о новом проекте
     */
    private function formatNewProjectMessage(array $data): string
    {
        $message = "🆕 <b>Новый проект зарегистрирован!</b>\n\n";
        $message .= "🏢 <b>Название:</b> {$data['project_name']}\n";
        $message .= "👤 <b>Владелец:</b> {$data['owner_name']}\n";
        $message .= "📧 <b>Email:</b> {$data['email']}\n";
        $message .= "📞 <b>Телефон:</b> {$data['phone']}\n";
        $message .= "📅 <b>Дата регистрации:</b> {$data['registered_at']}\n";
        
        if (!empty($data['website'])) {
            $message .= "🌐 <b>Сайт:</b> {$data['website']}\n";
        }
        
        if (!empty($data['address'])) {
            $message .= "📍 <b>Адрес:</b> {$data['address']}\n";
        }

        return $message;
    }

    /**
     * Форматирование сообщения о новой подписке
     */
    private function formatNewSubscriptionMessage(array $data): string
    {
        $message = "💳 <b>Новая подписка!</b>\n\n";
        $message .= "🏢 <b>Проект:</b> {$data['project_name']}\n";
        $message .= "📦 <b>Тариф:</b> {$data['plan_name']}\n";
        $message .= "💰 <b>Стоимость:</b> {$data['amount']} {$data['currency']}\n";
        $message .= "📅 <b>Период:</b> {$data['period']}\n";
        $message .= "⏰ <b>Истекает:</b> {$data['expires_at']}\n";
        $message .= "👤 <b>Пользователь:</b> {$data['user_name']}\n";

        return $message;
    }

    /**
     * Форматирование сообщения о новом сообщении в поддержку
     */
    private function formatNewMessageMessage(array $data): string
    {
        $message = "💬 <b>Новое сообщение в поддержку!</b>\n\n";
        $message .= "🏢 <b>Проект:</b> {$data['project_name']}\n";
        $message .= "👤 <b>От:</b> {$data['user_name']}\n";
        $message .= "📧 <b>Email:</b> {$data['user_email']}\n";
        $message .= "📝 <b>Сообщение:</b>\n{$data['message']}\n";
        $message .= "📅 <b>Время:</b> {$data['created_at']}\n";

        return $message;
    }

    /**
     * Форматирование сообщения об истечении подписки
     */
    private function formatSubscriptionExpiresMessage(array $data): string
    {
        $message = "⚠️ <b>Подписка истекает!</b>\n\n";
        $message .= "🏢 <b>Проект:</b> {$data['project_name']}\n";
        $message .= "📦 <b>Тариф:</b> {$data['plan_name']}\n";
        $message .= "⏰ <b>Истекает:</b> {$data['expires_at']}\n";
        $message .= "👤 <b>Пользователь:</b> {$data['user_name']}\n";
        $message .= "📧 <b>Email:</b> {$data['user_email']}\n";

        return $message;
    }

    /**
     * Форматирование сообщения о проблемах с платежами
     */
    private function formatPaymentIssueMessage(array $data): string
    {
        $message = "❌ <b>Проблема с платежом!</b>\n\n";
        $message .= "🏢 <b>Проект:</b> {$data['project_name']}\n";
        $message .= "💰 <b>Сумма:</b> {$data['amount']} {$data['currency']}\n";
        $message .= "📦 <b>Тариф:</b> {$data['plan_name']}\n";
        $message .= "❌ <b>Ошибка:</b> {$data['error']}\n";
        $message .= "👤 <b>Пользователь:</b> {$data['user_name']}\n";
        $message .= "📅 <b>Время:</b> {$data['created_at']}\n";

        return $message;
    }

    /**
     * Получить информацию о боте
     */
    public function getBotInfo(): ?array
    {
        if (empty($this->botToken)) {
            return null;
        }

        try {
            $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getMe");
            
            if ($response->successful()) {
                return $response->json()['result'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get bot info', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Тестовое сообщение
     */
    public function sendTestMessage(): bool
    {
        $botInfo = $this->getBotInfo();
        
        $message = "🧪 <b>Тестовое уведомление админ панели</b>\n\n";
        $message .= "✅ Подключение к Telegram успешно настроено!\n";
        
        if ($botInfo) {
            $message .= "🤖 <b>Бот:</b> @{$botInfo['username']}\n";
        }
        
        $message .= "📅 <b>Дата:</b> " . now()->format('d.m.Y H:i:s');

        return $this->sendMessage($message);
    }
}
