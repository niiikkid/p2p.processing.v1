<?php

namespace App\Http\Resources;

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
            'name' => $this->name_with_currency,
            'original_name' => $this->name,
            'code' => $this->code,
            'detail_types' => $this->detail_types,
            'sub_payment_gateways' => $this->sub_payment_gateways?->pluck('id')?->toArray() ?? [],
            'sub_methods' => $this->sub_payment_gateways?->pluck('code')?->toArray() ?? [],
            'currency' => $this->currency->getCode(),
            'min_limit' => $this->min_limit,
            'max_limit' => $this->max_limit,
            'commission_rate' => $this->commission_rate,
            'is_active' => $this->is_active,
            'reservation_time' => $this->reservation_time,
        ];
    }
}
