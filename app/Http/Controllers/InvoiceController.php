<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreRequest;
use App\Services\Money\Currency;
use App\Services\Money\Money;

class InvoiceController extends Controller
{
    public function store(StoreRequest $request)
    {
        services()->invoice()->createWithdrawal(
            wallet: auth()->user()->wallet,
            amount: Money::fromPrecision($request->amount, Currency::USDT()),
            address: $request->address,
            sourceType: $request->source_type,
        );
    }
}
