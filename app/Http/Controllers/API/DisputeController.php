<?php

namespace App\Http\Controllers\API;

use App\Exceptions\DisputeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Dispute\StoreRequest;
use App\Http\Resources\API\DisputeResource;
use App\Models\Order;
use Illuminate\Http\UploadedFile;

class DisputeController extends Controller
{
    public function show(Order $order)
    {
        //TODO validate that user is owner of order
        return response()->success(
            DisputeResource::make($order->dispute)
        );
    }

    public function store(StoreRequest $request, Order $order)
    {
        //TODO validate that user is owner of order
        try {
            services()->dispute()->create($order, $request->receipt);
        } catch (DisputeException $e) {
            return response()->failWithMessage($e->getMessage());
        }

        return response()->success();
    }
}
