<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    private string $apiUrl;
    private string $token;

    public function __construct()
    {
        $this->token = config('services.telegram.token');
        $this->apiUrl = config('services.telegram.api_url') . $this->token . '/';
    }

    /**
     * Send request to Telegram API to send a message.
     *
     * @param int $chatId
     * @param string $text
     * @param array|null $replyMarkup
     */
    public function sendMessage(int $chatId, string $text, ?array $replyMarkup = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        Http::post($this->apiUrl . 'sendMessage', $payload);
    }

    /**
     * Build an inline keyboard for Telegram messages.
     * 
     * @param array $buttons
     * @return array
     */
    public function buildKeyboard(array $buttons): array
    {
        return [
            'inline_keyboard' => $buttons
        ];
    }
}
