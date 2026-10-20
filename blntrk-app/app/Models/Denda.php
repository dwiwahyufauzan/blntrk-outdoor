<?php

namespace App\Models;

use App\Enums\JenisDenda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denda extends Model
{
    use HasFactory;

    protected $table = 'denda';

    protected $fillable = [
        'pesanan_id',
        'pengembalian_id',
        'pengembalian_detail_id',
        'jenis',
        'keterangan',
        'jumlah',
        'status_bayar',
        'dilunasi_pada',
        'dilunasi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisDenda::class,
            'jumlah' => 'decimal:2',
            'dilunasi_pada' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(Pengembalian::class);
    }

    public function pengembalianDetail(): BelongsTo
    {
        return $this->belongsTo(PengembalianDetail::class);
    }

    public function petugasLunas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilunasi_oleh');
    }

    public function getIsLunasAttribute(): bool
    {
        return $this->status_bayar === 'lunas';
    }
}
