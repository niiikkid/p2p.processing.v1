<?php

namespace App\Http\Controllers\API\Merchant;

use App\Contracts\OrderServiceContract;
use App\DTO\Order\OrderCreateDTO;
use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Merchant\Order\StoreRequest;
use App\Http\Resources\API\Merchant\OrderResource;
use App\Models\Merchant;
use App\Models\Order;
use App\Services\Money\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function show(Order $order): JsonResponse
    {
        if ($order->is_h2h) {
            return response()->failWithMessage('Сделка предназначена для H2H API.');
        }

        Gate::authorize('access-to-order', $order);

        return response()->success(
            OrderResource::make($order)
        );
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $merchant = Merchant::where('uuid', $request->merchant_id)->first();

        Gate::authorize('access-to-merchant', $merchant);

        try {
            $parts = explode('-', $request->external_id);
            if (count($parts) === 2) {
                $external_id_trimmed = $parts[1];

                if (! empty($request->payment_gateway)) {
                    $paymentGateway = queries()->paymentGateway()->getByCode($request->payment_gateway);

                    $amount = Money::fromPrecision($request->amount, $paymentGateway->currency);
                } else if (! empty($request->currency)) {
                    $amount = Money::fromPrecision($request->amount, $request->currency);
                }

                if (isset($amount)) {

                    $order = Order::query()
                        ->whereDoesntHave('dispute')
                        ->whereRelation('merchant', 'uuid', $request->merchant_id)
                        ->when($request->payment_detail_type, function (Builder $query) use ($request) {
                            $query->whereRelation('paymentDetail', 'detail_type', $request->payment_detail_type);
                        })
                        ->when($request->currency, function (Builder $query) use ($request) {
                            $query->where('currency', $request->currency);
                        })
                        ->when($request->payment_gateway, function (Builder $query) use ($request) {
                            $query->whereRelation('paymentGateway', 'code', $request->payment_gateway);
                        })
                        ->where('external_id', 'like', "%-{$external_id_trimmed}")
                        ->where('amount', $amount->toUnits())
                        ->where('status', OrderStatus::PENDING)
                        ->first();

                    if ($order) {
                        return response()->success(
                            OrderResource::make($order)
                        );
                    } else {
                        $orders = Order::query()
                            ->whereDoesntHave('dispute')
                            ->whereRelation('merchant', 'uuid', $request->merchant_id)
                            ->where('external_id', 'like', "%-{$external_id_trimmed}")
                            ->where('status', OrderStatus::PENDING)
                            ->get();

                        if ($orders->count() === 1) {
                            $order = $orders->first();

                            services()->order()->fail($order, TransactionType::REFUND_FOR_CANCELED_ORDER);
                        }
                    }
                }
            }

            $order = cache()->lock('creating-order', 5)
                ->block(7, function () use ($request) {
                    return make(OrderServiceContract::class)->create(
                        OrderCreateDTO::make($request->validated())
                    );
                });

            return response()->success(
                OrderResource::make($order)
            );
        } catch (OrderException $e) {
            return response()->failWithMessage($e->getMessage());
        }
    }
}
