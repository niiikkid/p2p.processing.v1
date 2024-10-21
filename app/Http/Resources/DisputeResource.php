<?php

namespace App\Http\Resources;

use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisputeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var Dispute $this
         */
        return [
            'id' => $this->id,
            'receipt' => $this->receipt,
            'receipt_url' => route('disputes.receipt', $this->id),
            'order' => [
                'id' => $this->order->id,
                'amount' => $this->order->amount->toBeauty(),
                'amount_usdt' => $this->order->amount_usdt->toBeauty(),
                'profit' => $this->order->profit->toBeauty(),
                'currency' => $this->order->currency->getCode(),
                'profit_currency' => $this->order->profit_currency->getCode(),
                'status' => $this->order->status,
                'status_name' => $this->order->status_name,
                'created_at' => $this->order->created_at->toDateTimeString(),
            ],
            'payment_detail' => [
                'id' => $this->order->paymentDetail->id,
                'detail' => $this->order->paymentDetail->detail,
                'type' => $this->order->paymentDetail->detail_type->value,
                'name' => $this->order->paymentDetail->name,
            ],
            'user' => [
                'id' => $this->order->paymentDetail->user->id,
                'name' => $this->order->paymentDetail->user->name,
                'email' => $this->order->paymentDetail->user->email,
            ],
            'status' => $this->status->value,
            'reason' => $this->reason,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
