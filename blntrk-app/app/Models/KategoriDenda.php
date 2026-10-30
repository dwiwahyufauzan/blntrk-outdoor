<?php

namespace App\Models;

use App\Enums\JenisDenda;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriDenda extends Model
{
    use HasFactory;

    protected $table = 'kategori_denda';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'tarif_standar',
        'satuan',
        'deskripsi',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'tarif_standar' => 'decimal:2',
            'is_aktif' => 'boolean',
        ];
    }

    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class, 'kategori_denda_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    public function getLabelJenisAttribute(): string
    {
        if ($this->jenis instanceof JenisDenda) {
            return $this->jenis->label();
        }

        return match ($this->jenis) {
            'keterlambatan' => 'Denda Keterlambatan',
            'kehilangan' => 'Biaya Kehilangan Alat',
            'kerusakan' => 'Biaya Kerusakan Alat',
            default => ucfirst($this->jenis),
        };
    }

    /**
     * Memastikan data kategori denda bawaan tersedia di sistem.
     */
    public static function seedDefault(): void
    {
        $defaultCategories = [
            [
                'kode' => 'TELAT',
                'nama' => 'Keterlambatan Pengembalian',
                'jenis' => 'keterlambatan',
                'tarif_standar' => 0,
                'satuan' => 'per hari',
                'deskripsi' => 'Dikenakan per hari jika pengembalian melewati batas pukul 12:00 siang (toleransi 60 menit gratis). Tarif per hari dihitung proporsional dari total alat yang disewa.',
                'is_aktif' => true,
            ],
            [
                'kode' => 'HILANG',
                'nama' => 'Kehilangan Alat / Perlengkapan',
                'jenis' => 'kehilangan',
                'tarif_standar' => 0,
                'satuan' => 'sesuai harga beli',
                'deskripsi' => 'Ganti rugi penuh perlengkapan outdoor yang hilang, dihitung otomatis sesuai harga beli/pengadaan resmi aset alat tersebut.',
                'is_aktif' => true,
            ],
            [
                'kode' => 'RUSAK_BERAT',
                'nama' => 'Kerusakan Berat / Patah',
                'jenis' => 'kerusakan',
                'tarif_standar' => 75000,
                'satuan' => 'per unit',
                'deskripsi' => 'Biaya penggantian atau perbaikan untuk kerusakan struktural parah (frame tenda patah, sobek besar, resleting jebol parah, dsb).',
                'is_aktif' => true,
            ],
            [
                'kode' => 'RUSAK_RINGAN',
                'nama' => 'Kerusakan Ringan / Pembersihan Khusus',
                'jenis' => 'kerusakan',
                'tarif_standar' => 25000,
                'satuan' => 'per unit',
                'deskripsi' => 'Biaya perbaikan kerusakan minor atau pembersihan khusus untuk alat yang dikembalikan dalam kondisi kotor lumpur/tanah pekat.',
                'is_aktif' => true,
            ],
        ];

        foreach ($defaultCategories as $cat) {
            self::updateOrCreate(['kode' => $cat['kode']], $cat);
        }
    }
}
