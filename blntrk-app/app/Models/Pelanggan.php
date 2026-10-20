<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelanggan';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'no_hp',
        'alamat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function getIsWalkInAttribute(): bool
    {
        return is_null($this->user_id);
    }
}
