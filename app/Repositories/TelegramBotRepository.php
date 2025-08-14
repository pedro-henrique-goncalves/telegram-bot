<?php

namespace App\Repositories;

use App\Models\TelegramUser;

class TelegramBotRepository
{
    /**
     * Find a Telegram user by their Telegram ID.
     *
     * @param int $telegramId
     * @return TelegramUser|null
     */
    public function findByTelegramId(int $telegramId): ?TelegramUser
    {
        return TelegramUser::query()
            ->where('telegram_id', $telegramId)
            ->first();
    }

    /**
     * Create or update a Telegram user.
     *
     * @param array $data
     * @return TelegramUser
     */
    public function createOrUpdate(array $data): TelegramUser
    {
        return TelegramUser::updateOrCreate(
            ['telegram_id' => $data['telegram_id']],
            $data
        );
    }

    /**
     * Update the email of a Telegram user.
     *
     * @param int $telegramId
     * @param string $email
     * @return bool
     */
    public function updateEmail(int $telegramId, string $email): bool
    {
        return TelegramUser::where('telegram_id', $telegramId)
            ->update(['email' => $email]);
    }

    /**
     * Set the VIP status of a Telegram user.
     *
     * @param int $telegramId
     * @param bool $status
     * @return bool
     */
    public function setVipStatus(int $telegramId, bool $status): bool
    {
        return TelegramUser::where('telegram_id', $telegramId)
            ->update(['is_vip' => $status]);
    }
}
