<?php

namespace App\Http\Requests;

use App\Enums\StatusPesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->punyaRole('admin');
    }

    public function rules(): array
    {
        return [
            'jumlah' => ['required', 'numeric', 'min:0'],
            'metode' => ['required', 'string', 'in:tunai,transfer'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $pesanan = $this->route('pesanan');
            if ($pesanan) {
                if ($pesanan->status !== StatusPesanan::Menunggu) {
                    $validator->errors()->add('status', "Aksi tidak valid untuk status {$pesanan->status->value}.");
                }
                $jumlahInput = (float) $this->input('jumlah');
                if (abs($jumlahInput - (float) $pesanan->total_sewa) > 0.001) {
                    $validator->errors()->add('jumlah', 'Jumlah harus sama dengan total sewa.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'jumlah.required' => 'Jumlah pembayaran wajib diisi.',
            'jumlah.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'metode.required' => 'Metode pembayaran wajib dipilih.',
            'metode.in' => 'Metode pembayaran harus berupa tunai atau transfer.',
        ];
    }
}
