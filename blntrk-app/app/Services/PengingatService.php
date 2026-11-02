<?php

namespace App\Services;

use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use App\Notifications\PengingatPesanan;
use Carbon\Carbon;

/**
 * Mengirim email pengingat jatuh tempo dan keterlambatan, satu kali per jenis per pesanan.
 * Pelanggan walk-in (tanpa akun/email) dilewati; mereka tetap dihubungi lewat tautan WhatsApp di panel admin.
 */
class PengingatService
{
    /** Pengingat jatuh tempo dikirim bila batas kembali jatuh dalam rentang jam ini. */
    public const JAM_SEBELUM_JATUH_TEMPO = 24;

    public function __construct(protected DendaService $dendaService) {}

    /**
     * @return array{kembali: int, terlambat: int}
     */
    public function kirimSemua(?Carbon $now = null): array
    {
        $now ??= now();

        if (! email_aktif()) {
            return ['kembali' => 0, 'terlambat' => 0];
        }

        return [
            'kembali' => $this->kirimPengingatKembali($now),
            'terlambat' => $this->kirimPeringatanTerlambat($now),
        ];
    }

    public function kirimPengingatKembali(Carbon $now): int
    {
        $pesanan = Pesanan::with('pelanggan.user')
            ->where('status', StatusPesanan::Disewa)
            ->whereNull('pengingat_kembali_pada')
            ->where('jatuh_tempo', '>', $now)
            ->where('jatuh_tempo', '<=', $now->copy()->addHours(self::JAM_SEBELUM_JATUH_TEMPO))
            ->get();

        return $pesanan->filter(fn (Pesanan $p) => $this->kirim($p, PengingatPesanan::KEMBALI, 'pengingat_kembali_pada', $now))->count();
    }

    public function kirimPeringatanTerlambat(Carbon $now): int
    {
        $pesanan = Pesanan::with('pelanggan.user')
            ->where('status', StatusPesanan::Terlambat)
            ->whereNull('pengingat_terlambat_pada')
            ->get();

        return $pesanan->filter(function (Pesanan $p) use ($now) {
            $denda = (float) $this->dendaService->hitungKeterlambatan($p, $now)['jumlah'];

            return $this->kirim($p, PengingatPesanan::TERLAMBAT, 'pengingat_terlambat_pada', $now, $denda);
        })->count();
    }

    private function kirim(Pesanan $p, string $jenis, string $kolom, Carbon $now, float $denda = 0.0): bool
    {
        $user = $p->pelanggan?->user;

        if (! $user || blank($user->email) || ! $user->is_aktif) {
            return false;
        }

        try {
            $user->notify(new PengingatPesanan($p, $jenis, $denda));
        } catch (\Throwable $e) {
            // Kegagalan SMTP tidak boleh menghentikan scheduler; pesanan dicoba lagi pada putaran berikutnya
            report($e);

            return false;
        }

        $p->forceFill([$kolom => $now])->saveQuietly();

        return true;
    }
}
