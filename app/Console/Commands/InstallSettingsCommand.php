<?php

namespace App\Console\Commands;

use App\Enums\DetailType;
use App\Models\Dispute;
use App\Models\Notification;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\SmsParser;
use App\Models\User;
use App\Services\Money\Currency;
use App\Services\TelegramBot\Notifications\AdminGlobal;
use App\Services\TelegramBot\Notifications\NewDispute;
use App\Services\TelegramBot\Notifications\NewOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Telegram\Bot\Laravel\Facades\Telegram;

class InstallSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        services()->settings()->createAll();

        $this->comment('Новые настройки были добавлены.');
    }
}
