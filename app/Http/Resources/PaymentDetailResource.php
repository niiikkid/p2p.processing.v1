<?php

namespace App\Http\Resources;

use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var PaymentDetail $this
         */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'detail' => $this->detail,
            'detail_type' => $this->detail_type->value,
            'initials' => $this->initials,
            'is_active' => $this->is_active,
            'daily_limit' => $this->daily_limit->toBeauty(),
            'current_daily_limit' => $this->current_daily_limit->toBeauty(),
            'currency' => $this->currency->getCode(),
            'payment_gateway_id' => $this->paymentGateway->id,
            'sub_payment_gateway_id' => $this->subPaymentGateway?->id,
            'payment_gateway_code' => $this->paymentGateway->code,
            'payment_gateway_name' => $this->paymentGateway->name_with_currency,
            'owner_email' => $this->user->email,
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
