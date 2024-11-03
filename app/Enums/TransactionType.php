<?php

namespace App\Enums;

use App\Traits\Enumable;

enum TransactionType: string
{
    use Enumable;

    //out
    case PAYMENT_FOR_OPENED_ORDER = 'payment_for_opened_order';
    case PAYMENT_FOR_OPENED_DISPUTE = 'payment_for_opened_dispute';
    case WITHDRAWAL_BY_ADMIN = 'withdrawal_by_admin';
    case WITHDRAWAL_BY_USER = 'withdrawal_by_user';

    //in
    case REFUND_FOR_CANCELED_ORDER = 'refund_for_canceled_order';
    case REFUND_FOR_CANCELED_DISPUTE = 'refund_for_canceled_dispute';
    case DEPOSIT_BY_ADMIN = 'deposit_by_admin';
    case DEPOSIT_BY_USER = 'deposit_by_user';

    public function direction(): TransactionDirection
    {
        return match ($this)
        {
            static::PAYMENT_FOR_OPENED_ORDER,
            static::PAYMENT_FOR_OPENED_DISPUTE,
            static::WITHDRAWAL_BY_ADMIN,
            static::WITHDRAWAL_BY_USER => TransactionDirection::OUT,
            static::REFUND_FOR_CANCELED_ORDER,
            static::REFUND_FOR_CANCELED_DISPUTE,
            static::DEPOSIT_BY_ADMIN,
            static::DEPOSIT_BY_USER => TransactionDirection::IN,
        };
    }
}
