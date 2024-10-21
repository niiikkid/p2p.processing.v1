<?php

namespace App\Services\Wallet;

use App\Contracts\WalletServiceContract;
use App\Enums\TransactionDirection;
use App\Enums\TransactionType;
use App\Exceptions\WalletException;
use App\Jobs\SendTelegramNotificationJob;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use App\Services\TelegramBot\Notifications\LowBalance;

class WalletService implements WalletServiceContract
{
    const RESERVE_BALANCE = 1000;

    public function getMaxReserveBalance(): int
    {
        return self::RESERVE_BALANCE;
    }

    public function create(User $user): Wallet
    {
        return Wallet::create([
            'trast_balance' => Money::fromPrecision(0, Currency::USDT()),
            'reserve_balance' => Money::fromPrecision(0, Currency::USDT()),
            'currency' => Currency::USDT(),
            'user_id' => $user->id,
        ]);
    }

    public function take(Wallet $wallet, Money $amount, TransactionType $type): void
    {
        if ($type->direction()->notEquals(TransactionDirection::OUT)) {
            throw WalletException::make('Invalid transaction type.');
        }

        $trast = $wallet->trast_balance->sub($amount);

        if ($trast->toPrecision() < 0) {
            $reserve = $wallet->reserve_balance->sub(abs($trast->toBeauty()));
            $wallet->update([
                'trast_balance' => Money::fromPrecision(0, Currency::USDT()),
                'reserve_balance' => $reserve,
            ]);
        } else {
            $wallet->update([
                'trast_balance' => $trast,
            ]);
        }

        if (self::RESERVE_BALANCE / 10 > intval($wallet->trast_balance->toBeauty())) {
            SendTelegramNotificationJob::dispatch(
                new LowBalance(
                    telegram: $wallet->user->telegram,
                    wallet: $wallet
                )
            );
        }

        Transaction::create([
            'amount' => $amount,
            'currency' => $amount->getCurrency(),
            'direction' => TransactionDirection::OUT,
            'type' => $type,
            'wallet_id' => $wallet->id,
        ]);
    }

    public function give(Wallet $wallet, Money $amount, TransactionType $type): void
    {
        if ($type->direction()->notEquals(TransactionDirection::IN)) {
            throw WalletException::make('Invalid transaction type.');
        }

        $reserve = $wallet->reserve_balance->sub($this->getMaxReserveBalance());

        if ($reserve->toPrecision() < 0) {
            $reserve = abs($reserve->toBeauty());
        }

        $trast = $amount->sub($reserve);

        if ($trast->toPrecision() > 0) {
            $wallet->update([
                'trast_balance' => $wallet->trast_balance->add($trast),
                'reserve_balance' => $wallet->reserve_balance->add($reserve),
            ]);
        } else {
            $wallet->update([
                'reserve_balance' => $wallet->reserve_balance->add($amount),
            ]);
        }

        Transaction::create([
            'amount' => $amount,
            'currency' => $amount->getCurrency(),
            'direction' => TransactionDirection::IN,
            'type' => $type,
            'wallet_id' => $wallet->id,
        ]);
    }
}
