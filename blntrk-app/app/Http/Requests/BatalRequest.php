<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'alasan_batal' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'alasan_batal.required' => 'Alasan pembatalan pesanan wajib diisi.',
            'alasan_batal.max' => 'Alasan pembatalan maksimal 255 karakter.',
        ];
    }
}
