<?php

namespace App\Models;

use App\Enums\JenisJaminan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jaminan extends Model
{
    use HasFactory;

    protected $table = 'jaminan';

    protected $fillable = [
        'pesanan_id',
        'jenis',
        'nomor_identitas_samar',
        'keterangan',
        'status',
        'diterima_pada',
        'diterima_oleh',
        'dikembalikan_pada',
        'dikembalikan_oleh',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisJaminan::class,
            'diterima_pada' => 'datetime',
            'dikembalikan_pada' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    public function pengembali(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikembalikan_oleh');
    }
}
