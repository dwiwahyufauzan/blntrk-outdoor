<?php

namespace App\Notifications;

use App\Models\Pesanan;
use App\Support\PengaturanToko;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email pengingat otomatis untuk pelanggan terdaftar: jatuh tempo pengembalian (kembali) dan keterlambatan (terlambat).
 */
class PengingatPesanan extends Notification
{
    public const KEMBALI = 'kembali';

    public const TERLAMBAT = 'terlambat';

    public function __construct(
        public Pesanan $pesanan,
        public string $jenis,
        public float $estimasiDenda = 0.0
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->pesanan;
        $toko = PengaturanToko::namaToko();
        $jatuhTempo = $p->jatuh_tempo?->translatedFormat('d F Y, H:i') ?? $p->tanggal_selesai->translatedFormat('d F Y');

        $mail = (new MailMessage)->greeting('Halo '.($p->pelanggan->nama_lengkap ?? 'Kak').',');

        if ($this->jenis === self::TERLAMBAT) {
            return $mail
                ->subject("[{$toko}] Pengembalian terlambat - {$p->no_resi}")
                ->line("Sewa perlengkapan dengan No Resi {$p->no_resi} telah melewati batas pengembalian ({$jatuhTempo} WIB).")
                ->line('Estimasi denda keterlambatan berjalan: Rp'.number_format($this->estimasiDenda, 0, ',', '.').'. Denda bertambah setiap hari hingga alat dikembalikan.')
                ->line('Mohon segera kembalikan perlengkapan ke toko agar kartu identitas jaminan Anda dapat diserahkan kembali.')
                ->action('Lihat Pesanan', route('pesanan.show', $p->no_resi));
        }

        return $mail
            ->subject("[{$toko}] Pengingat pengembalian - {$p->no_resi}")
            ->line("Sewa perlengkapan dengan No Resi {$p->no_resi} jatuh tempo pada {$jatuhTempo} WIB.")
            ->line('Mohon kembalikan perlengkapan tepat waktu untuk menghindari denda keterlambatan. Perlu memperpanjang? Hubungi toko sebelum jatuh tempo.')
            ->action('Lihat Pesanan', route('pesanan.show', $p->no_resi));
    }
}
