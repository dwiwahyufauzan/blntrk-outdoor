<?php

namespace App\Http\Requests;

use App\Data\RefundBaru;
use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $rules = [
            'alasan_batal' => ['required', 'string', 'max:255'],
        ];

        // Pesanan yang sudah dibayar: admin wajib memutuskan nasib dana (dikembalikan atau hangus)
        if ($this->adminMembatalkanPesananBerbayar()) {
            $rules += [
                'penanganan_dana' => ['required', 'in:refund,hangus'],
                'jumlah_refund' => ['required_if:penanganan_dana,refund', 'nullable', 'numeric', 'min:1'],
                'metode_refund' => ['required_if:penanganan_dana,refund', 'nullable', Rule::enum(MetodePembayaran::class)],
                'catatan_refund' => ['nullable', 'string', 'max:255'],
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'alasan_batal.required' => 'Alasan pembatalan pesanan wajib diisi.',
            'alasan_batal.max' => 'Alasan pembatalan maksimal 255 karakter.',
            'penanganan_dana.required' => 'Pilih penanganan dana: dikembalikan (refund) atau hangus.',
            'penanganan_dana.in' => 'Penanganan dana harus berupa refund atau hangus.',
            'jumlah_refund.required_if' => 'Jumlah refund wajib diisi.',
            'jumlah_refund.min' => 'Jumlah refund minimal Rp 1.',
            'metode_refund.required_if' => 'Metode pengembalian dana wajib dipilih.',
        ];
    }

    private function adminMembatalkanPesananBerbayar(): bool
    {
        $pesanan = $this->route('pesanan');

        return $pesanan instanceof Pesanan
            && $pesanan->status === StatusPesanan::Dibayar
            && $this->user()?->punyaRole('admin')
            && $pesanan->pembayaran()->exists();
    }

    /**
     * Mengambil data refund bila admin memilih mengembalikan dana; null berarti dana hangus atau tidak ada pembayaran.
     */
    public function refund(): ?RefundBaru
    {
        if ($this->input('penanganan_dana') !== 'refund') {
            return null;
        }

        return RefundBaru::dari($this->validated());
    }
}
