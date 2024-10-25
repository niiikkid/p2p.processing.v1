<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\Merchant\StoreRequest;
use App\Http\Resources\MerchantResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentGatewayResource;
use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::query()
            ->where('user_id', auth()->user()->id)
            ->orderByDesc('id')
            ->paginate(10);

        $merchants = MerchantResource::collection($merchants);

        return Inertia::render('Merchant/Index', compact('merchants'));
    }

    public function show(Merchant $merchant)
    {
        $orders = Order::query()
            ->where('merchant_id', $merchant->id)
            ->where('status', OrderStatus::SUCCESS)
            ->orderByDesc('id')
            ->paginate(10);

        $paymentGateways = queries()->paymentGateway()->getAllActive();

        $commissionSettings = auth()->user()->meta->service_commissions;

        $paymentGateways->each(function ($paymentGateway) use (&$commissionSettings) {
            $commissionSettings[$paymentGateway->id] = $paymentGateway->service_commission_rate;
        });

        $orders = OrderResource::collection($orders);
        $paymentGateways = PaymentGatewayResource::collection($paymentGateways);

        return Inertia::render('Merchant/Show', compact('merchant', 'orders', 'paymentGateways', 'commissionSettings'));
    }

    public function create()
    {
        return Inertia::render('Merchant/Add');
    }

    public function store(StoreRequest $request)
    {
        $merchant = Merchant::create([
            'token' => Str::lower(Str::random(32)),
            'user_id' => auth()->id(),
            'active' => true,
            'name' => $request->name,
            'description' => $request->description,
            'domain' => parse_url($request->project_link)['host'],
        ]);

        return redirect()->route('merchants.show', $merchant->id);
    }
}
