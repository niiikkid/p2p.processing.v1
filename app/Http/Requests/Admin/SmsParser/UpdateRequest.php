<?php

namespace App\Http\Requests\Admin\SmsParser;

use App\Rules\ParserFormatIsUnique;
use App\Rules\ParserRegexIsUnique;
use App\Rules\ParserRegexValidToFormat;
use App\Services\Money\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        //TODO refactoring

        $smsParser = $this->route('sms_parser');

        return [
            //'payment_gateway_id' => ['required', 'exists:payment_gateways,id'],
            'currency' => ['required', Rule::in(Currency::getAllCodes())],
            'format' => ['required', 'string', 'max:255'],
            'regex' => [
                'required',
                'string',
                'max:255',
                'regex:/\?\<amount\>/m',
                new ParserRegexValidToFormat('format'),
                new ParserRegexIsUnique($smsParser->id),
                new ParserFormatIsUnique($smsParser->id)
            ],
        ];
    }
}
