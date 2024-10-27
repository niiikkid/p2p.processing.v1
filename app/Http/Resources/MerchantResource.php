<?php

namespace App\Http\Resources;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var Merchant $this
         */
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'domain' => $this->domain,
            'user_id' => $this->user_id,
            'active' => $this->active,
            'owner' => [ //TODO hide if not exists
                'id' => $this->user->id,
                'email' => $this->user->email,
            ],
            'validated_at' => $this->validated_at?->toDateTimeString(),
            'banned_at' => $this->banned_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
