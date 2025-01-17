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
use App\Services\Order\OrderDetails\Values\Detail;
use App\Services\Order\OrderDetails\Values\Gateway;
use App\Services\Order\OrderDetails\Values\Trader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class OrderDetailProvider
{
    public function __construct(
        protected Merchant $merchant,
        protected Money $amount,
        protected ?Currency $currency = null,
        protected ?PaymentGateway $gateway = null,
        protected ?DetailType $detailType = null,
    )
    {}

    /**
     * @throws OrderException
     */
    public function provide(): Detail
    {
        DB::connection()->enableQueryLog();
        //TODO remove
        $time_start = microtime(true);

        $gateways = $this->getGateways();

        $traders = $this->getTraders($gateways);

        $details = $this->getDetails($gateways, $traders);

        $details = $this->filterDetails($details);

        //TODO remove
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;

        $queries = DB::getQueryLog();
        dump(count($queries));
        dump($execution_time);
        dump($details->count());
        dd($details->toArray());

        if ($details->isEmpty()) {
            throw OrderException::make('Подходящие платежные реквизиты не найдены.');
        }

        return $details->random();
    }

    /**
     * @param Collection<int, Detail> $details
     * @return Collection<int, Detail>
     */
    public function filterDetails(Collection $details): Collection
    {
        $paymentDetails = PaymentDetail::query()
            ->whereHas('orders', function (Builder $query) {
                $query->where('status', OrderStatus::PENDING);
            })
            ->with(['orders' => function ($query) {
                $query->where('status', OrderStatus::PENDING);
                $query->select('id', 'payment_detail_id', 'amount', 'currency', 'status');
            }])
            ->get(['id', 'payment_gateway_id', 'user_id', 'sub_payment_gateway_id']);

        //достаточно средств на траст балансе
        $details = $details->filter(function (Detail $detail) {
            $trustBalance = (int)$detail->trader->trustBalance->toUnits();
            $amount = (int)$detail->profitTotal->toUnits();

            return $trustBalance >= $amount;
        });

        //дневной лимит карты не исчерпан
        $details = $details->filter(function (Detail $detail) {
            $limit = (int)$detail->dailyLimit->sub($detail->currentDailyLimit)->toUnits();
            $amount = (int)$detail->gateway->amountWithServiceCommission->toUnits();

            return $limit >= $amount;
        });

        //Фильтр по $uniqueBy
        $details = $details->filter(function (Detail $detail) use ($paymentDetails) {
            $amount = (int)$detail->gateway->amountWithServiceCommission->toUnits();
            $uniqueBy = $detail->gateway->uniqueBy;

            if ($uniqueBy === 'card') {
                //то бери любую карту и используй
                return true;
            } else if ($uniqueBy === 'amount') {
                $unique = $paymentDetails
                    ->where('payment_gateway_id', $detail->gateway->id)
                    ->where('user_id', $detail->trader->id)
                    ->pluck('orders')
                    ->collapse()
                    ->filter(function (Order $order) use ($amount) {
                        return intval($order->amount->toUnits()) === $amount;
                    })
                    ->count();

                return $unique;
            }

            return false;
        });

        //Фильтры для СБП
        //1 Если метод сбп, то проверить что для под метода нет сделок с такой суммой
        //2 Если метод не сбп, то проверить что у сбп с таким под методом нет сделок с такой суммой
        $details = $details->filter(function (Detail $detail) use ($paymentDetails) {
            $amount = (int)$detail->gateway->amountWithServiceCommission->toUnits();

            if ($detail->gateway->isSBP) {//СБП
                $unique = !$paymentDetails
                    ->where('orders.amount', $amount)
                    ->where('payment_gateway_id', $detail->subPaymentGatewayID)
                    ->where('user_id', $detail->trader->id)
                    ->count();

                return $unique;
            } else {//не СБП
                $unique = !$paymentDetails
                    ->where('orders.amount', $amount)
                    ->where('sub_payment_gateway_id', $detail->paymentGatewayID)
                    ->where('user_id', $detail->trader->id)
                    ->count();

                return $unique;
            }
        });

        return $details;
    }

    /**
     * @return Collection<int, Gateway>
     * @throws OrderException
     */
    protected function getGateways(): Collection
    {
        if ($this->gateway) {
            $paymentGateways = queries()
                ->paymentGateway()
                ->getByCodeForOrderCreate($this->gateway->code, $this->amount);
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

        $gateways = collect();

        $paymentGateways->each(function (PaymentGateway $gateway) use (&$gateways) {
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

            $gateways->push(
                new Gateway(
                    id: $gateway->id,
                    code: $gateway->code,
                    reservationTime: $gateway->reservation_time,
                    amountWithServiceCommission: $amount,
                    traderMarkupRate: $gateway->commission_rate,
                    serviceCommissionRateTotal: $commission['total'],
                    serviceCommissionRateMerchant: $commission['merchant'],
                    serviceCommissionRateClient: $commission['client'],
                    uniqueBy: $uniqueBy,
                    isSBP: $gateway->is_sbp
                )
            );
        });

        return $gateways;
    }

    /**
     * @param Collection<int, Gateway> $gateways
     * @return Collection<int, Trader>
     */
    protected function getTraders(Collection $gateways): Collection
    {
        $traders = collect();

        User::query()
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
            ->get()
            ->each(function (User $user) use (&$traders) {
                $traders->push(
                    new Trader(
                        id: $user->id,
                        trustBalance: $user->wallet->trust_balance,
                        exchangeMarkupRates: $user->meta->exchange_markup_rates,
                    )
                );
            });

        return $traders;
    }

    /**
     * @param Collection<int, Gateway> $gateways
     * @param Collection<int, Trader> $traders
     * @return Collection<int, Detail>
     */
    protected function getDetails(Collection $gateways, Collection $traders): Collection
    {
        $paymentDetails = PaymentDetail::query()
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

        $details = collect();

        $paymentDetails->each(function (PaymentDetail $detail) use ($gateways, $traders, &$details) {
            /**
             * @var Gateway $gateway
             * @var Trader $trader
             */
            $gateway = $gateways->where('id', $detail->payment_gateway_id)->first();
            $trader = $traders->where('id', $detail->user_id)->first();

            $traderMarkupRate = TraderMarkupRate::calculate($gateway, $trader);

            $baseExchangePrice = services()->market()->getBuyPrice($this->amount->getCurrency());
            $exchangePriceWithMarkup = $baseExchangePrice->add(
                $baseExchangePrice->mul($traderMarkupRate / 100)
            );

            $profit = $gateway->amountWithServiceCommission
                ->convert($exchangePriceWithMarkup, Currency::USDT());
            $serviceProfit = $profit->mul($gateway->serviceCommissionRateTotal / 100);
            $merchantProfit = $profit->sub($serviceProfit);

            $traderMarkup = $gateway->amountWithServiceCommission
                ->convert($baseExchangePrice, Currency::USDT())
                ->sub($profit);

            $details->push(
                new Detail(
                    id: $detail->id,
                    userID: $detail->user_id,
                    paymentGatewayID: $detail->payment_gateway_id,
                    subPaymentGatewayID: $detail->sub_payment_gateway_id,
                    dailyLimit: $detail->daily_limit,
                    currentDailyLimit: $detail->current_daily_limit,
                    currency: $detail->currency,
                    exchangePriceInitial: $baseExchangePrice,
                    exchangePriceWithMarkup: $exchangePriceWithMarkup,
                    profitTotal: $profit,
                    profitServicePart: $serviceProfit,
                    profitMerchantPart: $merchantProfit,
                    traderMarkupRate: $traderMarkupRate,
                    traderMarkup: $traderMarkup,
                    gateway: $gateway,
                    trader: $trader,
                )
            );
        });

        return $details;
    }
}
