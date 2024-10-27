<?php

namespace App\Http\Controllers\API;

use App\Contracts\OrderServiceContract;
use App\DTO\Order\OrderCreateDTO;
use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreRequest;
use App\Http\Resources\API\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function show(Order $order): JsonResponse
    {
        //TODO validate that user owner of order
        return response()->success(
            OrderResource::make($order)
        );
    }

    public function store(StoreRequest $request): JsonResponse
    {
        //TODO validate that user owner of merchant
        //TODO validate that merchant is not banned, is validated, and active
        try {
            $order = make(OrderServiceContract::class)->create(
                OrderCreateDTO::make($request->validated())
            );

            return response()->success(
                OrderResource::make($order)
            );
        } catch (OrderException $e) {
            return response()->failWithMessage($e->getMessage());
        }
    }

    public function cancel(Order $order): JsonResponse
    {
        //TODO validate that user owner of order
        if ($order->status->notEquals(OrderStatus::PENDING)) {
            return response()->failWithMessage('It is not possible to cancel a completed order.');
        }

        try {
            services()->order()->fail($order, TransactionType::REFUND_FOR_CANCELED_ORDER);

            return response()->success(
                OrderResource::make($order)
            );
        } catch (OrderException $e) {
            return response()->failWithMessage($e->getMessage());
        }
    }
}
