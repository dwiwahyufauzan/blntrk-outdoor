<?php

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Storage;

if (! function_exists('wa_link')) {
    /**
     * Menghasilkan tautan langsung WhatsApp dengan normalisasi nomor 628xxx.
     */
    function wa_link(?string $noHp, string $pesan): string
    {
        return WhatsAppService::link($noHp, $pesan);
    }
}

if (! function_exists('like_pattern')) {
    /**
     * Membuat pola LIKE `%kata%` yang aman dari wildcard buatan pengguna.
     * Karakter `%`, `_`, dan `\` dibuang (bukan di-escape) agar perilakunya identik di MySQL, PostgreSQL, dan SQLite,
     * yang masing-masing menangani karakter escape LIKE secara berbeda.
     */
    function like_pattern(?string $kata): string
    {
        return '%'.str_replace(['%', '_', '\\'], '', trim((string) $kata)).'%';
    }
}

if (! function_exists('foto_url')) {
    /**
     * URL publik sebuah foto alat/paket sesuai disk penyimpanan aktif (FOTO_DISK).
     * Disk `public` memakai host permintaan saat ini; disk lain (mis. S3/R2) memakai URL disk tersebut.
     */
    function foto_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $disk = foto_disk();

        return $disk === 'public'
            ? asset('storage/'.$path)
            : Storage::disk($disk)->url($path);
    }
}

if (! function_exists('foto_disk')) {
    /**
     * Nama disk penyimpanan foto alat. Di hosting berdisk sementara (Render) isi FOTO_DISK=s3.
     */
    function foto_disk(): string
    {
        return (string) config('filesystems.foto_disk', 'public');
    }
}

if (! function_exists('reset_sandi_tersedia')) {
    /**
     * Tautan lupa kata sandi hanya berguna bila email benar-benar terkirim. Dengan mailer `log`/`array`
     * tautan hanya masuk ke berkas log, sehingga fitur disembunyikan dan reset dilakukan oleh admin/pemilik.
     */
    function reset_sandi_tersedia(): bool
    {
        return email_aktif();
    }
}

if (! function_exists('email_aktif')) {
    /**
     * Apakah email benar-benar terkirim (mailer bukan `log`/`array`). Dipakai untuk reset sandi dan pengingat otomatis.
     */
    function email_aktif(): bool
    {
        return ! in_array(config('mail.default'), ['log', 'array'], true);
    }
}
