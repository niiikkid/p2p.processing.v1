<?php

namespace App\Services\TelegramBot\Features;

use Telegram\Bot\Keyboard\Keyboard;

class MainMenu extends Feature
{
    public function handle(): void
    {
        $replyMarkup = Keyboard::make()
            ->setOneTimeKeyboard(true)
            ->setResizeKeyboard(true)
            ->inline()
            ->row([
                Keyboard::inlineButton([
                    'text' => 'Посмотреть баланс',
                    'callback_data' => $this->setCallback('ShowBalance')
                ]),
            ])
            ->row([
                Keyboard::inlineButton([
                    'text' => 'Мои реквизиты',
                    'callback_data' => $this->setCallback('ShowPaymentDetailsList')
                ]),
            ]);

        $this->replyWithMessage([
            'text' => 'Главное меню',
            'reply_markup' => $replyMarkup
        ]);
    }
}
