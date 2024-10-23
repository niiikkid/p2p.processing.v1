<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => ['required', 'string', 'min:5', 'max:20'],
            'description' => ['required', 'string', 'min:5', 'max:2000'],
            'project_link' => ['required', 'url:https', 'min:3', 'max:500']
        ];
    }

    public function attributes()
    {
        return [
            'project_link' => __('ссылка на проект'),
        ];
    }
}
