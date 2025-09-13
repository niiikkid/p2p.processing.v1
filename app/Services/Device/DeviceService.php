<?php

namespace App\Services\Device;

use App\Contracts\DeviceServiceContract;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Cache;

class DeviceService implements DeviceServiceContract
{
    public function getByToken(string $token): ?UserDevice
    {
        return Cache::remember("device_by_token_{$token}", 600, function () use ($token) {
            return UserDevice::where('token', $token)->first();
        });
    }

    public function connect(UserDevice $device, string $androidId, array $meta): UserDevice
    {
        $device->update([
            'android_id' => $androidId,
            'device_model' => $meta['device_model'] ?? null,
            'android_version' => $meta['android_version'] ?? null,
            'manufacturer' => $meta['manufacturer'] ?? null,
            'brand' => $meta['brand'] ?? null,
            'connected_at' => now(),
        ]);

        Cache::forget("device_by_token_{$device->token}");

        return $device->fresh();
    }
}


