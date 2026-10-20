<?php

namespace App\Models;

use App\Enums\StatusPesanan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pesanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pesanan';

    protected $fillable = [
        'no_resi',
        'pelanggan_id',
        'dibuat_oleh',
        'diproses_oleh',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'jatuh_tempo',
        'batas_ambil',
        'status',
        'total_sewa',
        'diambil_pada',
        'dibatalkan_pada',
        'alasan_batal',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPesanan::class,
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'durasi_hari' => 'integer',
            'jatuh_tempo' => 'datetime',
            'batas_ambil' => 'datetime',
            'total_sewa' => 'decimal:2',
            'diambil_pada' => 'datetime',
            'dibatalkan_pada' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    public function alat(): BelongsToMany
    {
        return $this->belongsToMany(Alat::class, 'pesanan_detail')
            ->withPivot('id', 'jumlah', 'harga_per_hari', 'subtotal');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function jaminan(): HasOne
    {
        return $this->hasOne(Jaminan::class);
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }

    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class);
    }
}
