<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PaketBundling extends Model
{
    use HasFactory;

    protected $table = 'paket_bundling';

    protected $fillable = [
        'nomor_paket',
        'kode_paket',
        'nama',
        'slug',
        'kapasitas_orang',
        'deskripsi',
        'foto',
        'harga_paket_per_hari',
        'harga_asli_per_hari',
        'is_aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'nomor_paket' => 'integer',
            'harga_paket_per_hari' => 'decimal:2',
            'harga_asli_per_hari' => 'decimal:2',
            'is_aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paket) {
            if (empty($paket->slug)) {
                $paket->slug = Str::slug($paket->nama);
            }
        });
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PaketBundlingDetail::class, 'paket_bundling_id');
    }

    public function items(): HasMany
    {
        return $this->detail();
    }

    public function alat(): BelongsToMany
    {
        return $this->belongsToMany(Alat::class, 'paket_bundling_detail', 'paket_bundling_id', 'alat_id')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('nomor_paket');
    }

    /**
     * Label format 'Paket 1', 'Paket 2', dst.
     */
    public function getLabelPaketAttribute(): string
    {
        return 'Paket '.$this->nomor_paket;
    }

    /**
     * Menghitung nilai penghematan rupiah per hari dibanding harga sewa eceran.
     */
    public function getHematPerHariAttribute(): float
    {
        return max(0, (float) $this->harga_asli_per_hari - (float) $this->harga_paket_per_hari);
    }

    /**
     * Menghitung persentase diskon bundling.
     */
    public function getPersenHematAttribute(): int
    {
        if ((float) $this->harga_asli_per_hari <= 0) {
            return 0;
        }

        return (int) round(($this->hemat_per_hari / (float) $this->harga_asli_per_hari) * 100);
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return asset('storage/'.$this->foto);
        }

        // Default cover paket
        return match ($this->nomor_paket) {
            1 => 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=800&q=80',
            2 => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=800&q=80',
            3 => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=800&q=80',
            default => 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=800&q=80',
        };
    }
}
