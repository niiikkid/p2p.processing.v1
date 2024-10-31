<?php

namespace App\Contracts;

use App\Enums\TransactionType;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Money\Money;

interface WalletServiceContract
{
    public function create(User $user): Wallet;

    public function takeMerchant(Wallet $wallet, Money $amount): void;

    public function giveMerchant(Wallet $wallet, Money $amount): void;

    public function takeTrust(Wallet $wallet, Money $amount, TransactionType $type): void;

    public function giveTrust(Wallet $wallet, Money $amount, TransactionType $type): void;

    public function getMaxReserveBalance(): int;
}
