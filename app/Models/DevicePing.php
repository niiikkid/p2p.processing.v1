<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicePing extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_device_id',
        'pinged_at',
    ];

    protected $casts = [
        'pinged_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(UserDevice::class, 'user_device_id');
    }
}


