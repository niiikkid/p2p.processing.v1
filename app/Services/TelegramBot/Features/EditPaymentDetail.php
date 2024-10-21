<?php

namespace App\Services\TelegramBot\Features;

use App\Models\PaymentDetail;
use Telegram\Bot\Keyboard\Keyboard;

class EditPaymentDetail extends Feature
{
    public function handle(): void
    {
        $telegram = $this->getTelegram();

        if (! $telegram) {
            return;
        }

        $user = $telegram->user;

        $detailID = $this->getArgument('detail_id');

        if (! $detailID) {
            return;
        }

        $paymentDetail = PaymentDetail::find($detailID);

        if (! $paymentDetail || $paymentDetail->user?->id !== $user->id) {
            return;
        }

        $replyMarkup = Keyboard::make()
            ->setOneTimeKeyboard(true)
            ->setResizeKeyboard(true)
            ->inline();

        if (! $paymentDetail->is_active) {
            $replyMarkup->row([
                Keyboard::inlineButton([
                    'text' => 'Включить',
                    'callback_data' => $this->setCallback('EnablePaymentDetail', ['detail_id' => $detailID]),
                ]),
            ]);
        } else {
            $replyMarkup->row([
                Keyboard::inlineButton([
                    'text' => 'Выключить',
                    'callback_data' => $this->setCallback('DisablePaymentDetail', ['detail_id' => $detailID]),
                ]),
            ]);
        }

        $replyMarkup->row([
            Keyboard::inlineButton([
                'text' => 'Мои реквизиты',
                'callback_data' => $this->setCallback('ShowPaymentDetailsList'),
            ])
        ]);

        $text = "Реквизит: $paymentDetail->detail\r\n";
        $text .= "Название: $paymentDetail->name\r\n";
        $text .= "Лимит: (" . $paymentDetail->current_daily_limit->toBeauty()
            . "/" . $paymentDetail->daily_limit->toBeauty() . ") \r\n";
        $text .= "Статус: " . ($paymentDetail->is_active ? "\u{1F7E2}Включен" : "\u{1F534}Выключен");

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $replyMarkup
        ]);
    }
}
