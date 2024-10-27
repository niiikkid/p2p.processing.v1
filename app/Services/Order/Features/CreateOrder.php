<?php

namespace App\Services\Order\Features;

use App\DTO\Order\OrderCreateDTO;
use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use App\Exceptions\OrderException;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use Illuminate\Support\Carbon;

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
        //TODO validate that merchant is not banned, is validated, and active
        /**
         * @var Merchant $merchant
         */
        $merchant = Merchant::where('uuid', $this->dto->merchant_uuid)->first();

        if (! $merchant->validated_at) {
            throw new OrderException('The merchant is under moderation.');
        }
        if ($merchant->banned_at) {
            throw new OrderException('The merchant is banned.');
        }
        if (! $merchant->active) {
            throw new OrderException('The merchant is not active.');
        }


        //TODO добавить возможность создавать множественные ордера с одной суммой для одного и тогоже юзера
        list($paymentGateway, $paymentDetail)
            = $this->getPaymentGatewayAndDetail();

        $expires_at = $this->getExpirationTime($paymentGateway);

        $commission_rate = $this->commissionRateBonus($paymentGateway->commission_rate);

        list($conversion_price, $conversion_price_with_commission)
            = $this->calcConversionPrices($paymentGateway->currency, $commission_rate);

        $profit = $this->calcProfit($conversion_price, $conversion_price_with_commission);

        $amount_usdt = $this->dto->amount->convert($conversion_price_with_commission, Currency::USDT());

        services()->wallet()->take(
            wallet: $paymentDetail->user->wallet,
            amount: $amount_usdt,
            type: TransactionType::PAYMENT_FOR_OPENED_ORDER
        );

        return Order::create([
            'external_id' => $this->dto->external_id,
            'merchant_id' => $merchant->id,
            'amount' => $this->dto->amount,
            'profit' => $profit,
            'currency' => $paymentGateway->currency,
            'profit_currency' => $profit->getCurrency(),
            'conversion_price' => $conversion_price,
            'conversion_price_with_commission' => $conversion_price_with_commission,
            'commission_rate' => $commission_rate,
            'status' => OrderStatus::PENDING,
            'callback_url' => $this->dto->callback_url,
            'payment_gateway_id' => $paymentGateway->id,
            'payment_detail_id' => $paymentDetail->id,
            'currency_id' => $paymentGateway->currency_id,
            'expires_at' => $expires_at,
        ]);
    }

    /**
     * @throws OrderException
     */
    protected function getPaymentGatewayAndDetail(): array
    {
        if ($this->dto->payment_gateway) {
            $paymentGateways = queries()
                ->paymentGateway()
                ->getByCodeForOrderCreate($this->dto->payment_gateway, $this->dto->amount);
        } elseif ($this->dto->currency) {
            $paymentGateways = queries()
                ->paymentGateway()
                ->getByCurrencyForOrderCreate($this->dto->currency, $this->dto->amount);
        } else {
            throw OrderException::make('Currency or Payment Gateway is required');
        }

        if ($paymentGateways->isEmpty()) {
            throw OrderException::make('Payment Gateway is not found');
        }

        $commission_rate = $this->commissionRateBonus($paymentGateways->max('commission_rate'));

        list($conversion_price, $conversion_price_with_commission)
            = $this->calcConversionPrices($this->dto->amount->getCurrency(), $commission_rate);

        $amount_usdt = $this->dto->amount->convert($conversion_price_with_commission, Currency::USDT());

        $paymentDetail = queries()
            ->paymentDetail()
            ->getForOrderCreate(
                amount: $this->dto->amount,
                amount_usdt: $amount_usdt,
                payment_gateway_ids: $paymentGateways->pluck('id')->toArray(),
                payment_detail_type: $this->dto->payment_detail_type
            );

        if (! $paymentDetail) {
            throw OrderException::make('Empty payment detail');
        }

        return [$paymentDetail->paymentGateway, $paymentDetail];
    }

    protected function calcConversionPrices(Currency $currency, float $commission_rate): array
    {
        $conversion_price = services()->market()->getBuyPrice($currency);
        $commission_multiplier = $commission_rate / 100;
        $commission_part = $conversion_price->mul($commission_multiplier);
        $conversion_price_with_commission = $conversion_price->add($commission_part);

        return [$conversion_price, $conversion_price_with_commission];
    }

    protected function getExpirationTime(PaymentGateway $paymentGateway): Carbon
    {
        return now()->addMinutes($paymentGateway->reservation_time);
    }

    protected function calcProfit(Money $conversion_price, Money $conversion_price_with_commission): Money
    {
        return $this->dto->amount
            ->convert($conversion_price, Currency::USDT())
            ->sub(
                $this->dto->amount->convert($conversion_price_with_commission, Currency::USDT())
            );
    }

    //TODO refactoring
    protected function commissionRateBonus(float $commission_rate): float
    {
        $primeTimeBonus = services()->settings()->getPrimeTimeBonus();
        $start = Carbon::createFromTimeString($primeTimeBonus->starts);
        $end = Carbon::createFromTimeString($primeTimeBonus->ends);

        if (now()->between($start, $end)) {
            return round($commission_rate + $primeTimeBonus->rate, 2);
        }

        return $commission_rate;
    }
}
