<?php

namespace App\Models;

use App\Enums\MetodePembayaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $table = 'refund';

    protected $fillable = [
        'pesanan_id',
        'jumlah',
        'metode',
        'direfund_pada',
        'dicatat_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'metode' => MetodePembayaran::class,
            'direfund_pada' => 'datetime',
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
