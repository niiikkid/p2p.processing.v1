<?php

namespace App\DTO\Order;

use App\DTO\BaseDTO;
use App\Enums\DetailType;
use App\Models\PaymentGateway;
use App\Services\Money\Currency;
use App\Services\Money\Money;

class OrderCreateDTO extends BaseDTO
{
    public function __construct(
        public string $external_id,
        public Money $amount,
        public string $callback_url,
        public string $merchant_uuid,
        public ?string $payment_gateway = null,
        public ?Currency $currency = null,
        public ?DetailType $payment_detail_type = null,
    )
    {}

    public static function make(array $data): static
    {
        if (! empty($data['payment_gateway'])) {
            $paymentGateway = queries()->paymentGateway()->getByCode($data['payment_gateway']);

            $data['amount'] = Money::fromPrecision($data['amount'], $paymentGateway->currency);
        } else if (! empty($data['currency'])) {
            $data['currency'] = new Currency($data['currency']);
            $data['amount'] = Money::fromPrecision($data['amount'], $data['currency']);
        }

        $data['payment_detail_type'] = ! empty($data['payment_detail_type']) ? DetailType::from($data['payment_detail_type']) : null;
        $data['merchant_uuid'] = $data['merchant_id'];

        return make(static::class, $data);
    }
}
