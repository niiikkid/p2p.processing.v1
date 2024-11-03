<?php

namespace App\Services\Invoice;

use App\Contracts\InvoiceServiceContract;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\InvoiceWithdrawalSourceType;
use App\Enums\TransactionType;
use App\Exceptions\InvoiceException;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Services\Money\Currency;
use App\Services\Money\Money;

class InvoiceService implements InvoiceServiceContract
{
    public function createWithdrawal(Wallet $wallet, Money $amount, string $address, InvoiceWithdrawalSourceType $sourceType): Invoice
    {
        /**
         * @var Wallet $wallet
         */
        $wallet = auth()->user()->wallet;

        $max = intval($wallet->trust_balance->add($wallet->reserve_balance)->toBeauty());

        if (intval($amount->toBeauty()) > $max) {
            throw new InvoiceException('Insufficient balance');
        }

        $invoice = Invoice::create([
            'amount' => $amount,
            'currency' => $amount->getCurrency(),
            'address' => $address,
            'type' => InvoiceType::WITHDRAWAL,
            'source_type' => $sourceType,
            'status' => InvoiceStatus::PENDING,
            'wallet_id' => $wallet->id,
        ]);

        if ($invoice->source_type->equals(InvoiceWithdrawalSourceType::TRUST)) {
            services()->wallet()->takeTrust($wallet, $amount, TransactionType::WITHDRAWAL_BY_USER);
        } else if ($invoice->source_type->equals(InvoiceWithdrawalSourceType::MERCHANT)) {
            services()->wallet()->takeMerchant($wallet, $amount);
        }

        return $invoice;
    }

    public function deposit(Wallet $wallet, Money $amount, InvoiceWithdrawalSourceType $sourceType): void
    {
        Invoice::create([
            'amount' => $amount,
            'currency' => Currency::USDT(),
            'address' => null,
            'type' => InvoiceType::DEPOSIT,
            'source_type' => $sourceType,
            'status' => InvoiceStatus::SUCCESS,
            'wallet_id' => $wallet->id,
        ]);

        if ($sourceType->equals(InvoiceWithdrawalSourceType::TRUST)) {
            services()->wallet()->giveTrust($wallet, $amount, TransactionType::DEPOSIT_BY_ADMIN);
        } else if ($sourceType->equals(InvoiceWithdrawalSourceType::MERCHANT)) {
            services()->wallet()->giveMerchant($wallet, $amount);
        }
    }

    public function withdraw(Wallet $wallet, Money $amount, InvoiceWithdrawalSourceType $sourceType): void
    {
        Invoice::create([
            'amount' => $amount,
            'currency' => Currency::USDT(),
            'address' => null,
            'type' => InvoiceType::WITHDRAWAL,
            'source_type' => $sourceType,
            'status' => InvoiceStatus::SUCCESS,
            'wallet_id' => $wallet->id,
        ]);

        if ($sourceType->equals(InvoiceWithdrawalSourceType::TRUST)) {
            services()->wallet()->takeTrust($wallet, $amount, TransactionType::WITHDRAWAL_BY_ADMIN);
        } else if ($sourceType->equals(InvoiceWithdrawalSourceType::MERCHANT)) {
            services()->wallet()->takeMerchant($wallet, $amount);
        }
    }
}
