<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Casts\MoneyCast;
use App\Services\Money\Currency;
use App\Services\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property Money $trast_balance
 * @property Money $reserve_balance
 * @property Currency $currency
 * @property int $user_id
 * @property User $user
 * @property Collection<int, Invoice> $invoices
 * @property Collection<int, Transaction> $transactions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'trast_balance',
        'reserve_balance',
        'currency',
        'user_id',
    ];

    protected $casts = [
        'trast_balance' => MoneyCast::class,
        'reserve_balance' => MoneyCast::class,
        'currency' => CurrencyCast::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
