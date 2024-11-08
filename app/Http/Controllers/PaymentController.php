<?php

namespace App\Http\Controllers;

use App\Exceptions\OrderException;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentGatewayResource;
use App\Models\Merchant;
use App\Services\Money\Currency;
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

        $currencies = Currency::getAll()->transform(function ($currency) {
            return [
                'code' => strtoupper($currency->getCode()),
                'name' => strtoupper($currency->getCode()) . ' - ' . $currency->getName(),
            ];
        })->toArray();

        $merchants = Merchant::query()
            ->where('user_id', auth()->user()->id)
            ->whereNotNull('validated_at')
            ->whereNull('banned_at')
            ->where('active', true)
            ->orderByDesc('id')
            ->get()
            ->transform(function (Merchant $merchant) {
                $data['id'] = $merchant->uuid;
                $data['name'] = $merchant->name;

                return $data;
            });

        return Inertia::render('Payment/Add', compact('paymentGateways', 'currencies', 'merchants'));
    }

    public function store(Request $request)
    {
        //
    }
}
