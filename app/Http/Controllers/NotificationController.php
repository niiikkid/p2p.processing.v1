<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $tgBot = [
            'username' => config('services.telegram.bot'),
            'redirectUrl' => config('services.telegram.redirect'),
            'openTelegramBot' => 'https://t.me/'.config('services.telegram.bot'),
            'user_telegram_id' => auth()->user()->telegram?->telegram_id,
        ];

        return Inertia::render('Notifications/Index', compact('tgBot'));
    }
}
