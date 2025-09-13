<?php

namespace App\Console\Commands;

use App\Models\DevicePing;
use Illuminate\Console\Command;

class CleanupDevicePingsCommand extends Command
{
    protected $signature = 'device-pings:cleanup';
    protected $description = 'Удаляет записи пингов старше 1 часа';

    public function handle(): int
    {
        $threshold = now()->subHour();
        $deleted = DevicePing::where('pinged_at', '<', $threshold)->delete();
        $this->info("Deleted {$deleted} old device pings older than {$threshold->toDateTimeString()}");
        return self::SUCCESS;
    }
}


