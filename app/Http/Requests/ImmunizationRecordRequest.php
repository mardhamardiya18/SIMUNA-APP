<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImmunizationRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'child_name' => ['required', 'string', 'max:255'],
            'head_of_family' => ['required', 'string', 'max:255'],
            'father_job' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'mother_job' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:laki-laki,perempuan'],
            'age_text' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'immunization_status' => ['required', 'in:lengkap,tidak lengkap'],
            'immunization_types' => ['nullable', 'array'],
            'incomplete_reason' => ['required_if:immunization_status,tidak lengkap', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_name.required' => 'Yuk isi dulu nama si kecil!',
            'head_of_family.required' => 'Nama kepala keluarga jangan lupa diisi ya!',
            'mother_name.required' => 'Nama ibu diisi dulu ya!',
            'gender.required' => 'Pilih jenis kelamin si kecil ya (Laki-laki / Perempuan)!',
            'gender.in' => 'Pilih jenis kelamin yang sesuai ya!',
            'age_text.required' => 'Usia anak belum diisi nih. Pilih tanggal lahir dulu ya!',
            'birth_date.required' => 'Tanggal lahir si kecil diisi dulu ya!',
            'address.required' => 'Alamat rumah tinggalnya diisi lengkap ya!',
            'phone.required' => 'Nomor WhatsApp diisi dulu ya!',
            'immunization_status.required' => 'Pilih dulu status imunisasi si kecil saat ini ya!',
            'incomplete_reason.required_if' => 'Tolong beri tahu alasan imunisasi belum lengkap ya, supaya tim medis bisa membantu!',
        ];
    }
}
