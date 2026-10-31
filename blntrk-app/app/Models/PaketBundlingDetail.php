<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaketBundlingDetail extends Model
{
    use HasFactory;

    protected $table = 'paket_bundling_detail';

    protected $fillable = [
        'paket_bundling_id',
        'alat_id',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketBundling::class, 'paket_bundling_id');
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}
