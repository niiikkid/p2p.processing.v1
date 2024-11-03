<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\InvoiceWithdrawalSourceType;
use App\Enums\TransactionType;
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
        if ($invoice->type->notEquals(InvoiceType::WITHDRAWAL) || $invoice->status->notEquals(InvoiceStatus::PENDING)) {
            return;
        }

        $invoice->update(['status' => InvoiceStatus::SUCCESS]);

        if ($invoice->source_type->equals(InvoiceWithdrawalSourceType::TRUST)) {
            services()->wallet()->takeTrust($invoice->wallet, $invoice->amount, TransactionType::WITHDRAWAL_BY_USER);
        } else if ($invoice->source_type->equals(InvoiceWithdrawalSourceType::MERCHANT)) {
            services()->wallet()->takeMerchant($invoice->wallet, $invoice->amount);
        }
    }

    public function fail(Invoice $invoice)
    {
        if ($invoice->type->notEquals(InvoiceType::WITHDRAWAL) || $invoice->status->notEquals(InvoiceStatus::PENDING)) {
            return;
        }

        $invoice->update(['status' => InvoiceStatus::FAIL]);
    }
}
