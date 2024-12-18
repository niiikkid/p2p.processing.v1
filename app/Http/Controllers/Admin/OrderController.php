<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        dump(request()->all());
        $orders = queries()->order()->paginateForAdmin();

        $orders = OrderResource::collection($orders);

        $orderStatuses = [];
        foreach (OrderStatus::values() as $status) {
            $orderStatuses[] = [
                'name' => trans("order.status.{$status}"),
                'value' => $status,
            ];
        }

        return Inertia::render('Order/Index', compact('orders', 'orderStatuses'));
    }
}
