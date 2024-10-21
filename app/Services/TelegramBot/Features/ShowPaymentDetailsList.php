<?php

namespace App\Services\TelegramBot\Features;

use Telegram\Bot\Keyboard\Keyboard;

class ShowPaymentDetailsList extends Feature
{
    public function handle(): void
    {
        $telegram = $this->getTelegram();

        if (! $telegram) {
            return;
        }

        $user = $telegram->user;

        $replyMarkup = Keyboard::make()
            ->setOneTimeKeyboard(true)
            ->setResizeKeyboard(true)
            ->inline();


        foreach ($user->paymentDetails as $paymentDetail) {
            $text = "";

            $text .= $paymentDetail->detail . " ";
            $text .= $paymentDetail->name . " ";
            $text .= "(" . $paymentDetail->current_daily_limit->toBeauty()
                . "/" . $paymentDetail->daily_limit->toBeauty() . ") ";
            $text .= $paymentDetail->is_active ? "\u{1F7E2}Включен" : "\u{1F534}Выключен";

            $replyMarkup->row([
                Keyboard::inlineButton([
                    'text' => $text,
                    'callback_data' => $this->setCallback('EditPaymentDetail', ['detail_id' => $paymentDetail->id])
                ]),
            ]);
        }

        $replyMarkup->row([
            Keyboard::inlineButton([
                'text' => 'Обновить',
                'callback_data' => $this->setCallback('ShowPaymentDetailsList')
            ]),
        ]);


        $this->replyWithMessage([
            'text' => "Ваши реквизиты. Вы можете перейти к редактированию реквизита, нажав на него.",
            'reply_markup' => $replyMarkup
        ]);
    }
}
