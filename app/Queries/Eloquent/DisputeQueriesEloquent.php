<?php

namespace App\Queries\Eloquent;

use App\Models\Dispute;
use App\Models\User;
use App\Queries\Interfaces\DisputeQueries;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DisputeQueriesEloquent implements DisputeQueries
{
    public function paginateForAdmin(): LengthAwarePaginator
    {
        return Dispute::query()
            ->with(['order.paymentDetail.user'])
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function paginateForUser(User $user): LengthAwarePaginator
    {
        return Dispute::query()
            ->whereRelation('order.paymentDetail', 'user_id', auth()->user()->id)
            ->with(['order.paymentDetail.user'])
            ->orderByDesc('id')
            ->paginate(10);
    }
}
