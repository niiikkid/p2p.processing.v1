<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MerchantController extends Controller
{
    public function index()
    {
        return Inertia::render('Merchant/Index');
    }
}
