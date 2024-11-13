<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $payment_gateway_id
 * @property int $format
 * @property int $regex
 * @property PaymentGateway $paymentGateway
 */
class SmsParser extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_gateway_id',
        'format',
        'regex',
    ];

    public $timestamps = false;

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }
}
