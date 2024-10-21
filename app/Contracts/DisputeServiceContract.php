<?php

namespace App\Contracts;

use App\Exceptions\DisputeException;
use App\Models\Dispute;
use App\Models\Order;
use Illuminate\Http\UploadedFile;

interface DisputeServiceContract
{
    /**
     * @throws DisputeException
     */
    public function create(Order $order, UploadedFile $receipt): Dispute;

    public function accept(Dispute $dispute): bool;

    public function cancel(Dispute $dispute, string $reason): bool;

    public function rollback(Dispute $dispute): bool;
}
