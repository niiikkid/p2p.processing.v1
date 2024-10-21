<?php

namespace App\Services\TelegramBot;

use App\Contracts\TelegramBotServiceContract;
use App\Services\TelegramBot\Features\EditPaymentDetail;
use App\Services\TelegramBot\Notifications\Notification;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotService implements TelegramBotServiceContract
{
    public function __construct(
        protected string $webhookToken
    )
    {}

    public function setWebhook(): bool
    {
        return Telegram::setWebhook([
            'url' => route('telegram-bot.webhook', $this->webhookToken)
        ]);
    }

    public function sendNotification(Notification $notification): bool
    {
        if ($notification->getMemberStatus() === 'kicked') {
            return false;
        }

        Telegram::sendMessage([
            'chat_id' => $notification->getChat(),
            'text' => $notification->getMessage(),
            'parse_mode' => 'HTML'
        ]);

        $notification->afterSend();

        return true;
    }

    public function handleWebhook(): void
    {
        $update = Telegram::commandsHandler(true);

        try {
            if ($update?->myChatMember?->newChatMember?->status === 'kicked') {
                \App\Models\Telegram::where('telegram_id', $update->myChatMember->from->id)->update([
                    'member_status' => $update?->myChatMember?->newChatMember?->status
                ]);
            } else if ($update?->myChatMember?->newChatMember?->status === 'member') {
                $telegram = \App\Models\Telegram::where('telegram_id', $update->myChatMember->from->id)
                    ->first();

                if ($telegram->member_status === 'kicked') {
                    \App\Models\Telegram::where('telegram_id', $update->myChatMember->from->id)->update([
                        'member_status' => $update?->myChatMember?->newChatMember?->status
                    ]);
                }
            }
        } catch (\Exception $e) {
            report($e);
        }

        try {
            if ($update->callbackQuery) {
                Telegram::answerCallbackQuery([
                    'callback_query_id' => $update->callbackQuery->id,
                ]);

                $data = json_decode($update->callbackQuery->data, true);

                if (! empty($data['feature'])) {
                    $featureName = $data['feature'];
                    $class = 'App\\Services\\TelegramBot\\Features\\' . $featureName;

                    $feature = new $class($update, $data['arguments']);

                    $feature->handle();
                }
            }
        } catch (\Exception $e) {
            report($e);
        }
    }
}
