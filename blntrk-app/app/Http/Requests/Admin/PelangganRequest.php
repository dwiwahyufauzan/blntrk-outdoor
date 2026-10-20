<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->punyaRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'regex:/^[0-9+\s\-]{10,20}$/'],
            'alamat' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap pelanggan wajib diisi.',
            'no_hp.required' => 'Nomor HP/WhatsApp pelanggan wajib diisi.',
            'no_hp.regex' => 'Format nomor HP tidak valid (10-20 digit angka).',
            'alamat.required' => 'Alamat pelanggan wajib diisi.',
        ];
    }
}
