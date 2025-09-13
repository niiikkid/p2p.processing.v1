<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserDeviceResource;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserDeviceController extends Controller
{
    public function index()
    {
        $devices = auth()->user()->devices()->latest()->get();

        $devices = UserDeviceResource::collection($devices)->resolve();

        return Inertia::render('UserDevice/Index', compact('devices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:64'],
        ]);

        UserDevice::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'token' => UserDevice::generateToken(),
        ]);
    }
}


