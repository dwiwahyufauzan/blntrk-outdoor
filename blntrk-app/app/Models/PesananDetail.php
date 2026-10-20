<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PesananDetail extends Model
{
    use HasFactory;

    protected $table = 'pesanan_detail';

    public $timestamps = false;

    protected $fillable = [
        'pesanan_id',
        'alat_id',
        'jumlah',
        'harga_per_hari',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_per_hari' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class)->withTrashed();
    }

    public function pengembalianDetail(): HasOne
    {
        return $this->hasOne(PengembalianDetail::class, 'pesanan_detail_id');
    }
}
