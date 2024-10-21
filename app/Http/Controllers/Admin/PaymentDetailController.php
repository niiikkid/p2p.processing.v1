<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentDetailResource;
use Inertia\Inertia;

class PaymentDetailController extends Controller
{
    public function index()
    {
        $paymentDetails = queries()->paymentDetail()->paginateForAdmin();

        $paymentDetails = PaymentDetailResource::collection($paymentDetails);

        return Inertia::render('PaymentDetail/Index', compact('paymentDetails'));
    }
}
