<?php

namespace App\Services\Order\Features;

use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use App\Exceptions\OrderException;
use App\Models\Order;

class RollbackOrder extends BaseFeature
{
    public function __construct(
        protected Order $order,
        protected TransactionType $transactionType,
    )
    {}

    /**
     * @throws OrderException
     */
    public function handle(): bool
    {
        if ($this->order->status->notEquals(OrderStatus::PENDING)) {
            if ($this->order->status->equals(OrderStatus::FAIL)) {
                services()->wallet()->take(
                    wallet: $this->order->paymentDetail->user->wallet,
                    amount: $this->order->profit,
                    type: $this->transactionType
                );
            }
            $this->order->update([
                'status' => OrderStatus::PENDING,
                'finished_at' => null
            ]);
        } else {
            throw OrderException::make('Cant rollback order');
        }

        return true;
    }
}
