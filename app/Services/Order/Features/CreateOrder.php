<?php

namespace App\Services\Order\Features;

use App\DTO\Order\OrderCreateDTO;
use App\Enums\DetailType;
use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use App\Exceptions\OrderException;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\PaymentDetail;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

//TODO добавить возможность создавать множественные ордера с одной суммой для одного и тогоже юзера
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

        /**
         * @var PaymentGateway $paymentGateway
         * @var PaymentDetail $paymentDetail
         */
        list($paymentGateway, $paymentDetail) = $this->getPaymentGatewayAndDetail($merchant);

        //

        $service_commissions = $merchant->user->meta->service_commissions;

        $service_commission_rate_total = $paymentGateway->service_commission_rate;
        $service_commission_rate_merchant = $paymentGateway->service_commission_rate;
        $service_commission_rate_client = 0;

        if (isset($service_commissions[$merchant->id][$paymentGateway->id])) {
            if ($service_commissions[$merchant->id][$paymentGateway->id]['gateway_total_commission'] > 0) {
                $service_commission_rate_total = $service_commissions[$merchant->id][$paymentGateway->id]['gateway_total_commission'];
            }

            $service_commission_rate_merchant = $service_commissions[$merchant->id][$paymentGateway->id]['merchant_commission'];
            $service_commission_rate_client = $service_commission_rate_total - $service_commission_rate_merchant;

            if ($service_commissions[$merchant->id][$paymentGateway->id]['gateway_total_commission'] === 0) {
                $service_commission_rate_total = 0;
                $service_commission_rate_merchant = 0;
                $service_commission_rate_client = 0;
            }
        }

        $client_commission_amount = 0;
        if ($service_commission_rate_client > 0) {
            $client_commission_amount = $this->dto
                ->amount
                ->mul($service_commission_rate_client / 100);

            $client_commission_amount = intval(round($client_commission_amount->toBeauty()));
        }

        $amount = $this->dto->amount->add($client_commission_amount);

        //

        $expires_at = $this->getExpirationTime($paymentGateway);

        $trader_commission_rate = $this->commissionRateBonus($paymentGateway, $paymentDetail->user);

        list($base_conversion_price, $conversion_price)
            = $this->calcConversionPrices($paymentGateway->currency, $trader_commission_rate);

        $profit = $amount->convert($conversion_price, Currency::USDT());
        $trader_profit = $amount
            ->convert($base_conversion_price, Currency::USDT())
            ->sub($profit);

        $service_profit = $profit->mul($service_commission_rate_total / 100);
        $merchant_profit = $profit->sub($service_profit);

        services()->wallet()->takeTrust(
            wallet: $paymentDetail->user->wallet,
            amount: $profit,
            type: TransactionType::PAYMENT_FOR_OPENED_ORDER
        );

        return Order::create([
            'uuid' => (string)Str::uuid(),
            'external_id' => $this->dto->external_id,
            'merchant_id' => $merchant->id,
            'base_amount' => $this->dto->amount,
            'amount' => $amount,
            'profit' => $profit,
            'trader_profit' => $trader_profit,
            'merchant_profit' => $merchant_profit,
            'service_profit' => $service_profit,
            'currency' => $paymentGateway->currency,
            'base_conversion_price' => $base_conversion_price,
            'conversion_price' => $conversion_price,
            'trader_commission_rate' => $trader_commission_rate,
            'service_commission_rate_total' => $service_commission_rate_total,
            'service_commission_rate_merchant' => $service_commission_rate_merchant,
            'service_commission_rate_client' => $service_commission_rate_client,
            'status' => OrderStatus::PENDING,
            'callback_url' => $this->dto->callback_url,
            'success_url' => $this->dto->success_url,
            'fail_url' => $this->dto->fail_url,
            'is_h2h' => $this->dto->h2h,
            'payment_gateway_id' => $paymentGateway->id,
            'payment_detail_id' => $paymentDetail->id,
            'currency_id' => $paymentGateway->currency_id,
            'expires_at' => $expires_at,
        ]);
    }

    /**
     * @throws OrderException
     */
    protected function getPaymentGatewayAndDetail(Merchant $merchant): array
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
            throw OrderException::make('Требуется валюта или платежный метод.');
        }

        if ($paymentGateways->isEmpty()) {
            throw OrderException::make('Подходящий платежный метод не найден. Попробуйте изменить метод/валюту или сумму.');
        }

        $base_conversion_price = services()->market()->getBuyPrice(
            $this->dto->amount->getCurrency()
        );
        $amount_usdt = $this->dto->amount->convert($base_conversion_price, Currency::USDT());

        $lastDigitsProcessableGateways = $paymentGateways
            ->filter(function (PaymentGateway $paymentGateway) {
                return $paymentGateway->payment_confirmation_by_card_last_digits
                    && in_array(DetailType::CARD, $paymentGateway->detail_types);
            });


        //====

        $amount = $this->dto->amount;

        $paymentGateways->each(function (PaymentGateway $paymentGateway) use ($amount, $amount_usdt, $merchant) {
            $paymentDetails = PaymentDetail::query()
                ->whereHas('user.wallet', function (Builder $query) use ($amount_usdt) {
                    $query->where('trust_balance', '>=', (int)$amount_usdt->toUnits());
                })
                ->when($merchant, function (Builder $query) use ($merchant) {
                    $query->where(function (Builder $query) use ($merchant) {
                        $query->whereDoesntHave('user.personalMerchants');
                        $query->orWhereRelation('user.personalMerchants', 'id', $merchant->id);
                    });
                })
                ->active()
                ->where('payment_gateway_id', $paymentGateway->id)
                ->with(['paymentGateway', 'orders' => function (HasMany $query) {
                    $query->where('status', OrderStatus::PENDING);
                }])
                ->get();

            $commission = $this->calcCommission($amount, $merchant, $paymentGateway);

            $uniqueBy = $paymentGateway->payment_confirmation_by_card_last_digits ? 'card' : 'amount';

            $availablePaymentDetails = $paymentDetails->filter(function (PaymentDetail $paymentDetail) {
                return $paymentDetail->orders->count() === 0;
            });

            if ($uniqueBy === 'card') {
                //то бери любую карту и используй
            }

            if ($uniqueBy === 'amount') {
                $result = $paymentDetails->filter(function (PaymentDetail $paymentDetail) use ($commission) {
                    if ($paymentDetail->orders->count() === 0) {
                        return false;
                    }

                    return bccomp(
                        $paymentDetail->orders->first()->amount->toPrecision(),
                        $commission['amount']->toPrecision(),
                    ) === 0;
                });

                if ($result->count() !== 0) {
                    $availablePaymentDetails = null;
                }
            }

            dump($availablePaymentDetails?->toArray());
            dd($paymentDetails->toArray(), $commission);
        });

        dd('stop');

        //====




        if ($this->dto->payment_detail_type?->equals(DetailType::CARD) && $lastDigitsProcessableGateways->isNotEmpty()) {
            $paymentDetail = queries()
                ->paymentDetail()
                ->getCardConfirmableForOrderCreate(
                    amount: $this->dto->amount,
                    amount_usdt: $amount_usdt,
                    payment_gateway_ids: $paymentGateways->pluck('id')->toArray(),
                    card_payment_gateway_ids: $lastDigitsProcessableGateways->pluck('id')->toArray(),
                    merchant: $merchant,
                );
        } else {
            $paymentDetail = queries()
                ->paymentDetail()
                ->getForOrderCreate(
                    amount: $this->dto->amount,
                    amount_usdt: $amount_usdt,
                    payment_gateway_ids: $paymentGateways->pluck('id')->toArray(),
                    payment_detail_type: $this->dto->payment_detail_type,
                    merchant: $merchant,
                );
        }

        if (! $paymentDetail) {
            throw OrderException::make('Подходящие платежные реквизиты не найдены.');
        }

        return [$paymentDetail->paymentGateway, $paymentDetail];
    }

    protected function calcCommission(Money $baseAmount, Merchant $merchant, PaymentGateway $paymentGateway)
    {
        $service_commissions = $merchant->user->meta->service_commissions;

        $service_commission_rate_total = $paymentGateway->service_commission_rate;
        $service_commission_rate_merchant = $paymentGateway->service_commission_rate;
        $service_commission_rate_client = 0;

        if (isset($service_commissions[$merchant->id][$paymentGateway->id])) {
            if ($service_commissions[$merchant->id][$paymentGateway->id]['gateway_total_commission'] > 0) {
                $service_commission_rate_total = $service_commissions[$merchant->id][$paymentGateway->id]['gateway_total_commission'];
            }

            $service_commission_rate_merchant = $service_commissions[$merchant->id][$paymentGateway->id]['merchant_commission'];
            $service_commission_rate_client = $service_commission_rate_total - $service_commission_rate_merchant;

            if ($service_commissions[$merchant->id][$paymentGateway->id]['gateway_total_commission'] === 0) {
                $service_commission_rate_total = 0;
                $service_commission_rate_merchant = 0;
                $service_commission_rate_client = 0;
            }
        }

        $client_commission_amount = 0;
        if ($service_commission_rate_client > 0) {
            $client_commission_amount = $baseAmount
                ->mul($service_commission_rate_client / 100);

            $client_commission_amount = intval(round($client_commission_amount->toBeauty()));
        }

        $amount = $baseAmount->add($client_commission_amount);

        return [
            'base_amount' => $baseAmount,
            'amount' => $amount,
            'service_commission_rate_total' => $service_commission_rate_total,
            'service_commission_rate_merchant' => $service_commission_rate_merchant,
            'service_commission_rate_client' => $service_commission_rate_client,
        ];
    }

    protected function calcConversionPrices(Currency $currency, float $commission_rate): array
    {
        $base_conversion_price = services()->market()->getBuyPrice($currency);
        $commission_multiplier = $commission_rate / 100;
        $commission_part = $base_conversion_price->mul($commission_multiplier);
        $conversion_price = $base_conversion_price->add($commission_part);

        return [$base_conversion_price, $conversion_price];
    }

    protected function getExpirationTime(PaymentGateway $paymentGateway): Carbon
    {
        return now()->addMinutes($paymentGateway->reservation_time);
    }

    //TODO refactoring
    protected function commissionRateBonus(PaymentGateway $paymentGateway, User $trader): float
    {
        $commission_rate = $paymentGateway->commission_rate;

        $personalMarkup = array_filter($trader->meta->exchange_markup_rates, function ($value) use ($paymentGateway) {
            return $value['id'] === $paymentGateway->id;
        });

        $personalMarkup = array_values($personalMarkup);

        if (! empty($personalMarkup[0]['markup_rate'])) {
            $commission_rate = (float)$personalMarkup[0]['markup_rate'];
        }

        $primeTimeBonus = services()->settings()->getPrimeTimeBonus();
        $start = Carbon::createFromTimeString($primeTimeBonus->starts);
        $end = Carbon::createFromTimeString($primeTimeBonus->ends);

        if (now()->between($start, $end)) {
            return round($commission_rate + $primeTimeBonus->rate, 2);
        }

        return $commission_rate;
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
