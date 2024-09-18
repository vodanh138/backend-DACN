<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NameRequest extends FormRequest
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
            'lastname' => 'required|string',
            'firstname' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => __('validation.required'),
            'username.string' => __('validation.string'),
            'password.required' => __('validation.required'),
        ];
    }
}
