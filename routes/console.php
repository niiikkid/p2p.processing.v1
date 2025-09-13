<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CleanupDevicePingsCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command('app:update-p2p-prices')->everyMinute();
Schedule::command('app:reset-payment-detail-limits')->dailyAt('00:00');
Schedule::command('app:load-payment-methods-list')->hourly();
Schedule::command('device-pings:cleanup')->everyMinute();
