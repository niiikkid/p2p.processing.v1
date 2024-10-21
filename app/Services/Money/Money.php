<?php

namespace App\Services\Money;

use App\Services\Money\Utils\FormatMoney;

class Money
{
    public function __construct(
        private readonly string   $amount, //in units
        private readonly Currency $currency
    )
    {}

    public static function fromPrecision(string $amount, string $currency): static
    {
        //TODO amount validation

        $currency = new Currency($currency);

        $amount = FormatMoney::precisionToUnits($amount, $currency->getPrecision());

        return new static($amount, $currency);
    }

    public static function fromUnits(string $amount, string $currency): static
    {
        //TODO amount validation

        $currency = new Currency($currency);

        return new static($amount, $currency);
    }

    //100.50
    public function toPrecision(): string
    {
        return FormatMoney::unitsToPrecision($this->amount, $this->currency->getPrecision());
    }

    //10050
    public function toUnits(): string
    {
        return $this->amount;
    }

    //100.5
    public function toBeauty(): string
    {
        return FormatMoney::beautifyPrecision($this->toPrecision());
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function add(string | Money $amount): self
    {
        if ($amount instanceof Money) {
            $amount = $amount->toPrecision();
        }

        return self::fromPrecision(
            amount: bcadd($this->toPrecision(), $amount, 50),
            currency: $this->currency
        );
    }

    public function sub(string | Money $amount): self
    {
        if ($amount instanceof Money) {
            $amount = $amount->toPrecision();
        }

        return self::fromPrecision(
            amount: bcsub($this->toPrecision(), $amount, 50),
            currency: $this->currency
        );
    }

    public function mul(string | Money $amount): self
    {
        if ($amount instanceof Money) {
            $amount = $amount->toPrecision();
        }

        return self::fromPrecision(
            amount: bcmul($this->toPrecision(), $amount, 50),
            currency: $this->currency
        );
    }

    public function div(string | Money $amount): self
    {
        if ($amount instanceof Money) {
            $amount = $amount->toPrecision();
        }

        return self::fromPrecision(
            amount: bcdiv($this->toPrecision(), $amount, 50),
            currency: $this->currency
        );
    }

    public function convert(Money $conversion_amount, Currency $currency): Money
    {
        if ($this->getCurrency()->getCode() !== $conversion_amount->getCurrency()->getCode()) {
            throw new \Exception('Currencies must be equal');
        }

        return Money::fromUnits(
            amount: $this->div($conversion_amount),
            currency: $currency,
        );
    }

    public function __toString(): string
    {
        return $this->toUnits();
    }
}
