<?php

namespace App\Models;

use App\Enums\MetodePembayaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id',
        'jumlah',
        'metode',
        'jenis',
        'dibayar_pada',
        'dicatat_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'metode' => MetodePembayaran::class,
            'dibayar_pada' => 'datetime',
            'jumlah' => 'decimal:2',
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
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
