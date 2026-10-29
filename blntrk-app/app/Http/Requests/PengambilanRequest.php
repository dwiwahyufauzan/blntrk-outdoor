<?php

namespace App\Http\Requests;

use App\Enums\StatusPesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PengambilanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->punyaRole('admin');
    }

    public function rules(): array
    {
        return [
            'jenis_jaminan' => ['required', 'string', 'in:ktp,sim,kartu_pelajar,lainnya'],
            'nomor_identitas' => ['required', 'string', 'regex:/\d{4,}/'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $pesanan = $this->route('pesanan');
            if ($pesanan && $pesanan->status !== StatusPesanan::Dibayar) {
                $validator->errors()->add('status', "Aksi tidak valid untuk status {$pesanan->status->value}.");
            }
        });
    }

    public function messages(): array
    {
        return [
            'jenis_jaminan.required' => 'Jenis jaminan kartu identitas wajib dipilih.',
            'jenis_jaminan.in' => 'Pilihan jenis jaminan tidak valid.',
            'nomor_identitas.required' => 'Nomor kartu identitas fisik wajib diisi.',
            'nomor_identitas.regex' => 'Nomor identitas harus berisi minimal 4 digit angka.',
        ];
    }
}
