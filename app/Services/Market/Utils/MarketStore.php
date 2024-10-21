<?php

namespace App\Services\Market\Utils;

class MarketStore
{
    protected static function cacheKey(string $currency): string
    {
        return 'conversion-price-for-' . $currency;
    }

    public static function putPrice(string $currency, string $buy_price, string $sell_price): void
    {
        cache()->put(self::cacheKey($currency), [
            'buy_price' => $buy_price,
            'sell_price' => $sell_price,
        ]);
    }

    public static function getBuyPrice(string $currency): string
    {
        return cache()->get(self::cacheKey($currency))['buy_price'];
    }

    public static function getSellPrice(string $currency): string
    {
        return cache()->get(self::cacheKey($currency))['sell_price'];
    }

    public static function putPaymentMethodsList(array $paymentMethods): void
    {
        cache()
            ->put(
                key: 'currencies.price-parsers.methods-list',
                value: $paymentMethods,
                ttl: 60 * 60 * 24
            );
    }

    public static function getPaymentMethodsList(): array
    {
        return cache()->get('currencies.price-parsers.methods-list');
    }
}
