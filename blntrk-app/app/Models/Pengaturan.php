<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $primaryKey = 'kunci';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
    ];

    /**
     * Ambil nilai pengaturan dengan caching singkat (5 menit).
     */
    public static function ambil(string $kunci, mixed $default = null): mixed
    {
        return Cache::remember("pengaturan.{$kunci}", 300, function () use ($kunci, $default) {
            $item = static::find($kunci);
            return $item ? $item->nilai : $default;
        });
    }

    /**
     * Simpan atau perbarui nilai pengaturan dan reset cache.
     */
    public static function setel(string $kunci, string $nilai, ?string $keterangan = null): static
    {
        $data = ['nilai' => $nilai];
        if ($keterangan !== null) {
            $data['keterangan'] = $keterangan;
        }

        $item = static::updateOrCreate(['kunci' => $kunci], $data);
        Cache::forget("pengaturan.{$kunci}");

        return $item;
    }
}
