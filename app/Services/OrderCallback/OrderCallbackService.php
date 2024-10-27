<?php

namespace App\Services\OrderCallback;

use App\Contracts\OrderCallbackServiceContract;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderCallbackService implements OrderCallbackServiceContract
{
    public function send(Order $order): void
    {
        $callback_url = $order->callback_url ?? $order->merchant->callback_url;

        if (! $callback_url) {
            return;
        }

        $data = [
            'id' => $order->id,
            'external_id' => $order->external_id,
            'merchant_id' => $order->merchant->uuid,
            'amount' => $order->amount->toBeauty(),
            'profit' => $order->profit->toBeauty(),
            'currency' => $order->currency->getCode(),
            'profit_currency' => $order->profit_currency->getCode(),
            'conversion_price' => $order->conversion_price->toBeauty(),
            'conversion_price_with_commission' => $order->conversion_price_with_commission->toBeauty(),
            'commission_rate' => $order->commission_rate,
            'status' => $order->status->value,
            'payment_gateway' => $order->paymentGateway->code,
            'payment_detail_id' => $order->paymentDetail->id,
            'payment_detail' => $order->paymentDetail->detail,
            'payment_detail_type' => $order->paymentDetail->detail_type->value,
            'finished_at' => $order->finished_at?->getTimestamp(),
            'expires_at' => $order->expires_at->getTimestamp(),
            'created_at' => $order->created_at->getTimestamp(),
            'current_server_time' => now()->getTimestamp(),
        ];

        $token = $order->merchant->user->api_access_token;

        Http::withoutVerifying()
            ->withHeader('Access-Token', $token)
            ->post(
                url: $callback_url,
                data: $data
            );
    }
}
