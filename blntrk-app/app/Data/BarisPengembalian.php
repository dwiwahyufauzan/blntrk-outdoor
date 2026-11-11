<?php

namespace App\Data;

/**
 * Hasil pemeriksaan kondisi satu baris alat (satu pesanan_detail) saat dikembalikan.
 */
final readonly class BarisPengembalian
{
    public function __construct(
        public int $baik = 0,
        public int $rusakRingan = 0,
        public int $rusakBerat = 0,
        public int $hilang = 0,
        public float $biaya = 0.0,
        public ?int $kategoriDendaId = null,
        public ?string $catatan = null,
    ) {}

    public function jumlahKondisi(): int
    {
        return $this->baik + $this->rusakRingan + $this->rusakBerat + $this->hilang;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function dari(array $data): self
    {
        return new self(
            (int) ($data['baik'] ?? 0),
            (int) ($data['rusak_ringan'] ?? 0),
            (int) ($data['rusak_berat'] ?? 0),
            (int) ($data['hilang'] ?? 0),
            (float) ($data['biaya'] ?? 0),
            ! empty($data['kategori_denda_id']) ? (int) $data['kategori_denda_id'] : null,
            ! empty($data['catatan']) ? (string) $data['catatan'] : null,
        );
    }
}
