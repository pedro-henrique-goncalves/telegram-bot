<?php

namespace App\Services;

use App\Repositories\TelegramBotRepository;
use App\Jobs\SendTelegramMessageJob;
use Illuminate\Support\Facades\Validator;

class TelegramBotService
{
    private TelegramService $telegramService;
    private TelegramBotRepository $userRepo;

    public function __construct(TelegramService $telegramService, TelegramBotRepository $userRepo)
    {
        $this->telegramService = $telegramService;
        $this->userRepo = $userRepo;
    }

    /**
     * Process incoming updates from Telegram.
     *
     * @param array $update
     * @return void
     */
    public function processUpdate(array $update): void
    {
        $message  = $update['message'] ?? null;
        $callback = $update['callback_query'] ?? null;

        if ($message && isset($message['text'])) {
            $this->handleTextMessage($message);
        } elseif ($callback) {
            $this->handleCallback($callback);
        }
    }

    /**
     * Handle text messages sent by the user.
     *
     * @param array $message
     * @return void
     */
        private function handleTextMessage(array $message): void
    {
        $chatId     = $message['chat']['id'];
        $firstName  = $message['from']['first_name'] ?? '';
        $username   = $message['from']['username'] ?? '';
        $text       = trim($message['text']);

        // Ensure the user is saved/updated
        $this->userRepo->createOrUpdate([
            'telegram_id' => $chatId,
            'first_name'  => $firstName,
            'username'    => $username
        ]);

        if ($text === '/start') {
            $this->sendStartMessage(
                $chatId,
                "Seja bem-vindo, {$firstName}! É um prazer ter você por aqui. O que você deseja hoje?"
            );
            return;
        }

        if ($this->isValidEmail($text)) {
            $this->processVipRequest($chatId, $firstName, $text);
            return;
        }

        // Not a valid email and not /start → treat as unknown command
        SendTelegramMessageJob::dispatch($chatId, "Comando não reconhecido. Digite /start para iniciar.");
    }

    /**
     * Handle callback queries from inline buttons.
     *
     * @param array $callback
     * @return void
     */
    private function handleCallback(array $callback): void
    {
        $chatId = $callback['message']['chat']['id'];
        $data   = $callback['data'];

        match ($data) {
            'quero_vip' => SendTelegramMessageJob::dispatch($chatId, "Por favor, envie seu e-mail."),
            'sair_vip'  => $this->removeVipStatus($chatId),
            'acompanhar' => SendTelegramMessageJob::dispatch($chatId, "Sua solicitação está em análise."),
        };
    }

    /**
     * Send a welcome or custom message with the appropriate keyboard
     * based on the user's VIP status.
     *
     * @param int $chatId
     * @param string $messageText
     * @return void
     */
    private function sendStartMessage(int $chatId, string $messageText): void
    {
        $user = $this->userRepo->findByTelegramId($chatId);

        $keyboard = $user && $user->is_vip
            ? $this->telegramService->buildKeyboard([
                [['text' => 'QUERO SAIR DO VIP', 'callback_data' => 'sair_vip']]
              ])
            : $this->telegramService->buildKeyboard([
                [['text' => 'QUERO SER VIP', 'callback_data' => 'quero_vip']],
                [['text' => 'ACOMPANHAR MINHA SOLICITAÇÃO', 'callback_data' => 'acompanhar']]
              ]);

        SendTelegramMessageJob::dispatch($chatId, $messageText, $keyboard);
    }

    /**
     * Process the VIP membership request.
     * Updates email, sets VIP status, and sends a confirmation
     * along with the updated menu.
     *
     * @param int $chatId
     * @param string $firstName
     * @param string $email
     * @return void
     */
    private function processVipRequest(int $chatId, string $firstName, string $email): void
    {
        $this->userRepo->updateEmail($chatId, $email);
        // logic to add user VIP status
        // $this->userRepo->setVipStatus($chatId, true);

        $this->sendStartMessage($chatId, "Seja bem-vindo, {$firstName}! É um prazer ter você por aqui. Solicitação enviada.");
    }

    /**
     * Remove VIP status from the user.
     *
     * @param int $chatId
     * @return void
     */

    private function removeVipStatus(int $chatId): void
    {
        // logic to remove user VIP status
        // $this->userRepo->setVipStatus($chatId, false);
        SendTelegramMessageJob::dispatch($chatId, "Você saiu do VIP.");
    }

    /**
     * Validate if the provided string is a valid email.
     *
     * @param string $text
     * @return bool
     */
    private function isValidEmail(string $text): bool
    {
        $validate = Validator::make(
            ['email' => $text], 
            ['email' => 'required|email']
        );

        return !$validate->fails();
    }
}
