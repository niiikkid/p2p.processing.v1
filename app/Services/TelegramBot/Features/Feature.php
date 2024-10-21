<?php

namespace App\Services\TelegramBot\Features;


use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

abstract class Feature
{
    public function __construct(
        protected Update $update,
        protected array $arguments = []
    )
    {}

    abstract public function handle(): void;

    public function getName(): string
    {
        $reflect = new \ReflectionClass($this);

        return $reflect->getShortName();
    }

    protected function replyWithMessage(array $params): void
    {
        if ($this->getName() !== 'MainMenu') {
            if (empty($params['reply_markup'])) {
                $params['reply_markup'] = Keyboard::make()
                    ->setOneTimeKeyboard(true)
                    ->setResizeKeyboard(true)
                    ->inline();
            }

            $params['reply_markup']->row([
                Keyboard::inlineButton([
                    'text' => 'Главное меню',
                    'callback_data' => $this->setCallback('MainMenu'),
                ]),
            ]);
        }

        Telegram::sendMessage($params + [
            'chat_id' => $this->update->getChat()->id,
        ]);
    }

    protected function getTelegramUserID(): int
    {
        return $this->update->getChat()->id;
    }

    protected function getTelegram(): ?\App\Models\Telegram
    {
        return \App\Models\Telegram::where('telegram_id', $this->getTelegramUserID())->first();
    }

    protected function getArgument(string $name): mixed
    {
        return $this->arguments[$name] ?? null;
    }

    protected function setCallback(string $featureName, array $arguments = []): string
    {
        return json_encode([
            'feature' => $featureName,
            'arguments' => $arguments,
        ]);
    }

    protected function callFeature(string $featureClass, array $arguments): void
    {
        $feature = new $featureClass($this->update, $arguments);

        $feature->handle();
    }
}
