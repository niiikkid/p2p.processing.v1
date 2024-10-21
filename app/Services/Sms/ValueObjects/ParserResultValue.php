<?php

namespace App\Services\Sms\ValueObjects;

use App\Services\Money\Money;

class ParserResultValue
{
    public function __construct(
        public Money $amount,
        public ?string $card_type,
        public ?string $card_last_digits,
    )
    {}
}
