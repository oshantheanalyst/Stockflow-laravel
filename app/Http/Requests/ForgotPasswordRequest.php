<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    // Determine if the user is authorized to make this request.
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request.
    public function rules(): array
    {
        return [
            'email' => 'required|string|min:1|max:255',
        ];
    }

    // Get custom messages for validator errors.
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your username or email address.',
            'email.min' => 'Please enter your username or email address.',
            'email.max' => 'The username or email is too long.',
        ];
    }
}
