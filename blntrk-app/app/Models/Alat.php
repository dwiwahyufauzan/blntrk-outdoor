<?php

namespace App\Models;

use App\Services\KetersediaanService;
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
        'harga_beli',
        'stok_total',
        'stok_perawatan',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_hari' => 'decimal:2',
            'harga_beli' => 'decimal:2',
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

    /**
     * Jumlah unit alat yang sedang ditahan oleh pesanan aktif (menunggu, dibayar, disewa, terlambat).
     */
    public function getStokDisewaAttribute(): int
    {
        if (isset($this->attributes['stok_disewa'])) {
            return (int) $this->attributes['stok_disewa'];
        }

        return (int) PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->where('pesanan_detail.alat_id', $this->id)
            ->whereIn('pesanan.status', KetersediaanService::STATUS_AKTIF)
            ->whereNull('pesanan.deleted_at')
            ->sum('pesanan_detail.jumlah');
    }

    /**
     * Sisa stok unit yang benar-benar siap disewa oleh pelanggan saat ini.
     * Otomatis berkurang saat disewa, dan otomatis bertambah (restock) saat barang dikembalikan.
     */
    public function getStokTersediaAttribute(): int
    {
        if (isset($this->attributes['stok_tersedia'])) {
            return max(0, (int) $this->attributes['stok_tersedia']);
        }

        return max(0, $this->kapasitas_fisik - $this->stok_disewa);
    }

    /**
     * Helper static untuk memasang stok_tersedia dan stok_disewa secara massal (eager aggregation) tanpa N+1 query.
     *
     * @param  iterable<Alat>  $alatList
     * @return iterable<Alat>
     */
    public static function muatStokTersedia(iterable $alatList): iterable
    {
        $alatCollection = collect($alatList);
        if ($alatCollection->isEmpty()) {
            return $alatList;
        }

        $ids = $alatCollection->pluck('id')->all();

        $terpakaiMap = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->whereIn('pesanan_detail.alat_id', $ids)
            ->whereIn('pesanan.status', KetersediaanService::STATUS_AKTIF)
            ->whereNull('pesanan.deleted_at')
            ->groupBy('pesanan_detail.alat_id')
            ->selectRaw('pesanan_detail.alat_id, SUM(pesanan_detail.jumlah) as total_terpakai')
            ->pluck('total_terpakai', 'pesanan_detail.alat_id');

        foreach ($alatCollection as $alat) {
            $disewa = (int) ($terpakaiMap[$alat->id] ?? 0);
            $alat->stok_disewa = $disewa;
            $alat->stok_tersedia = max(0, $alat->kapasitas_fisik - $disewa);
        }

        return $alatList;
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return asset('storage/'.$this->foto);
        }

        return null;
    }
}
