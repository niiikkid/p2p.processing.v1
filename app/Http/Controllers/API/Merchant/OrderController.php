<?php

namespace App\Http\Controllers\API\Merchant;

use App\Contracts\OrderServiceContract;
use App\DTO\Order\OrderCreateDTO;
use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Merchant\Order\StoreRequest;
use App\Http\Resources\API\Merchant\OrderResource;
use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http as HttpClient;

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
            $order = cache()->lock('creating-order', 3)
                ->block(5, function () use ($request) {
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
