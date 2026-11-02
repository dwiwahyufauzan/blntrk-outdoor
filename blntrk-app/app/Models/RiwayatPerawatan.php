<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPerawatan extends Model
{
    public const MASUK = 'masuk';

    public const SELESAI = 'selesai';

    protected $table = 'riwayat_perawatan';

    protected $fillable = [
        'alat_id',
        'jenis',
        'jumlah',
        'keterangan',
        'pesanan_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class)->withTrashed();
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mencatat satu kejadian keluar/masuk perawatan. Kegagalan pencatatan tidak menggagalkan transaksi utama.
     */
    public static function catat(Alat $alat, string $jenis, int $jumlah, ?string $keterangan = null, ?User $user = null, ?int $pesananId = null): ?self
    {
        if ($jumlah <= 0) {
            return null;
        }

        return static::create([
            'alat_id' => $alat->id,
            'jenis' => $jenis,
            'jumlah' => $jumlah,
            'keterangan' => $keterangan !== null ? mb_substr($keterangan, 0, 255) : null,
            'pesanan_id' => $pesananId,
            'user_id' => $user?->id,
        ]);
    }
}
