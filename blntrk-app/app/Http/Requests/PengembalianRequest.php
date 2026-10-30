<?php

namespace App\Http\Requests;

use App\Enums\StatusPesanan;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->punyaRole('admin');
    }

    public function rules(): array
    {
        return [
            'dikembalikan_pada' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'baris' => ['required', 'array'],
            'baris.*.baik' => ['required', 'integer', 'min:0'],
            'baris.*.rusak_ringan' => ['required', 'integer', 'min:0'],
            'baris.*.rusak_berat' => ['required', 'integer', 'min:0'],
            'baris.*.hilang' => ['required', 'integer', 'min:0'],
            'baris.*.biaya' => ['nullable', 'numeric', 'min:0'],
            'baris.*.kategori_denda_id' => ['nullable', 'exists:kategori_denda,id'],
            'baris.*.catatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $pesanan = $this->route('pesanan');
            if ($pesanan) {
                if (! in_array($pesanan->status, [StatusPesanan::Disewa, StatusPesanan::Terlambat], true)) {
                    $validator->errors()->add('status', "Pengembalian tidak valid untuk pesanan dengan status {$pesanan->status->value}.");

                    return;
                }

                $waktuKembali = Carbon::parse($this->input('dikembalikan_pada'));
                if ($waktuKembali->lt($pesanan->tanggal_mulai->startOfDay())) {
                    $validator->errors()->add('dikembalikan_pada', 'Waktu pengembalian tidak boleh mendahului tanggal mulai sewa.');
                }

                $barisInput = $this->input('baris', []);
                foreach ($pesanan->detail as $det) {
                    $item = $barisInput[$det->id] ?? null;
                    if (! $item) {
                        $validator->errors()->add("baris.{$det->id}", "Rincian kondisi untuk {$det->alat->nama} wajib diisi.");

                        continue;
                    }

                    $totalKondisi = (int) ($item['baik'] ?? 0)
                        + (int) ($item['rusak_ringan'] ?? 0)
                        + (int) ($item['rusak_berat'] ?? 0)
                        + (int) ($item['hilang'] ?? 0);

                    if ($totalKondisi !== $det->jumlah) {
                        $validator->errors()->add(
                            "baris.{$det->id}",
                            "Jumlah kondisi alat {$det->alat->nama} ({$totalKondisi}) harus sama dengan jumlah disewa ({$det->jumlah} unit)."
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'dikembalikan_pada.required' => 'Waktu pengembalian alat wajib diisi.',
            'dikembalikan_pada.date' => 'Format waktu pengembalian tidak valid.',
            'baris.required' => 'Rincian kondisi alat yang dikembalikan wajib diisi.',
        ];
    }
}
