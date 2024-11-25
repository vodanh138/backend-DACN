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
            'lastname.required' => __('validation.required',['attribute' => 'lastname']),
            'lastname.string' => __('validation.string',['attribute' => 'lastname']),
            'firstname.required' => __('validation.required',['attribute' => 'firstname']),
            'firstname.string' => __('validation.string',['attribute' => 'firstname']),
        ];
    }
}
