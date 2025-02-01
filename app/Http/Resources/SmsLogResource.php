<?php

namespace App\Http\Resources;

use App\Models\SmsLog;
use App\Services\Sms\Parser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parser = new Parser();
        $amount = $parser->parseAmountFromMessage($this->message);

        /**
         * @var SmsLog $this
         */
        return [
            'id' => $this->id,
            'sender' => $this->sender,
            'message' => $this->message,
            'timestamp' => Carbon::createFromTimestamp($this->timestamp)->toDateTimeString(),
            'type' => $this->type->value,
            'created_at' => $this->created_at->toDateTimeString(),
            'sender_exists' => (bool)(new Parser())->getGatewayBySender($this->sender),
            'parsing_results' => $amount ? [
                'amount' => $parser->parseAmountFromMessage($this->message),
                'digits' => $parser->parseCardLastDigitsFromMessage($this->message),
            ] : null,
            $this->mergeWhen($this->resource->relationLoaded('user'), function () {
                return [
                    'user' => UserResource::make($this->user)
                ];
            }),
        ];
    }
}
