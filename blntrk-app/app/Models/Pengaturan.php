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
     * Penanda di cache untuk kunci yang tidak ada di tabel, agar nilai default pemanggil
     * tidak ikut tersimpan (dan tidak terbawa ke pemanggil lain dengan default berbeda).
     */
    private const TIDAK_ADA = '__pengaturan_tidak_ada__';

    /**
     * Ambil nilai pengaturan dengan caching singkat (5 menit).
     */
    public static function ambil(string $kunci, mixed $default = null): mixed
    {
        $nilai = Cache::remember("pengaturan.{$kunci}", 300, function () use ($kunci) {
            $item = static::find($kunci);

            return $item ? $item->nilai : self::TIDAK_ADA;
        });

        return $nilai === self::TIDAK_ADA ? $default : $nilai;
    }

    /**
     * Batas maksimum durasi sewa (hari) sesuai pengaturan toko; minimal 1.
     */
    public static function maksDurasiHari(): int
    {
        return max(1, (int) static::ambil('maks_durasi_hari', 14));
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
