<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property array $service_commissions
 * @property int $user_id
 * @property array $payment_detail_turnover
 * @property array $exchange_markup_rates
 * @property User $user
 */
class UserMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_commissions',
        'user_id',
        'payment_detail_turnover',
        'exchange_markup_rates',
    ];

    public $timestamps = false;

    protected $casts = [
        'service_commissions' => 'array',
        'payment_detail_turnover' => 'array',
        'exchange_markup_rates' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
