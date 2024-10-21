<?php

namespace App\Services\Order\Features;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Services\Money\Money;
use Illuminate\Support\Facades\DB;

class SucceedOrder extends BaseFeature
{
    public function __construct(
        protected Order $order,
    )
    {}

    /**
     * @throws OrderException
     */
    public function handle(): bool
    {
        if ($this->order->status->equals(OrderStatus::PENDING)) {
            DB::transaction(function () {
                $this->order->update([
                    'status' => OrderStatus::SUCCESS,
                    'finished_at' => now()
                ]);

                $current_daily_limit = $this->calcCurrentDailyLimit();

                $this->order->paymentDetail->update([
                    'current_daily_limit' => $current_daily_limit
                ]);
            });
        } else {
            throw OrderException::make('Cant succeed not pending order');
        }

        return true;
    }

    protected function calcCurrentDailyLimit(): Money
    {
        return $this->order->paymentDetail->current_daily_limit->add($this->order->amount);
    }
}
