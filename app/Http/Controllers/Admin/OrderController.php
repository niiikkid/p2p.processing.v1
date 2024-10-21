<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = queries()->order()->paginateForAdmin();

        $orders = OrderResource::collection($orders);

        return Inertia::render('Order/Index', compact('orders'));
    }
}
