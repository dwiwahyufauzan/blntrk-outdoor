<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AdminPesananRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->punyaRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'tipe_pelanggan' => ['required', 'in:terdaftar,baru'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.alat_id' => ['required', 'integer', 'exists:alat,id', 'distinct'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->input('tipe_pelanggan') === 'terdaftar') {
            $rules['pelanggan_id'] = ['required', 'integer', 'exists:pelanggan,id'];
        } else {
            $rules['nama_lengkap'] = ['required', 'string', 'max:100'];
            $rules['no_hp'] = ['required', 'string', 'max:20'];
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
            'tipe_pelanggan.required' => 'Pilih jenis pelanggan (terdaftar atau walk-in baru).',
            'pelanggan_id.required' => 'Pilih pelanggan yang terdaftar.',
            'pelanggan_id.exists' => 'Pelanggan yang dipilih tidak ditemukan.',
            'nama_lengkap.required' => 'Nama lengkap pelanggan walk-in wajib diisi.',
            'no_hp.required' => 'Nomor HP/WhatsApp pelanggan walk-in wajib diisi.',
            'alamat.required' => 'Alamat pelanggan walk-in wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal pengambilan alat wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal pengambilan tidak valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal pengambilan tidak boleh lebih lampau dari hari ini.',
            'tanggal_selesai.required' => 'Tanggal pengembalian alat wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal pengembalian tidak valid.',
            'tanggal_selesai.after' => 'Tanggal pengembalian harus setelah tanggal pengambilan.',
            'items.required' => 'Pilih minimal satu perlengkapan outdoor untuk disewa.',
            'items.min' => 'Pilih minimal satu perlengkapan outdoor untuk disewa.',
            'items.*.alat_id.required' => 'Perlengkapan alat wajib dipilih.',
            'items.*.alat_id.exists' => 'Perlengkapan alat tidak terdaftar di sistem.',
            'items.*.alat_id.distinct' => 'Terdapat alat yang dipilih lebih dari sekali.',
            'items.*.jumlah.required' => 'Jumlah unit sewa wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah unit sewa minimal 1 unit.',
            'catatan.max' => 'Catatan pesanan maksimal 500 karakter.',
        ];
    }
}
