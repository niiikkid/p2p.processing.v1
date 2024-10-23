<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $project_link
 * @property string $token
 * @property boolean $active
 * @property int $user_id
 * @property User $user
 * @property Collection<int, Order> $orders
 * @property Carbon $validated_at
 * @property Carbon $banned_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'project_link',
        'token',
        'user_id',
        'active',
        'validated_at',
        'banned_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'banned_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
