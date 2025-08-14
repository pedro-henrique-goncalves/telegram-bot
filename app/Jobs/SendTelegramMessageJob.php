<?php

namespace App\Jobs;

use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $chatId;
    public string $message;
    public ?array $keyboard;

    public function __construct(int $chatId, string $message, ?array $keyboard = null)
    {
        $this->chatId = $chatId;
        $this->message = $message;
        $this->keyboard = $keyboard;
    }

    public function handle(TelegramService $telegramService): void
    {
        $telegramService->sendMessage($this->chatId, $this->message, $this->keyboard);
    }
}
