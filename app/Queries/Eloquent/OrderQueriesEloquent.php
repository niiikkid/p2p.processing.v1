<?php

namespace App\Queries\Eloquent;

use App\Enums\DetailType;
use App\Enums\OrderStatus;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Queries\Interfaces\OrderQueries;
use App\Services\Money\Money;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderQueriesEloquent implements OrderQueries
{
    public function findPendingForSBP(Money $amount, User $user, PaymentGateway $paymentGateway): ?Order
    {
        return Order::where('amount', $amount->toUnits())
            ->whereDoesntHave('dispute')
            ->where('status', OrderStatus::PENDING)
            ->where('currency', $amount->getCurrency()->getCode())
            ->whereRelation('paymentDetail', 'user_id', $user->id)
            ->whereRelation('paymentGateway', 'code', 'sbp_rub')
            ->whereRelation('paymentDetail', 'sub_payment_gateway_id', $paymentGateway->id)
            ->first();
    }

    public function findPending(Money $amount, User $user, PaymentGateway $paymentGateway): ?Order
    {
        return Order::where('amount', $amount->toUnits())
            ->whereDoesntHave('dispute')
            ->where('status', OrderStatus::PENDING)
            ->where('currency', $amount->getCurrency()->getCode())
            ->whereRelation('paymentDetail', 'user_id', $user->id)
            ->where('payment_gateway_id', $paymentGateway->id)
            ->first();
    }

    public function findPendingByDevice(Money $amount, User $user, PaymentGateway $paymentGateway, ?int $userDeviceId): ?Order
    {
        return Order::where('amount', $amount->toUnits())
            ->whereDoesntHave('dispute')
            ->where('status', OrderStatus::PENDING)
            ->where('currency', $amount->getCurrency()->getCode())
            ->whereRelation('paymentDetail', function($q) use ($user, $userDeviceId) {
                $q->where('user_id', $user->id)
                  ->when($userDeviceId, fn($qq) => $qq->where('user_device_id', $userDeviceId));
            })
            ->where('payment_gateway_id', $paymentGateway->id)
            ->first();
    }

    public function findPendingWithPattern(Money $amount, User $user, PaymentGateway $paymentGateway, string $pattern): ?Order
    {
        return Order::where('amount', $amount->toUnits())
            ->whereDoesntHave('dispute')
            ->where('status', OrderStatus::PENDING)
            ->where('currency', $amount->getCurrency()->getCode())
            ->whereRelation('paymentDetail', function($query) use ($user, $pattern) {
                $query->where('user_id', $user->id)
                      ->where('sms_detail_value', $pattern);
            })
            ->where('payment_gateway_id', $paymentGateway->id)
            ->first();
    }

    /**
     * @return Collection<int, Dispute>
     */
    public function findPendingMultipleCardConfirmation(Money $amount, User $user, PaymentGateway $paymentGateway, string $cardLastDigits): Collection
    {
        return Order::where('amount', $amount->toUnits())
            ->whereDoesntHave('dispute')
            ->where('status', OrderStatus::PENDING)
            ->where('currency', $amount->getCurrency()->getCode())
            ->whereRelation('paymentDetail', 'user_id', $user->id)
            ->whereRelation('paymentDetail', 'detail', 'like', "%$cardLastDigits")
            ->whereRelation('paymentDetail', 'detail_type', DetailType::CARD)
            ->where('payment_gateway_id', $paymentGateway->id)
            ->get();
    }

    public function paginateForAdmin(array $statuses = [], ?Carbon $startDate = null, ?Carbon $endDate = null, ?string $externalID = null, ?string $uuid = null, ?array $merchantID = null): LengthAwarePaginator
    {
        return Order::query()
            ->with(['paymentDetail.subPaymentGateway', 'paymentGateway', 'smsLog', 'merchant', 'dispute'])
            ->when(! empty($statuses), function ($query) use ($statuses) {
                $query->whereIn('status', $statuses);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($externalID, function ($query) use ($externalID) {
                $query->where('external_id', 'LIKE', '%' . $externalID . '%');
            })
            ->when($uuid, function ($query) use ($uuid) {
                $query->where('uuid', 'LIKE', '%' . $uuid . '%');
            })
            ->when(!empty($merchantID), function ($query) use ($merchantID) {
                $query->whereRelation('merchant', function ($query) use ($merchantID) {
                    $query->whereIn('id', $merchantID);
                });
            })
            ->orderByDesc('id')
            ->paginate(20);
    }

    public function paginateForUser(User $user, array $statuses = [], ?Carbon $startDate = null, ?Carbon $endDate = null, ?string $uuid = null): LengthAwarePaginator
    {
        return Order::query()
            ->whereRelation('paymentDetail', 'user_id', $user->id)
            ->with(['paymentDetail.subPaymentGateway', 'paymentGateway', 'smsLog', 'merchant', 'dispute'])
            ->when(! empty($statuses), function ($query) use ($statuses) {
                $query->whereIn('status', $statuses);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($uuid, function ($query) use ($uuid) {
                $query->where('uuid', 'LIKE', '%' . $uuid . '%');
            })
            ->orderByDesc('id')
            ->paginate(20);
    }

    public function paginateForMerchant(User $user, ?string $uuid = null): LengthAwarePaginator
    {
        return Order::query()
            ->with(['paymentDetail.subPaymentGateway', 'paymentGateway', 'smsLog', 'merchant', 'dispute'])
            ->whereRelation('merchant', 'user_id', $user->id)
            ->when($uuid, function ($query) use ($uuid) {
                $query->where('uuid', 'LIKE', '%' . $uuid . '%');
            })
            ->orderByDesc('id')
            ->paginate(20);
    }


    /**
     * @return Collection<int, Dispute>
     */
    public function getForAdminApiDisputeCreate(): Collection
    {
        return Order::query()
            ->where('status', OrderStatus::FAIL)
            ->whereDoesntHave('dispute')
            ->whereDate('created_at', '>=', now()->subDay())
            ->orderByDesc('id')
            ->get(['id', 'amount', 'currency']);
    }
}
