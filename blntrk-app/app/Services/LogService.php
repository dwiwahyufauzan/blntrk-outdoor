<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\User;

class LogService
{
    /**
     * Mencatat jejak audit log aktivitas pengguna ke dalam sistem.
     *
     * @param  User|null  $u  Pengguna yang melakukan aksi (null bila tamu/sistem)
     * @param  string  $aksi  Nama aksi (contoh: 'login', 'bayar_pesanan', 'kembalikan_alat')
     * @param  string  $modul  Modul terkait (contoh: 'pesanan', 'pengembalian', 'alat', 'pengaturan')
     * @param  int|null  $ref  ID referensi data entitas yang diubah
     * @param  string|null  $desk  Deskripsi atau rincian tambahan aksi
     */
    public static function catat(?User $u, string $aksi, string $modul, ?int $ref = null, ?string $desk = null): void
    {
        try {
            LogAktivitas::create([
                'user_id' => $u?->id ?? auth()->id(),
                'aksi' => $aksi,
                'modul' => $modul,
                'ref_id' => $ref,
                'deskripsi' => $desk,
                'ip_address' => request()?->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // Kegagalan pencatatan log tidak boleh menggagalkan transaksi utama
        }
    }
}
