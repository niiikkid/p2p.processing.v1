<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PaymentLinkController extends Controller
{
    public function index()
    {
        return Inertia::render('PaymentLink/Index');
    }
}
