<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PesananRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.alat_id' => ['required', 'integer', 'exists:alat,id', 'distinct'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];

        // Pengguna tanpa profil pelanggan wajib melengkapi kontak (dipakai untuk pengingat WhatsApp)
        if (! $this->user()?->pelanggan) {
            $rules['no_hp'] = ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{8,20}$/'];
            $rules['alamat'] = ['required', 'string', 'max:500'];
        }

        return $rules;
    }

    /**
     * Validasi tambahan setelah aturan dasar.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($this->filled(['tanggal_mulai', 'tanggal_selesai'])) {
                try {
                    $mulai = Carbon::parse($this->input('tanggal_mulai'))->startOfDay();
                    $selesai = Carbon::parse($this->input('tanggal_selesai'))->startOfDay();
                    $durasi = $mulai->diffInDays($selesai);

                    if ($durasi > 14) {
                        $v->errors()->add('tanggal_selesai', 'Durasi sewa maksimal adalah 14 hari.');
                    }
                } catch (\Exception) {
                    $v->errors()->add('tanggal_mulai', 'Format tanggal tidak valid.');
                }
            }
        });
    }

    /**
     * Custom error messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'tanggal_mulai.required' => 'Tanggal pengambilan alat wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal pengambilan tidak valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal pengambilan tidak boleh lebih lampau dari hari ini.',
            'tanggal_selesai.required' => 'Tanggal pengembalian alat wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal pengembalian tidak valid.',
            'tanggal_selesai.after' => 'Tanggal pengembalian harus setelah tanggal pengambilan.',
            'items.required' => 'Pilih minimal satu perlengkapan outdoor untuk disewa.',
            'items.min' => 'Pilih minimal satu perlengkapan outdoor untuk disewa.',
            'items.*.alat_id.required' => 'Perlengkapan alat wajib dipilih.',
            'items.*.alat_id.exists' => 'Perlengkapan alat yang dipilih tidak terdaftar di sistem.',
            'items.*.alat_id.distinct' => 'Terdapat alat yang dipilih lebih dari sekali, satukan jumlah unitnya.',
            'items.*.jumlah.required' => 'Jumlah unit sewa wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah unit sewa minimal 1 unit.',
            'items.*.jumlah.max' => 'Jumlah unit sewa maksimal 50 unit per baris.',
            'catatan.max' => 'Catatan pesanan maksimal 500 karakter.',
            'no_hp.required' => 'Nomor WhatsApp / HP wajib diisi untuk melengkapi profil pelanggan.',
            'no_hp.regex' => 'Format nomor HP tidak valid.',
            'alamat.required' => 'Alamat wajib diisi untuk melengkapi profil pelanggan.',
        ];
    }
}
