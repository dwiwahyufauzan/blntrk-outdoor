<?php

namespace App\Enums;

enum JenisJaminan: string
{
    case KTP = 'ktp';
    case SIM = 'sim';
    case KartuPelajar = 'kartu_pelajar';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::KTP => 'KTP',
            self::SIM => 'SIM',
            self::KartuPelajar => 'Kartu Pelajar / Mahasiswa',
            self::Lainnya => 'Identitas Lainnya',
        };
    }
}
