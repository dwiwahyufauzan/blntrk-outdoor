<?php

namespace App\Enums;

enum JenisDenda: string
{
    case Keterlambatan = 'keterlambatan';
    case Kerusakan = 'kerusakan';

    public function label(): string
    {
        return match ($this) {
            self::Keterlambatan => 'Denda Keterlambatan',
            self::Kerusakan => 'Biaya Kerusakan / Kehilangan',
        };
    }
}
