<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $orders = queries()->order()->paginateForMerchant(auth()->user());

        $orders = OrderResource::collection($orders);

        return Inertia::render('Payment/Index', compact('orders'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }
}
