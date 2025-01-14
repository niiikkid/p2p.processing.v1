<?php

namespace App\Services\Order\OrderDetails\Classes;

use App\Models\PaymentGateway;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Carbon;

class TraderMarkupRate
{
    public static function calculate(PaymentGateway $paymentGateway, User $trader): float
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
}
