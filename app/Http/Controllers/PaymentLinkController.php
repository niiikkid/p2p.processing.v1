<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;

class PaymentLinkController extends Controller
{
    public function show(Order $order)
    {
        $data = [
            'uuid' => $order->uuid,
            'name' => $order->merchant->name,
            'amount' => $order->amount->toBeauty(),
            'amount_formated' => number_format($order->amount->toBeauty(), 0, ',', '.'),
            'currency_symbol' => $order->amount->getCurrency()->getSymbol(),
            'payment_link' => services()->settings()->getSupportLink(),
        ];

        return Inertia::render('PaymentLink/Index', compact('data'));
    }
}
