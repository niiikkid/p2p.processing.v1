<?php

namespace App\Jobs;

use App\Contracts\SmsServiceContract;
use App\DTO\SMS\SmsDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private SmsDTO $sms,
    )
    {
        $this->afterCommit();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            make(SmsServiceContract::class)->handleSms($this->sms);
        } catch (\Throwable $e) {
            logger()->error(json_encode(get_object_vars($this->sms)));
            report($e);
        }
    }
}
