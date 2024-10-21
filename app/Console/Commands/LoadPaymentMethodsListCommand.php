<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LoadPaymentMethodsListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-payment-methods-list';

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
        services()->market()->loadPaymentMethodsList();
    }
}
