<?php

namespace App\Http\Resources\API;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var Order $this
         */
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'merchant_id' => $this->merchant->uuid,
            'amount' => $this->amount->toBeauty(),
            'profit' => $this->profit->toBeauty(),
            'currency' => $this->currency->getCode(),
            'profit_currency' => $this->profit_currency->getCode(),
            'conversion_price' => $this->conversion_price->toBeauty(),
            'conversion_price_with_commission' => $this->conversion_price_with_commission->toBeauty(),
            'commission_rate' => $this->commission_rate,
            'status' => $this->status->value,
            'callback_url' => $this->callback_url,
            'payment_gateway' => $this->paymentGateway->code,
            'payment_gateway_name' => $this->paymentGateway->name,
            'method' => optional($this->paymentDetail->subPaymentGateway)->code,
            'method_name' => optional($this->paymentDetail->subPaymentGateway)->public_name,
            'payment_detail' => [
                'id' => $this->paymentDetail->id,
                'detail' => $this->paymentDetail->detail,
                'detail_type' => $this->paymentDetail->detail_type,
                'initials' => $this->paymentDetail->initials,
            ],
            'expires_at' => $this->expires_at->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'current_server_time' => now()->toDateTimeString(),
        ];
    }
}
