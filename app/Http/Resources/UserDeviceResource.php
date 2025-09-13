<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\DevicePing;

class UserDeviceResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		$format = 'Y-m-d H:i:s';

		$token = (string) $this->token;
		$maskedToken = $token;
		if ($token !== '') {
			$prefix = substr($token, 0, 4);
			$suffix = substr($token, -4);
			$maskedToken = strlen($token) > 8
				? $prefix . '…' . $suffix
				: $token;
		}

		// Последний пинг из БД
		$lastPingAt = DevicePing::where('user_device_id', $this->id)
			->orderByDesc('pinged_at')
			->value('pinged_at');
		$lastPingSecondsAgo = $lastPingAt ? abs(round(now()->diffInSeconds($lastPingAt))) : null;

		// Построим таймлайн пингов за последний час с шагом 5 секунд
		$now = now();
		$endTs = $now->getTimestamp();
		$startTs = $endTs - 3600; // последние 60 минут
		$bucketSize = 10; // сек
		$totalBuckets = (int) floor(3600 / $bucketSize); // 720
		$timeline = array_fill(0, $totalBuckets, 0);

		$pings = DevicePing::where('user_device_id', $this->id)
			->whereBetween('pinged_at', [date('Y-m-d H:i:s', $startTs), date('Y-m-d H:i:s', $endTs)])
			->pluck('pinged_at');

		foreach ($pings as $pingedAt) {
			$ts = (int) strtotime($pingedAt);
			if ($ts < $startTs || $ts > $endTs) { continue; }
			$index = (int) floor(($ts - $startTs) / $bucketSize);
			if ($index >= 0 && $index < $totalBuckets) {
				$timeline[$index] = 1;
			}
		}

		return [
			'id' => $this->id,
			'name' => $this->name,
			'token' => $maskedToken,
			'token_full' => $token,
			'android_id' => $this->android_id,
			'device_model' => $this->device_model,
			'android_version' => $this->android_version,
			'manufacturer' => $this->manufacturer,
			'brand' => $this->brand,
			'connected_at' => $this->connected_at ? $this->connected_at->format($format) : null,
			'created_at' => $this->created_at ? $this->created_at->format($format) : null,
			'last_ping_seconds_ago' => $lastPingSecondsAgo,
			'ping_timeline' => array_reverse($timeline),
		];
	}
}


