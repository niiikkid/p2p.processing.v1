<?php

namespace App\Console\Commands;

use App\Models\SmsLog;
use App\Services\Sms\Utils\NormalizeMessage;
use Illuminate\Console\Command;

class NormalizeSmsLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:normalize-sms-logs';

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
        SmsLog::query()
            ->chunk(100, function ($logs) {
                $logs->each(function ($log) {
                    $message = NormalizeMessage::normalize($log->message);

                    $log->update([
                        'message' => $message,
                    ]);
                });
            });
    }
}
