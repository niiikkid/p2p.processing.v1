<?php

namespace App\Enums;

use App\Traits\Enumable;

enum InvoiceWithdrawalSourceType: string
{
    use Enumable;

    case MERCHANT = 'merchant';
    case TRUST = 'trust';
}
