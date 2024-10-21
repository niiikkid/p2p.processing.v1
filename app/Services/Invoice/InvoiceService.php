<?php

namespace App\Services\Invoice;

use App\Contracts\InvoiceServiceContract;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\TransactionType;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Services\Money\Currency;
use App\Services\Money\Money;

class InvoiceService implements InvoiceServiceContract
{
    public function deposit(Wallet $wallet, Money $amount): void
    {
        Invoice::create([
            'amount' => $amount,
            'currency' => Currency::USDT(),
            'type' => InvoiceType::DEPOSIT,
            'status' => InvoiceStatus::SUCCESS,
            'wallet_id' => $wallet->id,
        ]);

        services()->wallet()->give($wallet, $amount, TransactionType::DEPOSIT_BY_ADMIN);
    }

    public function withdraw(Wallet $wallet, Money $amount): void
    {
        Invoice::create([
            'amount' => $amount,
            'currency' => Currency::USDT(),
            'type' => InvoiceType::WITHDRAWAL,
            'status' => InvoiceStatus::SUCCESS,
            'wallet_id' => $wallet->id,
        ]);

        services()->wallet()->take($wallet, $amount, TransactionType::WITHDRAWAL_BY_ADMIN);
    }
}
