<?php

namespace App\Services\TelegramBot\Features;

class ShowBalance extends Feature
{
    protected string $name = 'balance';
    protected string $description = 'Показывает данные по балансам';

    public function handle(): void
    {
        $telegram = $this->getTelegram();

        if (! $telegram) {
            return;
        }

        $user = $telegram->user;
        $wallet = $user->wallet;

        $trust_balance = $wallet->trust_balance->toBeauty() . ' ' . strtoupper($wallet->currency->getCode());
        $reserve_balance = $wallet->reserve_balance->toBeauty() . ' ' . strtoupper($wallet->currency->getCode());

        $this->replyWithMessage([
            'text' => "Текущий баланс\r\n"
            . "Траст: $trust_balance\r\n"
            . "Резерв: $reserve_balance\r\n",
        ]);
    }
}
