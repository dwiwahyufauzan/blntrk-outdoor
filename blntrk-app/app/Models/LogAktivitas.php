<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'aksi',
        'modul',
        'ref_id',
        'deskripsi',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper static untuk mencatat log aktivitas pengguna.
     */
    public static function catat(?User $user, string $aksi, ?string $deskripsi = null, ?string $modul = null, ?int $refId = null): ?static
    {
        try {
            return static::create([
                'user_id' => $user?->id,
                'aksi' => $aksi,
                'modul' => $modul ?? (str_contains($aksi, '.') ? explode('.', $aksi)[0] : 'umum'),
                'ref_id' => $refId,
                'deskripsi' => $deskripsi,
                'ip_address' => request()?->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            return null;
        }
    }
}
