<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private ?string $botToken;
    private ?string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    /**
     * Установить настройки для конкретного проекта
     */
    public function setProjectSettings(?string $botToken, ?string $chatId): void
    {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    /**
     * Отправка сообщения в Telegram
     */
    public function sendMessage(string $message): bool
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::warning('Telegram bot token or chat ID not configured', [
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
                Log::info('Telegram message sent successfully', [
                    'message' => $message,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send Telegram message', [
                    'message' => $message,
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending Telegram message', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Отправка уведомления о новой записи
     */
    public function sendAppointmentNotification(array $appointmentData): bool
    {
        $message = $this->formatAppointmentMessage($appointmentData);
        return $this->sendMessage($message);
    }

    /**
     * Форматирование сообщения о записи
     */
    private function formatAppointmentMessage(array $data): string
    {
        $date = \Carbon\Carbon::parse($data['date'])->format('d.m.Y');
        $time = $data['time'];
        
        $message = "🆕 <b>Новая запись!</b>\n\n";
        $message .= "👤 <b>Клиент:</b> {$data['client_name']}\n";
        $message .= "📞 <b>Телефон:</b> {$data['client_phone']}\n";
        
        if (!empty($data['client_email'])) {
            $message .= "📧 <b>Email:</b> {$data['client_email']}\n";
        }
        
        $message .= "💇‍♀️ <b>Услуга:</b> {$data['service_name']}\n";
        $message .= "👨‍💼 <b>Мастер:</b> {$data['master_name']}\n";
        $message .= "📅 <b>Дата:</b> {$date}\n";
        $message .= "🕐 <b>Время:</b> {$time}\n";
        
        if (!empty($data['price'])) {
            $message .= "💰 <b>Стоимость:</b> {$data['price']} ₽\n";
        }
        
        if (!empty($data['notes'])) {
            $message .= "📝 <b>Примечания:</b> {$data['notes']}\n";
        }
        
        $message .= "\n🏢 <b>Салон:</b> {$data['salon_name']}";
        
        return $message;
    }

    /**
     * Проверка конфигурации Telegram
     */
    public function isConfigured(): bool
    {
        return !empty($this->botToken) && !empty($this->chatId);
    }

    /**
     * Получение информации о боте
     */
    public function getBotInfo(): ?array
    {
        if (empty($this->botToken)) {
            Log::warning('Bot token is empty, cannot get bot info');
            return null;
        }

        try {
            $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getMe");
            
            if ($response->successful()) {
                return $response->json()['result'];
            }
        } catch (\Exception $e) {
            Log::error('Failed to get bot info', ['error' => $e->getMessage()]);
        }

        return null;
    }
} 