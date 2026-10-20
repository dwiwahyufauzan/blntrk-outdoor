<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->punyaRole('admin') ?? false;
    }

    public function rules(): array
    {
        $kategoriId = $this->route('kategori')?->id ?? $this->route('kategori');

        return [
            'nama' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kategori_alat', 'nama')->ignore($kategoriId),
            ],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
            'nama.max' => 'Nama kategori maksimal 50 karakter.',
        ];
    }
}
