<?php

namespace App\Http\Resources;

use App\Models\PaymentDetail;
use App\Services\Money\Currency;
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
            'primary_currency' => Currency::USDT()->getCode(),
            'payment_gateway_id' => $this->payment_gateway_id,
            'sub_payment_gateway_id' => $this->sub_payment_gateway_id,
            'created_at' => $this->created_at->toDateString(),
            $this->mergeWhen($this->resource->relationLoaded('user'), function () {
                return [
                    'owner_email' => $this->user->email,
                ];
            }),
            $this->mergeWhen($this->resource->relationLoaded('paymentGateway'), function () {
                return [
                    'payment_gateway_code' => $this->paymentGateway->code,
                    'payment_gateway_name' => $this->paymentGateway->name_with_currency,
                    'payment_gateway_logo' => $this->paymentGateway->logo ? asset('storage/payment-gateways/'.$this->paymentGateway->logo) : null,
                ];
            }),
            'primary_turnover_amount' => $this->when($this->primary_turnover_amount, $this->primary_turnover_amount?->toBeauty()),
            'secondary_turnover_amount' => $this->when($this->secondary_turnover_amount, $this->secondary_turnover_amount?->toBeauty()),
        ];
    }
}
