<?php

namespace App\Http\Controllers\API\APP;

use App\Contracts\DeviceServiceContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\APP\DeviceConnectRequest;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function connect(DeviceConnectRequest $request)
    {
        /** @var \App\Models\UserDevice $device */
        $device = $request->attributes->get('device');

        if (! $device) {
            return response()->failWithMessage('Device not found');
        }

        $androidId = $request->string('android_id');

        if ($device->connected_at && $device->android_id && $device->android_id !== $androidId) {
            return response()->failWithMessage('Token already connected to another device');
        }

        if ($device->connected_at && $device->android_id === $androidId) {
            return response()->success([
                'name' => $device->name,
                'android_id' => $device->android_id,
                'device_model' => $device->device_model,
                'android_version' => $device->android_version,
                'manufacturer' => $device->manufacturer,
                'brand' => $device->brand,
                'connected_at' => $device->connected_at,
            ]);
        }

        /** @var DeviceServiceContract $devices */
        $devices = app(DeviceServiceContract::class);

        $device = $devices->connect($device, $androidId, $request->only(['device_model', 'android_version', 'manufacturer', 'brand']));

        return response()->success([
            'name' => $device->name,
            'android_id' => $device->android_id,
            'device_model' => $device->device_model,
            'android_version' => $device->android_version,
            'manufacturer' => $device->manufacturer,
            'brand' => $device->brand,
            'connected_at' => $device->connected_at,
        ]);
    }

    public function ping(Request $request)
    {
        /** @var \App\Models\UserDevice $device */
        $device = $request->attributes->get('device');

        if (! $device || ! $device->android_id) {
            return response()->failWithMessage('Device not connected');
        }

        cache()->put("user-apk-latest-ping-at-{$device->user_id}", now());

        return response()->success();
    }
}


