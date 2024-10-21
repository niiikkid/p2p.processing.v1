<?php

namespace App\Services\TelegramBot\Commands;

use App\Services\TelegramBot\Features\MainMenu;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Запускает бота';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => 'Привет! Добро пожаловать!'
        ]);

        $feature = new MainMenu($this->getUpdate(), []);
        $feature->handle();
    }
}
