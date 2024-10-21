<?php

namespace App\Contracts;

use App\Enums\TransactionType;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Money\Money;

interface WalletServiceContract
{
    public function create(User $user): Wallet;

    public function take(Wallet $wallet, Money $amount, TransactionType $type): void;

    public function give(Wallet $wallet, Money $amount, TransactionType $type): void;

    public function getMaxReserveBalance(): int;
}
