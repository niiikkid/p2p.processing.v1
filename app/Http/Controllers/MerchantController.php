<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\Merchant\StoreRequest;
use App\Http\Resources\MerchantResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentGatewayResource;
use App\Models\Merchant;
use App\Models\Order;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::query()
            ->withSum(['orders' => function ($query) {
                $query->where('status', OrderStatus::SUCCESS);
                $query->whereDate('created_at', now()->today());
            }], 'profit')
            ->where('user_id', auth()->user()->id)
            ->orderByDesc('id')
            ->paginate(9);

        $merchants->transform(function (Merchant $merchant) {
            $merchant->orders_sum_profit = $merchant->orders_sum_profit ?? 0;
            return $merchant;
        });

        $merchants = MerchantResource::collection($merchants);

        return Inertia::render('Merchant/Index', compact('merchants'));
    }

    public function show(Merchant $merchant)
    {
        //TODO check ownership
        $orders = Order::query()
            ->where('merchant_id', $merchant->id)
            ->where('status', OrderStatus::SUCCESS)
            ->orderByDesc('id')
            ->paginate(10);

        $paymentGateways = queries()->paymentGateway()->getAllActive();

        $commissionSettings = $merchant->user->meta->service_commissions;

        if (empty($commissionSettings[$merchant->id])) {
            $commissionSettings = [];

            $paymentGateways->each(function ($paymentGateway) use (&$commissionSettings) {
                $commissionSettings[$paymentGateway->id] = $paymentGateway->service_commission_rate;
            });
        } else {
            $commissionSettings = $commissionSettings[$merchant->id];
        }

        $orders = OrderResource::collection($orders);
        $paymentGateways = PaymentGatewayResource::collection($paymentGateways);

        $merchant = MerchantResource::make($merchant)->resolve();

        $today = Order::query()
                ->where('status', OrderStatus::SUCCESS)
                ->whereDate('created_at', now()->today());

        $yesterday = Order::query()
                ->where('status', OrderStatus::SUCCESS)
                ->whereDate('created_at', now()->yesterday());

        $month = Order::query()
                ->where('status', OrderStatus::SUCCESS)
                ->whereDate('created_at', '>', now()->startOfMonth());

        $total = Order::query()
                ->where('status', OrderStatus::SUCCESS);

        $statistics = [
            'today_profit' => Money::fromUnits($today->sum('profit') ?? 0, Currency::USDT())->toBeauty(),
            'yesterday_profit' => Money::fromUnits($yesterday->sum('profit') ?? 0, Currency::USDT())->toBeauty(),
            'month_profit' => Money::fromUnits($month->sum('profit') ?? 0, Currency::USDT())->toBeauty(),
            'total_profit' => Money::fromUnits($total->sum('profit') ?? 0, Currency::USDT())->toBeauty(),
            'today_orders_count' => $today->count('id'),
            'yesterday_orders_count' => $yesterday->count('id'),
            'month_orders_count' => $month->count('id'),
            'total_orders_count' => $total->count('id'),
            'currency' => Currency::USDT()->getCode(),
        ];

        return Inertia::render('Merchant/Show', compact('merchant', 'orders', 'paymentGateways', 'commissionSettings', 'statistics'));
    }

    public function create()
    {
        return Inertia::render('Merchant/Add');
    }

    public function store(StoreRequest $request)
    {
        $merchant = Merchant::create([
            'uuid' => (string)Str::uuid(),
            'user_id' => auth()->id(),
            'active' => true,
            'name' => $request->name,
            'description' => $request->description,
            'domain' => parse_url($request->project_link)['host'],
        ]);

        return redirect()->route('merchants.show', $merchant->id);
    }

    public function updateCallbackURL(Request $request, Merchant $merchant)
    {
        //TODO check ownership
        $request->validate(['callback_url' => 'required', 'string', 'url', 'max:256']);

        $merchant->update([
            'callback_url' => $request->callback_url
        ]);
    }

    public function updateCommissionSettings(Request $request, Merchant $merchant)
    {
        //TODO check ownership
        $request->validate([
            'commission_settings' => 'required', 'array',
        ]);

        $commissionSettings = $merchant->user->meta->service_commissions;

        if (empty($commissionSettings[$merchant->id])) {
            $commissionSettings = [];
        }

        $commissionSettings[$merchant->id] = $request->commission_settings;

        $merchant->user->meta->update([
            'service_commissions' => $commissionSettings
        ]);
    }
}
