<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceWithdrawalSourceType;
use App\Exceptions\InvoiceException;
use App\Http\Requests\Invoice\StoreRequest;
use App\Services\Money\Currency;
use App\Services\Money\Money;

class InvoiceController extends Controller
{
    public function store(StoreRequest $request)
    {
        try {
            services()->invoice()->createWithdrawal(
                wallet: auth()->user()->wallet,
                amount: Money::fromPrecision($request->amount, Currency::USDT()),
                address: $request->address,
                sourceType: InvoiceWithdrawalSourceType::from($request->source_type),
            );
        } catch (InvoiceException $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }
}
