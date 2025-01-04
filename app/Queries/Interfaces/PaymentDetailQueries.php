<?php

namespace App\Queries\Interfaces;

use App\Enums\DetailType;
use App\Models\Merchant;
use App\Models\PaymentDetail;
use App\Models\User;
use App\Services\Money\Money;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentDetailQueries
{
    public function paginateForAdmin(?Carbon $startDate = null, ?Carbon $endDate = null): LengthAwarePaginator;

    public function paginateForUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): LengthAwarePaginator;

    public function getForOrderCreate(Money $amount, Money $amount_usdt, array $payment_gateway_ids, ?DetailType $payment_detail_type = null, ?Merchant $merchant = null): ?PaymentDetail;

    public function getCardConfirmableForOrderCreate(Money $amount, Money $amount_usdt, array $payment_gateway_ids, array $card_payment_gateway_ids, ?Merchant $merchant = null): ?PaymentDetail;
}
