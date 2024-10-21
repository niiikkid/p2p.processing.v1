<?php

namespace App\Contracts;

use App\Models\Wallet;
use App\Services\Money\Money;

interface InvoiceServiceContract
{
    public function deposit(Wallet $wallet, Money $amount): void;

    public function withdraw(Wallet $wallet, Money $amount): void;
}
