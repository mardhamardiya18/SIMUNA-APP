<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:10', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Yuk isi dulu nama kepala keluarga / penanggung jawab ya!',
            'email.required' => 'Email aktifnya jangan lupa diisi ya!',
            'email.email' => 'Format emailnya sepertinya belum pas nih, contoh: bunda@gmail.com',
            'email.unique' => 'Email ini sudah terdaftar nih. Yuk langsung masuk ke akun Anda!',
            'phone.required' => 'Nomor WhatsApp aktif diisi dulu ya supaya bisa dihubungi posyandu!',
            'phone.min' => 'Nomor WhatsApp minimal 10 digit ya!',
            'phone.unique' => 'Nomor WhatsApp ini sudah terdaftar nih. Mau coba masuk langsung?',
            'password.required' => 'Passwordnya diisi dulu ya!',
            'password.min' => 'Passwordnya kurang panjang nih, minimal 6 karakter ya!',
            'password.confirmed' => 'Konfirmasi passwordnya belum cocok nih, coba ketik ulang ya!',
        ];
    }
}
