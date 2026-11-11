<?php

namespace App\Data;

final readonly class ItemPesanan
{
    public function __construct(
        public int $alatId,
        public int $jumlah,
    ) {}
}
