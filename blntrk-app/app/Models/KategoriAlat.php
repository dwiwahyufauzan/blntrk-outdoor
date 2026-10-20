<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriAlat extends Model
{
    use HasFactory;

    protected $table = 'kategori_alat';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
    ];

    public function alat(): HasMany
    {
        return $this->hasMany(Alat::class, 'kategori_id');
    }
}
