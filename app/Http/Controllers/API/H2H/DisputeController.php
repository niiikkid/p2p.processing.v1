<?php

namespace App\Http\Controllers\API\H2H;

use App\Exceptions\DisputeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\H2H\Dispute\StoreRequest;
use App\Http\Resources\API\H2H\DisputeResource;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;

class DisputeController extends Controller
{
    public function show(Order $order)
    {
        Gate::authorize('access-to-order', $order);

        return response()->success(
            DisputeResource::make($order->dispute)
        );
    }

    public function store(StoreRequest $request, Order $order)
    {
        Gate::authorize('access-to-order', $order);

        try {
            services()->dispute()->create($order, $request->receipt);
        } catch (DisputeException $e) {
            return response()->failWithMessage($e->getMessage());
        }

        return response()->success();
    }
}
