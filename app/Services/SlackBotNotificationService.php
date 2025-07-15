<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SlackBotNotificationService
{
    protected string $token;
    protected string $defaultChannel;

    public function __construct()
    {
        $this->token = config('services.slack.notifications.bot_user_oauth_token');
        $this->defaultChannel = config('services.slack.notifications.channel');
    }

    public function sendMessage(string $message, ?string $channel = null): bool
    {
        $response = Http::withToken($this->token)
            ->post('https://slack.com/api/chat.postMessage', [
                'channel' => $channel ?? $this->defaultChannel,
                'text' => $message,
            ]);

        return $response->ok() && $response->json('ok');
    }
}
