<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Casts\MoneyCast;
use App\Enums\OrderStatus;
use App\Observers\OrderObserver;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $external_id
 * @property Money $amount
 * @property Money $amount_usdt
 * @property Money $profit
 * @property Currency $currency
 * @property Currency $profit_currency
 * @property Money $conversion_price
 * @property Money $conversion_price_with_commission
 * @property float $commission_rate
 * @property OrderStatus $status
 * @property string $status_name
 * @property string $callback_url
 * @property int $payment_gateway_id
 * @property int $payment_detail_id
 * @property int $merchant_id
 * @property PaymentGateway $paymentGateway
 * @property PaymentDetail $paymentDetail
 * @property Merchant $merchant
 * @property SmsLog $smsLog
 * @property Dispute $dispute
 * @property Carbon $finished_at
 * @property Carbon $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'amount',
        'profit',
        'currency',
        'profit_currency',
        'conversion_price',
        'conversion_price_with_commission',
        'commission_rate',
        'status',
        'callback_url',
        'payment_gateway_id',
        'payment_detail_id',
        'merchant_id',
        'expires_at',
        'finished_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'expires_at' => 'datetime',
        'finished_at' => 'datetime',
        'currency' => CurrencyCast::class,
        'profit_currency' => CurrencyCast::class,
        'amount' => MoneyCast::class,
        'profit' => MoneyCast::class,
        'conversion_price' => MoneyCast::class,
        'conversion_price_with_commission' => MoneyCast::class,
    ];

    protected function statusName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => trans("order.status.{$attributes['status']}"),
        );
    }

    protected function amountUsdt(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->amount->convert($this->conversion_price_with_commission, Currency::USDT()),
        );
    }


    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function paymentDetail(): BelongsTo
    {
        return $this->belongsTo(PaymentDetail::class);
    }

    public function smsLog(): HasOne
    {
        return $this->hasOne(SmsLog::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
