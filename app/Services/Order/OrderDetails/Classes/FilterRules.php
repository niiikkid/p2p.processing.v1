<?php

namespace App\Services\Order\OrderDetails\Classes;

use App\Models\Order;
use App\Services\Order\OrderDetails\Values\Detail;
use Illuminate\Database\Eloquent\Collection;

class FilterRules
{
    public function uniqueByAmount(Collection $paymentDetails, Detail $detail, int $amount): bool
    {
        $unique = !$paymentDetails
            ->where('payment_gateway_id', $detail->gateway->id)
            ->where('user_id', $detail->trader->id)
            ->pluck('orders')
            ->collapse()
            ->filter(function (Order $order) use ($amount) {
                return intval($order->amount->toUnits()) === $amount;
            })
            ->count();

        return $unique;
    }

    public function uniqueByAmountForSBP(Collection $paymentDetails, Detail $detail, int $amount): bool
    {
        $unique = !$paymentDetails
            ->where('payment_gateway_id', $detail->subPaymentGatewayID)
            ->where('user_id', $detail->trader->id)
            ->pluck('orders')
            ->collapse()
            ->filter(function (Order $order) use ($amount) {
                return intval($order->amount->toUnits()) === $amount;
            })
            ->count();

        return $unique;
    }

    public function uniqueByAmountSubGateway(Collection $paymentDetails, Detail $detail, int $amount): bool
    {
        $unique = !$paymentDetails
            ->where('sub_payment_gateway_id', $detail->paymentGatewayID)
            ->where('user_id', $detail->trader->id)
            ->pluck('orders')
            ->collapse()
            ->filter(function (Order $order) use ($amount) {
                return intval($order->amount->toUnits()) === $amount;
            })
            ->count();

        return $unique;
    }
}
