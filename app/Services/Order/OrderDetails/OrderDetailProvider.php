<?php

namespace App\Services\Order\OrderDetails;

use App\Enums\DetailType;
use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Merchant;
use App\Models\PaymentDetail;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use App\Services\Order\OrderDetails\Classes\ServiceCommissionRate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        $gateways = $this->getGateways();

        $traders = $this->getTraders($gateways);

        $this->details = $this->getDetails($gateways, $traders);

        $gateways = $this->setGatewaysMeta($gateways);

        dd($this->details->toArray());
    }

    public function setGatewaysMeta(Collection $gateways): Collection
    {
        $gateways->each(function (PaymentGateway $gateway) {
            $commission = ServiceCommissionRate::calculate($this->merchant, $gateway, $this->merchant->user->meta);
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
                'final_amount' => $amount,
                'commission' => $commission,
                'uniqueBy' => $uniqueBy,
            ];

            return $gateway;
        });

        return $gateways;
    }

    public function getDetails(Collection $gateways, Collection $traders): Collection
    {
        return PaymentDetail::query()
            ->whereIn('user_id', $traders->pluck('id'))
            ->active()
            ->whereIn('payment_gateway_id', $gateways->pluck('id'))
            ->when($this->detailType, function (Builder $query) {
                $query->where('detail_type', $this->detailType);
            })
            ->select([
                'id', 'user_id', 'payment_gateway_id'
            ])
            ->get();
    }

    public function getTraders(Collection $gateways): Collection
    {
        return User::query()
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

    public function getGateways(): Collection
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
