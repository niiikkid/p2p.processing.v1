<?php

namespace App\Http\Resources\API;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var PaymentGateway $this
         */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'currency' => $this->currency->getCode(),
            'buy_price' => services()->market()->getBuyPrice($this->currency)->toPrecision(),
            'sell_price' => services()->market()->getSellPrice($this->currency)->toPrecision(),
            'min_limit' => $this->min_limit,
            'max_limit' => $this->max_limit,
            'commission_rate' => $this->commission_rate,
        ];
    }
}
