<?php

namespace App\Data;

use App\Enums\MetodePembayaran;

/**
 * Uang yang dikembalikan kepada pelanggan atas pesanan yang sudah dibayar lalu dibatalkan.
 */
final readonly class RefundBaru
{
    public function __construct(
        public float $jumlah,
        public MetodePembayaran $metode,
        public ?string $catatan = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data  ['jumlah_refund', 'metode_refund', 'catatan_refund']
     */
    public static function dari(array $data): self
    {
        return new self(
            round((float) $data['jumlah_refund'], 2),
            MetodePembayaran::from($data['metode_refund']),
            ! empty($data['catatan_refund']) ? (string) $data['catatan_refund'] : null,
        );
    }
}
