<?php

namespace App\Enums;

enum JenisDenda: string
{
    case Keterlambatan = 'keterlambatan';
    case Kerusakan = 'kerusakan';
    case Kehilangan = 'kehilangan';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Keterlambatan => 'Denda Keterlambatan',
            self::Kerusakan => 'Biaya Kerusakan Alat',
            self::Kehilangan => 'Biaya Kehilangan Alat',
            self::Lainnya => 'Biaya Denda Lainnya',
        };
    }
}
