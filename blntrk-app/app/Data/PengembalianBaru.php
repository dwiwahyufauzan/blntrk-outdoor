<?php

namespace App\Data;

use Carbon\Carbon;

/**
 * Masukan pemrosesan pengembalian untuk PengembalianService::proses.
 */
final readonly class PengembalianBaru
{
    /**
     * @param  Carbon|null  $dikembalikanPada  Waktu yang diketik admin; null berarti memakai waktu sistem saat diproses
     * @param  array<int, BarisPengembalian>  $baris  Dikunci berdasarkan pesanan_detail_id
     */
    public function __construct(
        public ?Carbon $dikembalikanPada,
        public array $baris,
        public ?string $catatan = null,
    ) {}

    /**
     * Membentuk dari data tervalidasi: ['dikembalikan_pada', 'catatan', 'baris' => [pesanan_detail_id => [...]]]
     *
     * @param  array<string, mixed>  $data
     */
    public static function dari(array $data): self
    {
        return new self(
            ! empty($data['dikembalikan_pada']) ? Carbon::parse($data['dikembalikan_pada']) : null,
            array_map(fn (array $baris) => BarisPengembalian::dari($baris), $data['baris'] ?? []),
            $data['catatan'] ?? null,
        );
    }
}
