<?php

namespace App\Http\Resources;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var Wallet $this
         */
        return [
            'id' => $this->id,
            'trust_balance' => $this->trust_balance->toBeauty(),
            'reserve_balance' => $this->reserve_balance->toBeauty(),
            'currency' => $this->currency->getCode(),
            'user_id' => $this->user_id,
        ];
    }
}
