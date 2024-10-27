<?php

namespace App\Http\Requests\API\Order;

use App\Enums\DetailType;
use App\Services\Money\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'external_id' => [
                'required',
                Rule::unique('orders')->where(function ($query) {
                    return $query->where('external_id', $this->external_id);
                }),
            ],
            'amount' => ['required', 'integer'],
            'callback_url' => ['required', 'string', 'min:1', 'max:1000'],
            'payment_gateway' => [
                'required_without:currency',
                'prohibits:currency',
                'exists:payment_gateways,code'
            ],
            'currency' => [
                'required_without:payment_gateway',
                'prohibits:payment_gateway',
                Rule::in(Currency::getAllCodes())
            ],
            'payment_detail_type' => ['nullable', Rule::in(DetailType::values())],
            'text' => ['nullable', 'integer'],
            'merchant_id' => ['required', 'exists:merchants,uuid'],
        ];
    }
}
