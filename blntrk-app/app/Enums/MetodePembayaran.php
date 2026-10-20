<?php

namespace App\Enums;

enum MetodePembayaran: string
{
    case Tunai = 'tunai';
    case Transfer = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::Tunai => 'Tunai di Toko',
            self::Transfer => 'Transfer Bank Manual',
        };
    }
}
