<?php

namespace App\Http\Controllers\API\APP;

use App\Contracts\SmsServiceContract;
use App\DTO\SMS\SmsDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\SMS\StoreRequest;
use App\Jobs\HandleSmsJob;
use App\Models\User;

class SmsController extends Controller
{
    public function store(StoreRequest $request)
    {
        /** @var \App\Models\UserDevice $device */
        $device = $request->attributes->get('device');
        $user = $device?->user;

        HandleSmsJob::dispatch(
            SmsDTO::fromArray($request->validated() + [
                    'user' => $user,
                    'user_device_id' => $device?->id,
                ])
        );

        return response()->success();
    }
}
