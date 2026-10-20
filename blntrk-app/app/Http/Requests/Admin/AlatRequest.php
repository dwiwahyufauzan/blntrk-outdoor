<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->punyaRole('admin') ?? false;
    }

    public function rules(): array
    {
        $alatId = $this->route('alat')?->id ?? $this->route('alat');

        return [
            'kategori_id' => ['required', 'exists:kategori_alat,id'],
            'kode_alat' => [
                'required',
                'string',
                'max:20',
                Rule::unique('alat', 'kode_alat')->ignore($alatId)->whereNull('deleted_at'),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'harga_per_hari' => ['required', 'numeric', 'min:0'],
            'stok_total' => ['required', 'integer', 'min:0'],
            'stok_perawatan' => ['required', 'integer', 'min:0', 'lte:stok_total'],
            'is_aktif' => ['nullable', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori alat wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'kode_alat.required' => 'Kode alat wajib diisi.',
            'kode_alat.unique' => 'Kode alat sudah terdaftar pada sistem.',
            'nama.required' => 'Nama alat outdoor wajib diisi.',
            'harga_per_hari.required' => 'Tarif sewa per hari wajib diisi.',
            'harga_per_hari.min' => 'Tarif sewa tidak boleh bernilai negatif.',
            'stok_total.required' => 'Total stok wajib diisi.',
            'stok_total.min' => 'Total stok tidak boleh bernilai negatif.',
            'stok_perawatan.required' => 'Stok perawatan wajib diisi (minimal 0).',
            'stok_perawatan.lte' => 'Stok dalam perawatan tidak boleh melebihi total stok.',
            'foto.image' => 'File foto harus berupa gambar valid.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran file foto tidak boleh melebihi 2MB (2048 KB).',
        ];
    }
}
