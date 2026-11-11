<?php

namespace App\Models;

use App\Enums\StatusPesanan;
use App\Support\PengaturanToko;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Atribut transien (tidak ada di tabel) yang dipasang controller untuk kebutuhan tampilan daftar pesanan terlambat.
 *
 * @property array{hari: int, jumlah: float, tarif_harian: float} $kalkulasi_telat
 * @property array{hari: int, jumlah: float, tarif_harian: float} $info_telat
 * @property string $no_wa
 * @property string $pesan_wa
 */
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
        'refund.pencatat',
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
        'batas_bayar',
        'status',
        'total_sewa',
        'jumlah_paket',
        'diskon_paket',
        'diambil_pada',
        'dibatalkan_pada',
        'alasan_batal',
        'catatan',
        'bukti_transfer',
        'bukti_diunggah_pada',
        'pengingat_kembali_pada',
        'pengingat_terlambat_pada',
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
            'batas_bayar' => 'datetime',
            'total_sewa' => 'decimal:2',
            'jumlah_paket' => 'integer',
            'diskon_paket' => 'decimal:2',
            'diambil_pada' => 'datetime',
            'dibatalkan_pada' => 'datetime',
            'bukti_diunggah_pada' => 'datetime',
            'pengingat_kembali_pada' => 'datetime',
            'pengingat_terlambat_pada' => 'datetime',
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

        return $query->whereHas('pelanggan', fn ($q) => $q->where('user_id', $user->id ?? 0));
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

    /**
     * Total uang yang sudah diterima dari pelanggan (sewa awal + perpanjangan).
     */
    public function getTotalDibayarAttribute(): float
    {
        return (float) $this->pembayaran->sum('jumlah');
    }

    public function getTotalRefundAttribute(): float
    {
        return (float) $this->refund->sum('jumlah');
    }

    /**
     * Uang yang tidak dikembalikan saat pesanan dibatalkan (hangus), yang tetap menjadi pendapatan toko.
     */
    public function getDanaHangusAttribute(): float
    {
        if ($this->status !== StatusPesanan::Dibatalkan) {
            return 0.0;
        }

        return max(0.0, round($this->total_dibayar - $this->total_refund, 2));
    }

    /**
     * @return BelongsTo<PaketBundling, $this>
     */
    public function paketBundling(): BelongsTo
    {
        return $this->belongsTo(PaketBundling::class, 'paket_bundling_id');
    }

    /**
     * Pesanan yang secara nyata sudah terlambat: berstatus terlambat, atau masih berstatus disewa tetapi sudah melewati
     * jatuh tempo + toleransi (scheduler belum sempat menandainya). Aturannya sama dengan PesananStatusSyncService.
     */
    public function scopeSedangTerlambat(Builder $query): Builder
    {
        $batas = now()->subMinutes(PengaturanToko::toleransiMenit());

        return $query->where(fn (Builder $q) => $q
            ->where('status', StatusPesanan::Terlambat)
            ->orWhere(fn (Builder $s) => $s->where('status', StatusPesanan::Disewa)->where('jatuh_tempo', '<', $batas)));
    }

    /**
     * Memuat seluruh relasi yang dibutuhkan halaman detail, resi, dan PDF pesanan.
     */
    public function scopeDenganRincian(Builder $query): Builder
    {
        return $query->with(self::RELASI_RINCIAN);
    }

    /**
     * @return BelongsTo<Pelanggan, $this>
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    /**
     * @return HasMany<PesananDetail, $this>
     */
    public function detail(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    /**
     * @return BelongsToMany<Alat, $this>
     */
    public function alat(): BelongsToMany
    {
        return $this->belongsToMany(Alat::class, 'pesanan_detail')
            ->withPivot('id', 'jumlah', 'harga_per_hari', 'subtotal');
    }

    /**
     * @return HasMany<Pembayaran, $this>
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * @return HasMany<Refund, $this>
     */
    public function refund(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * @return HasOne<Jaminan, $this>
     */
    public function jaminan(): HasOne
    {
        return $this->hasOne(Jaminan::class);
    }

    /**
     * @return HasOne<Pengembalian, $this>
     */
    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }

    /**
     * @return HasMany<Denda, $this>
     */
    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class);
    }
}
