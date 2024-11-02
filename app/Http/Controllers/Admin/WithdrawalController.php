<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceType;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Inertia\Inertia;

class WithdrawalController extends Controller
{
    public function index()
    {
        $invoices = Invoice::query()
            ->where('type', InvoiceType::WITHDRAWAL)
            ->orderByDesc('id')
            ->paginate(10);

        $invoices = InvoiceResource::collection($invoices);

        return Inertia::render('Withdrawal/Index', compact('invoices'));
    }

    public function success(Invoice $invoice)
    {

    }

    public function fail(Invoice $invoice)
    {

    }
}
