<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SmsLogResource;
use App\Models\SmsLog;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;

class SmsLogController extends Controller
{
    public function index()
    {
        $startDate = request()->input('filters.start_date');
        if ($startDate) {
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate);
        }

        $endDate = request()->input('filters.end_date');
        if ($endDate) {
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate);
        }

        if ($startDate && $endDate?->lessThan($startDate)) {
            $endDate = null;
        }

        $user = request()->input('filters.user');

        $currentFilters = [
            'user' => $user,
            'startDate' => $startDate?->format('d/m/Y'),
            'endDate' => $endDate?->format('d/m/Y'),
        ];

        $users = User::query()
            ->whereHas('smsLogs')
            ->get()
            ->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                ];
            });

        $sms_logs = SmsLog::query()
            ->with('user')
            ->when($user, function ($query) use ($user) {
                $query->where('user_id', $user);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->orderByDesc('id')
            ->paginate(10);

        $sms_logs = SmsLogResource::collection($sms_logs);

        return Inertia::render('SmsLog/Index', compact('sms_logs', 'currentFilters', 'users'));
    }
}
