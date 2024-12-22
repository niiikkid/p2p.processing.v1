<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentDetailResource;
use Carbon\Carbon;
use Inertia\Inertia;

class PaymentDetailController extends Controller
{
    public function index()
    {
        $startDate = request()->input('filters.start_date');
        if ($startDate) {
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate);
        }

        $endDate = request()->input('filters.end_date');
        if ($endDate) {
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate);
        }

        if ($endDate?->lessThan($startDate)) {
            $endDate = null;
        }

        $currentFilters = [
            'startDate' => $startDate?->format('d/m/Y'),
            'endDate' => $endDate?->format('d/m/Y'),
        ];

        $paymentDetails = queries()->paymentDetail()->paginateForAdmin($startDate, $endDate);

        $paymentDetails = PaymentDetailResource::collection($paymentDetails);

        return Inertia::render('PaymentDetail/Index', compact('paymentDetails', 'currentFilters'));
    }
}
