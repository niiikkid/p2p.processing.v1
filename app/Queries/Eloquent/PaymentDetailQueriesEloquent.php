<?php

namespace App\Queries\Eloquent;

use App\Casts\BaseCurrencyMoneyCast;
use App\Casts\MoneyCast;
use App\Enums\DetailType;
use App\Enums\OrderStatus;
use App\Models\Merchant;
use App\Models\PaymentDetail;
use App\Models\User;
use App\Queries\Interfaces\PaymentDetailQueries;
use App\Services\Money\Money;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PaymentDetailQueriesEloquent implements PaymentDetailQueries
{
    public function paginateForAdmin(?Carbon $startDate = null, ?Carbon $endDate = null): LengthAwarePaginator
    {
        return PaymentDetail::query()
            ->with(['paymentGateway', 'user'])
            ->withSum(['orders as primary_turnover_amount' => function (Builder $query)  use ($startDate, $endDate) {
                $query->where('status', OrderStatus::SUCCESS);
                $query->when($startDate, function ($query) use ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                });
                $query->when($endDate, function ($query) use ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                });
            }], 'profit')
            ->withSum(['orders as secondary_turnover_amount' => function (Builder $query) use ($startDate, $endDate) {
                $query->where('status', OrderStatus::SUCCESS);
                $query->when($startDate, function ($query) use ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                });
                $query->when($endDate, function ($query) use ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                });
            }], 'amount')
            ->orderByDesc('id')
            ->withCasts([
                'primary_turnover_amount' => BaseCurrencyMoneyCast::class,
                'secondary_turnover_amount' => BaseCurrencyMoneyCast::class
            ])
            ->paginate(10);
    }

    public function paginateForUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): LengthAwarePaginator
    {
        return PaymentDetail::query()
            ->where('user_id', $user->id)
            ->with(['paymentGateway'])
            ->withSum(['orders as primary_turnover_amount' => function (Builder $query)  use ($startDate, $endDate) {
                $query->where('status', OrderStatus::SUCCESS);
                $query->when($startDate, function ($query) use ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                });
                $query->when($endDate, function ($query) use ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                });
            }], 'profit')
            ->withSum(['orders as secondary_turnover_amount' => function (Builder $query) use ($startDate, $endDate) {
                $query->where('status', OrderStatus::SUCCESS);
                $query->when($startDate, function ($query) use ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                });
                $query->when($endDate, function ($query) use ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                });
            }], 'amount')
            ->orderByDesc('id')
            ->withCasts([
                'primary_turnover_amount' => BaseCurrencyMoneyCast::class,
                'secondary_turnover_amount' => BaseCurrencyMoneyCast::class
            ])
            ->paginate(10);
    }

    public function getForOrderCreate(Money $amount, Money $amount_usdt, array $payment_gateway_ids, ?DetailType $payment_detail_type = null, ?Merchant $merchant = null): ?PaymentDetail
    {
        $users_ids = PaymentDetail::whereHas('orders', function (Builder $query) use ($amount) {
            $query->where('status', OrderStatus::PENDING);
            $query->where('amount', $amount->toUnits());
        })
            ->select('user_id')
            ->pluck('user_id')
            ->toArray();

        $users_ids = array_unique($users_ids);

        return PaymentDetail::query()
            ->whereDoesntHave('orders', function (Builder $query) {
                $query->where('status', OrderStatus::PENDING);
            })
            ->whereHas('user.wallet', function (Builder $query) use ($amount_usdt) {
                $query->where('trust_balance', '>=', (int)$amount_usdt->toUnits());
            })
            ->when($payment_detail_type, function (Builder $query) use ($payment_detail_type) {
                $query->where('detail_type', $payment_detail_type);
            })
            ->when($merchant, function (Builder $query) use ($merchant) {
                $query->where(function (Builder $query) use ($merchant) {
                    $query->whereDoesntHave('user.personalMerchants');
                    $query->orWhereRelation('user.personalMerchants', 'id', $merchant->id);
                });
            })
            ->whereIn('payment_gateway_id', $payment_gateway_ids)
            ->active()
            ->whereRaw("daily_limit - current_daily_limit >= {$amount->toUnits()}")
            ->whereNotIn('user_id', $users_ids)
            ->inRandomOrder()
            ->first();
    }

    public function getCardConfirmableForOrderCreate(Money $amount, Money $amount_usdt, array $payment_gateway_ids, array $card_payment_gateway_ids, ?Merchant $merchant = null): ?PaymentDetail
    {
        $query = PaymentDetail::query()
            ->whereDoesntHave('orders', function (Builder $query) {
                $query->where('status', OrderStatus::PENDING);
            })
            ->whereHas('user.wallet', function (Builder $query) use ($amount_usdt) {
                $query->where('trust_balance', '>=', (int)$amount_usdt->toUnits());
            })
            ->when($merchant, function (Builder $query) use ($merchant) {
                $query->where(function (Builder $query) use ($merchant) {
                    $query->whereDoesntHave('user.personalMerchants');
                    $query->orWhereRelation('user.personalMerchants', 'id', $merchant->id);
                });
            })
            ->where('detail_type', DetailType::CARD)
            ->active()
            ->whereRaw("daily_limit - current_daily_limit >= {$amount->toUnits()}");

        //по номеру карты
        $users_ids = PaymentDetail::whereHas('orders', function (Builder $query) use ($amount) {
            $query->where('status', OrderStatus::PENDING);
            $query->where('amount', $amount->toUnits());
        })
            ->whereRelation('paymentGateway', 'payment_confirmation_by_card_last_digits', false)
            ->select('user_id')
            ->pluck('user_id')
            ->toArray();
        $users_ids = array_unique($users_ids);

        $cardDetails = $query->clone()
            ->whereNotIn('user_id', $users_ids)
            ->whereIn('payment_gateway_id', $card_payment_gateway_ids)
            ->get();

        //остальные карты
        $users_ids = PaymentDetail::whereHas('orders', function (Builder $query) use ($amount) {
            $query->where('status', OrderStatus::PENDING);
            $query->where('amount', $amount->toUnits());
        })
            ->select('user_id')
            ->pluck('user_id')
            ->toArray();
        $users_ids = array_unique($users_ids);

        $simpleDetails = $query->clone()
            ->whereNotIn('user_id', $users_ids)
            ->whereIn('payment_gateway_id', $payment_gateway_ids)
            ->whereNotIn('id', $cardDetails->pluck('id')->toArray())
            ->get();

        $details = $simpleDetails->merge($cardDetails);
        $details = $details->unique('id');

        if ($details->isEmpty()) {
            return null;
        }

        return $details->random();
    }
}
