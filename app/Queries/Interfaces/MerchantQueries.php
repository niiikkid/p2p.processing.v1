<?php

namespace App\Queries\Interfaces;

use App\Models\Merchant;
use Illuminate\Support\Collection;

interface MerchantQueries
{
    public function findByUUID(string $uuid): ?Merchant;
    public function getForFilter(): Collection;
}
