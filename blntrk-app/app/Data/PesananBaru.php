<?php

namespace App\Data;

use Carbon\Carbon;

/**
 * Masukan pembuatan pesanan untuk PesananService::buat.
 */
final readonly class PesananBaru
{
    /**
     * @param  list<ItemPesanan>  $items
     */
    public function __construct(
        public Carbon $tanggalMulai,
        public Carbon $tanggalSelesai,
        public array $items,
        public ?string $catatan = null,
    ) {}

    /**
     * Membentuk dari data tervalidasi: ['tanggal_mulai', 'tanggal_selesai', 'items' => [['alat_id', 'jumlah']], 'catatan']
     * Kunci lain (data pelanggan walk-in, dsb.) diabaikan.
     *
     * @param  array<string, mixed>  $data
     */
    public static function dari(array $data): self
    {
        return new self(
            Carbon::parse($data['tanggal_mulai'])->startOfDay(),
            Carbon::parse($data['tanggal_selesai'])->startOfDay(),
            array_values(array_map(
                fn (array $item) => new ItemPesanan((int) $item['alat_id'], (int) $item['jumlah']),
                $data['items'] ?? []
            )),
            $data['catatan'] ?? null,
        );
    }
}
