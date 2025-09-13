<?php

namespace App\Http\Requests\API\APP;

use Illuminate\Foundation\Http\FormRequest;

class DeviceConnectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'android_id' => ['required', 'string', 'min:8', 'max:128'],
            'device_model' => ['nullable', 'string', 'max:128'],
            'android_version' => ['nullable', 'string', 'max:64'],
            'manufacturer' => ['nullable', 'string', 'max:128'],
            'brand' => ['nullable', 'string', 'max:128'],
        ];
    }
}


