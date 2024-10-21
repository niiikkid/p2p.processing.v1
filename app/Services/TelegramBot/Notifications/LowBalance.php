<?php

namespace App\Services\TelegramBot\Notifications;

use App\Models\Telegram;
use App\Models\Wallet;

class LowBalance extends Notification
{
    public function __construct(
        protected Telegram $telegram,
        protected Wallet $wallet,
    )
    {}

    public function getMessage(): string
    {
        $trast_balance = $this->wallet->trast_balance->toBeauty() . ' ' . strtoupper($this->wallet->currency->getCode());
        $reserve_balance = $this->wallet->reserve_balance->toBeauty() . ' ' . strtoupper($this->wallet->currency->getCode());

        return "На балансе осталось мало средств для совершения сделок!\r\n"
            ."\r\n"
            ."Текущий баланс\r\n"
            . "Траст: $trast_balance\r\n"
            . "Резерв: $reserve_balance\r\n";
    }

    protected function getTelegram(): Telegram
    {
        return $this->telegram;
    }
}
