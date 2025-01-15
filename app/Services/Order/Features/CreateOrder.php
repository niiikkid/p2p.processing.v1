<?php

namespace App\Services\Order\Features;

use App\DTO\Order\OrderCreateDTO;
use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use App\Exceptions\OrderException;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Services\Order\OrderDetails\OrderDetailProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CreateOrder extends BaseFeature
{
    public function __construct(
        protected OrderCreateDTO $dto
    )
    {}

    /**
     * @throws OrderException
     */
    public function handle(): Order
    {
        $merchant = queries()->merchant()->findByUUID($this->dto->merchant_uuid);

        $this->validateMerchant($merchant);

        $orderDetails = (new OrderDetailProvider(
            merchant: $merchant,
            amount: $this->dto->amount,
            currency: $this->dto->currency,
            gateway: $this->dto->payment_gateway,
            detailType: $this->dto->payment_detail_type,
        ))->provide();
dd($orderDetails);
        $expires_at = $this->getExpirationTime($orderDetails['gateway']);

        services()->wallet()->takeTrust(
            wallet: $orderDetails['trader']->wallet,
            amount: $orderDetails['profit_amount'],
            type: TransactionType::PAYMENT_FOR_OPENED_ORDER
        );

        return Order::create([
            'uuid' => (string)Str::uuid(),
            'external_id' => $this->dto->external_id,
            'merchant_id' => $merchant->id,
            'base_amount' => $this->dto->amount,
            'amount' => $orderDetails['amount'],
            'profit' => $orderDetails['profit_amount'],
            'merchant_profit' => $orderDetails['merchant_profit_amount'],
            'service_profit' => $orderDetails['service_profit_amount'],
            'trader_profit' => $orderDetails['trader_markup_amount'],
            'currency' => $orderDetails['gateway']->currency,
            'base_conversion_price' => $orderDetails['exchange_price'],
            'conversion_price' => $orderDetails['exchange_price_with_commission'],
            'trader_commission_rate' => $orderDetails['trader_markup_rate'],
            'service_commission_rate_total' => $orderDetails['service_commission_rate_total'],
            'service_commission_rate_merchant' => $orderDetails['service_commission_rate_merchant'],
            'service_commission_rate_client' => $orderDetails['service_commission_rate_client'],
            'status' => OrderStatus::PENDING,
            'callback_url' => $this->dto->callback_url,
            'success_url' => $this->dto->success_url,
            'fail_url' => $this->dto->fail_url,
            'is_h2h' => $this->dto->h2h,
            'payment_gateway_id' => $orderDetails['gateway']->id,
            'payment_detail_id' => $orderDetails['detail']->id,
            'expires_at' => $expires_at,
        ]);
    }

    protected function getExpirationTime(PaymentGateway $paymentGateway): Carbon
    {
        return now()->addMinutes($paymentGateway->reservation_time);
    }

    protected function validateMerchant(Merchant $merchant): void
    {
        if (!$merchant->validated_at) {
            throw new OrderException('Мерчант находится на модерации.');
        }
        if ($merchant->banned_at) {
            throw new OrderException('Мерчант заблокирован.');
        }
        if (!$merchant->active) {
            throw new OrderException('Мерчант отключен.');
        }
    }
}
