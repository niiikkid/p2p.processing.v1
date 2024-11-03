<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceWithdrawalSourceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\Wallet\DepositRequest;
use App\Http\Requests\Admin\User\Wallet\WithdrawRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WalletResource;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use Inertia\Inertia;

class UserWalletController extends Controller
{
    public function index(User $user)
    {
        $wallet = $user->wallet;
        $invoices = Invoice::query()
            ->where('wallet_id', $wallet->id)
            ->orderByDesc('id')
            ->paginate(10);
        $transactions = Transaction::query()
            ->where('wallet_id', $wallet->id)
            ->orderByDesc('id')
            ->paginate(10);

        $total_trust_withdrawable_amount = intval($wallet->trust_balance->add($wallet->reserve_balance)->toBeauty());
        $total_merchant_withdrawable_amount = intval($wallet->merchant_balance->toBeauty());

        $trust_locked_for_withdrawal = Invoice::query()
            ->where('wallet_id', $wallet->id)
            ->where('status', InvoiceStatus::PENDING)
            ->where('source_type', InvoiceWithdrawalSourceType::TRUST)
            ->sum('amount');
        $trust_locked_for_withdrawal = Money::fromUnits($trust_locked_for_withdrawal, Currency::USDT())->toBeauty();

        $merchant_locked_for_withdrawal = Invoice::query()
            ->where('wallet_id', $wallet->id)
            ->where('status', InvoiceStatus::PENDING)
            ->where('source_type', InvoiceWithdrawalSourceType::MERCHANT)
            ->sum('amount');
        $merchant_locked_for_withdrawal = Money::fromUnits($merchant_locked_for_withdrawal, Currency::USDT())->toBeauty();

        $wallet = WalletResource::make($wallet)->resolve();
        $invoices = InvoiceResource::collection($invoices);
        $transactions = TransactionResource::collection($transactions);
        $user = UserResource::make($user)->resolve();

        $reserve_balance = services()->wallet()->getMaxReserveBalance();

        return Inertia::render('Wallet/Index', compact('wallet', 'reserve_balance', 'invoices', 'transactions', 'user', 'total_trust_withdrawable_amount', 'total_merchant_withdrawable_amount', 'trust_locked_for_withdrawal', 'merchant_locked_for_withdrawal'));
    }

    public function deposit(DepositRequest $request, User $user)
    {
        services()->invoice()->deposit(
            wallet: $user->wallet,
            amount: Money::fromPrecision($request->amount, Currency::USDT()),
            sourceType: $request->source_type
        );
    }

    public function withdraw(WithdrawRequest $request, User $user)
    {
        services()->invoice()->withdraw(
            wallet: $user->wallet,
            amount: Money::fromPrecision($request->amount, Currency::USDT()),
            sourceType: $request->source_type
        );
    }
}
