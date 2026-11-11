<?php

namespace App\Http\Requests;

use App\Data\RefundBaru;
use App\Enums\MetodePembayaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->punyaRole('admin');
    }

    public function rules(): array
    {
        return [
            'jumlah_refund' => ['required', 'numeric', 'min:1'],
            'metode_refund' => ['required', Rule::enum(MetodePembayaran::class)],
            'catatan_refund' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'jumlah_refund.required' => 'Jumlah refund wajib diisi.',
            'jumlah_refund.min' => 'Jumlah refund minimal Rp 1.',
            'metode_refund.required' => 'Metode pengembalian dana wajib dipilih.',
        ];
    }

    public function refund(): RefundBaru
    {
        return RefundBaru::dari($this->validated());
    }
}
