<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationService
{
    public static function send(string $message): void
    {
        try {
            Http::post(config('services.slack_webhook_url'), [
                'text' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('Slack通知失敗: ' . $e->getMessage());
        }
    }
}
