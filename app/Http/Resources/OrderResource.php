<?php

namespace App\Http\Resources;

use App\Enums\DisputeStatus;
use App\Models\Order;
use App\Services\Money\Currency;
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
            'amount' => $this->amount->toBeauty(),
            'amount_usdt' => $this->profit->toBeauty(),
            'profit' => $this->profit->toBeauty(),
            'currency' => $this->currency->getCode(),
            'profit_currency' => Currency::USDT()->getCode(),
            'conversion_price' => $this->base_conversion_price->toBeauty(),
            'conversion_price_with_commission' => $this->conversion_price->toBeauty(),
            'commission_rate' => $this->commission_rate,
            'status' => $this->status->value,
            'status_name' => $this->status_name,
            'callback_url' => $this->callback_url,
            'payment_gateway_code' => $this->paymentGateway->code,
            'sub_payment_gateway_code' => optional($this->paymentDetail->subPaymentGateway)->code,
            'payment_gateway_name' => $this->paymentGateway->name_with_currency,
            'sub_payment_gateway_name' => optional($this->paymentDetail->subPaymentGateway)->name,
            'payment_detail' => $this->paymentDetail->detail,
            'payment_detail_type' => $this->paymentDetail->detail_type->value,
            'payment_detail_name' => $this->paymentDetail->name,
            'user' => UserResource::make($this->paymentDetail->user),
            'sms_log' => $this->smsLog ? [
                    'sender' => $this->smsLog->sender,
                    'message' => $this->smsLog->message,
                    'created_at' => $this->smsLog->created_at->toDateTimeString(),
                ] : null,
            'merchant' => [ //TODO hide if not presented
                'id' => $this->merchant->id,
                'name' => $this->merchant->name,
            ],
            'has_dispute' => (bool)$this->dispute,
            'expires_at' => $this->expires_at->toDateTimeString(),
            'finished_at' => $this->finished_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
