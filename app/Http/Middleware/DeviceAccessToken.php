<?php

namespace App\Http\Middleware;

use App\Contracts\DeviceServiceContract;
use App\Models\UserDevice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DeviceAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Access-Token');

        if (! $token) {
            return response()->failWithMessage('Missing Access Token.');
        }

        /** @var DeviceServiceContract $devices */
        $devices = app(DeviceServiceContract::class);
        $device = $devices->getByToken($token);

        if (! $device) {
            return response()->failWithMessage('Неверный токен доступа.');
        }

        // Пробросим устройство и залогиним владельца
        $request->attributes->set('device', $device);
        Auth::login($device->user);

        return $next($request);
    }
}


