<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentGatewayResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $orders = queries()->order()->paginateForMerchant(auth()->user());

        $orders = OrderResource::collection($orders);

        return Inertia::render('Payment/Index', compact('orders'));
    }

    public function create()
    {
        $paymentGateways = PaymentGatewayResource::collection(queries()->paymentGateway()->getAllActive())->resolve();

        return Inertia::render('Payment/Add', compact('paymentGateways'));
    }

    public function store(Request $request)
    {
        //
    }
}
