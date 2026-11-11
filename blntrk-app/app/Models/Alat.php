<?php

namespace App\Models;

use App\Services\KetersediaanService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $stok_tersedia_rentang Atribut transien: stok minimum sepanjang rentang tanggal yang dicari di katalog.
 */
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

    /**
     * @return BelongsTo<KategoriAlat, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriAlat::class, 'kategori_id');
    }

    /**
     * @return HasMany<PesananDetail, $this>
     */
    public function pesananDetail(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'alat_id');
    }

    /**
     * @return HasMany<RiwayatPerawatan, $this>
     */
    public function riwayatPerawatan(): HasMany
    {
        return $this->hasMany(RiwayatPerawatan::class, 'alat_id');
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
     * Jumlah unit alat yang tidak ada di toko hari ini: pesanan aktif yang masa sewanya mencakup hari ini, ditambah unit
     * berstatus disewa yang jatuh tempo hari ini tetapi belum dikembalikan. Pesanan untuk tanggal mendatang tidak
     * dihitung; stok pada tanggal itu dilihat lewat rentang tanggal.
     */
    public function getStokDisewaAttribute(): int
    {
        if (isset($this->attributes['stok_disewa'])) {
            return (int) $this->attributes['stok_disewa'];
        }

        return self::pemakaianHariIni([$this->id])[$this->id];
    }

    /**
     * Sisa stok unit yang siap disewa hari ini, atau pada rentang tanggal bila dimuat lewat muatStokTersedia().
     * Otomatis bertambah (restock) saat barang dikembalikan.
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
     * Tanpa rentang, yang dihitung adalah stok hari ini; dengan rentang [mulai, selesai) yang dihitung adalah
     * stok minimum sepanjang rentang itu, sama seperti pengecekan saat pesanan dibuat.
     *
     * @param  iterable<Alat>  $alatList
     * @return iterable<Alat>
     */
    public static function muatStokTersedia(iterable $alatList, ?Carbon $mulai = null, ?Carbon $selesai = null): iterable
    {
        $alatCollection = collect($alatList);
        if ($alatCollection->isEmpty()) {
            return $alatList;
        }

        $ids = $alatCollection->pluck('id')->all();

        if ($mulai && $selesai && $selesai->gt($mulai)) {
            $terpakaiMap = self::pemakaianMaksimum($ids, $mulai->copy()->startOfDay(), $selesai->copy()->startOfDay());
        } else {
            $terpakaiMap = self::pemakaianHariIni($ids);
        }

        foreach ($alatCollection as $alat) {
            $disewa = $terpakaiMap[$alat->id] ?? 0;
            $alat->stok_disewa = $disewa;
            $alat->stok_tersedia = max(0, $alat->kapasitas_fisik - $disewa);
        }

        return $alatList;
    }

    /**
     * Unit yang tidak ada di toko hari ini per alat.
     *
     * @param  array<int, int>  $alatIds
     * @return array<int, int> Format: [alat_id => unit_terpakai]
     */
    private static function pemakaianHariIni(array $alatIds): array
    {
        $hasil = self::pemakaianMaksimum($alatIds, today(), today()->addDay());

        // Hari kembali dipakai ulang oleh mesin ketersediaan, padahal unit baru benar-benar di toko setelah dikembalikan
        $belumKembali = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->whereIn('pesanan_detail.alat_id', $alatIds)
            ->where('pesanan.status', 'disewa')
            ->whereDate('pesanan.tanggal_selesai', '<=', today())
            ->whereNull('pesanan.deleted_at')
            ->groupBy('pesanan_detail.alat_id')
            ->selectRaw('pesanan_detail.alat_id, SUM(pesanan_detail.jumlah) as total')
            ->pluck('total', 'pesanan_detail.alat_id');

        foreach ($belumKembali as $alatId => $total) {
            $hasil[$alatId] = ($hasil[$alatId] ?? 0) + (int) $total;
        }

        return $hasil;
    }

    /**
     * Pemakaian unit tertinggi pada satu hari mana pun dalam rentang [mulai, selesai) untuk tiap alat.
     *
     * @param  array<int, int>  $alatIds
     * @return array<int, int> Format: [alat_id => unit_terpakai]
     */
    private static function pemakaianMaksimum(array $alatIds, Carbon $mulai, Carbon $selesai): array
    {
        $perHari = app(KetersediaanService::class)->terpakaiPerHariBanyak($alatIds, $mulai, $selesai);

        $hasil = [];
        foreach ($alatIds as $id) {
            $hasil[$id] = empty($perHari[$id]) ? 0 : max($perHari[$id]);
        }

        return $hasil;
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return foto_url($this->foto);
        }

        return null;
    }
}
