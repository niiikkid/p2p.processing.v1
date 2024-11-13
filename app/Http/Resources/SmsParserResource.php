<?php

namespace App\Http\Resources;

use App\Models\SmsParser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsParserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var SmsParser $this
         */
        return [
            'id' => $this->id,
            'payment_gateway_id' => $this->paymentGateway->id,
            'payment_gateway_name' => $this->paymentGateway->name,
            'format' => $this->format,
            'regex' => $this->regex,
        ];
    }
}
