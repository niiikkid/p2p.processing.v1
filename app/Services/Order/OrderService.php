<?php

namespace App\Services\Order;

use App\Contracts\OrderServiceContract;
use App\DTO\Order\OrderCreateDTO;
use App\Enums\TransactionType;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Services\Order\Features\CreateOrder;
use App\Services\Order\Features\FailOrder;
use App\Services\Order\Features\RollbackOrder;
use App\Services\Order\Features\SucceedOrder;

class OrderService implements OrderServiceContract
{
    /**
     * @throws OrderException
     */
    public function create(OrderCreateDTO $dto): Order
    {
        return (new CreateOrder($dto))->handle();
    }

    /**
     * @throws OrderException
     */
    public function succeed(Order $order): bool
    {
        return (new SucceedOrder($order))->handle();
    }

    /**
     * @throws OrderException
     */
    public function fail(Order $order, TransactionType $transactionType): bool
    {
        return (new FailOrder($order, $transactionType))->handle();
    }

    /**
     * @throws OrderException
     */
    public function rollback(Order $order, TransactionType $transactionType): bool
    {
        return (new RollbackOrder($order, $transactionType))->handle();
    }
}
