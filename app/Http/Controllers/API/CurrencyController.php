<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Money\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::getAll()
            ->transform(function ($currency) {
                return [
                    'currency' => $currency->getCode(),
                    //'buy_price' => services()->market()->getBuyPrice($currency)->toPrecision(),
                    //'sell_price' => services()->market()->getSellPrice($currency)->toPrecision(),
                ];
            })->pluck('currency');

        return response()->success($currencies);
    }
}
