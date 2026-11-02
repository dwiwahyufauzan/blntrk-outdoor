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
            'jenis_jaminan' => ['required', 'string', 'in:ktp,sim,kartu_pelajar,lainnya,deposit'],
            'nomor_identitas' => ['required_unless:jenis_jaminan,deposit', 'nullable', 'string', 'regex:/\d{4,}/'],
            'nominal_deposit' => ['required_if:jenis_jaminan,deposit', 'nullable', 'numeric', 'min:1000', 'max:50000000'],
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
            'nominal_deposit.required_if' => 'Nominal deposit uang wajib diisi.',
            'nominal_deposit.min' => 'Nominal deposit minimal Rp 1.000.',
            'nomor_identitas.regex' => 'Nomor identitas harus berisi minimal 4 digit angka.',
        ];
    }
}
