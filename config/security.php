<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Настройки безопасности для системы тикетов и уведомлений
    |
    */

    'rate_limits' => [
        'tickets' => [
            'max_attempts' => env('RATE_LIMIT_TICKETS', 10),
            'decay_minutes' => env('RATE_LIMIT_TICKETS_DECAY', 1),
        ],
        'messages' => [
            'max_attempts' => env('RATE_LIMIT_MESSAGES', 30),
            'decay_minutes' => env('RATE_LIMIT_MESSAGES_DECAY', 1),
        ],
        'notifications' => [
            'max_attempts' => env('RATE_LIMIT_NOTIFICATIONS', 60),
            'decay_minutes' => env('RATE_LIMIT_NOTIFICATIONS_DECAY', 1),
        ],
    ],

    'logging' => [
        'enabled' => env('SECURITY_LOGGING_ENABLED', true),
        'level' => env('SECURITY_LOG_LEVEL', 'info'),
        'channels' => [
            'security' => env('SECURITY_LOG_CHANNEL', 'daily'),
        ],
    ],

    'notifications' => [
        'max_per_user' => env('MAX_NOTIFICATIONS_PER_USER', 1000),
        'cleanup_old_days' => env('NOTIFICATIONS_CLEANUP_DAYS', 30),
    ],

    'tickets' => [
        'max_subject_length' => env('MAX_TICKET_SUBJECT_LENGTH', 255),
        'max_message_length' => env('MAX_TICKET_MESSAGE_LENGTH', 5000),
        'max_tickets_per_user' => env('MAX_TICKETS_PER_USER', 100),
    ],
]; 