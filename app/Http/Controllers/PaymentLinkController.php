<?php

namespace App\Http\Controllers;

use App\Exceptions\DisputeException;
use App\Http\Requests\PaymentLink\Dispute\StoreRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentLinkController extends Controller
{
    public function show(Order $order)
    {
        $data = [
            'order_status' => $order->status->value,
            'uuid' => $order->uuid,
            'name' => $order->merchant->name,
            'amount' => $order->amount->toBeauty(),
            'amount_formated' => number_format($order->amount->toBeauty(), 0, ',', '.'),
            'currency_symbol' => $order->amount->getCurrency()->getSymbol(),
            'payment_link' => services()->settings()->getSupportLink(),
            'detail' => $order->paymentDetail->detail,
            'detail_type' => $order->paymentDetail->detail_type->value,
            'initials' => $order->paymentDetail->initials,
            'sub_payment_gateway' => $order->paymentDetail->subPaymentGateway?->name,
            'success_url' => $order->success_url,
            'fail_url' => $order->fail_url,
            'created_at' => $order->created_at->toDateTimeString(),
            'expires_at' => $order->expires_at->toDateTimeString(),
            'now' => now()->toDateTimeString(),
            'has_dispute' => intval(!! $order->dispute),
            'dispute_status' => $order->dispute?->status->value,
            'dispute_cancel_reason' => $order->dispute?->reason,
        ];

        return Inertia::render('PaymentLink/Index', compact('data'));
    }

    public function storeDispute(StoreRequest $request, Order $order)
    {
        services()->dispute()->create($order, $request->receipt);
    }
}
