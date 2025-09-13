<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
		];
	}
}


