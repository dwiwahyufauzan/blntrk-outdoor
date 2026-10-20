<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case Menunggu = 'menunggu';
    case Dibayar = 'dibayar';
    case Disewa = 'disewa';
    case Terlambat = 'terlambat';
    case Dikembalikan = 'dikembalikan';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu Pembayaran',
            self::Dibayar => 'Sudah Dibayar',
            self::Disewa => 'Sedang Disewa',
            self::Terlambat => 'Terlambat',
            self::Dikembalikan => 'Selesai / Dikembalikan',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Menunggu => 'warning',
            self::Dibayar => 'info',
            self::Disewa => 'success',
            self::Terlambat => 'danger',
            self::Dikembalikan => 'secondary',
            self::Dibatalkan => 'dark',
        };
    }

    public function isAktif(): bool
    {
        return in_array($this, [self::Menunggu, self::Dibayar, self::Disewa, self::Terlambat]);
    }
}
