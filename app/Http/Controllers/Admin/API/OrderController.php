<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreRequest;
use App\Http\Resources\PaymentGatewayResource;
use App\Models\Merchant;
use App\Models\Order;
use App\Services\Money\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $paymentGateways = PaymentGatewayResource::collection(queries()->paymentGateway()->getAllActive())->resolve();

        $currencies = Currency::getAll()->transform(function ($currency) {
            return ['code' => strtoupper($currency->getCode())];
        })->toArray();

        $merchants = Merchant::query()
            ->where('user_id', auth()->user()->id)
            ->orderByDesc('id')
            ->get()
            ->transform(function (Merchant $merchant) {
                $data['id'] = $merchant->id;
                $data['name'] = $merchant->name;

                return $data;
            });

        return Inertia::render('Admin/API/Order/Index', compact('currencies', 'paymentGateways', 'merchants'));
    }

    public function store(StoreRequest $request)
    {
        //TODO
        $data = $request->all();

        $data['nonce'] = now()->getTimestampMs();

        $merchant = Merchant::find($request->merchant);

        $result = Http::asJson()
            ->withoutVerifying()
            ->withHeaders([
                'Idempotency-Key' => Str::random(32),
                'Token' => $merchant->token,
                'Accept' => 'application/json',
            ])
            ->timeout(15)
            ->post(
                url: /*'https://p2p.payment.local:8891'.*/route('api.order', absolute: true), //TODO
                data: $data
            );

        if ($result->failed()) {
            return redirect()->route('admin.api.orders.index')->with(['message' => $result->json()['message']]);
        }

        return redirect()->route('admin.orders.index');
    }
}
