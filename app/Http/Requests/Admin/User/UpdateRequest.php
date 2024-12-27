<?php

namespace App\Http\Requests\Admin\User;

use App\Models\User;
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
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'banned' => ['required', 'boolean'],
            'personal_merchants' => ['nullable', 'array'],
            'personal_merchants.*' => ['nullable', 'integer', 'exists:merchants,id'],
            'exchange_markup_rates' => ['nullable', 'array'],
            'exchange_markup_rates.*.id' => ['required', 'integer', 'exists:payment_gateways,id'],
            'exchange_markup_rates.*.markup_rate' => ['required', 'numeric', 'min:0', 'max:99'],
        ];
    }

    public function attributes()
    {
        return [
            'role_id' => __('роль'),
            'personal_merchants' => __('мерчанты'),
            'exchange_markup_rates' => __('персональная комиссия трейдера'),
            'exchange_markup_rates.*.id' => __('комиссия'),
            'exchange_markup_rates.*.markup_rate' => __('комиссия'),
        ];
    }
}
