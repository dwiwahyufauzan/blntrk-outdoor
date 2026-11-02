<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Bukti transfer disimpan di disk privat (BUKTI_DISK) dan hanya dapat dibuka lewat rute terautentikasi.
 */
class BuktiTransferService
{
    public static function disk(): string
    {
        return (string) config('filesystems.bukti_disk', 'local');
    }

    public function simpan(Pesanan $pesanan, UploadedFile $file, User $pengunggah): void
    {
        $disk = Storage::disk(self::disk());

        $lama = $pesanan->bukti_transfer;
        $path = $file->storeAs(
            'bukti-transfer',
            $pesanan->no_resi.'-'.bin2hex(random_bytes(8)).'.'.$file->guessExtension(),
            self::disk()
        );

        $pesanan->update(['bukti_transfer' => $path, 'bukti_diunggah_pada' => now()]);

        if ($lama && $lama !== $path && $disk->exists($lama)) {
            $disk->delete($lama);
        }

        LogAktivitas::catat($pengunggah, 'pesanan.bukti', "Mengunggah bukti transfer pesanan [{$pesanan->no_resi}]", 'pesanan', $pesanan->id);
    }

    public function kirim(Pesanan $pesanan): StreamedResponse
    {
        $disk = Storage::disk(self::disk());

        abort_unless($pesanan->bukti_transfer && $disk->exists($pesanan->bukti_transfer), 404);

        return $disk->response($pesanan->bukti_transfer, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
