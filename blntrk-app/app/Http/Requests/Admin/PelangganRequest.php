<?php

namespace App\Http\Requests\Admin;

use App\Rules\NomorHpIndonesia;
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
            'no_hp' => ['required', 'string', 'max:20', new NomorHpIndonesia],
            'alamat' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap pelanggan wajib diisi.',
            'no_hp.required' => 'Nomor HP/WhatsApp pelanggan wajib diisi.',
            'alamat.required' => 'Alamat pelanggan wajib diisi.',
        ];
    }
}
