<?php

namespace App\Queries\Eloquent;

use App\Models\Merchant;
use App\Queries\Interfaces\MerchantQueries;
use Illuminate\Support\Collection;

class MerchantQueriesEloquent implements MerchantQueries
{
    public function findByUUID(string $uuid): ?Merchant
    {
        return Merchant::where('uuid', $uuid)->first();
    }

    public function getForFilter(): Collection
    {
        return Merchant::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(function ($merchant) {
                return [
                    'name' => $merchant->name,
                    'value' => (string) $merchant->id,
                ];
            });
    }
}
