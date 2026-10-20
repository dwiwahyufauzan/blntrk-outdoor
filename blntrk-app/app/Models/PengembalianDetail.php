<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengembalianDetail extends Model
{
    use HasFactory;

    protected $table = 'pengembalian_detail';

    public $timestamps = false;

    protected $fillable = [
        'pengembalian_id',
        'pesanan_detail_id',
        'jml_baik',
        'jml_rusak_ringan',
        'jml_rusak_berat',
        'jml_hilang',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jml_baik' => 'integer',
            'jml_rusak_ringan' => 'integer',
            'jml_rusak_berat' => 'integer',
            'jml_hilang' => 'integer',
        ];
    }

    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(Pengembalian::class);
    }

    public function pesananDetail(): BelongsTo
    {
        return $this->belongsTo(PesananDetail::class);
    }

    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class, 'pengembalian_detail_id');
    }
}
