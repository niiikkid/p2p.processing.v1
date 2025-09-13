<?php

namespace App\Contracts;

use App\Models\UserDevice;

interface DeviceServiceContract
{
    public function getByToken(string $token): ?UserDevice;

    public function connect(UserDevice $device, string $androidId, array $meta): UserDevice;
}


