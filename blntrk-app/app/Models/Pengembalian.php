<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';

    protected $fillable = [
        'pesanan_id',
        'dikembalikan_pada',
        'hari_terlambat',
        'diterima_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'dikembalikan_pada' => 'datetime',
            'hari_terlambat' => 'integer',
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
     * @return BelongsTo<User, $this>
     */
    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    /**
     * @return HasMany<PengembalianDetail, $this>
     */
    public function detail(): HasMany
    {
        return $this->hasMany(PengembalianDetail::class, 'pengembalian_id');
    }

    /**
     * @return HasMany<Denda, $this>
     */
    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class, 'pengembalian_id');
    }
}
