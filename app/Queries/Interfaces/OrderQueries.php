<?php

namespace App\Queries\Interfaces;

use App\Models\Dispute;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Services\Money\Money;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderQueries
{
    public function findPendingForSBP(Money $amount, User $user, PaymentGateway $paymentGateway): ?Order;

    public function findPending(Money $amount, User $user, PaymentGateway $paymentGateway): ?Order;

    /**
     * @return Collection<int, Dispute>
     */
    public function findPendingMultiple(Money $amount, User $user, PaymentGateway $paymentGateway): Collection;

    public function paginateForAdmin(array $statuses = [], ?Carbon $startDate = null, ?Carbon $endDate = null, ?string $externalID = null, ?string $uuid = null): LengthAwarePaginator;

    public function paginateForUser(User $user, array $statuses = [], ?Carbon $startDate = null, ?Carbon $endDate = null, ?string $uuid = null): LengthAwarePaginator;

    public function paginateForMerchant(User $user, ?string $uuid = null): LengthAwarePaginator;

    /**
     * @return Collection<int, Dispute>
     */
    public function getForAdminApiDisputeCreate(): Collection;
}
