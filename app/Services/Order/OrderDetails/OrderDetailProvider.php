<?php

namespace App\Services\Order\OrderDetails;

use App\Enums\DetailType;
use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\PaymentDetail;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use App\Services\Order\OrderDetails\Classes\ServiceCommissionRate;
use App\Services\Order\OrderDetails\Classes\TraderMarkupRate;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderDetailProvider
{
    protected Collection $details;

    public function __construct(
        protected Merchant $merchant,
        protected Money $amount,
        protected ?Currency $currency = null,
        protected ?PaymentGateway $gateway = null,
        protected ?DetailType $detailType = null,
    )
    {
        $this->details = new Collection();
    }

    /**
     * @throws OrderException
     */
    public function provide()
    {
        //TODO remove
        $time_start = microtime(true);

        $gateways = $this->getGateways();

        $traders = $this->getTraders($gateways);

        $this->details = $this->getDetails($gateways, $traders);

        $gateways = $this->setGatewaysMeta($gateways);

        $this->setDetailsMeta($gateways, $traders);

        $this->filterDetails();

        //TODO remove
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;

        dump($execution_time);
        dump($this->details->count());
        dd($this->details->toArray());

        if ($this->details->isEmpty()) {
            throw OrderException::make('Подходящие платежные реквизиты не найдены.');
        }

        /**
         * @var PaymentDetail $detail
         */
        $detail = $this->details->random();

        return [
            'detail' => PaymentDetail::find($detail->id),
            'gateway' => PaymentGateway::find($detail->payment_gateway_id),
            'trader' => User::find($detail->user_id),
            'base_amount' => $this->amount,
            'amount' => $detail->paymentGateway->meta['amount_with_service_commission'],
            'service_commission_rate_total' => $detail->paymentGateway->meta['service_commission_rate']['total'],
            'service_commission_rate_merchant' => $detail->paymentGateway->meta['service_commission_rate']['merchant'],
            'service_commission_rate_client' => $detail->paymentGateway->meta['service_commission_rate']['client'],
            'profit_amount' => $detail->meta['profit']['total'],
            'service_profit_amount' => $detail->meta['profit']['service'],
            'merchant_profit_amount' => $detail->meta['profit']['merchant'],
            'trader_markup_amount' => $detail->meta['trader_markup'],
            'exchange_price' => $detail->meta['exchange_price']['base'],
            'exchange_price_with_commission' => $detail->meta['exchange_price']['with_commission'],
            'trader_markup_rate' => $detail->meta['trader_markup_rate'],
        ];
    }

    public function filterDetails(): void
    {
        //достаточно средств на траст балансе
        $this->details = $this->details->filter(function (PaymentDetail $detail) {
            $trustBalance = (int)$detail->user->wallet->trust_balance->toUnits();
            $amount = (int)$detail->meta['profit']['total']->toUnits();

            return $trustBalance >= $amount;
        });

        //дневной лимит карты не исчерпан
        $this->details = $this->details->filter(function (PaymentDetail $detail) {
            $limit = (int)$detail->daily_limit->sub($detail->current_daily_limit)->toUnits();
            $amount = (int)$detail->meta['profit']['total']->toUnits();

            return $limit >= $amount;
        });

        //Фильтр по $uniqueBy
        $this->details = $this->details->filter(function (PaymentDetail $detail) {
            $amount = (int)$detail->meta['profit']['total']->toUnits();
            $uniqueBy = $detail->paymentGateway->meta['uniqueBy'];

            if ($uniqueBy === 'card') {
                //то бери любую карту и используй
                return true;
            } else if ($uniqueBy === 'amount') {
                $unique = PaymentDetail::query()
                    ->whereHas('orders', function (Builder $query) use ($amount) {
                        $query->where('amount', $amount);
                        $query->where('status', OrderStatus::PENDING);
                    })
                    ->where('payment_gateway_id', $detail->payment_gateway_id)
                    ->where('user_id', $detail->user_id)
                    ->doesntExist();

                return $unique;
            }

            return false;
        });

        //Фильтры для СБП
        //1 Если метод сбп, то проверить что для под метода нет сделок с такой суммой
        //2 Если метод не сбп, то проверить что у сбп с таким под методом нет сделок с такой суммой
        $this->details = $this->details->filter(function (PaymentDetail $detail) {
            $amount = (int)$detail->meta['profit']['total']->toUnits();

            if ($detail->paymentGateway->is_sbp) {//СБП
                $unique = PaymentDetail::query()
                    ->whereHas('orders', function (Builder $query) use ($amount) {
                        $query->where('amount', $amount);
                        $query->where('status', OrderStatus::PENDING);
                    })
                    ->where('payment_gateway_id', $detail->sub_payment_gateway_id)
                    ->where('user_id', $detail->user_id)
                    ->doesntExist();

                return $unique;
            } else {//не СБП
                $unique = PaymentDetail::query()
                    ->whereHas('orders', function (Builder $query) use ($amount) {
                        $query->where('amount', $amount);
                        $query->where('status', OrderStatus::PENDING);
                    })
                    ->where('sub_payment_gateway_id', $detail->payment_gateway_id)
                    ->where('user_id', $detail->user_id)
                    ->doesntExist();

                return $unique;
            }
        });
    }

    public function setDetailsMeta(Collection $gateways, Collection $traders): void
    {
        $this->details = $this->details->each(function (PaymentDetail $detail) use ($gateways, $traders) {
            $gateway = $gateways->where('id', $detail->payment_gateway_id)->first();
            $detail->setRelation('paymentGateway', $gateway);
            $trader = $traders->where('id', $detail->user_id)->first();
            $detail->setRelation('user', $trader);

            $traderMarkupRate = TraderMarkupRate::calculate($gateway, $detail->user);

            $baseExchangePrice = services()->market()->getBuyPrice($gateway->currency);
            $exchangePriceWithCommission = $baseExchangePrice->add(
                $baseExchangePrice->mul($traderMarkupRate / 100)
            );

            $amount = $gateway->meta['amount_with_service_commission'];
            $serviceCommissionRate = $gateway->meta['service_commission_rate'];

            $profit = $amount->convert($exchangePriceWithCommission, Currency::USDT());
            $serviceProfit = $profit->mul($serviceCommissionRate['total'] / 100);
            $merchantProfit = $profit->sub($serviceProfit);

            $traderMarkup = $amount
                ->convert($baseExchangePrice, Currency::USDT())
                ->sub($profit);

            $detail->meta = [
                'trader_markup_rate' => $traderMarkupRate,
                'exchange_price' => [
                    'base' => $baseExchangePrice,
                    'with_commission' => $exchangePriceWithCommission,
                ],
                'profit' => [
                    'total' => $profit,
                    'service' => $serviceProfit,
                    'merchant' => $merchantProfit,
                ],
                'trader_markup' => $traderMarkup,
            ];

            return $detail;
        });
    }

    protected function setGatewaysMeta(Collection $gateways): Collection
    {
        $gateways->each(function (PaymentGateway $gateway) {
            $commission = ServiceCommissionRate::calculate($this->merchant, $gateway);
            $uniqueBy = $gateway->payment_confirmation_by_card_last_digits ? 'card' : 'amount';

            $amount = $this->amount;
            if ($commission['client'] > 0) {
                $clientCommissionAmount = $this->amount
                    ->mul($commission['client'] / 100);

                $amount = $this->amount->add(
                    intval(round($clientCommissionAmount->toBeauty()))
                );
            }

            $gateway->meta = [
                'amount_with_service_commission' => $amount,
                'service_commission_rate' => $commission,
                'uniqueBy' => $uniqueBy,
            ];

            return $gateway;
        });

        return $gateways;
    }

    protected function getDetails(Collection $gateways, Collection $traders): Collection
    {
        return PaymentDetail::query()
            ->whereDoesntHave('orders', function (Builder $query) use ($gateways, $traders) {
                $query->where('status', OrderStatus::PENDING);
            })
            ->whereIn('user_id', $traders->pluck('id'))
            ->whereIn('payment_gateway_id', $gateways->pluck('id'))
            ->when($this->detailType, function (Builder $query) {
                $query->where('detail_type', $this->detailType);
            })
            ->active()
            ->select([
                'id', 'user_id', 'payment_gateway_id', 'sub_payment_gateway_id', 'daily_limit', 'current_daily_limit', 'currency'
            ])
            ->get();
    }

    protected function getTraders(Collection $gateways): Collection
    {
        return User::query()
            ->with(['meta' => function (HasOne $query) {
                $query->select(['user_id', 'exchange_markup_rates']);
            }])
            ->with(['wallet' => function (HasOne $query) {
                $query->select(['user_id', 'trust_balance', 'currency']);
            }])
            ->whereNull('banned_at')
            ->whereHas('paymentDetails', function ($query) use ($gateways) {
                $query->active();
                $query->whereIn('payment_gateway_id', $gateways->pluck('id')->toArray());
                $query->when($this->detailType, function (Builder $query) {
                    $query->where('detail_type', $this->detailType);
                });
            })
            ->where(function (Builder $query) {
                $query->whereDoesntHave('personalMerchants');
                $query->orWhereRelation('personalMerchants', 'id', $this->merchant->id);
            })
            ->select([
                'id'
            ])
            ->get();
    }

    protected function getGateways(): Collection
    {
        if ($this->gateway) {
            $paymentGateways = queries()
                ->paymentGateway()
                ->getByCodeForOrderCreate($this->gateway, $this->amount);
        } else if ($this->currency) {
            $paymentGateways = queries()
                ->paymentGateway()
                ->getByCurrencyForOrderCreate($this->currency, $this->amount);
        } else {
            throw OrderException::make('Требуется валюта или платежный метод.');
        }

        if ($paymentGateways->isEmpty()) {
            throw OrderException::make('Подходящий платежный метод не найден. Попробуйте изменить метод/валюту или сумму.');
        }

        return $paymentGateways;
    }
}
