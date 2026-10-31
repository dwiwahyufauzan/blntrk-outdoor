<?php

namespace App\Models;

use App\Enums\StatusPesanan;
use Illuminate\Database\Eloquent\Builder;
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

    public const RELASI_RINCIAN = [
        'pelanggan',
        'paketBundling',
        'pembuat',
        'pemroses',
        'detail.alat.kategori',
        'pembayaran.pencatat',
        'jaminan.penerima',
        'pengembalian.penerima',
        'pengembalian.detail.pesananDetail.alat',
        'denda.kategoriDenda',
    ];

    protected $fillable = [
        'no_resi',
        'pelanggan_id',
        'paket_bundling_id',
        'dibuat_oleh',
        'diproses_oleh',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'jatuh_tempo',
        'batas_ambil',
        'status',
        'total_sewa',
        'jumlah_paket',
        'diskon_paket',
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
            'jumlah_paket' => 'integer',
            'diskon_paket' => 'decimal:2',
            'diambil_pada' => 'datetime',
            'dibatalkan_pada' => 'datetime',
        ];
    }

    /**
     * Batasi pesanan yang dapat dijangkau: staf (admin/pemilik) semua, pelanggan hanya miliknya.
     * Pesanan milik orang lain diperlakukan seperti tidak ada (404) agar nomor resi tidak dapat ditebak.
     */
    public function scopeTampilkanUntuk(Builder $query, ?User $user): Builder
    {
        if ($user && $user->punyaRole('admin', 'pemilik')) {
            return $query;
        }

        return $query->whereHas('pelanggan', fn ($q) => $q->where('user_id', $user?->id ?? 0));
    }

    /**
     * Tarif sewa harian bersih (setelah diskon paket bundling) yang menjadi dasar denda keterlambatan.
     */
    public function getTarifHarianAttribute(): float
    {
        $kotor = (float) $this->detail->sum(fn ($d) => $d->harga_per_hari * $d->jumlah);
        $diskonHarian = (float) $this->diskon_paket / max(1, (int) $this->durasi_hari);

        return max(0.0, $kotor - $diskonHarian);
    }

    public function paketBundling(): BelongsTo
    {
        return $this->belongsTo(PaketBundling::class, 'paket_bundling_id');
    }

    /**
     * Memuat seluruh relasi yang dibutuhkan halaman detail, resi, dan PDF pesanan.
     */
    public function scopeDenganRincian(Builder $query): Builder
    {
        return $query->with(self::RELASI_RINCIAN);
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
