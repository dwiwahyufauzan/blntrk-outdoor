<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Nomor seluler Indonesia untuk WhatsApp: 08xx..., 628xx..., atau +628xx... (spasi, strip, dan kurung diabaikan).
 * Kontak utama toko adalah WhatsApp, sehingga nomor yang tidak bisa dihubungi lebih merugikan daripada email palsu.
 */
class NomorHpIndonesia implements ValidationRule
{
    public static function valid(?string $nilai): bool
    {
        $digit = preg_replace('/[\s\-()]/', '', (string) $nilai);

        return (bool) preg_match('/^(\+?62|0)8\d{8,11}$/', $digit);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::valid(is_scalar($value) ? (string) $value : null)) {
            $fail('Nomor HP/WhatsApp tidak valid. Gunakan nomor seluler Indonesia, contoh: 081234567890.');
        }
    }
}
