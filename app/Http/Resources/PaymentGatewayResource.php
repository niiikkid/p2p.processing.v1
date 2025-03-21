<?php

namespace App\Http\Resources;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'logo' => $this->logo ? Storage::disk('public')->url($this->logo) : null,
            'code' => $this->code,
            'detail_types' => $this->detail_types,
            'sub_payment_gateways' => $this->sub_payment_gateways?->pluck('id')?->toArray() ?? [],
            'sub_methods' => $this->sub_payment_gateways?->pluck('code')?->toArray() ?? [],
            'currency' => $this->currency->getCode(),
            'min_limit' => $this->min_limit,
            'max_limit' => $this->max_limit,
            'sms_senders' => $this->sms_senders,
            'commission_rate' => $this->commission_rate,
            'service_commission_rate' => $this->service_commission_rate,
            'is_active' => $this->is_active,
            'payment_confirmation_by_card_last_digits' => $this->payment_confirmation_by_card_last_digits,
            'make_order_amount_unique' => $this->make_order_amount_unique,
            'sms_parsers_count' => $this->whenHas('sms_parsers_count', $this->sms_parsers_count),
            'reservation_time' => $this->reservation_time,
        ];
    }
}
