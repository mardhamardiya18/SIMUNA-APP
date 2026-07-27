<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login_id.required' => 'Email atau Nomor WhatsAppnya diisi dulu ya!',
            'password.required' => 'Passwordnya jangan lupa dimasukkan ya!',
        ];
    }
}
