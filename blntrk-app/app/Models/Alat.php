<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alat';

    protected $fillable = [
        'kategori_id',
        'kode_alat',
        'nama',
        'varian',
        'deskripsi',
        'foto',
        'harga_per_hari',
        'stok_total',
        'stok_perawatan',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_hari' => 'decimal:2',
            'stok_total' => 'integer',
            'stok_perawatan' => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriAlat::class, 'kategori_id');
    }

    public function pesananDetail(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'alat_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Kapasitas fisik toko di luar unit yang sedang diperbaiki.
     */
    public function getKapasitasFisikAttribute(): int
    {
        return max(0, $this->stok_total - $this->stok_perawatan);
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return asset('storage/'.$this->foto);
        }

        return null;
    }
}
