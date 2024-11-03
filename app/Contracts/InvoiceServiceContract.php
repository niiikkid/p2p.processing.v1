<?php

namespace App\Contracts;

use App\Enums\InvoiceWithdrawalSourceType;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Services\Money\Money;

interface InvoiceServiceContract
{
    public function createWithdrawal(Wallet $wallet, Money $amount, string $address, InvoiceWithdrawalSourceType $sourceType): Invoice;

    public function deposit(Wallet $wallet, Money $amount, InvoiceWithdrawalSourceType $sourceType): void;

    public function withdraw(Wallet $wallet, Money $amount, InvoiceWithdrawalSourceType $sourceType): void;
}
