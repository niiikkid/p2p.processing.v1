<?php

namespace App\Http\Controllers\API\APP;

use App\Enums\DisputeStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\UserDevice $device */
        $device = $request->attributes->get('device');

        if (! $device || ! $device->android_id) {
            return response()->failWithMessage('Device not connected');
        }

        $userId = $device->user_id;

        $cacheKey = "user-apk-state-{$userId}";
        $state = Cache::remember($cacheKey, 30, function () use ($userId) {
            $rows = DB::table('disputes')
                ->selectRaw('min(created_at) as oldest_at, max(created_at) as latest_at, count(*) as cnt')
                ->where('status', DisputeStatus::PENDING)
                ->whereIn('order_id', function ($q) use ($userId) {
                    $q->select('id')->from('orders')->whereIn('payment_detail_id', function ($q2) use ($userId) {
                        $q2->select('id')->from('payment_details')->where('user_id', $userId);
                    });
                })
                ->first();

            return [
                'latest_at' => $rows?->latest_at,
                'oldest_at' => $rows?->oldest_at,
                'count' => $rows?->cnt ?? 0,
            ];
        });

        cache()->put("user-apk-latest-ping-at-{$userId}", now());

        return response()->success($state);
    }
}


