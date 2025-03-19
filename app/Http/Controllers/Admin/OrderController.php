<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $statuses = request()->input('filters.statuses', '');
        $statuses = explode(',', $statuses);

        foreach ($statuses as $key => $value) {
            if (! OrderStatus::tryFrom($value)) {
                unset($statuses[$key]);
            }
        }

        $selectedMerchants = request()->input('filters.merchants', '');
        $selectedMerchants = explode(',', $selectedMerchants);
        $selectedMerchants = array_filter($selectedMerchants);

        $startDate = request()->input('filters.start_date');
        if ($startDate) {
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate);
        }

        $endDate = request()->input('filters.end_date');
        if ($endDate) {
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate);
        }

        if ($startDate && $endDate?->lessThan($startDate)) {
            $endDate = null;
        }

        $externalID = request()->input('filters.external_id');
        $uuid = request()->input('filters.uuid');

        $orders = queries()->order()->paginateForAdmin($statuses, $startDate, $endDate, $externalID, $uuid, $selectedMerchants);

        $orders = OrderResource::collection($orders);

        $orderStatuses = [];
        foreach (OrderStatus::values() as $status) {
            $orderStatuses[] = [
                'name' => trans("order.status.{$status}"),
                'value' => $status,
            ];
        }

        $merchants = queries()->merchant()->getForFilter();

        $currentFilters = [
            'statuses' => $statuses,
            'merchants' => $selectedMerchants,
            'startDate' => $startDate?->format('d/m/Y'),
            'endDate' => $endDate?->format('d/m/Y'),
            'externalID' => $externalID,
            'uuid' => $uuid,
        ];

        return Inertia::render('Order/Index', compact('orders', 'orderStatuses', 'currentFilters', 'merchants'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
        ]);

        services()->order()->updateAmount(
            order: $order,
            finalAmount: Money::fromPrecision($request->input('amount'), $order->currency),
        );
    }
}
