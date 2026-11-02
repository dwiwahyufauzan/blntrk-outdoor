<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerpanjanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->punyaRole('admin');
    }

    public function rules(): array
    {
        return [
            'tanggal_selesai' => ['required', 'date', 'after:today'],
            'metode' => ['required', 'string', 'in:tunai,transfer'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_selesai.required' => 'Tanggal selesai baru wajib diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai baru harus setelah hari ini.',
            'metode.required' => 'Metode pembayaran tambahan wajib dipilih.',
            'metode.in' => 'Metode pembayaran harus berupa tunai atau transfer.',
        ];
    }
}
