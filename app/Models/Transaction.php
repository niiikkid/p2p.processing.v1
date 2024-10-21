<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Casts\MoneyCast;
use App\Enums\TransactionDirection;
use App\Enums\TransactionType;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Money $amount
 * @property Currency $currency
 * @property TransactionDirection $direction
 * @property TransactionType $type
 * @property int $wallet_id
 * @property Wallet $wallet
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'currency',
        'direction',
        'type',
        'wallet_id',
    ];

    protected $casts = [
        'amount' => MoneyCast::class,
        'currency' => CurrencyCast::class,
        'direction' => TransactionDirection::class,
        'type' => TransactionType::class,
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
