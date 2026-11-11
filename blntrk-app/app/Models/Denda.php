<?php

namespace App\Models;

use App\Enums\JenisDenda;
use App\Enums\MetodePembayaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denda extends Model
{
    use HasFactory;

    protected $table = 'denda';

    protected $fillable = [
        'pesanan_id',
        'kategori_denda_id',
        'pengembalian_id',
        'pengembalian_detail_id',
        'jenis',
        'keterangan',
        'jumlah',
        'status_bayar',
        'metode_bayar',
        'dilunasi_pada',
        'dilunasi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisDenda::class,
            'metode_bayar' => MetodePembayaran::class,
            'jumlah' => 'decimal:2',
            'dilunasi_pada' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Pesanan, $this>
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * @return BelongsTo<KategoriDenda, $this>
     */
    public function kategoriDenda(): BelongsTo
    {
        return $this->belongsTo(KategoriDenda::class, 'kategori_denda_id');
    }

    /**
     * @return BelongsTo<Pengembalian, $this>
     */
    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(Pengembalian::class);
    }

    /**
     * @return BelongsTo<PengembalianDetail, $this>
     */
    public function pengembalianDetail(): BelongsTo
    {
        return $this->belongsTo(PengembalianDetail::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function petugasLunas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilunasi_oleh');
    }

    public function getIsLunasAttribute(): bool
    {
        return $this->status_bayar === 'lunas';
    }
}
