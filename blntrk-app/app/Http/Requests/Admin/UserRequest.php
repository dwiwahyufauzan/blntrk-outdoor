<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->punyaRole('admin') ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');
        $isCreate = $this->isMethod('post');

        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at'),
            ],
            // Admin hanya boleh mengelola role admin dan pelanggan (Dilarang membuat role pemilik)
            'role' => ['required', 'string', 'in:admin,pelanggan'],
            'password' => [$isCreate ? 'required' : 'nullable', 'string', 'min:8'],
            'is_aktif' => ['nullable', 'boolean'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar pada akun lain.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'role.in' => 'Peran tidak valid. Admin hanya diperbolehkan membuat peran Admin atau Pelanggan.',
            'password.required' => 'Password wajib diisi untuk pengguna baru.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
        ];
    }
}
